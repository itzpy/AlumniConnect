<?php
/**
 * Handle Connection Request Action
 * Accept or reject a connection request
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
    $input = $_POST;
}

$connection_id = isset($input['connection_id']) ? intval($input['connection_id']) : 0;
$action = isset($input['action']) ? trim($input['action']) : '';

if (!$connection_id) {
    echo json_encode(['success' => false, 'message' => 'Connection ID is required']);
    exit;
}

if (!in_array($action, ['accept', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

require_once '../classes/connection_class.php';

$connection = new Connection();

if ($action === 'accept') {
    $result = $connection->acceptRequest($connection_id, $user_id);
    $message = $result ? 'Connection accepted' : 'Failed to accept connection';
} else {
    $result = $connection->rejectRequest($connection_id, $user_id);
    $message = $result ? 'Connection rejected' : 'Failed to reject connection';
}

echo json_encode([
    'success' => $result,
    'message' => $message
]);
?>
