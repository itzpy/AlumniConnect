<?php
require_once(dirname(__FILE__) . '/../classes/message_class.php');

// Send message
function send_message_ctr($sender_id, $receiver_id, $message_content) {
    $message = new Message();
    return $message->sendMessage($sender_id, $receiver_id, $message_content);
}

// Get conversation
function get_conversation_ctr($user1_id, $user2_id) {
    $message = new Message();
    return $message->getConversation($user1_id, $user2_id);
}

// Get user conversations
function get_user_conversations_ctr($user_id) {
    $message = new Message();
    return $message->getUserConversations($user_id);
}

// Get unread count
function get_unread_count_ctr($user_id) {
    $message = new Message();
    return $message->getUnreadCount($user_id);
}

// Mark as read
function mark_as_read_ctr($message_id, $user_id) {
    $message = new Message();
    return $message->markAsRead($message_id, $user_id);
}

// Mark conversation as read
function mark_conversation_as_read_ctr($receiver_id, $sender_id) {
    $message = new Message();
    return $message->markConversationAsRead($receiver_id, $sender_id);
}

// Delete message
function delete_message_ctr($message_id, $user_id) {
    $message = new Message();
    return $message->deleteMessage($message_id, $user_id);
}

// Search messages
function search_messages_ctr($user_id, $search_term) {
    $message = new Message();
    return $message->searchMessages($user_id, $search_term);
}
?>
