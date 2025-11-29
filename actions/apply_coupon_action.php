<?php
/**
 * Apply Coupon Action
 * Validates and applies a coupon code to the cart
 * Returns JSON response for AJAX handling
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to apply coupons']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$coupon_code = isset($input['coupon_code']) ? trim($input['coupon_code']) : '';

if (empty($coupon_code)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a coupon code']);
    exit;
}

require_once '../classes/coupon_class.php';
require_once '../classes/cart_class.php';

$coupon = new Coupon();
$cart = new Cart();

// Get cart total (before any discount)
$cart_summary = $cart->getCartSummary($user_id);
$cart_total = $cart_summary['subtotal'];

if ($cart_total <= 0) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
    exit;
}

// Validate coupon
$result = $coupon->validateCoupon($coupon_code, $user_id, $cart_total);

if ($result['valid']) {
    // Store coupon in session for checkout
    $_SESSION['applied_coupon'] = $result['coupon'];
    
    // Calculate new totals
    $discount = $result['coupon']['discount_amount'];
    $tax_rate = 0.00; // Your tax rate
    $new_subtotal = $cart_total - $discount;
    $tax_amount = $new_subtotal * $tax_rate;
    $new_total = $new_subtotal + $tax_amount;
    
    echo json_encode([
        'success' => true,
        'message' => $result['message'],
        'coupon' => $result['coupon'],
        'totals' => [
            'subtotal' => $cart_total,
            'discount' => $discount,
            'tax' => $tax_amount,
            'total' => $new_total
        ]
    ]);
} else {
    // Clear any previously applied coupon
    unset($_SESSION['applied_coupon']);
    
    echo json_encode([
        'success' => false,
        'message' => $result['message']
    ]);
}
?>
