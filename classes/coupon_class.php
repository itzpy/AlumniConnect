<?php
/**
 * Coupon Class
 * Manages discount coupons and promotional codes
 * Handles coupon validation, application, and usage tracking
 * 
 * @category E-Commerce
 * @package  AlumniConnect
 * @author   Alumni Connect Team
 */

require_once(dirname(__FILE__).'/../settings/db_class.php');

class Coupon extends db_connection {
    
    /**
     * Validate a coupon code
     * 
     * @param string $coupon_code Coupon code to validate
     * @param int $user_id User attempting to use the coupon
     * @param float $cart_total Current cart total
     * @return array Validation result with status, message, and coupon data
     */
    public function validateCoupon($coupon_code, $user_id, $cart_total) {
        $conn = $this->db_conn();
        $coupon_code = mysqli_real_escape_string($conn, strtoupper(trim($coupon_code)));
        $user_id = intval($user_id);
        $cart_total = floatval($cart_total);
        
        // Get coupon details
        $sql = "SELECT * FROM coupons WHERE coupon_code = '$coupon_code'";
        $coupon = $this->db_fetch_one($sql);
        
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code'];
        }
        
        // Check if coupon is active
        if (!$coupon['is_active']) {
            return ['valid' => false, 'message' => 'This coupon is no longer active'];
        }
        
        // Check validity dates
        $now = date('Y-m-d H:i:s');
        if ($coupon['valid_from'] && $now < $coupon['valid_from']) {
            return ['valid' => false, 'message' => 'This coupon is not yet valid'];
        }
        if ($coupon['valid_until'] && $now > $coupon['valid_until']) {
            return ['valid' => false, 'message' => 'This coupon has expired'];
        }
        
        // Check usage limit
        if ($coupon['usage_limit'] !== null && $coupon['usage_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit'];
        }
        
        // Check per-user limit
        $user_usage = $this->getUserCouponUsage($coupon['coupon_id'], $user_id);
        if ($coupon['per_user_limit'] !== null && $user_usage >= $coupon['per_user_limit']) {
            return ['valid' => false, 'message' => 'You have already used this coupon'];
        }
        
        // Check minimum order amount
        if ($cart_total < $coupon['min_order_amount']) {
            return [
                'valid' => false, 
                'message' => 'Minimum order amount of GHS ' . number_format($coupon['min_order_amount'], 2) . ' required'
            ];
        }
        
        // Calculate discount
        $discount = $this->calculateDiscount($coupon, $cart_total);
        
