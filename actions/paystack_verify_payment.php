<?php
/**
 * Paystack Payment Verification
 * Handles payment verification after user returns from Paystack gateway
 * Supports both regular orders and subscription payments
 */

session_start();
header('Content-Type: application/json');

require_once '../settings/paystack_config.php';

error_log("=== PAYSTACK VERIFICATION ===");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Session expired. Please login again.'
    ]);
    exit();
}

// Get verification reference from POST data
$input = json_decode(file_get_contents('php://input'), true);
$reference = isset($input['reference']) ? trim($input['reference']) : null;

if (!$reference) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No payment reference provided'
    ]);
    exit();
}

// Check if this is a subscription payment (reference starts with SUB-)
$is_subscription = strpos($reference, 'SUB-') === 0;

// Optional: Verify reference matches session
if (isset($_SESSION['paystack_ref']) && $_SESSION['paystack_ref'] !== $reference) {
    error_log("Reference mismatch - Expected: {$_SESSION['paystack_ref']}, Got: $reference");
}

try {
    error_log("Verifying Paystack transaction - Reference: $reference");
    
    // Verify transaction with Paystack
    $verification_response = paystack_verify_transaction($reference);
    
    if (!$verification_response) {
        throw new Exception("No response from Paystack verification API");
    }
    
    error_log("Paystack verification response: " . json_encode($verification_response));
    
    // Check if verification was successful
    if (!isset($verification_response['status']) || $verification_response['status'] !== true) {
        $error_msg = $verification_response['message'] ?? 'Payment verification failed';
        error_log("Payment verification failed: $error_msg");
        
        echo json_encode([
            'status' => 'error',
            'message' => $error_msg,
            'verified' => false
        ]);
        exit();
    }
    
    // Extract transaction data
    $transaction_data = $verification_response['data'] ?? [];
    $payment_status = $transaction_data['status'] ?? null;
    $amount_paid = isset($transaction_data['amount']) ? $transaction_data['amount'] / 100 : 0; // Convert from pesewas
    $customer_email = $transaction_data['customer']['email'] ?? '';
    $authorization = $transaction_data['authorization'] ?? [];
    $authorization_code = $authorization['authorization_code'] ?? '';
    $payment_method = $authorization['channel'] ?? 'card';
    
    error_log("Transaction status: $payment_status, Amount: $amount_paid GHS");
    
    // Validate payment status
    if ($payment_status !== 'success') {
        error_log("Payment status is not successful: $payment_status");
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Payment was not successful. Status: ' . ucfirst($payment_status),
            'verified' => false,
            'payment_status' => $payment_status
        ]);
        exit();
    }
    
    // Handle subscription payment
    if ($is_subscription) {
        handleSubscriptionPayment($reference, $amount_paid, $customer_email, $transaction_data);
        exit();
    }
    
    // Get cart summary to validate amount
    require_once '../classes/cart_class.php';
    require_once '../classes/coupon_class.php';
    $cart = new Cart();
    $coupon_handler = new Coupon();
    $user_id = $_SESSION['user_id'];
    $cart_summary = $cart->getCartSummary($user_id);
    
    if (empty($cart_summary['items'])) {
        throw new Exception("Cart is empty");
    }
    
    // Calculate expected amount including any applied coupon
    $expected_amount = $cart_summary['total'];
    $discount_amount = 0;
    $applied_coupon = null;
    
    // Check for applied coupon
    if (isset($_SESSION['applied_coupon'])) {
        $applied_coupon = $_SESSION['applied_coupon'];
        $discount_amount = floatval($applied_coupon['discount_amount']);
        $expected_amount = $expected_amount - $discount_amount;
        if ($expected_amount < 0) $expected_amount = 0;
        
        error_log("Coupon applied: {$applied_coupon['coupon_code']}, Discount: $discount_amount GHS");
    }
    
    error_log("Expected order total (server): $expected_amount GHS (after discount)");

    // Verify amount matches (with 1 pesewa tolerance)
    if (abs($amount_paid - $expected_amount) > 0.01) {
        error_log("Amount mismatch - Expected: $expected_amount GHS, Paid: $amount_paid GHS");

        echo json_encode([
            'status' => 'error',
            'message' => 'Payment amount does not match order total',
            'verified' => false,
            'expected' => number_format($expected_amount, 2),
            'paid' => number_format($amount_paid, 2)
        ]);
        exit();
    }
    
    // Payment is verified! Now create the order in our system
    require_once '../classes/order_class.php';
    require_once '../classes/payment_class.php';
    require_once '../classes/cart_class.php';
    
    $user_name = $_SESSION['name'] ?? 'Customer';
    
    try {
        // Prepare billing info from session/payment data
        $billing_info = [
            'name' => $user_name,
            'email' => $customer_email,
            'phone' => $_SESSION['phone'] ?? '',
            'notes' => 'Payment via Paystack',
            'discount' => $discount_amount
        ];
        
        // Create order (this handles its own transaction)
        $order_class = new Order();
        $order_id = $order_class->createOrder($user_id, $billing_info);
        
        if (!$order_id) {
            throw new Exception("Failed to create order in database");
        }
        
        // Get the order number that was created
        $order_number = $order_class->getOrderNumber($order_id);
        
        error_log("Order created - ID: $order_id, Number: $order_number");
        
        // Update order payment status and coupon info (separate transaction)
        $conn = $order_class->db_conn();
        $coupon_code = $applied_coupon ? mysqli_real_escape_string($conn, $applied_coupon['coupon_code']) : '';
        $coupon_id = $applied_coupon ? intval($applied_coupon['coupon_id']) : 'NULL';
        
        $update_sql = "UPDATE orders SET 
                      payment_status = 'paid', 
                      payment_method = 'paystack',
                      payment_reference = '$reference',
                      coupon_code = " . ($coupon_code ? "'$coupon_code'" : "NULL") . ",
                      coupon_id = $coupon_id
                      WHERE order_id = $order_id";
        
        if (!mysqli_query($conn, $update_sql)) {
            throw new Exception("Failed to update order payment status");
        }
        
        // Record coupon usage if a coupon was applied
        if ($applied_coupon) {
            $coupon_handler->recordUsage(
                $applied_coupon['coupon_id'],
                $user_id,
                $order_id,
                $discount_amount
            );
            error_log("Coupon usage recorded - Coupon: {$applied_coupon['coupon_code']}, Order: $order_id");
            
            // Clear the applied coupon from session
            unset($_SESSION['applied_coupon']);
        }
        
        // Record payment in payments table
        $payment = new Payment();
        $payment_data = [
            'order_id' => $order_id,
            'amount' => $amount_paid,
            'currency' => 'GHS',
            'payment_gateway' => 'paystack',
            'transaction_reference' => $reference,
            'payment_status' => 'success',  // Must match ENUM value
            'payment_channel' => $payment_method,
            'customer_email' => $customer_email,
            'customer_phone' => $_SESSION['phone'] ?? null,
            'gateway_response' => json_encode($transaction_data)
        ];
        
        $payment_id = $payment->recordPayment($payment_data);
        
        if (!$payment_id) {
            throw new Exception("Failed to record payment");
        }
        
        error_log("Payment recorded - ID: $payment_id, Reference: $reference");
        
        // Clear cart after successful order
        $cart = new Cart();
        $cart->clearCart($user_id);
        error_log("Cart cleared for user $user_id");
        
        // Clear session payment data
        unset($_SESSION['paystack_ref']);
        unset($_SESSION['paystack_amount']);
        unset($_SESSION['paystack_timestamp']);
        
        // Return success response
        echo json_encode([
            'status' => 'success',
            'verified' => true,
            'message' => 'Payment successful! Order confirmed.',
            'order_id' => $order_id,
            'order_number' => $order_number,
            'total_amount' => number_format($amount_paid, 2),
            'currency' => 'GHS',
            'order_date' => date('F j, Y'),
            'customer_name' => $user_name,
            'item_count' => count($cart_summary['items']),
            'payment_reference' => $reference,
            'payment_method' => ucfirst($payment_method),
            'customer_email' => $customer_email
        ]);
        
    } catch (Exception $e) {
        error_log("Error in order/payment processing: " . $e->getMessage());
        
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error in Paystack verification: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'verified' => false,
        'message' => 'Payment processing error: ' . $e->getMessage()
    ]);
}

