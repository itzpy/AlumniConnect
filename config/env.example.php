<?php
/**
 * Environment Configuration
 * Copy this file to .env.php and update with your production values
 * NEVER commit .env.php to version control
 */

return [
    // Application Environment: 'development', 'staging', 'production'
    'APP_ENV' => 'development',
    
    // Application Settings
    'APP_NAME' => 'Alumni Connect',
    'APP_URL' => 'http://localhost/AlumniConnect',
    'APP_DEBUG' => true,
    
    // Database Configuration
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'alumni_connect',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    
    // Paystack Configuration
    'PAYSTACK_SECRET_KEY' => 'sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxx',
    'PAYSTACK_PUBLIC_KEY' => 'pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxx',
    
    // Email Configuration (for future use)
    'MAIL_HOST' => 'smtp.mailtrap.io',
    'MAIL_PORT' => 2525,
    'MAIL_USERNAME' => '',
    'MAIL_PASSWORD' => '',
    'MAIL_FROM_ADDRESS' => 'noreply@alumniconnect.com',
    'MAIL_FROM_NAME' => 'Alumni Connect',
    
    // Security Settings
    'SESSION_LIFETIME' => 120, // minutes
    'CSRF_ENABLED' => true,
    
    // File Upload Settings
    'UPLOAD_MAX_SIZE' => 5242880, // 5MB in bytes
    'ALLOWED_IMAGE_TYPES' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
];
