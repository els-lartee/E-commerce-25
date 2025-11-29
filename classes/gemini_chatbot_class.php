<?php
/**
 * Gemini AI Chatbot Class
 * Handles communication with Google's Gemini API
 */

require_once __DIR__ . '/../settings/gemini_config.php';

class GeminiChatbot {
    
    private $api_key;
    private $model;
    private $api_url;
    private $max_tokens;
    private $temperature;
    private $system_prompt;
    
    public function __construct() {
        $this->api_key = GEMINI_API_KEY;
        $this->model = GEMINI_MODEL;
        $this->api_url = GEMINI_API_URL;
        $this->max_tokens = GEMINI_MAX_TOKENS;
        $this->temperature = GEMINI_TEMPERATURE;
        $this->system_prompt = GEMINI_SYSTEM_PROMPT;
    }
    
    /**
     * Send a message to Gemini and get a response
     * 
     * @param string $user_message The user's message
     * @param array $conversation_history Previous messages for context
     * @return array Response with status and message
     */
    public function chat($user_message, $conversation_history = []) {
        try {
            if (empty($user_message)) {
                return [
                    'status' => 'error',
                    'message' => 'Please enter a message'
                ];
            }
            
            // Build the contents array for the API
            $contents = $this->buildContents($user_message, $conversation_history);
            
            // Make API request
            $response = $this->makeRequest($contents);
            
            return $response;
            
        } catch (Exception $e) {
            error_log("Gemini Chat Error: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Sorry, I encountered an error. Please try again.'
            ];
        }
    }
    
    /**
     * Build the contents array for Gemini API
     * 
     * @param string $user_message Current message
     * @param array $conversation_history Previous messages
     * @return array Contents for API
     */
    private function buildContents($user_message, $conversation_history = []) {
        $contents = [];
        
        // Add system prompt as first user message (Gemini doesn't have system role)
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $this->system_prompt . "\n\nPlease acknowledge that you understand your role."]]
        ];
        
        // Add model acknowledgment
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => "I understand! I'm the friendly AI assistant for Elegant Jewels, your online jewellery store in Ghana. I'm here to help with product information, shopping assistance, order support, style advice, and store policies. How can I help you today?"]]
        ];
        
        // Add conversation history
        foreach ($conversation_history as $message) {
            $role = $message['role'] === 'user' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $message['content']]]
            ];
        }
        
        // Add current user message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $user_message]]
        ];
        
        return $contents;
    }
    
    /**
     * Make HTTP request to Gemini API
     * 
     * @param array $contents Message contents
     * @return array Response
     */
    private function makeRequest($contents) {
        $url = $this->api_url . $this->model . ':generateContent?key=' . $this->api_key;
        
        $data = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $this->temperature,
                'maxOutputTokens' => $this->max_tokens,
                'topP' => 0.95,
                'topK' => 40
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($error) {
            error_log("Gemini cURL Error: " . $error);
            return [
                'status' => 'error',
                'message' => 'Connection error. Please try again.'
            ];
        }
        
        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Gemini JSON Error: " . json_last_error_msg());
            return [
                'status' => 'error',
                'message' => 'Invalid response from AI service.'
            ];
        }
        
        // Log for debugging
        error_log("Gemini Response Code: $http_code");
        
        // Check for API errors
        if (isset($decoded['error'])) {
            error_log("Gemini API Error: " . json_encode($decoded['error']));
            return [
                'status' => 'error',
                'message' => 'AI service error: ' . ($decoded['error']['message'] ?? 'Unknown error')
            ];
        }
        
        // Extract the response text
        if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            $ai_message = $decoded['candidates'][0]['content']['parts'][0]['text'];
            return [
                'status' => 'success',
                'message' => $ai_message
            ];
        }
        
        // Check if blocked by safety filters
        if (isset($decoded['candidates'][0]['finishReason']) && 
            $decoded['candidates'][0]['finishReason'] === 'SAFETY') {
            return [
                'status' => 'error',
                'message' => "I'm sorry, I can't respond to that. Please ask something else about our jewellery or services."
            ];
        }
        
        return [
            'status' => 'error',
            'message' => 'Sorry, I could not generate a response. Please try again.'
        ];
    }
    
    /**
     * Get quick reply suggestions based on context
     * 
     * @param string $context Current context (home, product, cart, etc.)
     * @return array Quick reply options
     */
    public function getQuickReplies($context = 'home') {
        $replies = [
            'home' => [
                'What jewellery do you sell?',
                'How do I place an order?',
                'What payment methods do you accept?',
                'Help me find a gift'
            ],
            'product' => [
                'Tell me about this product',
                'What sizes are available?',
                'Is this suitable for a gift?',
                'Do you have similar items?'
            ],
            'cart' => [
                'How do I checkout?',
                'What are the payment options?',
                'Is my payment secure?',
                'How long is delivery?'
            ],
            'checkout' => [
                'How does mobile money work?',
                'Is card payment safe?',
                'Can I pay with MTN MoMo?',
                'What happens after payment?'
            ],
            'order' => [
                'How do I track my order?',
                'When will my order arrive?',
                'Can I change my order?',
                'What is the return policy?'
            ]
        ];
        
        return $replies[$context] ?? $replies['home'];
    }
    
    /**
     * Get welcome message
     * 
     * @return string Welcome message
     */
    public function getWelcomeMessage() {
        return "Hello! 👋 Welcome to Elegant Jewels! I'm your AI assistant. I can help you with:\n\n" .
               "💎 Finding the perfect jewellery\n" .
               "🛒 Shopping and ordering help\n" .
               "💳 Payment questions (MoMo, Cards)\n" .
               "🎁 Gift suggestions\n\n" .
               "How can I assist you today?";
    }
}

?>
