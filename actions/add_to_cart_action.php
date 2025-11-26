<?php
/**
 * Add to Cart Action
 * Handles adding services to user's shopping cart
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in first']);
    exit();
}

// Validate input
if (!isset($_POST['service_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$user_id = $_SESSION['user_id'];
$service_id = intval($_POST['service_id']);
$quantity = intval($_POST['quantity']);
$selected_date = isset($_POST['selected_date']) ? $_POST['selected_date'] : null;
$selected_time = isset($_POST['selected_time']) ? $_POST['selected_time'] : null;
$special_requests = isset($_POST['special_requests']) ? $_POST['special_requests'] : null;

// Validate quantity
if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit();
}

// Include required classes
require_once(dirname(__FILE__).'/../classes/cart_class.php');
require_once(dirname(__FILE__).'/../classes/service_class.php');

$cart = new Cart();
$service = new Service();

// Check if service exists and is available
$service_details = $service->getServiceById($service_id);
if (!$service_details) {
    echo json_encode(['success' => false, 'message' => 'Service not found']);
    exit();
}

// Check availability for events with limited stock
if (!$service->checkAvailability($service_id, $quantity)) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock available']);
    exit();
}

// Add to cart
$result = $cart->addToCart($user_id, $service_id, $quantity, $selected_date, $selected_time, $special_requests);

if ($result) {
    $cart_count = $cart->getCartCount($user_id);
    echo json_encode([
        'success' => true, 
        'message' => 'Added to cart successfully',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add to cart']);
}
?>
