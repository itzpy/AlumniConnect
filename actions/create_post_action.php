<?php
/**
 * Create Post Action
 * Creates a new post in the community feed
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

$content = isset($input['content']) ? trim($input['content']) : '';
$post_type = isset($input['post_type']) ? trim($input['post_type']) : 'general';
$title = isset($input['title']) ? trim($input['title']) : null;

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Post content is required']);
    exit;
}

if (strlen($content) > 2000) {
    echo json_encode(['success' => false, 'message' => 'Post content too long (max 2000 characters)']);
    exit;
}

// Handle image upload if present
$image_url = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = $_FILES['image']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image type']);
        exit;
    }
    
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($_FILES['image']['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'Image too large (max 5MB)']);
        exit;
    }
    
    $upload_dir = '../uploads/posts/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = 'post_' . $user_id . '_' . time() . '.' . $ext;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
        $image_url = $filename;
    }
}

require_once '../classes/post_class.php';

$post = new Post();
$post_id = $post->create_post($user_id, $content, $post_type, $title, $image_url);

if ($post_id) {
    // Get the created post with user info
    $new_post = $post->get_post($post_id);
    
    // Generate HTML for the new post
    $author_name = htmlspecialchars($new_post['first_name'] . ' ' . $new_post['last_name']);
    $author_role = ucfirst($new_post['user_role'] ?? 'Member');
    $colors = ['7A1E1E', '2563eb', '059669', '7c3aed', 'dc2626', 'ea580c'];
    $color = $colors[array_rand($colors)];
    
    // Build avatar HTML
    if (!empty($new_post['profile_image'])) {
        $avatar = '<img src="../uploads/profiles/' . htmlspecialchars($new_post['profile_image']) . '" alt="' . $author_name . '" class="w-12 h-12 rounded-full object-cover">';
    } else {
        $avatar = '<img src="https://ui-avatars.com/api/?name=' . urlencode($author_name) . '&background=' . $color . '&color=fff" alt="' . $author_name . '" class="w-12 h-12 rounded-full">';
    }
    
    // Build type label
    $type_label = '';
    if ($new_post['post_type'] !== 'general') {
        $type_class = $new_post['post_type'] === 'job' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700';
        $type_label = '<span class="px-2 py-1 text-xs rounded-full ' . $type_class . '">' . ucfirst($new_post['post_type']) . '</span>';
    }
    
    // Build title HTML
    $title_html = !empty($new_post['post_title']) ? '<h5 class="mt-2 font-semibold text-gray-900">' . htmlspecialchars($new_post['post_title']) . '</h5>' : '';
    
    // Build image HTML
    $image_html = '';
    if (!empty($new_post['image_url'])) {
        $image_html = '<div class="mt-3"><img src="../uploads/posts/' . htmlspecialchars($new_post['image_url']) . '" alt="Post image" class="rounded-lg max-h-96 object-cover w-full"></div>';
    }
    
    $post_html = '
        <div class="pb-6 border-b border-gray-200 post-item animate-fadeIn" data-post-id="' . $post_id . '">
            <div class="flex items-start space-x-3 mb-4">
                ' . $avatar . '
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-900">' . $author_name . '</h4>
                            <p class="text-sm text-gray-500">' . $author_role . '</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            ' . $type_label . '
                            <span class="text-sm text-gray-400">Just now</span>
                        </div>
                    </div>
                    ' . $title_html . '
                    <p class="mt-2 text-gray-700 leading-relaxed">' . nl2br(htmlspecialchars($new_post['post_content'])) . '</p>
                    ' . $image_html . '
                    <div class="flex items-center space-x-6 mt-4 text-sm">
                        <button onclick="toggleLike(' . $post_id . ', this)" 
                                class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors like-btn">
                            <i class="far fa-thumbs-up"></i>
                            <span class="like-count">0 Likes</span>
                        </button>
                        <button onclick="toggleComments(' . $post_id . ')" 
                                class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                            <i class="far fa-comment"></i>
                            <span>0 Comments</span>
                        </button>
                        <button class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                            <i class="far fa-share-square"></i>
                            <span>Share</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    ';
    
    echo json_encode([
        'success' => true,
        'message' => 'Post created successfully',
        'post_id' => $post_id,
        'post' => $new_post,
        'post_html' => $post_html
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create post']);
}
?>
