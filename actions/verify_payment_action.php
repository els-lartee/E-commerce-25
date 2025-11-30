<?php
/**
 * Verify Paystack Payment Action
 * Called after customer returns from Paystack payment page
 * 
 * Uses MVC pattern: Action -> Controller -> Class (Model)
 */

// Ensure no output before headers
if (ob_get_level()) ob_end_clean();
ob_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1); // Show errors temporarily for debugging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php_errors.log');

/**
 * Safe redirect function that handles output buffering
 */
function safe_redirect($url) {
    // Clean all output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Send redirect header
    header('Location: ' . $url);
    exit;
}

// Custom error handler to catch all errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    return false; // Let PHP handle it too
});

try {
    require_once __DIR__ . '/../settings/core.php';
} catch (Exception $e) {
    ob_end_clean();
    die("Error loading core.php: " . $e->getMessage());
}

try {
    require_once __DIR__ . '/../controllers/paystack_controller.php';
} catch (Exception $e) {
    ob_end_clean();
    die("Error loading paystack_controller.php: " . $e->getMessage());
}

try {
    require_once __DIR__ . '/../controllers/cart_controller.php';
} catch (Exception $e) {
    ob_end_clean();
    die("Error loading cart_controller.php: " . $e->getMessage());
}

// Log the incoming request for debugging
error_log("=== Payment Verification Started ===");
error_log("GET params: " . print_r($_GET, true));
error_log("SESSION data: " . print_r($_SESSION ?? [], true));

// Get reference from URL (Paystack redirects with ?reference=xxx)
$reference = $_GET['reference'] ?? $_GET['trxref'] ?? null;

if (!$reference) {
    // Check session as fallback
    $reference = $_SESSION['payment_reference'] ?? null;
}

error_log("Reference being verified: $reference");

if (!$reference) {
    error_log("No payment reference found - redirecting to failed page");
    safe_redirect('../view/payment_failed.php?message=' . urlencode('No payment reference found'));
}

// Verify the transaction through controller
$result = verify_payment_ctr($reference);

error_log("Payment verification result for reference $reference: " . json_encode($result));

if ($result['status'] === 'success') {
    $payment_status = $result['payment_status']; // 'success', 'failed', 'abandoned'
    
    error_log("Payment status: $payment_status");
    
    if ($payment_status === 'success') {
        // Payment successful
        $metadata = $result['metadata'] ?? [];
        error_log("Metadata received: " . json_encode($metadata));
        
        $order_id = $metadata['order_id'] ?? $_SESSION['payment_order_id'] ?? null;
        $customer_id = $metadata['customer_id'] ?? get_user_id();
        $amount = $result['amount'];
        $currency = $result['currency'];
        $channel = $result['channel'];
        
        error_log("Order ID: $order_id, Customer ID: $customer_id, Amount: $amount");
        
        if ($order_id) {
            // Complete payment through controller
            $complete_result = complete_payment_ctr($order_id, $amount, $channel, $reference);
            
            error_log("Complete payment result: " . json_encode($complete_result));
            
            if ($complete_result['status'] === 'success') {
                // Clear the cart
                empty_cart_ctr();
                
                // Clear session payment data
                unset($_SESSION['payment_reference']);
                unset($_SESSION['payment_order_id']);
                
                // Redirect to success page
                $success_params = http_build_query([
                    'order_ref' => 'ORD-' . ($metadata['invoice_no'] ?? $order_id) . '-' . $customer_id,
                    'reference' => $reference,
                    'amount' => $amount,
                    'currency' => $currency,
                    'channel' => $channel
                ]);
                
                error_log("Redirecting to success page with params: $success_params");
                safe_redirect('../view/payment_success.php?' . $success_params);
            } else {
                error_log("Failed to complete payment for order $order_id: " . json_encode($complete_result));
                safe_redirect('../view/payment_failed.php?message=' . urlencode('Payment received but failed to update order. Please contact support with reference: ' . $reference));
            }
        } else {
            error_log("Order ID not found for successful payment. Reference: $reference, Metadata: " . json_encode($metadata));
            
            // Even without order_id, payment was successful - show success with warning
            $success_params = http_build_query([
                'order_ref' => 'REF-' . $reference,
                'reference' => $reference,
                'amount' => $amount,
                'currency' => $currency,
                'channel' => $channel,
                'note' => 'Payment successful. Please contact support if order not updated.'
            ]);
            
            safe_redirect('../view/payment_success.php?' . $success_params);
        }
    } else {
        // Payment not successful
        $message = 'Payment ' . $payment_status;
        if (!empty($result['gateway_response'])) {
            $message .= ': ' . $result['gateway_response'];
        }
        
        error_log("Payment not successful: $message");
        safe_redirect('../view/payment_failed.php?message=' . urlencode($message) . '&reference=' . urlencode($reference));
    }
} else {
    // Verification failed
    $message = $result['message'] ?? 'Payment verification failed';
    error_log("Verification failed: $message");
    safe_redirect('../view/payment_failed.php?message=' . urlencode($message) . '&reference=' . urlencode($reference));
}

?>
