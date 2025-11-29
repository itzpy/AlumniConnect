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
    
    $delete_user_id = intval($_POST['user_id'] ?? 0);
    $current_user_id = $_SESSION['user_id'];
    
    if ($delete_user_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
        exit();
    }
    
    // Cannot delete self
    if ($delete_user_id == $current_user_id) {
        echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
        exit();
    }
    
    // Check if user exists
    $check_user = $db->db_fetch_one("SELECT user_id, profile_image FROM users WHERE user_id = $delete_user_id");
    if (!$check_user) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit();
    }
    
    // Delete profile image if exists
    if (!empty($check_user['profile_image'])) {
        $image_path = '../uploads/profiles/' . $check_user['profile_image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete user (cascading will handle related records if foreign keys are set up)
    $sql = "DELETE FROM users WHERE user_id = $delete_user_id";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            'success' => true, 
            'message' => 'User deleted successfully!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user: ' . mysqli_error($conn)]);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}
?>
