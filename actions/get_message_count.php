<?php
// Get unread message count for the logged-in user
session_start();

// Return JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'count' => 0]);
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Include database connection
require_once("../settings/db_class.php");

try {
    $db = new db_connection();
    $conn = $db->db_conn();
    
    if (!$conn) {
        echo json_encode(['success' => false, 'count' => 0]);
        exit();
    }
    
    // Count unread messages from the messages table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $count = intval($result['count']);
    
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
