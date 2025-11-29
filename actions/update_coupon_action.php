<?php
/**
 * Update Coupon Action
 * Updates an existing coupon
 */

session_start();
header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['coupon_id'])) {
    echo json_encode(['success' => false, 'message' => 'Coupon ID required']);
    exit;
}

require_once '../classes/coupon_class.php';
$coupon = new Coupon();

$coupon_id = intval($input['coupon_id']);
unset($input['coupon_id']);
unset($input['coupon_code']); // Don't allow code change

$result = $coupon->updateCoupon($coupon_id, $input);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Coupon updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update coupon']);
}
?>
