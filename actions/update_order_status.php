<?php
/**
 * Update Order Status Action
 * Updates order status from admin panel
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$order_id = isset($input['order_id']) ? intval($input['order_id']) : 0;
$status = isset($input['status']) ? trim($input['status']) : '';
$notes = isset($input['notes']) ? trim($input['notes']) : '';

if (!$order_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
    exit;
}

// Validate status
$valid_statuses = ['pending', 'processing', 'completed', 'cancelled', 'refunded'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

require_once '../classes/order_class.php';

$orderClass = new Order();

// Update order status
$result = $orderClass->updateOrderStatus($order_id, $status);

if ($result) {
    echo json_encode([
        'success' => true, 
        'message' => 'Order status updated successfully',
        'new_status' => $status
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
}
?>
