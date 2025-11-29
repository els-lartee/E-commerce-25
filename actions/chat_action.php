<?php
/**
 * Chat Action Endpoint
 * Handles chat requests from the frontend
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/chatbot_controller.php';

header('Content-Type: application/json');

// Get the action type
$action = $_POST['action'] ?? $_GET['action'] ?? 'chat';

switch ($action) {
    case 'chat':
        handleChatMessage();
        break;
        
    case 'welcome':
        handleWelcome();
        break;
        
    case 'quick_replies':
        handleQuickReplies();
        break;
        
    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
}

/**
 * Handle chat message
 */
function handleChatMessage() {
    $message = $_POST['message'] ?? '';
    $history = isset($_POST['history']) ? json_decode($_POST['history'], true) : [];
    $context = $_POST['context'] ?? 'home';
    $product_info = isset($_POST['product']) ? json_decode($_POST['product'], true) : [];
    
    if (empty(trim($message))) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please enter a message'
        ]);
        return;
    }
    
    // Validate history format
    if (!is_array($history)) {
        $history = [];
    }
    
    // Use product-aware response if product info is provided
    if (!empty($product_info)) {
        $response = chat_product_query_ctr($message, $product_info, $history);
    } else {
        $response = chat_send_message_ctr($message, $history);
    }
    
    // Add quick replies based on context
    $quick_replies_response = chat_get_quick_replies_ctr($context);
    if ($quick_replies_response['status'] === 'success') {
        $response['quick_replies'] = $quick_replies_response['replies'];
    }
    
    echo json_encode($response);
}

/**
 * Handle welcome message request
 */
function handleWelcome() {
    $response = chat_get_welcome_ctr();
    echo json_encode($response);
}

/**
 * Handle quick replies request
 */
function handleQuickReplies() {
    $context = $_GET['context'] ?? $_POST['context'] ?? 'home';
    $response = chat_get_quick_replies_ctr($context);
    echo json_encode($response);
}

?>
