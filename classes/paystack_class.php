<?php
/**
 * Paystack Payment Class
 * Handles all Paystack payment operations
 */

require_once __DIR__ . '/../settings/paystack_config.php';

class PaystackPayment {
    
    private $secret_key;
    private $public_key;
    private $api_url;
    
    public function __construct() {
        $this->secret_key = PAYSTACK_SECRET_KEY;
        $this->public_key = PAYSTACK_PUBLIC_KEY;
        $this->api_url = PAYSTACK_API_URL;
    }
    
    /**
     * Initialize a payment transaction
     * 
     * @param string $email Customer email
     * @param float $amount Amount in the main currency unit (e.g., GHS, not pesewas)
     * @param string $reference Unique transaction reference
     * @param array $metadata Additional data to store with transaction
     * @param string $callback_url URL to redirect after payment
     * @return array Response from Paystack
     */
    public function initializeTransaction($email, $amount, $reference, $metadata = [], $callback_url = null) {
        // Convert amount to smallest currency unit (pesewas for GHS, kobo for NGN)
        $amount_in_smallest_unit = $amount * 100;
        
        $data = [
            'email' => $email,
            'amount' => (int) $amount_in_smallest_unit,
            'reference' => $reference,
            'currency' => PAYSTACK_CURRENCY,
            'callback_url' => $callback_url ?? PAYSTACK_CALLBACK_URL,
            'channels' => json_decode(PAYSTACK_CHANNELS, true),
            'metadata' => $metadata
        ];
        
        return $this->makeRequest('/transaction/initialize', $data);
    }
    
    /**
     * Verify a transaction
     * 
     * @param string $reference Transaction reference
     * @return array Response from Paystack
     */
    public function verifyTransaction($reference) {
        return $this->makeRequest('/transaction/verify/' . rawurlencode($reference), null, 'GET');
    }
    
    /**
     * Get list of banks for bank transfer
     * 
     * @param string $country Country code (ghana, nigeria, south-africa)
     * @return array Response from Paystack
     */
    public function getBanks($country = 'ghana') {
        return $this->makeRequest('/bank?country=' . $country, null, 'GET');
    }
    
    /**
     * Charge mobile money
     * 
     * @param string $email Customer email
     * @param float $amount Amount in main currency unit
     * @param string $phone Customer phone number
     * @param string $provider Mobile money provider (mtn, vod, tgo for Ghana)
     * @param string $reference Unique reference
     * @return array Response from Paystack
     */
    public function chargeMobileMoney($email, $amount, $phone, $provider, $reference) {
        $amount_in_smallest_unit = $amount * 100;
        
        $data = [
            'email' => $email,
            'amount' => (int) $amount_in_smallest_unit,
            'currency' => PAYSTACK_CURRENCY,
            'reference' => $reference,
            'mobile_money' => [
                'phone' => $phone,
                'provider' => $provider // 'mtn', 'vod', 'tgo' for Ghana
            ]
        ];
        
        return $this->makeRequest('/charge', $data);
    }
    
    /**
     * Submit OTP for pending charge
     * 
     * @param string $otp OTP entered by customer
     * @param string $reference Transaction reference
     * @return array Response from Paystack
     */
    public function submitOTP($otp, $reference) {
        $data = [
            'otp' => $otp,
            'reference' => $reference
        ];
        
        return $this->makeRequest('/charge/submit_otp', $data);
    }
    
    /**
     * Get transaction details
     * 
     * @param int $transaction_id Paystack transaction ID
     * @return array Response from Paystack
     */
    public function getTransaction($transaction_id) {
        return $this->makeRequest('/transaction/' . $transaction_id, null, 'GET');
    }
    
    /**
     * List transactions
     * 
     * @param array $params Query parameters (perPage, page, from, to, status)
     * @return array Response from Paystack
     */
    public function listTransactions($params = []) {
        $query = http_build_query($params);
        return $this->makeRequest('/transaction?' . $query, null, 'GET');
    }
    
    /**
     * Generate unique payment reference
     * 
     * @param int $order_id Order ID
     * @param int $customer_id Customer ID
     * @return string Unique reference
     */
    public function generateReference($order_id = null, $customer_id = null) {
        $prefix = 'JWL'; // Jewellery
        $timestamp = time();
        $random = bin2hex(random_bytes(4));
        
        if ($order_id && $customer_id) {
            return sprintf('%s_%d_%d_%s', $prefix, $order_id, $customer_id, $random);
        }
        
        return sprintf('%s_%d_%s', $prefix, $timestamp, $random);
    }
    
    /**
     * Validate Paystack webhook signature
     * 
     * @param string $input Raw POST body
     * @param string $signature Paystack signature from header
     * @return bool True if valid
     */
    public function validateWebhook($input, $signature) {
        $calculated = hash_hmac('sha512', $input, $this->secret_key);
        return $calculated === $signature;
    }
    
    /**
     * Get public key for frontend
     * 
     * @return string Public key
     */
    public function getPublicKey() {
        return $this->public_key;
    }
    
    /**
     * Make HTTP request to Paystack API
     * 
     * @param string $endpoint API endpoint
     * @param array|null $data Request data
     * @param string $method HTTP method
     * @return array Response
     */
    private function makeRequest($endpoint, $data = null, $method = 'POST') {
        $url = $this->api_url . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . $this->secret_key,
            'Content-Type: application/json',
            'Cache-Control: no-cache'
        ];
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        if ($method === 'POST' && $data !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($error) {
            error_log("Paystack cURL Error: " . $error);
            return [
                'status' => false,
                'message' => 'Connection error: ' . $error
            ];
        }
        
        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Paystack JSON Error: " . json_last_error_msg() . " | Response: " . $response);
            return [
                'status' => false,
                'message' => 'Invalid response from payment provider'
            ];
        }
        
        // Log for debugging (remove in production)
        error_log("Paystack Response [$endpoint]: " . json_encode($decoded));
        
        return $decoded;
    }
}

?>
