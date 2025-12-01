<?php
require_once(dirname(__FILE__) . '/../settings/db_class.php');

class Post extends db_connection {
    
    // Create a new post
    public function create_post($user_id, $content, $post_type = 'general', $title = null, $image_url = null) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $user_id = intval($user_id);
        $content = mysqli_real_escape_string($this->db, $content);
        $post_type = mysqli_real_escape_string($this->db, $post_type);
        $title_sql = $title ? "'" . mysqli_real_escape_string($this->db, $title) . "'" : "NULL";
        $image_sql = $image_url ? "'" . mysqli_real_escape_string($this->db, $image_url) . "'" : "NULL";
        
        $sql = "INSERT INTO posts (user_id, post_content, post_type, post_title, image_url) 
                VALUES ($user_id, '$content', '$post_type', $title_sql, $image_sql)";
        
        if (mysqli_query($this->db, $sql)) {
            return mysqli_insert_id($this->db);
        }
        return false;
    }
    
    // Get all posts for feed
    public function get_all_posts($limit = 20, $offset = 0) {
        $sql = "SELECT p.*, u.first_name, u.last_name, u.profile_image, u.user_role,
                       ap.job_title, ap.current_company, ap.graduation_year
                FROM posts p
                JOIN users u ON p.user_id = u.user_id
                LEFT JOIN alumni_profiles ap ON p.user_id = ap.user_id
                WHERE p.is_published = 1
                ORDER BY p.date_created DESC
                LIMIT $limit OFFSET $offset";
        return $this->db_fetch_all($sql);
    }
    
    // Get posts by user
    public function get_user_posts($user_id, $limit = 20) {
        $user_id = intval($user_id);
        $sql = "SELECT p.*, u.first_name, u.last_name, u.profile_image
                FROM posts p
                JOIN users u ON p.user_id = u.user_id
                WHERE p.user_id = $user_id AND p.is_published = 1
                ORDER BY p.date_created DESC
                LIMIT $limit";
        return $this->db_fetch_all($sql);
    }
    
    // Get single post
    public function get_post($post_id) {
        $post_id = intval($post_id);
        $sql = "SELECT p.*, u.first_name, u.last_name, u.profile_image, u.user_role,
                       ap.job_title, ap.current_company, ap.graduation_year
                FROM posts p
                JOIN users u ON p.user_id = u.user_id
                LEFT JOIN alumni_profiles ap ON p.user_id = ap.user_id
                WHERE p.post_id = $post_id";
        return $this->db_fetch_one($sql);
    }
    
    // Update post
    public function update_post($post_id, $user_id, $content, $image_url = null) {
        if (!$this->db_connect()) {
            return false;
        }
        $post_id = intval($post_id);
        $user_id = intval($user_id);
        $content = mysqli_real_escape_string($this->db, $content);
        
        $sql = "UPDATE posts SET post_content = '$content'";
        if ($image_url) {
            $image_url = mysqli_real_escape_string($this->db, $image_url);
            $sql .= ", image_url = '$image_url'";
        }
        $sql .= " WHERE post_id = $post_id AND user_id = $user_id";
        return mysqli_query($this->db, $sql);
    }
    
    // Delete post
    public function delete_post($post_id, $user_id) {
        if (!$this->db_connect()) {
            return false;
        }
        $post_id = intval($post_id);
        $user_id = intval($user_id);
        $sql = "DELETE FROM posts WHERE post_id = $post_id AND user_id = $user_id";
        return mysqli_query($this->db, $sql);
    }
    
    // Like a post
    public function like_post($post_id, $user_id) {
        if (!$this->db_connect()) {
            return false;
        }
        $post_id = intval($post_id);
        $user_id = intval($user_id);
        
        // Check if already liked
        $check = $this->db_fetch_one("SELECT * FROM post_likes WHERE post_id = $post_id AND user_id = $user_id");
        
        if ($check) {
            // Unlike
            mysqli_query($this->db, "DELETE FROM post_likes WHERE post_id = $post_id AND user_id = $user_id");
            mysqli_query($this->db, "UPDATE posts SET likes_count = GREATEST(likes_count - 1, 0) WHERE post_id = $post_id");
            return ['action' => 'unliked'];
        } else {
            // Like
            mysqli_query($this->db, "INSERT INTO post_likes (post_id, user_id) VALUES ($post_id, $user_id)");
            mysqli_query($this->db, "UPDATE posts SET likes_count = likes_count + 1 WHERE post_id = $post_id");
            return ['action' => 'liked'];
        }
    }
    
    // Check if user liked post
    public function has_liked($post_id, $user_id) {
        $post_id = intval($post_id);
        $user_id = intval($user_id);
        $result = $this->db_fetch_one("SELECT * FROM post_likes WHERE post_id = $post_id AND user_id = $user_id");
        return $result ? true : false;
    }
    
    // Add comment
    public function add_comment($post_id, $user_id, $comment) {
        if (!$this->db_connect()) {
            return false;
        }
        $post_id = intval($post_id);
        $user_id = intval($user_id);
        $comment = mysqli_real_escape_string($this->db, $comment);
        
        $sql = "INSERT INTO post_comments (post_id, user_id, comment_content) VALUES ($post_id, $user_id, '$comment')";
        if (mysqli_query($this->db, $sql)) {
            mysqli_query($this->db, "UPDATE posts SET comments_count = comments_count + 1 WHERE post_id = $post_id");
            return mysqli_insert_id($this->db);
        }
        return false;
    }
    
    // Get post comments
    public function get_comments($post_id, $limit = 20) {
        $post_id = intval($post_id);
        $sql = "SELECT c.comment_id, c.post_id, c.user_id, c.comment_content, c.date_created,
                       u.first_name, u.last_name, u.profile_image
                FROM post_comments c
                JOIN users u ON c.user_id = u.user_id
                WHERE c.post_id = $post_id
                ORDER BY c.date_created ASC
                LIMIT $limit";
        return $this->db_fetch_all($sql);
    }
    
    // Delete comment
    public function delete_comment($comment_id, $user_id) {
        if (!$this->db_connect()) {
            return false;
        }
        $comment_id = intval($comment_id);
        $user_id = intval($user_id);
        
        // Get post_id first
        $comment = $this->db_fetch_one("SELECT post_id FROM post_comments WHERE comment_id = $comment_id AND user_id = $user_id");
        if ($comment) {
            mysqli_query($this->db, "DELETE FROM post_comments WHERE comment_id = $comment_id AND user_id = $user_id");
            mysqli_query($this->db, "UPDATE posts SET comments_count = GREATEST(comments_count - 1, 0) WHERE post_id = " . $comment['post_id']);
            return true;
        }
        return false;
    }
    
    // Helper: time ago
    public static function time_ago($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('M j', $time);
    }
}
