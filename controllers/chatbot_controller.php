<?php
/**
 * Chatbot Controller
 * Handles chatbot operations following MVC pattern
 */

require_once __DIR__ . '/../classes/gemini_chatbot_class.php';
require_once __DIR__ . '/../settings/core.php';

/**
 * Send a message to the chatbot and get a response
 * 
 * @param string $message User's message
 * @param array $history Conversation history
 * @return array Response with status and message
 */
function chat_send_message_ctr($message, $history = []) {
    try {
        if (empty(trim($message))) {
            return [
                'status' => 'error',
                'message' => 'Please enter a message'
            ];
        }
        
        $chatbot = new GeminiChatbot();
        $response = $chatbot->chat($message, $history);
        
        return $response;
        
    } catch (Exception $e) {
        error_log("Error in chat_send_message_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Sorry, something went wrong. Please try again.'
        ];
    }
}

/**
 * Get quick reply suggestions
 * 
 * @param string $context Page context
 * @return array Quick replies
 */
function chat_get_quick_replies_ctr($context = 'home') {
    try {
        $chatbot = new GeminiChatbot();
        return [
            'status' => 'success',
            'replies' => $chatbot->getQuickReplies($context)
        ];
    } catch (Exception $e) {
        error_log("Error in chat_get_quick_replies_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'replies' => []
        ];
    }
}

/**
 * Get welcome message
 * 
 * @return array Welcome message response
 */
function chat_get_welcome_ctr() {
    try {
        $chatbot = new GeminiChatbot();
        return [
            'status' => 'success',
            'message' => $chatbot->getWelcomeMessage(),
            'quick_replies' => $chatbot->getQuickReplies('home')
        ];
    } catch (Exception $e) {
        error_log("Error in chat_get_welcome_ctr: " . $e->getMessage());
        return [
            'status' => 'success',
            'message' => "Hello! Welcome to our jewellery store. How can I help you today?",
            'quick_replies' => []
        ];
    }
}

/**
 * Get product-aware response
 * Includes product context in the conversation
 * 
 * @param string $message User's message
 * @param array $product_info Current product info (if on product page)
 * @param array $history Conversation history
 * @return array Response
 */
function chat_product_query_ctr($message, $product_info = [], $history = []) {
    try {
        // Add product context to the message if available
        if (!empty($product_info)) {
            $context = "\n[Context: Customer is viewing: " . 
                       ($product_info['title'] ?? 'a product') . 
                       ", Price: GHS " . ($product_info['price'] ?? 'N/A') . 
                       ", Category: " . ($product_info['category'] ?? 'N/A') . "]";
            $message = $message . $context;
        }
        
        return chat_send_message_ctr($message, $history);
        
    } catch (Exception $e) {
        error_log("Error in chat_product_query_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Sorry, something went wrong. Please try again.'
        ];
    }
}

/**
 * Handle order-related queries
 * Can look up order status if user provides order reference
 * 
 * @param string $message User's message
 * @param int|null $customer_id Customer ID if logged in
 * @param array $history Conversation history
 * @return array Response
 */
function chat_order_query_ctr($message, $customer_id = null, $history = []) {
    try {
        // Check if message contains order reference
        if (preg_match('/ORD-\d+-\d+/', $message, $matches)) {
            // Add order lookup context
            $message .= "\n[Note: Customer mentioned order reference. Direct them to check order history or contact support for specific order status.]";
        }
        
        // Add customer context if logged in
        if ($customer_id) {
            $message .= "\n[Context: Customer is logged in.]";
        } else {
            $message .= "\n[Context: Customer is not logged in. They need to log in to see order history.]";
        }
        
        return chat_send_message_ctr($message, $history);
        
    } catch (Exception $e) {
        error_log("Error in chat_order_query_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Sorry, something went wrong. Please try again.'
        ];
    }
}

?>
