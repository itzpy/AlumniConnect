<?php
/**
 * Get Service Details Action
 * Returns service details as JSON for admin panel
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../classes/service_class.php';

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$service_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid service ID']);
    exit;
}

$serviceClass = new Service();

// Get service details
$service = $serviceClass->getServiceById($service_id);

if (!$service) {
    echo json_encode(['success' => false, 'message' => 'Service not found']);
    exit;
}

// Format response
$response = [
    'success' => true,
    'service_id' => $service['service_id'],
    'service_name' => $service['service_name'],
    'title' => $service['service_name'],
    'service_type' => $service['service_type'],
    'description' => $service['description'],
    'price' => $service['price'],
    'duration' => $service['duration'],
    'category' => $service['category'],
    'location' => $service['location'],
    'stock_quantity' => $service['stock_quantity'],
    'image' => $service['image_url'],
    'image_url' => $service['image_url'],
    'provider_name' => ($service['first_name'] ?? '') . ' ' . ($service['last_name'] ?? ''),
    'provider_email' => $service['email'] ?? '',
    'status' => $service['is_active'] ? 'Active' : 'Inactive',
    'is_active' => $service['is_active'],
    'date_created' => $service['date_created']
];

echo json_encode($response);
?>
