<?php
/**
 * Get Posts Action
 * Returns posts for the feed
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Also support direct offset parameter
if (isset($_GET['offset'])) {
    $offset = intval($_GET['offset']);
}

require_once '../classes/post_class.php';

$post = new Post();
$posts = $post->get_all_posts($limit, $offset);

// Add liked status and time_ago for each post
if ($posts) {
    foreach ($posts as &$p) {
        $p['user_liked'] = $post->has_liked($p['post_id'], $user_id);
        $p['time_ago'] = Post::time_ago($p['date_created']);
    }
}

echo json_encode([
    'success' => true,
    'posts' => $posts ?: [],
    'count' => count($posts ?: [])
]);
?>
