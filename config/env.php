<?php
/**
 * Environment Configuration
 * Production settings for Alumni Connect
 */

return [
    // Application Environment
    'APP_ENV' => 'production',
    
    // Application Settings - UPDATE THIS TO YOUR PRODUCTION URL
    'APP_NAME' => 'Alumni Connect',
    'APP_URL' => 'http://64.23.252.43',  // Your Digital Ocean server
    'APP_DEBUG' => false,
    
    // Database Configuration (production)
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'alumni_connect',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    
    // Paystack Configuration - Use your existing keys
    'PAYSTACK_SECRET_KEY' => 'sk_test_1a36a80adbbd3e6805714561faa5cbeae4f807dc',
    'PAYSTACK_PUBLIC_KEY' => 'pk_test_1f102a9bf310c0b1c3a926083f43fb0517c4b485',
    
    // Security Settings
    'SESSION_LIFETIME' => 120,
    'CSRF_ENABLED' => true,
];
