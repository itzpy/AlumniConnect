<?php
/**
 * Add Service Action
 * Handles the creation of new services in the platform
 * 
 * @category E-Commerce
 * @package  AlumniConnect
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin (check both user_type and user_role)
$is_admin = false;
if (isset($_SESSION['user_id'])) {
    if ((isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') ||
        (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin')) {
        $is_admin = true;
    }
}

if (!$is_admin) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please login as admin.']);
    exit;
}

require_once '../settings/db_class.php';
require_once '../classes/service_class.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

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

// Handle image upload
$image_url = null;
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
    
    // Create uploads directory if it doesn't exist - use absolute path
    $upload_dir = dirname(__DIR__) . '/uploads/services/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'service_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $image_url = $filename;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image. Check folder permissions.']);
        exit;
    }
}

// Prepare service data
$service_data = [
    'service_name' => trim($_POST['service_name']),
    'service_type' => $_POST['service_type'],
    'description' => trim($_POST['description']),
    'price' => floatval($_POST['price']),
    'duration' => !empty($_POST['duration']) ? intval($_POST['duration']) : null,
    'provider_id' => !empty($_POST['provider_id']) ? intval($_POST['provider_id']) : null,
    'category' => !empty($_POST['category']) ? trim($_POST['category']) : null,
    'location' => !empty($_POST['location']) ? trim($_POST['location']) : null,
    'stock_quantity' => !empty($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : null,
    'image_url' => $image_url
];

// Create service
$service = new Service();

try {
    $result = $service->addService($service_data);

    if ($result) {
        // If is_active was set to 0, update it
        if (isset($_POST['is_active']) && $_POST['is_active'] === '0') {
            $service->updateService($result, ['is_active' => 0]);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Service added successfully!',
            'service_id' => $result
        ]);
    } else {
        // Delete uploaded image if service creation failed
        if ($image_url && file_exists('../uploads/services/' . $image_url)) {
            unlink('../uploads/services/' . $image_url);
        }
        
        echo json_encode(['success' => false, 'message' => 'Failed to add service. Database error occurred.']);
    }
} catch (Exception $e) {
    // Delete uploaded image if service creation failed
    if ($image_url && file_exists('../uploads/services/' . $image_url)) {
        unlink('../uploads/services/' . $image_url);
    }
    
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
