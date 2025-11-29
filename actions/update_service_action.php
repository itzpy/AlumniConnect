<?php
/**
 * Update Service Action
 * Handles updating existing services in the platform
 * 
 * @category E-Commerce
 * @package  AlumniConnect
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once '../settings/db_class.php';
require_once '../classes/service_class.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check for service ID
if (empty($_POST['service_id']) || !is_numeric($_POST['service_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid service ID']);
    exit;
}

$service_id = intval($_POST['service_id']);

// Validate required fields
$required_fields = ['service_name', 'service_type', 'description', 'price'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
    }
}

// Validate description length
if (isset($_POST['description']) && strlen($_POST['description']) < 20) {
    $errors[] = 'Description must be at least 20 characters';
}

// Validate price
if (isset($_POST['price']) && (!is_numeric($_POST['price']) || floatval($_POST['price']) < 0)) {
    $errors[] = 'Price must be a valid positive number';
}

// Validate service type
$valid_types = ['job_posting', 'mentorship', 'event', 'premium_feature'];
if (!in_array($_POST['service_type'], $valid_types)) {
    $errors[] = 'Invalid service type';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

$service = new Service();

// Get existing service to check for existing image
$existing_service = $service->getServiceById($service_id);
if (!$existing_service) {
    echo json_encode(['success' => false, 'message' => 'Service not found']);
    exit;
}

// Handle image upload
$image_url = $_POST['existing_image'] ?? $existing_service['image_url'];

if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    $file = $_FILES['service_image'];
    
    // Validate file type
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image type. Allowed: JPG, PNG, GIF, WebP']);
        exit;
    }
    
    // Validate file size
    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'Image size must be less than 5MB']);
        exit;
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/services/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'service_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Delete old image if exists
        if ($existing_service['image_url'] && file_exists($upload_dir . $existing_service['image_url'])) {
            unlink($upload_dir . $existing_service['image_url']);
        }
        $image_url = $filename;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
        exit;
    }
}

// Prepare update data
$update_data = [
    'service_name' => trim($_POST['service_name']),
    'description' => trim($_POST['description']),
    'price' => floatval($_POST['price']),
    'is_active' => isset($_POST['is_active']) && $_POST['is_active'] === '1' ? 1 : 0,
    'image_url' => $image_url
];

// Add optional fields
if (!empty($_POST['duration'])) {
    $update_data['duration'] = intval($_POST['duration']);
}
if (!empty($_POST['provider_id'])) {
    $update_data['provider_id'] = intval($_POST['provider_id']);
}
if (!empty($_POST['category'])) {
    $update_data['category'] = trim($_POST['category']);
}
if (!empty($_POST['location'])) {
    $update_data['location'] = trim($_POST['location']);
}
if (!empty($_POST['stock_quantity'])) {
    $update_data['stock_quantity'] = intval($_POST['stock_quantity']);
}

// Update service
$result = $service->updateService($service_id, $update_data);

if ($result) {
    echo json_encode([
        'success' => true, 
        'message' => 'Service updated successfully!',
        'service_id' => $service_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update service. Please try again.']);
}
?>
