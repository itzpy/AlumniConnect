<?php
require_once(dirname(__FILE__) . '/../settings/db_class.php');

class Post extends db_connection {
    
    // Create a new post
    public function create_post($user_id, $user_type, $content, $image_url = null, $post_type = 'general') {
        $sql = "INSERT INTO posts (user_id, user_type, content, image_url, post_type, created_at) 
                VALUES ('$user_id', '$user_type', '$content', " . ($image_url ? "'$image_url'" : "NULL") . ", '$post_type', NOW())";
        return $this->db_query($sql);
    }
    
    // Get all posts for feed
    public function get_all_posts($limit = 20, $offset = 0) {
        $sql = "SELECT p.*, 
                       COALESCE(a.first_name, s.first_name) as first_name,
                       COALESCE(a.last_name, s.last_name) as last_name,
                       COALESCE(a.profile_image, s.profile_image) as profile_image,
                       COALESCE(a.current_company, 'Student') as current_company,
                       COALESCE(a.current_position, s.major) as position_or_major,
                       (SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id) as like_count,
                       (SELECT COUNT(*) FROM post_comments WHERE post_id = p.post_id) as comment_count
                FROM posts p
                LEFT JOIN alumni a ON p.user_id = a.alumni_id AND p.user_type = 'alumni'
                LEFT JOIN students s ON p.user_id = s.student_id AND p.user_type = 'student'
                ORDER BY p.created_at DESC
                LIMIT $limit OFFSET $offset";
        return $this->db_fetch_all($sql);
    }
    
    // Get posts by user
    public function get_user_posts($user_id, $user_type) {
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id) as like_count,
                       (SELECT COUNT(*) FROM post_comments WHERE post_id = p.post_id) as comment_count
                FROM posts p
                WHERE p.user_id = '$user_id' AND p.user_type = '$user_type'
                ORDER BY p.created_at DESC";
        return $this->db_fetch_all($sql);
    }
    
    // Get single post
    public function get_post($post_id) {
        $sql = "SELECT p.*, 
                       COALESCE(a.first_name, s.first_name) as first_name,
                       COALESCE(a.last_name, s.last_name) as last_name,
                       COALESCE(a.profile_image, s.profile_image) as profile_image,
                       (SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id) as like_count,
                       (SELECT COUNT(*) FROM post_comments WHERE post_id = p.post_id) as comment_count
                FROM posts p
                LEFT JOIN alumni a ON p.user_id = a.alumni_id AND p.user_type = 'alumni'
                LEFT JOIN students s ON p.user_id = s.student_id AND p.user_type = 'student'
                WHERE p.post_id = '$post_id'";
        return $this->db_fetch_one($sql);
    }
    
    // Update post
    public function update_post($post_id, $content, $image_url = null) {
        $sql = "UPDATE posts 
                SET content = '$content'" . ($image_url ? ", image_url = '$image_url'" : "") . "
                WHERE post_id = '$post_id'";
        return $this->db_query($sql);
    }
    
    // Delete post
    public function delete_post($post_id, $user_id) {
        $sql = "DELETE FROM posts WHERE post_id = '$post_id' AND user_id = '$user_id'";
        return $this->db_query($sql);
    }
    
    // Like a post
    public function like_post($post_id, $user_id, $user_type) {
        $sql = "INSERT INTO post_likes (post_id, user_id, user_type, created_at) 
                VALUES ('$post_id', '$user_id', '$user_type', NOW())";
        return $this->db_query($sql);
    }
    
    // Unlike a post
    public function unlike_post($post_id, $user_id) {
        $sql = "DELETE FROM post_likes WHERE post_id = '$post_id' AND user_id = '$user_id'";
        return $this->db_query($sql);
    }
    
    // Check if user liked post
    public function has_liked($post_id, $user_id) {
        $sql = "SELECT * FROM post_likes WHERE post_id = '$post_id' AND user_id = '$user_id'";
        return $this->db_fetch_one($sql) ? true : false;
    }
    
    // Add comment
    public function add_comment($post_id, $user_id, $user_type, $comment) {
        $sql = "INSERT INTO post_comments (post_id, user_id, user_type, comment, created_at) 
                VALUES ('$post_id', '$user_id', '$user_type', '$comment', NOW())";
        return $this->db_query($sql);
    }
    
    // Get post comments
    public function get_comments($post_id) {
        $sql = "SELECT c.*, 
                       COALESCE(a.first_name, s.first_name) as first_name,
                       COALESCE(a.last_name, s.last_name) as last_name,
                       COALESCE(a.profile_image, s.profile_image) as profile_image
                FROM post_comments c
                LEFT JOIN alumni a ON c.user_id = a.alumni_id AND c.user_type = 'alumni'
                LEFT JOIN students s ON c.user_id = s.student_id AND c.user_type = 'student'
                WHERE c.post_id = '$post_id'
                ORDER BY c.created_at ASC";
        return $this->db_fetch_all($sql);
    }
    
    // Delete comment
    public function delete_comment($comment_id, $user_id) {
        $sql = "DELETE FROM post_comments WHERE comment_id = '$comment_id' AND user_id = '$user_id'";
        return $this->db_query($sql);
    }
}
?>
