<?php
/**
 * Send Connection Request Action
 * Sends a connection request to another user
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Try POST
    $input = $_POST;
}

$receiver_id = isset($input['receiver_id']) ? intval($input['receiver_id']) : 0;
$message = isset($input['message']) ? trim($input['message']) : null;

if (!$receiver_id) {
    echo json_encode(['success' => false, 'message' => 'Receiver ID is required']);
    exit;
}

if ($receiver_id == $user_id) {
    echo json_encode(['success' => false, 'message' => 'Cannot connect with yourself']);
    exit;
}

require_once '../classes/connection_class.php';

$connection = new Connection();

$result = $connection->sendRequest($user_id, $receiver_id, $message);

echo json_encode($result);
?>
