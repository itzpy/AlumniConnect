<?php
session_start();
require_once '../settings/db_class.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new db_connection();
    $conn = $db->db_conn();
    
    $edit_user_id = intval($_POST['user_id'] ?? 0);
    $current_user_id = $_SESSION['user_id'];
    $is_self = ($edit_user_id == $current_user_id);
    
    if ($edit_user_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
        exit();
    }
    
    // Get and validate form data
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_role = mysqli_real_escape_string($conn, $_POST['user_role'] ?? 'student');
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $bio = mysqli_real_escape_string($conn, trim($_POST['bio'] ?? ''));
    $is_active = isset($_POST['is_active']) && $_POST['is_active'] === '1' ? 1 : 0;
    
    // Validation
    if (empty($first_name) || empty($last_name)) {
        echo json_encode(['success' => false, 'message' => 'First name and last name are required.']);
        exit();
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Valid email address is required.']);
        exit();
    }
    
    // Check if email already exists for another user
    $check_email = "SELECT user_id FROM users WHERE email = '$email' AND user_id != $edit_user_id";
    $result = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email address is already in use by another user.']);
        exit();
    }
    
    // Validate password if changing
    $password_sql = "";
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            exit();
        }
        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
            exit();
        }
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $password_sql = ", password = '$hashed_password'";
    }
    
    // Role validation
    if (!in_array($user_role, ['student', 'alumni', 'admin'])) {
        $user_role = 'student';
    }
    
    // If editing self, keep current role and active status
    if ($is_self) {
        $current_data = $db->db_fetch_one("SELECT user_role, is_active FROM users WHERE user_id = $edit_user_id");
        $user_role = $current_data['user_role'];
        $is_active = $current_data['is_active'];
    }
    
    // Handle profile image upload
    $image_sql = "";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/profiles/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['profile_image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.']);
            exit();
        }
        
        // Validate file size (max 2MB)
        if ($_FILES['profile_image']['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 2MB.']);
            exit();
        }
        
        // Generate unique filename
        $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $filepath)) {
            // Delete old image if exists
            $old_image = $db->db_fetch_one("SELECT profile_image FROM users WHERE user_id = $edit_user_id");
            if (!empty($old_image['profile_image']) && file_exists($upload_dir . $old_image['profile_image'])) {
                unlink($upload_dir . $old_image['profile_image']);
            }
            $image_sql = ", profile_image = '$filename'";
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload profile image.']);
            exit();
        }
    }
    
    // Update user
    $sql = "UPDATE users SET 
            first_name = '$first_name', 
            last_name = '$last_name', 
            email = '$email', 
            user_role = '$user_role', 
            phone = '$phone', 
            bio = '$bio', 
            is_active = $is_active
            $password_sql
            $image_sql
            WHERE user_id = $edit_user_id";
    
    if (mysqli_query($conn, $sql)) {
        // Check if role changed to alumni - create profile if needed
        $old_role = $db->db_fetch_one("SELECT user_role FROM users WHERE user_id = $edit_user_id");
        if ($user_role === 'alumni') {
            $check_profile = $db->db_fetch_one("SELECT profile_id FROM alumni_profiles WHERE user_id = $edit_user_id");
            if (!$check_profile) {
                $alumni_sql = "INSERT INTO alumni_profiles (user_id, major, graduation_year) VALUES ($edit_user_id, 'Not Specified', 2024)";
                mysqli_query($conn, $alumni_sql);
            }
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'User updated successfully!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user: ' . mysqli_error($conn)]);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}
?>
