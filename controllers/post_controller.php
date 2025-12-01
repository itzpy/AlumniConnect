<?php
require_once(dirname(__FILE__) . '/../classes/post_class.php');

// Create post
function create_post_ctr($user_id, $user_type, $content, $image_url = null, $post_type = 'general') {
    $post = new Post();
    return $post->create_post($user_id, $user_type, $content, $image_url, $post_type);
}

// Get all posts
function get_all_posts_ctr($limit = 20, $offset = 0) {
    $post = new Post();
    return $post->get_all_posts($limit, $offset);
}

// Get user posts
function get_user_posts_ctr($user_id, $user_type) {
    $post = new Post();
    return $post->get_user_posts($user_id, $user_type);
}

// Get single post
function get_post_ctr($post_id) {
    $post = new Post();
    return $post->get_post($post_id);
}

// Update post
function update_post_ctr($post_id, $content, $image_url = null) {
    $post = new Post();
    return $post->update_post($post_id, $content, $image_url);
}

// Delete post
function delete_post_ctr($post_id, $user_id) {
    $post = new Post();
    return $post->delete_post($post_id, $user_id);
}

// Like/Unlike post (toggle)
function like_post_ctr($post_id, $user_id) {
    $post = new Post();
    return $post->like_post($post_id, $user_id);
}

// Check if liked
function has_liked_post_ctr($post_id, $user_id) {
    $post = new Post();
    return $post->has_liked($post_id, $user_id);
}

// Add comment
function add_comment_ctr($post_id, $user_id, $user_type, $comment) {
    $post = new Post();
    return $post->add_comment($post_id, $user_id, $user_type, $comment);
}

// Get comments
function get_comments_ctr($post_id) {
    $post = new Post();
    return $post->get_comments($post_id);
}

// Delete comment
function delete_comment_ctr($comment_id, $user_id) {
    $post = new Post();
    return $post->delete_comment($comment_id, $user_id);
}
?>
