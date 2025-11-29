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
 * @param array $context Additional context (page, product info)
 * @return array Response with status and message
 */
function chat_send_message_ctr($message, $history = [], $context = []) {
    try {
        if (empty(trim($message))) {
            return [
                'status' => 'error',
                'message' => 'Please enter a message'
            ];
        }
        
        $chatbot = new GeminiChatbot();
        $response = $chatbot->chat($message, $history, $context);
        
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
        // Build context with product information
        $context = [
            'page' => 'product',
            'product' => $product_info
        ];
        
        return chat_send_message_ctr($message, $history, $context);
        
    } catch (Exception $e) {
        error_log("Error in chat_product_query_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Sorry, something went wrong. Please try again.'
        ];
    }
}

/**
 * Search products through chatbot
 * 
 * @param string $query Search query
 * @return array Products matching query
 */
function chat_search_products_ctr($query) {
    try {
        $chatbot = new GeminiChatbot();
        $products = $chatbot->searchProducts($query);
        
        return [
            'status' => 'success',
            'products' => $products,
            'count' => count($products)
        ];
    } catch (Exception $e) {
        error_log("Error in chat_search_products_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'products' => [],
            'count' => 0
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
        $context = [
            'page' => 'order',
            'customer_id' => $customer_id,
            'logged_in' => ($customer_id !== null)
        ];
        
        // Check if message contains order reference
        if (preg_match('/ORD-\d+-\d+/', $message, $matches)) {
            $context['order_reference'] = $matches[0];
        }
        
        return chat_send_message_ctr($message, $history, $context);
        
    } catch (Exception $e) {
        error_log("Error in chat_order_query_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Sorry, something went wrong. Please try again.'
        ];
    }
}

?>
