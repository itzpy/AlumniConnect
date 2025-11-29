<?php
/**
 * Security Helper Functions
 * Provides CSRF protection and other security utilities
 */

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validate_csrf_token($token, $max_age = 3600) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    // Check if token matches
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    
    // Check if token has expired
    if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > $max_age) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    return true;
}

/**
 * Get CSRF hidden input field
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Regenerate session ID (call after login)
 */
function regenerate_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_regenerate_id(true);
}

/**
 * Secure session configuration
 */
function secure_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Use secure cookies in production
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', 1);
        }
        
        session_start();
    }
}

/**
 * Sanitize output for XSS prevention
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate and sanitize email
 */
function sanitize_email($email) {
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
}

/**
 * Generate secure random string
 */
function generate_random_string($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Rate limiting check (basic implementation)
 */
function check_rate_limit($key, $max_attempts = 5, $decay_minutes = 15) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $rate_key = 'rate_limit_' . $key;
    $time_key = 'rate_limit_time_' . $key;
    
    // Initialize or reset if expired
    if (!isset($_SESSION[$time_key]) || (time() - $_SESSION[$time_key]) > ($decay_minutes * 60)) {
        $_SESSION[$rate_key] = 0;
        $_SESSION[$time_key] = time();
    }
    
    // Check if over limit
    if ($_SESSION[$rate_key] >= $max_attempts) {
        return false;
    }
    
    // Increment counter
    $_SESSION[$rate_key]++;
    
    return true;
}

/**
 * Reset rate limit (call after successful action)
 */
function reset_rate_limit($key) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    unset($_SESSION['rate_limit_' . $key]);
    unset($_SESSION['rate_limit_time_' . $key]);
}
?>
