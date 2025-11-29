<?php
session_start();
require_once '../settings/db_class.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new db_connection();
    
    $service_id = intval($_POST['service_id']);
    $service_name = $db->db_conn()->real_escape_string(trim($_POST['service_name']));
    $description = $db->db_conn()->real_escape_string(trim($_POST['description']));
    $price = floatval($_POST['price']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle image upload
    $image_sql = "";
    if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/services/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['service_image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.";
            header("Location: ../views/edit_service.php?id=" . $service_id);
            exit();
        }
        
        // Validate file size (max 5MB)
        if ($_FILES['service_image']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "File too large. Maximum size is 5MB.";
            header("Location: ../views/edit_service.php?id=" . $service_id);
            exit();
        }
        
        // Generate unique filename
        $extension = pathinfo($_FILES['service_image']['name'], PATHINFO_EXTENSION);
        $filename = 'service_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['service_image']['tmp_name'], $filepath)) {
            // Delete old image if exists
            $old_image_query = "SELECT service_image FROM services WHERE service_id = $service_id";
            $old_result = $db->db_query($old_image_query);
            if ($old_result && $old_row = $old_result->fetch_assoc()) {
                if (!empty($old_row['service_image']) && file_exists('../uploads/services/' . $old_row['service_image'])) {
                    unlink('../uploads/services/' . $old_row['service_image']);
                }
            }
            $image_sql = ", service_image = '$filename'";
        } else {
            $_SESSION['error'] = "Failed to upload image.";
            header("Location: ../views/edit_service.php?id=" . $service_id);
            exit();
        }
    }
    
    // Update service
    $sql = "UPDATE services SET 
            service_name = '$service_name', 
            description = '$description', 
            price = $price, 
            is_active = $is_active
            $image_sql
            WHERE service_id = $service_id";
    
    if ($db->db_query($sql)) {
        $_SESSION['success'] = "Service updated successfully!";
        header("Location: ../admin/services.php");
    } else {
        $_SESSION['error'] = "Failed to update service. Please try again.";
        header("Location: ../views/edit_service.php?id=" . $service_id);
    }
    exit();
} else {
    header("Location: ../admin/services.php");
    exit();
}
?>
