<?php
/**
 * Verify Payment Action
 * Verifies payment with Paystack and updates order status
 */

session_start();
header('Content-Type: application/json');

// Include Paystack config for API keys
require_once '../settings/paystack_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in first']);
    exit();
}

if (!isset($_POST['reference']) || !isset($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$reference = $_POST['reference'];
$order_id = intval($_POST['order_id']);

require_once(dirname(__FILE__).'/../classes/order_class.php');
$order = new Order();

// Get order details
$order_details = $order->getOrderById($order_id);
if (!$order_details || $order_details['user_id'] != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

// Verify payment with Paystack
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
        "Cache-Control: no-cache",
    ],
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(['success' => false, 'message' => 'Payment verification failed: ' . $err]);
    exit();
}

$result = json_decode($response, true);

if ($result && $result['status'] && $result['data']['status'] === 'success') {
    // Payment successful - update order
    $payment_method = 'paystack';
    $payment_reference = $reference;
    
    // Update order payment status
    $order->updatePaymentStatus($order_id, 'paid', $payment_method, $payment_reference);
    $order->updateOrderStatus($order_id, 'processing');
    
    // Record payment in payments table
    require_once(dirname(__FILE__).'/../classes/payment_class.php');
    $payment = new Payment();
    
    $payment_data = [
        'order_id' => $order_id,
        'payment_gateway' => 'paystack',
        'transaction_reference' => $reference,
        'amount' => $result['data']['amount'] / 100, // Convert from pesewas to GHS
        'currency' => $result['data']['currency'],
        'payment_status' => 'success',
        'payment_channel' => $result['data']['channel'],
        'customer_email' => $result['data']['customer']['email'],
        'customer_phone' => isset($result['data']['customer']['phone']) ? $result['data']['customer']['phone'] : null,
        'gateway_response' => json_encode($result['data'])
    ];
    
    $payment->recordPayment($payment_data);
    
    // Generate invoice
    $order->generateInvoice($order_id);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Payment verified successfully',
        'order_number' => $order_details['order_number']
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Payment verification failed: ' . ($result['message'] ?? 'Unknown error')
    ]);
}
?>