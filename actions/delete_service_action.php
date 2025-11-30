<?php
/**
 * Delete Service Action
 * Soft deletes (deactivates) a service from admin panel
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

$service_id = isset($input['service_id']) ? intval($input['service_id']) : 0;

if (!$service_id) {
    echo json_encode(['success' => false, 'message' => 'Service ID is required']);
    exit;
}

require_once '../settings/db_class.php';

// Use direct database connection for reliability
$db = new db_connection();
if (!$db->db_connect()) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Soft delete the service by setting is_active = 0
$sql = "UPDATE services SET is_active = 0 WHERE service_id = $service_id";
$result = $db->db_query($sql);

if ($result) {
    echo json_encode([
        'success' => true, 
        'message' => 'Service deleted successfully'
    ]);
} else {
    error_log("Failed to delete service ID: $service_id");
    echo json_encode(['success' => false, 'message' => 'Failed to delete service']);
}
?>
