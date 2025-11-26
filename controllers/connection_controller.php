<?php
require_once(dirname(__FILE__) . '/../classes/connection_class.php');

// Send connection request
function send_connection_request_ctr($requester_id, $requester_type, $receiver_id, $receiver_type) {
    $connection = new Connection();
    return $connection->send_request($requester_id, $requester_type, $receiver_id, $receiver_type);
}

// Accept connection
function accept_connection_ctr($connection_id) {
    $connection = new Connection();
    return $connection->accept_request($connection_id);
}

// Reject connection
function reject_connection_ctr($connection_id) {
    $connection = new Connection();
    return $connection->reject_request($connection_id);
}

// Remove connection
function remove_connection_ctr($connection_id) {
    $connection = new Connection();
    return $connection->remove_connection($connection_id);
}

// Get connections
function get_connections_ctr($user_id, $user_type) {
    $connection = new Connection();
    return $connection->get_connections($user_id, $user_type);
}

// Get pending requests
function get_pending_requests_ctr($user_id, $user_type) {
    $connection = new Connection();
    return $connection->get_pending_requests($user_id, $user_type);
}

// Get sent requests
function get_sent_requests_ctr($user_id, $user_type) {
    $connection = new Connection();
    return $connection->get_sent_requests($user_id, $user_type);
}

// Get connection status
function get_connection_status_ctr($user1_id, $user1_type, $user2_id, $user2_type) {
    $connection = new Connection();
    return $connection->get_connection_status($user1_id, $user1_type, $user2_id, $user2_type);
}

// Get connection count
function get_connection_count_ctr($user_id, $user_type) {
    $connection = new Connection();
    return $connection->get_connection_count($user_id, $user_type);
}
?>
