<?php
require_once(dirname(__FILE__) . '/../classes/connection_class.php');

// Send connection request
function send_connection_request_ctr($requester_id, $receiver_id, $message = null) {
    $connection = new Connection();
    return $connection->sendRequest($requester_id, $receiver_id, $message);
}

// Accept connection
function accept_connection_ctr($connection_id, $receiver_id) {
    $connection = new Connection();
    return $connection->acceptRequest($connection_id, $receiver_id);
}

// Reject connection
function reject_connection_ctr($connection_id, $receiver_id) {
    $connection = new Connection();
    return $connection->rejectRequest($connection_id, $receiver_id);
}

// Remove connection
function remove_connection_ctr($connection_id, $user_id) {
    $connection = new Connection();
    return $connection->removeConnection($connection_id, $user_id);
}

// Get connections
function get_connections_ctr($user_id) {
    $connection = new Connection();
    return $connection->getUserConnections($user_id);
}

// Get pending requests
function get_pending_requests_ctr($user_id) {
    $connection = new Connection();
    return $connection->getPendingRequests($user_id);
}

// Get sent requests
function get_sent_requests_ctr($user_id) {
    $connection = new Connection();
    return $connection->getSentRequests($user_id);
}

// Get connection status
function get_connection_status_ctr($user1_id, $user2_id) {
    $connection = new Connection();
    return $connection->getConnectionStatus($user1_id, $user2_id);
}

// Get connection count
function get_connection_count_ctr($user_id) {
    $connection = new Connection();
    return $connection->getConnectionCount($user_id);
}
?>
