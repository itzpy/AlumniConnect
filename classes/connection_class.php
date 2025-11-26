<?php

require_once '../settings/db_class.php';

/**
 * Connection class for handling user connections
 */
class Connection extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Send connection request
     */
    public function sendRequest($requester_id, $receiver_id, $message = null)
    {
        // Check if connection already exists
        if ($this->connectionExists($requester_id, $receiver_id)) {
            return ['success' => false, 'message' => 'Connection already exists'];
        }
        
        $stmt = $this->db->prepare("INSERT INTO connections (requester_id, receiver_id, request_message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $requester_id, $receiver_id, $message);
        
        if ($stmt->execute()) {
            return ['success' => true, 'connection_id' => $this->db->insert_id];
        }
        return ['success' => false, 'message' => 'Failed to send request'];
    }

    /**
     * Check if connection exists between two users
     */
    public function connectionExists($user1_id, $user2_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM connections WHERE (requester_id = ? AND receiver_id = ?) OR (requester_id = ? AND receiver_id = ?)");
        $stmt->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Accept connection request
     */
    public function acceptRequest($connection_id, $receiver_id)
    {
        $stmt = $this->db->prepare("UPDATE connections SET status = 'accepted', date_responded = CURRENT_TIMESTAMP WHERE connection_id = ? AND receiver_id = ?");
        $stmt->bind_param("ii", $connection_id, $receiver_id);
        return $stmt->execute();
    }

    /**
     * Reject connection request
     */
    public function rejectRequest($connection_id, $receiver_id)
    {
        $stmt = $this->db->prepare("UPDATE connections SET status = 'rejected', date_responded = CURRENT_TIMESTAMP WHERE connection_id = ? AND receiver_id = ?");
        $stmt->bind_param("ii", $connection_id, $receiver_id);
        return $stmt->execute();
    }

    /**
     * Get pending requests for a user
     */
    public function getPendingRequests($user_id)
    {
        $stmt = $this->db->prepare("SELECT c.*, u.first_name, u.last_name, u.profile_image, u.user_role FROM connections c JOIN users u ON c.requester_id = u.user_id WHERE c.receiver_id = ? AND c.status = 'pending' ORDER BY c.date_requested DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get sent requests by a user
     */
    public function getSentRequests($user_id)
    {
        $stmt = $this->db->prepare("SELECT c.*, u.first_name, u.last_name, u.profile_image FROM connections c JOIN users u ON c.receiver_id = u.user_id WHERE c.requester_id = ? ORDER BY c.date_requested DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all connections for a user
     */
    public function getUserConnections($user_id)
    {
        $stmt = $this->db->prepare("SELECT c.connection_id, u.user_id, u.first_name, u.last_name, u.profile_image, u.user_role 
                                     FROM connections c 
                                     JOIN users u ON (CASE WHEN c.requester_id = ? THEN c.receiver_id ELSE c.requester_id END) = u.user_id 
                                     WHERE (c.requester_id = ? OR c.receiver_id = ?) AND c.status = 'accepted'
                                     ORDER BY c.date_responded DESC");
        $stmt->bind_param("iii", $user_id, $user_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get connection count for a user
     */
    public function getConnectionCount($user_id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM connections WHERE (requester_id = ? OR receiver_id = ?) AND status = 'accepted'");
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    /**
     * Remove connection
     */
    public function removeConnection($connection_id, $user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM connections WHERE connection_id = ? AND (requester_id = ? OR receiver_id = ?)");
        $stmt->bind_param("iii", $connection_id, $user_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Get connection status between two users
     */
    public function getConnectionStatus($user1_id, $user2_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM connections WHERE (requester_id = ? AND receiver_id = ?) OR (requester_id = ? AND receiver_id = ?)");
        $stmt->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result;
    }
}

?>