        return [
            'valid' => true,
            'message' => 'Coupon applied successfully!',
            'coupon' => [
                'coupon_id' => $coupon['coupon_id'],
                'coupon_code' => $coupon['coupon_code'],
                'description' => $coupon['description'],
                'discount_type' => $coupon['discount_type'],
                'discount_value' => $coupon['discount_value'],
                'discount_amount' => $discount
            ]
        ];
    }
    
    /**
     * Calculate discount amount based on coupon type
     * 
     * @param array $coupon Coupon data
     * @param float $cart_total Cart total before discount
     * @return float Discount amount
     */
    public function calculateDiscount($coupon, $cart_total) {
        $discount = 0.00;
        
        if ($coupon['discount_type'] === 'percentage') {
            $discount = ($cart_total * $coupon['discount_value']) / 100;
            
            // Apply maximum discount cap if set
            if ($coupon['max_discount_amount'] !== null && $discount > $coupon['max_discount_amount']) {
                $discount = $coupon['max_discount_amount'];
            }
        } else {
            // Fixed amount discount
            $discount = $coupon['discount_value'];
            
            // Don't let discount exceed cart total
            if ($discount > $cart_total) {
                $discount = $cart_total;
            }
        }
        
        return round($discount, 2);
    }
    
    /**
     * Get user's usage count for a specific coupon
     * 
     * @param int $coupon_id Coupon ID
     * @param int $user_id User ID
     * @return int Number of times user has used this coupon
     */
    public function getUserCouponUsage($coupon_id, $user_id) {
        $coupon_id = intval($coupon_id);
        $user_id = intval($user_id);
        
        $sql = "SELECT COUNT(*) as usage_count FROM coupon_usage 
                WHERE coupon_id = $coupon_id AND user_id = $user_id";
        $result = $this->db_fetch_one($sql);
        
        return $result ? intval($result['usage_count']) : 0;
    }
    
    /**
     * Record coupon usage after successful order
     * 
     * @param int $coupon_id Coupon ID
     * @param int $user_id User ID
     * @param int $order_id Order ID
     * @param float $discount_applied Discount amount applied
     * @return bool True on success
     */
    public function recordUsage($coupon_id, $user_id, $order_id, $discount_applied) {
        $conn = $this->db_conn();
        $coupon_id = intval($coupon_id);
        $user_id = intval($user_id);
        $order_id = intval($order_id);
        $discount_applied = floatval($discount_applied);
        
        // Insert usage record
        $sql = "INSERT INTO coupon_usage (coupon_id, user_id, order_id, discount_applied) 
                VALUES ($coupon_id, $user_id, $order_id, $discount_applied)";
        
        if (!mysqli_query($conn, $sql)) {
            return false;
        }
        
        // Increment usage count on coupon
        $update_sql = "UPDATE coupons SET usage_count = usage_count + 1 WHERE coupon_id = $coupon_id";
        mysqli_query($conn, $update_sql);
        
        return true;
    }
    
    /**
     * Get coupon by ID
     * 
     * @param int $coupon_id Coupon ID
     * @return array|false Coupon data or false
     */
    public function getCouponById($coupon_id) {
        $coupon_id = intval($coupon_id);
        $sql = "SELECT * FROM coupons WHERE coupon_id = $coupon_id";
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get coupon by code
     * 
     * @param string $coupon_code Coupon code
     * @return array|false Coupon data or false
     */
    public function getCouponByCode($coupon_code) {
        $conn = $this->db_conn();
        $coupon_code = mysqli_real_escape_string($conn, strtoupper(trim($coupon_code)));
        $sql = "SELECT * FROM coupons WHERE coupon_code = '$coupon_code'";
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get all coupons (for admin)
     * 
     * @param bool $active_only Only return active coupons
     * @return array|false Array of coupons
     */
    public function getAllCoupons($active_only = false) {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM coupon_usage WHERE coupon_id = c.coupon_id) as times_used
                FROM coupons c";
        
        if ($active_only) {
            $sql .= " WHERE c.is_active = 1 AND (c.valid_until IS NULL OR c.valid_until > NOW())";
        }
        
        $sql .= " ORDER BY c.date_created DESC";
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Create a new coupon
     * 
     * @param array $data Coupon data
     * @return mixed Coupon ID on success, false on failure
     */
    public function createCoupon($data) {
        $conn = $this->db_conn();
        
        $code = mysqli_real_escape_string($conn, strtoupper(trim($data['coupon_code'])));
        $description = mysqli_real_escape_string($conn, $data['description'] ?? '');
        $discount_type = mysqli_real_escape_string($conn, $data['discount_type']);
        $discount_value = floatval($data['discount_value']);
        $min_order = floatval($data['min_order_amount'] ?? 0);
        $max_discount = isset($data['max_discount_amount']) && $data['max_discount_amount'] !== '' 
                       ? floatval($data['max_discount_amount']) : 'NULL';
        $usage_limit = isset($data['usage_limit']) && $data['usage_limit'] !== '' 
                      ? intval($data['usage_limit']) : 'NULL';
        $per_user_limit = intval($data['per_user_limit'] ?? 1);
        $valid_from = mysqli_real_escape_string($conn, $data['valid_from'] ?? date('Y-m-d H:i:s'));
        $valid_until = !empty($data['valid_until']) 
                      ? "'" . mysqli_real_escape_string($conn, $data['valid_until']) . "'" : 'NULL';
        $is_active = isset($data['is_active']) ? intval($data['is_active']) : 1;
        $created_by = intval($data['created_by'] ?? 0);
        
        $sql = "INSERT INTO coupons (coupon_code, description, discount_type, discount_value, 
                min_order_amount, max_discount_amount, usage_limit, per_user_limit, 
                valid_from, valid_until, is_active, created_by) 
                VALUES ('$code', '$description', '$discount_type', $discount_value, 
                $min_order, $max_discount, $usage_limit, $per_user_limit, 
                '$valid_from', $valid_until, $is_active, $created_by)";
        
        if (mysqli_query($conn, $sql)) {
            return mysqli_insert_id($conn);
        }
        return false;
    }
    
    /**
     * Update a coupon
     * 
     * @param int $coupon_id Coupon ID
     * @param array $data Updated data
     * @return bool True on success
     */
    public function updateCoupon($coupon_id, $data) {
        $conn = $this->db_conn();
        $coupon_id = intval($coupon_id);
        
        $updates = [];
        
        if (isset($data['description'])) {
            $updates[] = "description = '" . mysqli_real_escape_string($conn, $data['description']) . "'";
        }
        if (isset($data['discount_type'])) {
            $updates[] = "discount_type = '" . mysqli_real_escape_string($conn, $data['discount_type']) . "'";
        }
        if (isset($data['discount_value'])) {
            $updates[] = "discount_value = " . floatval($data['discount_value']);
        }
        if (isset($data['min_order_amount'])) {
            $updates[] = "min_order_amount = " . floatval($data['min_order_amount']);
        }
        if (isset($data['max_discount_amount'])) {
            $val = $data['max_discount_amount'] !== '' ? floatval($data['max_discount_amount']) : 'NULL';
            $updates[] = "max_discount_amount = $val";
        }
        if (isset($data['usage_limit'])) {
            $val = $data['usage_limit'] !== '' ? intval($data['usage_limit']) : 'NULL';
            $updates[] = "usage_limit = $val";
        }
        if (isset($data['per_user_limit'])) {
            $updates[] = "per_user_limit = " . intval($data['per_user_limit']);
        }
        if (isset($data['valid_until'])) {
            $val = !empty($data['valid_until']) 
                  ? "'" . mysqli_real_escape_string($conn, $data['valid_until']) . "'" : 'NULL';
            $updates[] = "valid_until = $val";
        }
        if (isset($data['is_active'])) {
            $updates[] = "is_active = " . intval($data['is_active']);
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE coupons SET " . implode(', ', $updates) . " WHERE coupon_id = $coupon_id";
        return $this->db_query($sql);
    }
    
    /**
     * Delete a coupon
     * 
     * @param int $coupon_id Coupon ID
     * @return bool True on success
     */
    public function deleteCoupon($coupon_id) {
        $coupon_id = intval($coupon_id);
        $sql = "DELETE FROM coupons WHERE coupon_id = $coupon_id";
        return $this->db_query($sql);
    }
    
    /**
     * Toggle coupon active status
     * 
     * @param int $coupon_id Coupon ID
     * @return bool True on success
     */
    public function toggleStatus($coupon_id) {
        $coupon_id = intval($coupon_id);
        $sql = "UPDATE coupons SET is_active = NOT is_active WHERE coupon_id = $coupon_id";
        return $this->db_query($sql);
    }
}
?>