/**
 * Handle subscription payment verification
 */
function handleSubscriptionPayment($reference, $amount_paid, $customer_email, $transaction_data) {
    global $is_subscription;
    
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['name'] ?? 'User';
    
    // Get subscription details from session OR extract from reference
    // Reference format: SUB-{plan}-{user_id}-{timestamp}
    $plan = $_SESSION['subscription_plan'] ?? null;
    $expected_amount = floatval($_SESSION['subscription_amount'] ?? 0);
    
    // If session data is lost, extract plan from reference
    if (!$plan) {
        // Parse reference: SUB-professional-123-1234567890
        $ref_parts = explode('-', $reference);
        if (count($ref_parts) >= 3 && $ref_parts[0] === 'SUB') {
            $plan = $ref_parts[1]; // 'professional' or 'premium'
            error_log("Extracted plan from reference: $plan");
            
            // Set expected amount based on plan
            $plan_prices = ['professional' => 49, 'premium' => 99];
            $expected_amount = $plan_prices[$plan] ?? 0;
        }
    }
    
    if (!$plan || !in_array($plan, ['professional', 'premium'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid subscription plan',
            'verified' => false
        ]);
        return;
    }
    
    // Verify amount matches
    if (abs($amount_paid - $expected_amount) > 0.01) {
        error_log("Subscription amount mismatch - Expected: $expected_amount GHS, Paid: $amount_paid GHS");
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Payment amount does not match subscription price',
            'verified' => false
        ]);
        return;
    }
    
    try {
        // Create subscription
        require_once '../classes/subscription_class.php';
        $subscription = new Subscription();
        
        $subscription_id = $subscription->createSubscription($user_id, $plan, $reference);
        
        if (!$subscription_id) {
            throw new Exception("Failed to create subscription");
        }
        
        error_log("Subscription created - ID: $subscription_id, Plan: $plan, User: $user_id");
        
        // Update session with new plan
        $_SESSION['subscription_plan_type'] = $plan;
        
        // Clear subscription session data
        unset($_SESSION['subscription_ref']);
        unset($_SESSION['subscription_plan']);
        unset($_SESSION['subscription_amount']);
        
        echo json_encode([
            'status' => 'success',
            'verified' => true,
            'is_subscription' => true,
            'message' => 'Subscription activated successfully!',
            'subscription_id' => $subscription_id,
            'plan' => ucfirst($plan),
            'amount' => number_format($amount_paid, 2),
            'currency' => 'GHS',
            'customer_name' => $user_name,
            'customer_email' => $customer_email,
            'expires_at' => date('F j, Y', strtotime('+1 month'))
        ]);
        
    } catch (Exception $e) {
        error_log("Error creating subscription: " . $e->getMessage());
        
        echo json_encode([
            'status' => 'error',
            'verified' => false,
            'message' => 'Subscription creation failed: ' . $e->getMessage()
        ]);
    }
}
?>
