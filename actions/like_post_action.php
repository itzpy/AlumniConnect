<?php
/**
 * Like Post Action
 * Toggles like on a post
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

$input = json_decode(file_get_contents('php://input'), true);
$post_id = isset($input['post_id']) ? intval($input['post_id']) : 0;

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Post ID required']);
    exit;
}

require_once '../classes/post_class.php';

$post = new Post();
$result = $post->like_post($post_id, $user_id);

if ($result) {
    // Get updated like count
    $post_data = $post->get_post($post_id);
    
    echo json_encode([
        'success' => true,
        'action' => $result['action'],
        'liked' => $result['action'] === 'liked',
        'likes_count' => $post_data['likes_count'] ?? 0
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update like']);
}
?>
