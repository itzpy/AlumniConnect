<?php
/**
 * Remove Coupon Action
 * Removes the applied coupon from the session
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Remove coupon from session
unset($_SESSION['applied_coupon']);

require_once '../classes/cart_class.php';
$cart = new Cart();
$cart_summary = $cart->getCartSummary($_SESSION['user_id']);

echo json_encode([
    'success' => true,
    'message' => 'Coupon removed',
    'totals' => [
        'subtotal' => $cart_summary['subtotal'],
        'discount' => 0,
        'tax' => $cart_summary['tax_amount'],
        'total' => $cart_summary['total']
    ]
]);
?>
