<?php
/**
 * Gemini AI Chatbot Class
 * Handles communication with Google's Gemini API
 * Enhanced with real product knowledge from database
 */

require_once __DIR__ . '/../settings/gemini_config.php';
require_once __DIR__ . '/../settings/db_class.php';

class GeminiChatbot extends db_connection {
    
    private $api_key;
    private $model;
    private $api_url;
    private $max_tokens;
    private $temperature;
    private $system_prompt;
    
    public function __construct() {
        parent::db_connect();
        $this->api_key = GEMINI_API_KEY;
        $this->model = GEMINI_MODEL;
        $this->api_url = GEMINI_API_URL;
        $this->max_tokens = GEMINI_MAX_TOKENS;
        $this->temperature = GEMINI_TEMPERATURE;
        $this->system_prompt = GEMINI_SYSTEM_PROMPT;
    }
    
    /**
     * Get store knowledge from database
     * This provides real-time product, category, and brand information
     * 
     * @return string Formatted knowledge string
     */
    private function getStoreKnowledge() {
        $conn = $this->db_conn();
        if (!$conn) {
            return "";
        }
        
        $knowledge = "\n\n--- CURRENT STORE INVENTORY ---\n";
        
        // Get categories
        $categories = [];
        $cat_result = mysqli_query($conn, "SELECT id, name FROM jewellery ORDER BY name");
        if ($cat_result) {
            while ($row = mysqli_fetch_assoc($cat_result)) {
                $categories[$row['id']] = $row['name'];
            }
            mysqli_free_result($cat_result);
        }
        
        if (!empty($categories)) {
            $knowledge .= "\n**Available Categories:** " . implode(", ", $categories) . "\n";
        }
        
        // Get brands
        $brands = [];
        $brand_result = mysqli_query($conn, "SELECT brand_id, brand_name FROM brands ORDER BY brand_name");
        if ($brand_result) {
            while ($row = mysqli_fetch_assoc($brand_result)) {
                $brands[$row['brand_id']] = $row['brand_name'];
            }
            mysqli_free_result($brand_result);
        }
        
        if (!empty($brands)) {
            $knowledge .= "**Available Brands:** " . implode(", ", $brands) . "\n";
        }
        
        // Get products with details
        $products_result = mysqli_query($conn, 
            "SELECT p.product_id, p.product_title, p.product_price, p.product_desc, 
                    j.name as category, b.brand_name 
             FROM product p 
             LEFT JOIN jewellery j ON p.product_cat = j.id 
             LEFT JOIN brands b ON p.product_brand = b.brand_id 
             ORDER BY p.product_id DESC 
             LIMIT 50"
        );
        
        if ($products_result && mysqli_num_rows($products_result) > 0) {
            $knowledge .= "\n**Current Products:**\n";
            while ($product = mysqli_fetch_assoc($products_result)) {
                $knowledge .= "- {$product['product_title']} (GHS " . number_format($product['product_price'], 2) . ")";
                if ($product['category']) {
                    $knowledge .= " | Category: {$product['category']}";
                }
                if ($product['brand_name']) {
                    $knowledge .= " | Brand: {$product['brand_name']}";
                }
                if ($product['product_desc']) {
                    $desc = substr($product['product_desc'], 0, 100);
                    $knowledge .= " | {$desc}";
                    if (strlen($product['product_desc']) > 100) {
                        $knowledge .= "...";
                    }
                }
                $knowledge .= "\n";
            }
            mysqli_free_result($products_result);
        }
        
        // Get price range
        $price_result = mysqli_query($conn, 
            "SELECT MIN(product_price) as min_price, MAX(product_price) as max_price, 
                    AVG(product_price) as avg_price, COUNT(*) as total 
             FROM product"
        );
        
        if ($price_result) {
            $stats = mysqli_fetch_assoc($price_result);
            if ($stats['total'] > 0) {
                $knowledge .= "\n**Price Range:** GHS " . number_format($stats['min_price'], 2) . 
                              " - GHS " . number_format($stats['max_price'], 2) . 
                              " (Average: GHS " . number_format($stats['avg_price'], 2) . ")\n";
                $knowledge .= "**Total Products Available:** {$stats['total']}\n";
            }
            mysqli_free_result($price_result);
        }
        
        $knowledge .= "--- END INVENTORY ---\n";
        
        return $knowledge;
    }
    
