<?php
require_once(dirname(__FILE__) . '/../classes/message_class.php');

// Send message
function send_message_ctr($sender_id, $receiver_id, $message_content) {
    $message = new Message();
    return $message->send_message($sender_id, $receiver_id, $message_content);
}

// Get conversation
function get_conversation_ctr($user1_id, $user2_id) {
    $message = new Message();
    return $message->get_conversation($user1_id, $user2_id);
}

// Get user conversations
function get_user_conversations_ctr($user_id) {
    $message = new Message();
    return $message->get_user_conversations($user_id);
}

// Get unread count
function get_unread_count_ctr($user_id) {
    $message = new Message();
    return $message->get_unread_count($user_id);
}

// Mark as read
function mark_as_read_ctr($message_id) {
    $message = new Message();
    return $message->mark_as_read($message_id);
}

// Mark conversation as read
function mark_conversation_as_read_ctr($receiver_id, $sender_id) {
    $message = new Message();
    return $message->mark_conversation_as_read($receiver_id, $sender_id);
}

// Delete message
function delete_message_ctr($message_id, $user_id) {
    $message = new Message();
    return $message->delete_message($message_id, $user_id);
}

// Search messages
function search_messages_ctr($user_id, $search_term) {
    $message = new Message();
    return $message->search_messages($user_id, $search_term);
}
?>
