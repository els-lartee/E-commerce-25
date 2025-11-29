<?php
/**
 * Chat Widget Component
 * Include this file in any page to add the AI chatbot
 * 
 * Usage:
 * - Include at the bottom of your page before </body>
 * - Optionally set $chat_context before including (e.g., 'home', 'product', 'cart', 'checkout')
 * - Optionally set $product_info array for product pages
 */

// Default context if not set
$chat_context = isset($chat_context) ? $chat_context : 'home';
$product_info = isset($product_info) ? $product_info : null;
$product_json = $product_info ? json_encode($product_info) : '';

// Determine the base path for scripts
$base_path = '';
$current_path = $_SERVER['PHP_SELF'];
if (strpos($current_path, '/view/') !== false || 
    strpos($current_path, '/admin/') !== false || 
    strpos($current_path, '/login/') !== false) {
    $base_path = '../';
}
?>

<!-- AI Chatbot Widget -->
<script src="<?php echo $base_path; ?>js/chatbot.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.chatbot = new ChatbotWidget({
        position: 'bottom-right',
        primaryColor: '#667eea',
        title: 'Jewellery Assistant',
        context: '<?php echo htmlspecialchars($chat_context); ?>',
        <?php if ($product_info): ?>
        productInfo: <?php echo $product_json; ?>,
        <?php endif; ?>
        apiEndpoint: 'actions/chat_action.php'
    });
});
</script>
