<?php
/**
 * Subscription Payment Action
 * Initializes Paystack payment for subscription
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$plan = $input['plan'] ?? '';
$email = $input['email'] ?? '';
$amount = floatval($input['amount'] ?? 0);

// Validate
if (!in_array($plan, ['professional', 'premium'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid plan selected']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid amount']);
    exit;
}

// Include Paystack config
require_once '../settings/paystack_config.php';

try {
    // Generate reference
    $reference = 'SUB-' . $plan . '-' . $user_id . '-' . time();
    
    // Store in session for verification
    $_SESSION['subscription_ref'] = $reference;
    $_SESSION['subscription_plan'] = $plan;
    $_SESSION['subscription_amount'] = $amount;
    
    // Initialize transaction with Paystack
    $paystack_response = paystack_initialize_transaction($amount, $email, $reference);
    
    if ($paystack_response && $paystack_response['status'] === true) {
        echo json_encode([
            'success' => true,
            'authorization_url' => $paystack_response['data']['authorization_url'],
            'reference' => $reference,
            'access_code' => $paystack_response['data']['access_code']
        ]);
    } else {
        throw new Exception($paystack_response['message'] ?? 'Paystack initialization failed');
    }
    
} catch (Exception $e) {
    error_log("Subscription payment error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Payment initialization failed: ' . $e->getMessage()
    ]);
}
?>
