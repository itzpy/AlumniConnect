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

require_once '../classes/service_class.php';

$serviceClass = new Service();

// Soft delete the service
$result = $serviceClass->deleteService($service_id);

if ($result) {
    echo json_encode([
        'success' => true, 
        'message' => 'Service deleted successfully'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete service']);
}
?>
