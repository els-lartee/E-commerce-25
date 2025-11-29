<?php
/**
 * Verify Paystack Payment Action
 * Called after customer returns from Paystack payment page
 * 
 * Uses MVC pattern: Action -> Controller -> Class (Model)
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/paystack_controller.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

// Get reference from URL (Paystack redirects with ?reference=xxx)
$reference = $_GET['reference'] ?? $_GET['trxref'] ?? null;

if (!$reference) {
    // Check session as fallback
    $reference = $_SESSION['payment_reference'] ?? null;
}

if (!$reference) {
    header('Location: ../view/payment_failed.php?message=' . urlencode('No payment reference found'));
    exit;
}

// Verify the transaction through controller
$result = verify_payment_ctr($reference);

error_log("Payment verification for reference $reference: " . json_encode($result));

if ($result['status'] === 'success') {
    $payment_status = $result['payment_status']; // 'success', 'failed', 'abandoned'
    
    if ($payment_status === 'success') {
        // Payment successful
        $metadata = $result['metadata'] ?? [];
        $order_id = $metadata['order_id'] ?? $_SESSION['payment_order_id'] ?? null;
        $customer_id = $metadata['customer_id'] ?? get_user_id();
        $amount = $result['amount'];
        $currency = $result['currency'];
        $channel = $result['channel'];
        
        if ($order_id) {
            // Complete payment through controller
            $complete_result = complete_payment_ctr($order_id, $amount, $channel, $reference);
            
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
                
                header('Location: ../view/payment_success.php?' . $success_params);
                exit;
            } else {
                error_log("Failed to complete payment for order $order_id");
                header('Location: ../view/payment_failed.php?message=' . urlencode('Payment received but failed to update order. Please contact support with reference: ' . $reference));
                exit;
            }
        } else {
            error_log("Order ID not found for successful payment. Reference: $reference");
            header('Location: ../view/payment_failed.php?message=' . urlencode('Order not found. Please contact support with reference: ' . $reference));
            exit;
        }
    } else {
        // Payment not successful
        $message = 'Payment ' . $payment_status;
        if (!empty($result['gateway_response'])) {
            $message .= ': ' . $result['gateway_response'];
        }
        
        header('Location: ../view/payment_failed.php?message=' . urlencode($message) . '&reference=' . urlencode($reference));
        exit;
    }
} else {
    // Verification failed
    $message = $result['message'] ?? 'Payment verification failed';
    header('Location: ../view/payment_failed.php?message=' . urlencode($message) . '&reference=' . urlencode($reference));
    exit;
}

?>
