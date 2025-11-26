<?php
/**
 * Remove from Cart Action
 * Removes items from shopping cart
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in first']);
    exit();
}

if (!isset($_POST['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$cart_id = intval($_POST['cart_id']);

require_once(dirname(__FILE__).'/../classes/cart_class.php');
$cart = new Cart();

$result = $cart->removeFromCart($cart_id);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
}
?>