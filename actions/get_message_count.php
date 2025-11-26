<?php
// Get message count for the logged-in user
session_start();

// Return JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'count' => 0]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Include database connection
require_once("../settings/db_class.php");

try {
    $db = new db_connection();
    $conn = $db->db_conn();
    
    if (!$conn) {
        echo json_encode(['success' => false, 'count' => 0]);
        exit();
    }
    
    // For now, return sample count
    // In the future, this would query a messages table
    // Example: SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0
    
    // Simulate dynamic count based on user activity
    // You can replace this with actual database query when messages table is created
    $count = 2; // Default sample count
    
    echo json_encode([
        'success' => true,
        'count' => $count
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'count' => 0,
        'error' => $e->getMessage()
    ]);
}
?>
