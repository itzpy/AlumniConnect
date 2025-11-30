<?php
/**
 * Get User Connections Action
 * Returns user's connections and pending requests
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

require_once '../classes/connection_class.php';

$connection = new Connection();

$type = isset($_GET['type']) ? $_GET['type'] : 'all';

$data = [];

switch ($type) {
    case 'pending':
        $data['pending'] = $connection->getPendingRequests($user_id);
        break;
    case 'sent':
        $data['sent'] = $connection->getSentRequests($user_id);
        break;
    case 'connections':
        $data['connections'] = $connection->getUserConnections($user_id);
        break;
    default:
        $data['pending'] = $connection->getPendingRequests($user_id);
        $data['sent'] = $connection->getSentRequests($user_id);
        $data['connections'] = $connection->getUserConnections($user_id);
        $data['count'] = $connection->getConnectionCount($user_id);
}

echo json_encode([
    'success' => true,
    'data' => $data
]);
?>
