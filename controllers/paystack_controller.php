<?php
/**
 * Paystack Controller
 * Handles all Paystack payment operations following MVC pattern
 * 
 * Controller layer that mediates between actions and the Paystack class (model)
 */

require_once __DIR__ . '/../classes/paystack_class.php';
require_once __DIR__ . '/../settings/core.php';

/**
 * Initialize a new payment transaction
 * 
 * @param string $email Customer email
 * @param float $amount Total amount to charge
 * @param array $metadata Order metadata (order_id, customer_id, items, etc.)
 * @param string|null $callback_url Custom callback URL (optional)
 * @return array Response with status and data
 */
function initialize_payment_ctr($email, $amount, $metadata = [], $callback_url = null) {
    try {
        if (empty($email)) {
            return [
                'status' => 'error',
                'message' => 'Customer email is required'
            ];
        }
        
        if ($amount <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid payment amount'
            ];
        }
        
        $paystack = new PaystackPayment();
        $reference = $paystack->generateReference(
            $metadata['order_id'] ?? null,
            $metadata['customer_id'] ?? null
        );
        
        $response = $paystack->initializeTransaction(
            $email,
            $amount,
            $reference,
            $metadata,
            $callback_url
        );
        
        if ($response['status'] === true && isset($response['data'])) {
            return [
                'status' => 'success',
                'message' => 'Payment initialized successfully',
                'authorization_url' => $response['data']['authorization_url'],
                'access_code' => $response['data']['access_code'],
                'reference' => $reference
            ];
        }
        
        return [
            'status' => 'error',
            'message' => $response['message'] ?? 'Failed to initialize payment'
        ];
        
    } catch (Exception $e) {
        error_log("Error in initialize_payment_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Payment initialization failed. Please try again.'
        ];
    }
}

/**
 * Verify a payment transaction
 * 
 * @param string $reference Transaction reference
 * @return array Response with status and transaction data
 */
function verify_payment_ctr($reference) {
    try {
        if (empty($reference)) {
            return [
                'status' => 'error',
                'message' => 'Payment reference is required'
            ];
        }
        
        $paystack = new PaystackPayment();
        $response = $paystack->verifyTransaction($reference);
        
        if ($response['status'] === true && isset($response['data'])) {
            $data = $response['data'];
            
            return [
                'status' => 'success',
                'payment_status' => $data['status'], // 'success', 'failed', 'abandoned'
                'amount' => $data['amount'] / 100, // Convert from pesewas
                'currency' => $data['currency'],
                'channel' => $data['channel'],
                'reference' => $data['reference'],
                'paid_at' => $data['paid_at'] ?? null,
                'metadata' => $data['metadata'] ?? [],
                'customer' => $data['customer'] ?? [],
                'authorization' => $data['authorization'] ?? [],
                'gateway_response' => $data['gateway_response'] ?? ''
            ];
        }
        
        return [
            'status' => 'error',
            'message' => $response['message'] ?? 'Payment verification failed'
        ];
        
    } catch (Exception $e) {
        error_log("Error in verify_payment_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Payment verification failed. Please try again.'
        ];
    }
}

/**
 * Charge mobile money directly
 * 
 * @param string $email Customer email
 * @param float $amount Amount to charge
 * @param string $phone Customer phone number
 * @param string $provider Mobile money provider (mtn, vod, tgo)
 * @param array $metadata Order metadata
 * @return array Response with status and data
 */
function charge_mobile_money_ctr($email, $amount, $phone, $provider, $metadata = []) {
    try {
        // Validate inputs
        if (empty($email) || empty($phone) || empty($provider)) {
            return [
                'status' => 'error',
                'message' => 'Email, phone, and provider are required'
            ];
        }
        
        // Validate provider
        $valid_providers = ['mtn', 'vod', 'tgo'];
        if (!in_array(strtolower($provider), $valid_providers)) {
            return [
                'status' => 'error',
                'message' => 'Invalid mobile money provider. Use: mtn, vod, or tgo'
            ];
        }
        
        if ($amount <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid payment amount'
            ];
        }
        
        $paystack = new PaystackPayment();
        $reference = $paystack->generateReference(
            $metadata['order_id'] ?? null,
            $metadata['customer_id'] ?? null
        );
        
        $response = $paystack->chargeMobileMoney(
            $email,
            $amount,
            $phone,
            strtolower($provider),
            $reference
        );
        
        if ($response['status'] === true) {
            return [
                'status' => 'success',
                'message' => $response['data']['display_text'] ?? 'Check your phone to authorize payment',
                'reference' => $reference,
                'data' => $response['data']
            ];
        }
        
        return [
            'status' => 'error',
            'message' => $response['message'] ?? 'Failed to initiate mobile money charge'
        ];
        
    } catch (Exception $e) {
        error_log("Error in charge_mobile_money_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Mobile money charge failed. Please try again.'
        ];
    }
}

/**
 * Get list of banks for transfer payments
 * 
 * @param string $country Country code (ghana, nigeria)
 * @return array Response with banks list
 */
function get_banks_ctr($country = 'ghana') {
    try {
        $paystack = new PaystackPayment();
        $response = $paystack->getBanks($country);
        
        if ($response['status'] === true && isset($response['data'])) {
            return [
                'status' => 'success',
                'banks' => $response['data']
            ];
        }
        
        return [
            'status' => 'error',
            'message' => $response['message'] ?? 'Failed to fetch banks'
        ];
        
    } catch (Exception $e) {
        error_log("Error in get_banks_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Failed to fetch banks list'
        ];
    }
}

