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
    
    // Get and validate form data
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
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
    
    if (empty($password) || strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
        exit();
    }
    
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit();
    }
    
    if (!in_array($user_role, ['student', 'alumni', 'admin'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid user role.']);
        exit();
    }
    
    // Check if email already exists
    $check_email = "SELECT user_id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email address is already registered.']);
        exit();
    }
    
    // Handle profile image upload
    $profile_image = '';
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
            $profile_image = $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload profile image.']);
            exit();
        }
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $sql = "INSERT INTO users (first_name, last_name, email, password, user_role, phone, bio, profile_image, is_active, date_created) 
            VALUES ('$first_name', '$last_name', '$email', '$hashed_password', '$user_role', '$phone', '$bio', '$profile_image', $is_active, NOW())";
    
    if (mysqli_query($conn, $sql)) {
        $new_user_id = mysqli_insert_id($conn);
        
        // If user is alumni, create alumni profile entry
        if ($user_role === 'alumni') {
            $alumni_sql = "INSERT INTO alumni_profiles (user_id, major, graduation_year) VALUES ($new_user_id, 'Not Specified', 2024)";
            mysqli_query($conn, $alumni_sql);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'User created successfully!',
            'user_id' => $new_user_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create user: ' . mysqli_error($conn)]);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}
?>
