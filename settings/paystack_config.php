<?php
/**
 * Paystack Configuration
 * Secure payment gateway settings for Alumni Connect
 * 
 * IMPORTANT: For production, replace test keys with live keys
 * and consider using environment variables
 */
require_once 'db_cred.php';

// Load environment config if exists
$env_config = [];
if (file_exists(dirname(__FILE__) . '/../config/env.php')) {
    $env_config = include dirname(__FILE__) . '/../config/env.php';
}

// Paystack API Keys - Use environment config or fallback to test keys
// IMPORTANT: Replace with live keys in production!
define('PAYSTACK_SECRET_KEY', $env_config['PAYSTACK_SECRET_KEY'] ?? 'sk_test_1a36a80adbbd3e6805714561faa5cbeae4f807dc');
define('PAYSTACK_PUBLIC_KEY', $env_config['PAYSTACK_PUBLIC_KEY'] ?? 'pk_test_1f102a9bf310c0b1c3a926083f43fb0517c4b485');

// Paystack URLs
define('PAYSTACK_API_URL', 'https://api.paystack.co');
define('PAYSTACK_INIT_ENDPOINT', PAYSTACK_API_URL . '/transaction/initialize');
define('PAYSTACK_VERIFY_ENDPOINT', PAYSTACK_API_URL . '/transaction/verify/');

// Application settings
define('APP_ENVIRONMENT', $env_config['APP_ENV'] ?? 'test');
define('APP_BASE_URL', $env_config['APP_URL'] ?? 'http://localhost/AlumniConnect');
define('PAYSTACK_CALLBACK_URL', APP_BASE_URL . '/views/paystack_callback.php'); // Callback after payment

/**
 * Initialize a Paystack transaction
 * 
 * @param float $amount Amount in GHS (will be converted to pesewas)
 * @param string $email Customer email
 * @param string $reference Optional reference
 * @return array Response with 'status' and 'data' containing authorization_url
 */
function paystack_initialize_transaction($amount, $email, $reference = null) {
    $reference = $reference ?? 'ref_' . uniqid();
    
    // Convert GHS to pesewas (1 GHS = 100 pesewas)
    $amount_in_pesewas = round($amount * 100);
    
    $data = [
        'amount' => $amount_in_pesewas,
        'email' => $email,
        'reference' => $reference,
        'callback_url' => PAYSTACK_CALLBACK_URL,
        'metadata' => [
            'currency' => 'GHS',
            'app' => 'Alumni Connect',
            'environment' => APP_ENVIRONMENT
        ]
    ];
    
    $response = paystack_api_request('POST', PAYSTACK_INIT_ENDPOINT, $data);
    
    return $response;
}

/**
 * Verify a Paystack transaction
 * 
 * @param string $reference Transaction reference
 * @return array Response with transaction details
 */
function paystack_verify_transaction($reference) {
    $response = paystack_api_request('GET', PAYSTACK_VERIFY_ENDPOINT . $reference);
    
    return $response;
}

/**
 * Make a request to Paystack API
 * 
 * @param string $method HTTP method (GET, POST, etc)
 * @param string $url Full API endpoint URL
 * @param array $data Optional data to send
 * @return array API response decoded as array
 */
function paystack_api_request($method, $url, $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Set headers
    $headers = [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
        'Content-Type: application/json'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Send data for POST/PUT requests
    if ($method !== 'GET' && $data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    curl_close($ch);
    
    // Handle curl errors
    if ($curl_error) {
        error_log("Paystack API CURL Error: $curl_error");
        return [
            'status' => false,
            'message' => 'Connection error: ' . $curl_error
        ];
    }
    
    // Decode response
    $result = json_decode($response, true);
    
    // Log for debugging
    error_log("Paystack API Response (HTTP $http_code): " . json_encode($result));
    
    return $result;
}

/**
 * Get currency symbol for display
 */
function get_currency_symbol($currency = 'GHS') {
    $symbols = [
        'GHS' => '₵',
        'USD' => '$',
        'EUR' => '€',
        'NGN' => '₦'
    ];
    
    return $symbols[$currency] ?? $currency;
}
?>