    /**
     * Search products based on user query
     * 
     * @param string $query Search terms
     * @return array Matching products
     */
    public function searchProducts($query) {
        $conn = $this->db_conn();
        if (!$conn) return [];
        
        $search_term = "%" . mysqli_real_escape_string($conn, $query) . "%";
        
        $stmt = mysqli_prepare($conn, 
            "SELECT p.product_id, p.product_title, p.product_price, p.product_desc, 
                    j.name as category, b.brand_name 
             FROM product p 
             LEFT JOIN jewellery j ON p.product_cat = j.id 
             LEFT JOIN brands b ON p.product_brand = b.brand_id 
             WHERE p.product_title LIKE ? 
                OR p.product_desc LIKE ? 
                OR p.product_keywords LIKE ?
                OR b.brand_name LIKE ?
                OR j.name LIKE ?
             ORDER BY p.product_id DESC
             LIMIT 10"
        );
        
        mysqli_stmt_bind_param($stmt, "sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        
        return $products;
    }
    
    /**
     * Get products by category
     * 
     * @param string $category Category name
     * @return array Products in category
     */
    public function getProductsByCategory($category) {
        $conn = $this->db_conn();
        if (!$conn) return [];
        
        $search_term = "%" . mysqli_real_escape_string($conn, $category) . "%";
        
        $stmt = mysqli_prepare($conn, 
            "SELECT p.product_id, p.product_title, p.product_price, p.product_desc, 
                    j.name as category, b.brand_name 
             FROM product p 
             LEFT JOIN jewellery j ON p.product_cat = j.id 
             LEFT JOIN brands b ON p.product_brand = b.brand_id 
             WHERE j.name LIKE ?
             ORDER BY p.product_price ASC
             LIMIT 10"
        );
        
        mysqli_stmt_bind_param($stmt, "s", $search_term);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        
        return $products;
    }
    
    /**
     * Get products within a price range
     * 
     * @param float $min_price Minimum price
     * @param float $max_price Maximum price
     * @return array Products in range
     */
    public function getProductsByPriceRange($min_price, $max_price) {
        $conn = $this->db_conn();
        if (!$conn) return [];
        
        $stmt = mysqli_prepare($conn, 
            "SELECT p.product_id, p.product_title, p.product_price, p.product_desc, 
                    j.name as category, b.brand_name 
             FROM product p 
             LEFT JOIN jewellery j ON p.product_cat = j.id 
             LEFT JOIN brands b ON p.product_brand = b.brand_id 
             WHERE p.product_price BETWEEN ? AND ?
             ORDER BY p.product_price ASC
             LIMIT 10"
        );
        
        mysqli_stmt_bind_param($stmt, "dd", $min_price, $max_price);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        
        return $products;
    }
    
    /**
     * Format product list for chat response
     * 
     * @param array $products Products to format
     * @return string Formatted product list
     */
    private function formatProductsForChat($products) {
        if (empty($products)) {
            return "";
        }
        
        $formatted = "\n\nHere are some products I found:\n";
        foreach ($products as $product) {
            $formatted .= "• {$product['product_title']} - GHS " . number_format($product['product_price'], 2);
            if ($product['category']) {
                $formatted .= " ({$product['category']})";
            }
            $formatted .= "\n";
        }
        
        return $formatted;
    }
    
    /**
     * Send a message to Gemini and get a response
     * 
     * @param string $user_message The user's message
     * @param array $conversation_history Previous messages for context
     * @param array $context Additional context (product info, page context)
     * @return array Response with status and message
     */
    public function chat($user_message, $conversation_history = [], $context = []) {
        try {
            if (empty($user_message)) {
                return [
                    'status' => 'error',
                    'message' => 'Please enter a message'
                ];
            }
            
            // Get real-time store knowledge
            $store_knowledge = $this->getStoreKnowledge();
            
            // Check if user is asking about specific products
            $product_context = "";
            if ($this->isProductQuery($user_message)) {
                $search_results = $this->searchProducts($user_message);
                if (!empty($search_results)) {
                    $product_context = $this->formatProductsForChat($search_results);
                }
            }
            
            // Add current product context if viewing a product
            if (!empty($context['product'])) {
                $product_context .= "\n\n**Customer is currently viewing:**\n";
                $product_context .= "Product: " . ($context['product']['name'] ?? 'Unknown') . "\n";
                $product_context .= "Price: GHS " . number_format($context['product']['price'] ?? 0, 2) . "\n";
                $product_context .= "Category: " . ($context['product']['category'] ?? 'N/A') . "\n";
                $product_context .= "Brand: " . ($context['product']['brand'] ?? 'N/A') . "\n";
                if (!empty($context['product']['description'])) {
                    $product_context .= "Description: " . $context['product']['description'] . "\n";
                }
            }
            
            // Build the contents array for the API
            $contents = $this->buildContents($user_message, $conversation_history, $store_knowledge, $product_context);
            
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
     * Check if user message is asking about products
     * 
     * @param string $message User message
     * @return bool
     */
    private function isProductQuery($message) {
        $product_keywords = [
            'product', 'products', 'ring', 'rings', 'necklace', 'necklaces', 
            'bracelet', 'bracelets', 'earring', 'earrings', 'pendant', 'pendants',
            'gold', 'silver', 'diamond', 'price', 'cost', 'how much', 'show me',
            'find', 'search', 'looking for', 'buy', 'purchase', 'available',
            'cheapest', 'expensive', 'affordable', 'budget', 'under', 'below',
            'jewellery', 'jewelry', 'chain', 'chains', 'watch', 'watches',
            'gift', 'recommend', 'suggestion', 'what do you have', 'what\'s available'
        ];
        
        $message_lower = strtolower($message);
        foreach ($product_keywords as $keyword) {
            if (strpos($message_lower, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Build the contents array for Gemini API
     * 
     * @param string $user_message Current message
     * @param array $conversation_history Previous messages
     * @param string $store_knowledge Current inventory data
     * @param string $product_context Relevant product context
     * @return array Contents for API
     */
    private function buildContents($user_message, $conversation_history = [], $store_knowledge = "", $product_context = "") {
        $contents = [];
        
        // Build enhanced system prompt with store knowledge
        $enhanced_prompt = $this->system_prompt;
        
        if (!empty($store_knowledge)) {
            $enhanced_prompt .= "\n\n" . $store_knowledge;
        }
        
        if (!empty($product_context)) {
            $enhanced_prompt .= "\n\n**RELEVANT CONTEXT:**" . $product_context;
        }
        
        $enhanced_prompt .= "\n\n**IMPORTANT:** When mentioning products, always include the actual price in GHS from the inventory data. Be specific about what products are available.";
        
        // Add system prompt as first user message (Gemini doesn't have system role)
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $enhanced_prompt . "\n\nPlease acknowledge that you understand your role and the current inventory."]]
        ];
        
        // Add model acknowledgment
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => "I understand! I'm the AI assistant for Elegant Jewels in Ghana. I have access to the current inventory and can help customers with accurate product information, prices in GHS, and shopping assistance. How can I help you today?"]]
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
     * Enhanced to include dynamic category/product suggestions
     * 
     * @param string $context Current context (home, product, cart, etc.)
     * @return array Quick reply options
     */
    public function getQuickReplies($context = 'home') {
        $base_replies = [
            'home' => [
                'What jewellery do you have?',
                'Show me your best sellers',
                'What payment methods do you accept?',
                'Help me find a gift'
            ],
            'product' => [
                'Tell me about this product',
                'Is this suitable for a gift?',
                'Do you have similar items?',
                'What\'s your return policy?'
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
        
        $replies = $base_replies[$context] ?? $base_replies['home'];
        
        // For home context, add dynamic category suggestions
        if ($context === 'home') {
            $conn = $this->db_conn();
            if ($conn) {
                // Get top categories with products
                $result = mysqli_query($conn, 
                    "SELECT j.name, COUNT(p.product_id) as product_count 
                     FROM jewellery j 
                     LEFT JOIN product p ON j.id = p.product_cat 
                     GROUP BY j.id 
                     HAVING product_count > 0 
                     ORDER BY product_count DESC 
                     LIMIT 2"
                );
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $replies[] = "Show me {$row['name']}";
                    }
                    mysqli_free_result($result);
                }
            }
        }
        
        return array_slice($replies, 0, 5); // Return max 5 options
    }
    
    /**
     * Get welcome message with store summary
     * 
     * @return string Welcome message
     */
    public function getWelcomeMessage() {
        $welcome = "Hello! Welcome to Elegant Jewels! I'm your AI assistant.\n\n";
        
        // Get quick store stats
        $conn = $this->db_conn();
        if ($conn) {
            $stats = mysqli_query($conn, "SELECT COUNT(*) as total FROM product");
            if ($stats) {
                $row = mysqli_fetch_assoc($stats);
                if ($row['total'] > 0) {
                    $welcome .= "We currently have {$row['total']} beautiful pieces for you to explore! ";
                }
                mysqli_free_result($stats);
            }
        }
        
        $welcome .= "I can help you with:\n\n" .
                    "Finding the perfect jewellery\n" .
                    "Shopping and ordering help\n" .
                    "Payment questions (MoMo, Cards)\n" .
                    "Gift suggestions\n\n" .
                    "How can I assist you today?";
        
        return $welcome;
    }
}

?>
