<?php

require_once '../settings/db_class.php';

/**
 * User class for handling all user operations (students, alumni, admin)
 */
class User extends db_connection
{
    private $user_id;
    private $first_name;
    private $last_name;
    private $email;
    private $password;
    private $user_role;
    private $phone;
    private $profile_image;
    private $bio;
    private $date_created;
    private $last_login;
    private $is_active;

    public function __construct($user_id = null)
    {
        parent::db_connect();
        if ($user_id) {
            $this->user_id = $user_id;
            $this->loadUser();
        }
    }

    private function loadUser()
    {
        if (!$this->user_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->first_name = $result['first_name'];
            $this->last_name = $result['last_name'];
            $this->email = $result['email'];
            $this->user_role = $result['user_role'];
            $this->phone = $result['phone'];
            $this->profile_image = $result['profile_image'];
            $this->bio = $result['bio'];
            $this->date_created = $result['date_created'];
            $this->last_login = $result['last_login'];
            $this->is_active = $result['is_active'];
        }
        return true;
    }

    /**
     * Register a new user
     */
    public function registerUser($first_name, $last_name, $email, $password, $user_role, $phone = null)
    {
        // Check if email already exists
        if ($this->getUserByEmail($email)) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Password should already be hashed before calling this method
        $stmt = $this->db->prepare("INSERT INTO users (first_name, last_name, email, password, user_role, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $password, $user_role, $phone);
        
        if ($stmt->execute()) {
            $user_id = $this->db->insert_id;
            return ['success' => true, 'user_id' => $user_id];
        }
        return ['success' => false, 'message' => 'Registration failed'];
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Login user
     */
    public function loginUser($email, $password)
    {
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'Account is inactive'];
        }
        
        if (password_verify($password, $user['password'])) {
            // Update last login
            $this->updateLastLogin($user['user_id']);
            unset($user['password']);
            return ['success' => true, 'user' => $user];
        }
        
        return ['success' => false, 'message' => 'Invalid password'];
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin($user_id)
    {
        $stmt = $this->db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    /**
     * Get user by ID
     */
    public function getUserById($user_id)
    {
        $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, email, user_role, phone, profile_image, bio, date_created, last_login FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update user profile
     */
    public function updateProfile($user_id, $data)
    {
        $fields = [];
        $values = [];
        $types = "";
        
        if (isset($data['first_name'])) {
            $fields[] = "first_name = ?";
            $values[] = $data['first_name'];
            $types .= "s";
        }
        if (isset($data['last_name'])) {
            $fields[] = "last_name = ?";
            $values[] = $data['last_name'];
            $types .= "s";
        }
        if (isset($data['phone'])) {
            $fields[] = "phone = ?";
            $values[] = $data['phone'];
            $types .= "s";
        }
        if (isset($data['bio'])) {
            $fields[] = "bio = ?";
            $values[] = $data['bio'];
            $types .= "s";
        }
        if (isset($data['profile_image'])) {
            $fields[] = "profile_image = ?";
            $values[] = $data['profile_image'];
            $types .= "s";
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $user_id;
        $types .= "i";
        
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    /**
     * Search users by name or email
     */
    public function searchUsers($search_term, $role = null)
    {
        if ($role) {
            $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, email, user_role, profile_image FROM users WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?) AND user_role = ? AND is_active = 1 LIMIT 50");
            $search = "%$search_term%";
            $stmt->bind_param("ssss", $search, $search, $search, $role);
        } else {
            $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, email, user_role, profile_image FROM users WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?) AND is_active = 1 LIMIT 50");
            $search = "%$search_term%";
            $stmt->bind_param("sss", $search, $search, $search);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all users with optional role filter
     */
    public function getAllUsers($role = null, $limit = 50, $offset = 0)
    {
        if ($role) {
            $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, email, user_role, profile_image, date_created FROM users WHERE user_role = ? AND is_active = 1 ORDER BY date_created DESC LIMIT ? OFFSET ?");
            $stmt->bind_param("sii", $role, $limit, $offset);
        } else {
            $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, email, user_role, profile_image, date_created FROM users WHERE is_active = 1 ORDER BY date_created DESC LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Delete/deactivate user
     */
    public function deactivateUser($user_id)
    {
        $stmt = $this->db->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    /**
     * Get user statistics
     */
    public function getUserStats()
    {
        $stats = [];
        
        // Total active users
        $result = $this->db->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
        $stats['total_users'] = $result->fetch_assoc()['total'];
        
        // Alumni count
        $result = $this->db->query("SELECT COUNT(*) as total FROM users WHERE user_role = 'alumni' AND is_active = 1");
        $stats['total_alumni'] = $result->fetch_assoc()['total'];
        
        // Student count
        $result = $this->db->query("SELECT COUNT(*) as total FROM users WHERE user_role = 'student' AND is_active = 1");
        $stats['total_students'] = $result->fetch_assoc()['total'];
        
        return $stats;
    }
}

?>