/**
 * Validate Paystack webhook signature
 * 
 * @param string $payload Raw request body
 * @param string $signature Paystack signature header
 * @return bool True if valid
 */
function validate_webhook_ctr($payload, $signature) {
    try {
        if (empty($payload) || empty($signature)) {
            return false;
        }
        
        $paystack = new PaystackPayment();
        return $paystack->validateWebhook($payload, $signature);
        
    } catch (Exception $e) {
        error_log("Error in validate_webhook_ctr: " . $e->getMessage());
        return false;
    }
}

/**
 * Get Paystack public key for frontend use
 * 
 * @return string Public key
 */
function get_paystack_public_key_ctr() {
    $paystack = new PaystackPayment();
    return $paystack->getPublicKey();
}

/**
 * Calculate order totals with tax
 * 
 * @param array $cart_items Cart items array
 * @param float $tax_rate Tax rate (default 0.125 for 12.5% VAT)
 * @return array Calculated totals
 */
function calculate_order_totals_ctr($cart_items, $tax_rate = 0.125) {
    $subtotal = 0;
    $items_summary = [];
    
    foreach ($cart_items as $item) {
        $item_total = $item['product_price'] * $item['qty'];
        $subtotal += $item_total;
        
        $items_summary[] = [
            'product_id' => $item['p_id'],
            'product_title' => $item['product_title'],
            'quantity' => $item['qty'],
            'unit_price' => $item['product_price'],
            'total' => $item_total
        ];
    }
    
    $tax_amount = $subtotal * $tax_rate;
    $total = $subtotal + $tax_amount;
    
    return [
        'subtotal' => round($subtotal, 2),
        'tax_rate' => $tax_rate,
        'tax_rate_percent' => ($tax_rate * 100) . '%',
        'tax_amount' => round($tax_amount, 2),
        'total' => round($total, 2),
        'items' => $items_summary,
        'item_count' => count($cart_items)
    ];
}

/**
 * Process a complete checkout
 * Creates order, calculates totals, and initializes payment
 * 
 * @param int $customer_id Customer ID
 * @param string $customer_email Customer email
 * @param array $cart_items Cart items
 * @return array Response with payment URL or error
 */
function process_checkout_ctr($customer_id, $customer_email, $cart_items) {
    try {
        if (empty($cart_items)) {
            return [
                'status' => 'error',
                'message' => 'Cart is empty'
            ];
        }
        
        if (empty($customer_email)) {
            return [
                'status' => 'error',
                'message' => 'Customer email is required'
            ];
        }
        
        // Calculate totals
        $totals = calculate_order_totals_ctr($cart_items);
        
        // Create order
        require_once __DIR__ . '/order_controller.php';
        
        $invoice_no = time();
        $order_id = create_order_ctr($customer_id, $invoice_no, $totals['total']);
        
        if (!$order_id) {
            return [
                'status' => 'error',
                'message' => 'Failed to create order'
            ];
        }
        
        // Add order details
        foreach ($cart_items as $item) {
            add_order_details_ctr($order_id, $item['p_id'], $item['qty'], $item['product_price']);
        }
        
        // Prepare metadata
        $metadata = [
            'order_id' => $order_id,
            'customer_id' => $customer_id,
            'invoice_no' => $invoice_no,
            'subtotal' => $totals['subtotal'],
            'tax' => $totals['tax_amount'],
            'tax_rate' => $totals['tax_rate_percent'],
            'items' => $totals['items'],
            'custom_fields' => [
                [
                    'display_name' => 'Order ID',
                    'variable_name' => 'order_id',
                    'value' => $order_id
                ],
                [
                    'display_name' => 'Invoice Number',
                    'variable_name' => 'invoice_no',
                    'value' => $invoice_no
                ]
            ]
        ];
        
        // Initialize payment
        $payment_result = initialize_payment_ctr($customer_email, $totals['total'], $metadata);
        
        if ($payment_result['status'] === 'success') {
            return [
                'status' => 'success',
                'authorization_url' => $payment_result['authorization_url'],
                'reference' => $payment_result['reference'],
                'access_code' => $payment_result['access_code'],
                'order_id' => $order_id,
                'invoice_no' => $invoice_no,
                'totals' => $totals
            ];
        }
        
        return $payment_result;
        
    } catch (Exception $e) {
        error_log("Error in process_checkout_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Checkout processing failed. Please try again.'
        ];
    }
}

/**
 * Complete payment after verification
 * Records payment and updates order status
 * 
 * @param int $order_id Order ID
 * @param float $amount Amount paid
 * @param string $channel Payment channel used
 * @param string $reference Payment reference
 * @return array Response with status
 */
function complete_payment_ctr($order_id, $amount, $channel, $reference) {
    try {
        require_once __DIR__ . '/order_controller.php';
        
        // Record payment
        $payment_recorded = record_payment_ctr($order_id, $amount, $channel, 'completed');
        
        if ($payment_recorded) {
            return [
                'status' => 'success',
                'message' => 'Payment completed successfully',
                'order_id' => $order_id
            ];
        }
        
        return [
            'status' => 'error',
            'message' => 'Failed to record payment'
        ];
        
    } catch (Exception $e) {
        error_log("Error in complete_payment_ctr: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Failed to complete payment'
        ];
    }
}

?>
