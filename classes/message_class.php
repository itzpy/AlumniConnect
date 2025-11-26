<?php

require_once '../settings/db_class.php';

/**
 * Message class for handling direct messaging
 */
class Message extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Send a message
     */
    public function sendMessage($sender_id, $receiver_id, $content)
    {
        $stmt = $this->db->prepare("INSERT INTO messages (sender_id, receiver_id, message_content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $content);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message_id' => $this->db->insert_id];
        }
        return ['success' => false, 'message' => 'Failed to send message'];
    }

    /**
     * Get conversation between two users
     */
    public function getConversation($user1_id, $user2_id, $limit = 50)
    {
        $stmt = $this->db->prepare("SELECT m.*, u1.first_name as sender_first_name, u1.last_name as sender_last_name, u1.profile_image as sender_image 
                                     FROM messages m 
                                     JOIN users u1 ON m.sender_id = u1.user_id 
                                     WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) 
                                     ORDER BY m.date_sent DESC 
                                     LIMIT ?");
        $stmt->bind_param("iiiii", $user1_id, $user2_id, $user2_id, $user1_id, $limit);
        $stmt->execute();
        return array_reverse($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    }

    /**
     * Get all conversations for a user
     */
    public function getUserConversations($user_id)
    {
        $sql = "SELECT 
                    CASE 
                        WHEN m.sender_id = ? THEN m.receiver_id 
                        ELSE m.sender_id 
                    END as other_user_id,
                    u.first_name, 
                    u.last_name, 
                    u.profile_image,
                    MAX(m.date_sent) as last_message_date,
                    (SELECT message_content FROM messages m2 
                     WHERE (m2.sender_id = ? AND m2.receiver_id = other_user_id) 
                        OR (m2.receiver_id = ? AND m2.sender_id = other_user_id) 
                     ORDER BY m2.date_sent DESC LIMIT 1) as last_message,
                    (SELECT COUNT(*) FROM messages m3 
                     WHERE m3.receiver_id = ? AND m3.sender_id = other_user_id AND m3.is_read = 0) as unread_count
                FROM messages m
                JOIN users u ON (CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END) = u.user_id
                WHERE m.sender_id = ? OR m.receiver_id = ?
                GROUP BY other_user_id, u.first_name, u.last_name, u.profile_image
                ORDER BY last_message_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiiiiii", $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mark message as read
     */
    public function markAsRead($message_id, $user_id)
    {
        $stmt = $this->db->prepare("UPDATE messages SET is_read = 1, date_read = CURRENT_TIMESTAMP WHERE message_id = ? AND receiver_id = ?");
        $stmt->bind_param("ii", $message_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Mark all messages in a conversation as read
     */
    public function markConversationAsRead($receiver_id, $sender_id)
    {
        $stmt = $this->db->prepare("UPDATE messages SET is_read = 1, date_read = CURRENT_TIMESTAMP WHERE receiver_id = ? AND sender_id = ? AND is_read = 0");
        $stmt->bind_param("ii", $receiver_id, $sender_id);
        return $stmt->execute();
    }

    /**
     * Get unread message count for a user
     */
    public function getUnreadCount($user_id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    /**
     * Delete a message
     */
    public function deleteMessage($message_id, $user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM messages WHERE message_id = ? AND (sender_id = ? OR receiver_id = ?)");
        $stmt->bind_param("iii", $message_id, $user_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Search messages
     */
    public function searchMessages($user_id, $search_term)
    {
        $search = "%$search_term%";
        $stmt = $this->db->prepare("SELECT m.*, u.first_name, u.last_name FROM messages m JOIN users u ON (CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END) = u.user_id WHERE (m.sender_id = ? OR m.receiver_id = ?) AND m.message_content LIKE ? ORDER BY m.date_sent DESC LIMIT 50");
        $stmt->bind_param("iiis", $user_id, $user_id, $user_id, $search);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

?>
