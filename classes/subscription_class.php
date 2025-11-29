<?php
/**
 * Subscription Class
 * Manages user subscriptions and plan features
 */

require_once(dirname(__FILE__).'/../settings/db_class.php');

class Subscription extends db_connection {
    
    // Plan definitions
    private $plans = [
        'free' => [
            'name' => 'Free',
            'price' => 0,
            'features' => [
                'messages_per_month' => 5,
                'mentorship_sessions' => 0,
                'job_postings' => 0,
                'event_discount' => 0,
                'service_discount' => 0,
                'early_job_access' => false,
                'profile_badge' => null,
                'vip_events' => false,
                'priority_support' => false
            ]
        ],
        'professional' => [
            'name' => 'Professional',
            'price' => 49,
            'features' => [
                'messages_per_month' => -1, // Unlimited
                'mentorship_sessions' => 5,
                'job_postings' => 3,
                'event_discount' => 10,
                'service_discount' => 0,
                'early_job_access' => true,
                'profile_badge' => 'star',
                'vip_events' => false,
                'priority_support' => false
            ]
        ],
        'premium' => [
            'name' => 'Premium',
            'price' => 99,
            'features' => [
                'messages_per_month' => -1, // Unlimited
                'mentorship_sessions' => -1, // Unlimited
                'job_postings' => -1, // Unlimited
                'event_discount' => 25,
                'service_discount' => 25,
                'early_job_access' => true,
                'profile_badge' => 'crown',
                'vip_events' => true,
                'priority_support' => true
            ]
        ]
    ];
    
    /**
     * Get user's current subscription
     */
    public function getUserSubscription($user_id) {
        $user_id = intval($user_id);
        
        $sql = "SELECT * FROM subscriptions 
                WHERE user_id = $user_id 
                AND status = 'active' 
                AND (expires_at IS NULL OR expires_at > NOW())
                ORDER BY created_at DESC 
                LIMIT 1";
        
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get user's plan type
     */
    public function getUserPlanType($user_id) {
        $subscription = $this->getUserSubscription($user_id);
        return $subscription ? $subscription['plan_type'] : 'free';
    }
    
    /**
     * Get plan details
     */
    public function getPlanDetails($plan_type) {
        return $this->plans[$plan_type] ?? $this->plans['free'];
    }
    
    /**
     * Get all plans
     */
    public function getAllPlans() {
        return $this->plans;
    }
    
    /**
     * Check if user has a specific feature
     */
    public function hasFeature($user_id, $feature) {
        $plan_type = $this->getUserPlanType($user_id);
        $plan = $this->getPlanDetails($plan_type);
        
        if (!isset($plan['features'][$feature])) {
            return false;
        }
        
        $value = $plan['features'][$feature];
        
        // For boolean features
        if (is_bool($value)) {
            return $value;
        }
        
        // For numeric features (-1 means unlimited)
        if (is_numeric($value)) {
            return $value !== 0;
        }
        
        return !empty($value);
    }
    
    /**
     * Get feature limit for user
     */
    public function getFeatureLimit($user_id, $feature) {
        $plan_type = $this->getUserPlanType($user_id);
        $plan = $this->getPlanDetails($plan_type);
        
        return $plan['features'][$feature] ?? 0;
    }
    
    /**
     * Get discount percentage for user
     */
    public function getUserDiscount($user_id, $discount_type = 'service_discount') {
        $plan_type = $this->getUserPlanType($user_id);
        $plan = $this->getPlanDetails($plan_type);
        
        return $plan['features'][$discount_type] ?? 0;
    }
    
    /**
     * Create a new subscription
     */
    public function createSubscription($user_id, $plan_type, $payment_reference = null) {
        $conn = $this->db_conn();
        $user_id = intval($user_id);
        $plan_type = mysqli_real_escape_string($conn, $plan_type);
        
        // Calculate expiry (1 month from now)
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 month'));
        
        // Deactivate any existing subscriptions
        $this->deactivateUserSubscriptions($user_id);
        
        // Get plan price
        $plan = $this->getPlanDetails($plan_type);
        $amount = $plan['price'];
        
        $payment_ref = $payment_reference ? "'".mysqli_real_escape_string($conn, $payment_reference)."'" : "NULL";
        
        $sql = "INSERT INTO subscriptions (user_id, plan_type, amount, status, payment_reference, expires_at) 
                VALUES ($user_id, '$plan_type', $amount, 'active', $payment_ref, '$expires_at')";
        
        if ($this->db_query($sql)) {
            return mysqli_insert_id($conn);
        }
        
        return false;
    }
    
    /**
     * Deactivate all user subscriptions
     */
    public function deactivateUserSubscriptions($user_id) {
        $user_id = intval($user_id);
        $sql = "UPDATE subscriptions SET status = 'cancelled' WHERE user_id = $user_id AND status = 'active'";
        return $this->db_query($sql);
    }
    
    /**
     * Cancel subscription
     */
    public function cancelSubscription($subscription_id) {
        $subscription_id = intval($subscription_id);
        $sql = "UPDATE subscriptions SET status = 'cancelled', cancelled_at = NOW() WHERE subscription_id = $subscription_id";
        return $this->db_query($sql);
    }
    
    /**
     * Get user's profile badge based on subscription
     */
    public function getUserBadge($user_id) {
        $plan_type = $this->getUserPlanType($user_id);
        $plan = $this->getPlanDetails($plan_type);
        
        return $plan['features']['profile_badge'] ?? null;
    }
    
    /**
     * Check subscription usage (e.g., messages sent this month)
     */
    public function getUsageThisMonth($user_id, $usage_type) {
        $user_id = intval($user_id);
        $usage_type = mysqli_real_escape_string($this->db_conn(), $usage_type);
        
        $sql = "SELECT COUNT(*) as count FROM subscription_usage 
                WHERE user_id = $user_id 
                AND usage_type = '$usage_type' 
                AND MONTH(used_at) = MONTH(NOW()) 
                AND YEAR(used_at) = YEAR(NOW())";
        
        $result = $this->db_fetch_one($sql);
        return $result ? intval($result['count']) : 0;
    }
    
    /**
     * Record usage
     */
    public function recordUsage($user_id, $usage_type) {
        $conn = $this->db_conn();
        $user_id = intval($user_id);
        $usage_type = mysqli_real_escape_string($conn, $usage_type);
        
        $sql = "INSERT INTO subscription_usage (user_id, usage_type) VALUES ($user_id, '$usage_type')";
        return $this->db_query($sql);
    }
    
    /**
     * Check if user can perform action based on limits
     */
    public function canPerformAction($user_id, $action_type) {
        $limit = $this->getFeatureLimit($user_id, $action_type);
        
        // -1 means unlimited
        if ($limit === -1) {
            return true;
        }
        
        // 0 means not allowed
        if ($limit === 0) {
            return false;
        }
        
        // Check current usage
        $usage = $this->getUsageThisMonth($user_id, $action_type);
        
        return $usage < $limit;
    }
}
?>
