<?php
/**
 * Create Coupon Action
 * Creates a new coupon in the system
 */

session_start();
header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['coupon_code']) || empty($input['discount_type']) || !isset($input['discount_value'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

require_once '../classes/coupon_class.php';
$coupon = new Coupon();

// Check if coupon code already exists
$existing = $coupon->getCouponByCode($input['coupon_code']);
if ($existing) {
    echo json_encode(['success' => false, 'message' => 'Coupon code already exists']);
    exit;
}

$input['created_by'] = $_SESSION['user_id'];
$result = $coupon->createCoupon($input);

if ($result) {
    echo json_encode(['success' => true, 'coupon_id' => $result, 'message' => 'Coupon created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create coupon']);
}
?>
