<?php
/**
 * Get Order Details Action
 * Returns order details as JSON for admin panel
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../classes/order_class.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

$orderClass = new Order();

// Get order details
$order = $orderClass->getOrderById($order_id);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

// Get order items
$items = $orderClass->getOrderItems($order_id);

// Format response
$response = [
    'success' => true,
    'order_id' => $order['order_id'],
    'order_number' => $order['order_number'],
    'customer_name' => $order['first_name'] . ' ' . $order['last_name'],
    'customer_email' => $order['email'],
    'total_amount' => $order['final_amount'],
    'status' => $order['order_status'],
    'payment_status' => $order['payment_status'],
    'created_at' => $order['date_created'],
    'billing_name' => $order['billing_name'],
    'billing_email' => $order['billing_email'],
    'billing_phone' => $order['billing_phone'],
    'notes' => $order['notes'],
    'items' => []
];

if ($items) {
    foreach ($items as $item) {
        $response['items'][] = [
            'service_name' => $item['service_name'],
            'quantity' => $item['quantity'],
            'price' => $item['unit_price'],
            'total' => $item['total_price'],
            'selected_date' => $item['selected_date'],
            'selected_time' => $item['selected_time'],
            'special_requests' => $item['special_requests'],
            'fulfillment_status' => $item['fulfillment_status']
        ];
    }
}

echo json_encode($response);
?>
