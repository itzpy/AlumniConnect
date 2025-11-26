<?php
/**
 * Cart Class
 * Manages shopping cart functionality for the platform
 * Handles adding, removing, updating items in user's cart before checkout
 * 
 * @category E-Commerce
 * @package  AlumniConnect
 * @author   Alumni Connect Team
 */

require_once(dirname(__FILE__).'/../settings/db_class.php');

class Cart extends db_connection {
    
    /**
     * Add item to cart
     * If item already exists, update quantity instead
     * 
     * @param int $user_id User ID
     * @param int $service_id Service ID to add
     * @param int $quantity Quantity to add
     * @param string $selected_date Optional date for scheduled services
     * @param string $selected_time Optional time for scheduled services
     * @param string $special_requests Optional special requests
     * @return mixed Cart ID on success, false on failure
     */
    public function addToCart($user_id, $service_id, $quantity = 1, $selected_date = null, $selected_time = null, $special_requests = null) {
        $user_id = intval($user_id);
        $service_id = intval($service_id);
        $quantity = intval($quantity);
        
        // Check if item already exists in cart
        $existing = $this->getCartItem($user_id, $service_id);
        
        if ($existing) {
            // Update quantity
            $new_quantity = $existing['quantity'] + $quantity;
            return $this->updateCartQuantity($existing['cart_id'], $new_quantity);
        }
        
        // Escape strings
        $selected_date = $selected_date ? "'" . mysqli_real_escape_string($this->db_conn(), $selected_date) . "'" : 'NULL';
        $selected_time = $selected_time ? "'" . mysqli_real_escape_string($this->db_conn(), $selected_time) . "'" : 'NULL';
        $special_requests = $special_requests ? "'" . mysqli_real_escape_string($this->db_conn(), $special_requests) . "'" : 'NULL';
        
        // Insert new cart item
        $sql = "INSERT INTO cart (user_id, service_id, quantity, selected_date, selected_time, special_requests) 
                VALUES ($user_id, $service_id, $quantity, $selected_date, $selected_time, $special_requests)";
        
        if ($this->db_query($sql)) {
            return mysqli_insert_id($this->db_conn());
        }
        return false;
    }
    
    /**
     * Get a specific cart item
     * 
     * @param int $user_id User ID
     * @param int $service_id Service ID
     * @return array|false Cart item or false if not found
     */
    private function getCartItem($user_id, $service_id) {
        $user_id = intval($user_id);
        $service_id = intval($service_id);
        
        $sql = "SELECT * FROM cart WHERE user_id = $user_id AND service_id = $service_id";
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get all items in user's cart with service details
     * 
     * @param int $user_id User ID
     * @return array|false Array of cart items with service details or false
     */
    public function getCartItems($user_id) {
        $user_id = intval($user_id);
        
        $sql = "SELECT c.*, s.service_name, s.service_type, s.description, s.price, 
                s.image_url, s.category, s.location, s.stock_quantity,
                (c.quantity * s.price) as subtotal
                FROM cart c
                INNER JOIN services s ON c.service_id = s.service_id
                WHERE c.user_id = $user_id AND s.is_active = 1
                ORDER BY c.date_added DESC";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Get cart count (total number of items)
     * 
     * @param int $user_id User ID
     * @return int Number of items in cart
     */
    public function getCartCount($user_id) {
        $user_id = intval($user_id);
        
        $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id";
        $result = $this->db_fetch_one($sql);
        
        return $result ? intval($result['total']) : 0;
    }
    
    /**
     * Get cart total amount
     * 
     * @param int $user_id User ID
     * @return float Total cart value
     */
    public function getCartTotal($user_id) {
        $user_id = intval($user_id);
        
        $sql = "SELECT SUM(c.quantity * s.price) as total
                FROM cart c
                INNER JOIN services s ON c.service_id = s.service_id
                WHERE c.user_id = $user_id AND s.is_active = 1";
        
        $result = $this->db_fetch_one($sql);
        return $result ? floatval($result['total']) : 0.00;
    }
    
    /**
     * Update cart item quantity
     * 
     * @param int $cart_id Cart item ID
     * @param int $quantity New quantity
     * @return bool True on success, false on failure
     */
    public function updateCartQuantity($cart_id, $quantity) {
        $cart_id = intval($cart_id);
        $quantity = intval($quantity);
        
        if ($quantity <= 0) {
            return $this->removeFromCart($cart_id);
        }
        
        $sql = "UPDATE cart SET quantity = $quantity WHERE cart_id = $cart_id";
        return $this->db_query($sql);
    }
    
    /**
     * Update cart item details (date, time, requests)
     * 
     * @param int $cart_id Cart item ID
     * @param array $update_data Array of fields to update
     * @return bool True on success, false on failure
     */
    public function updateCartItem($cart_id, $update_data) {
        $cart_id = intval($cart_id);
        $updates = [];
        
        if (isset($update_data['selected_date'])) {
            $date = mysqli_real_escape_string($this->db_conn(), $update_data['selected_date']);
            $updates[] = "selected_date = '$date'";
        }
        
        if (isset($update_data['selected_time'])) {
            $time = mysqli_real_escape_string($this->db_conn(), $update_data['selected_time']);
            $updates[] = "selected_time = '$time'";
        }
        
        if (isset($update_data['special_requests'])) {
            $requests = mysqli_real_escape_string($this->db_conn(), $update_data['special_requests']);
            $updates[] = "special_requests = '$requests'";
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE cart SET " . implode(', ', $updates) . " WHERE cart_id = $cart_id";
        return $this->db_query($sql);
    }
    
    /**
     * Remove item from cart
     * 
     * @param int $cart_id Cart item ID
     * @return bool True on success, false on failure
     */
    public function removeFromCart($cart_id) {
        $cart_id = intval($cart_id);
        $sql = "DELETE FROM cart WHERE cart_id = $cart_id";
        return $this->db_query($sql);
    }
    
    /**
     * Remove item from cart by service ID
     * 
     * @param int $user_id User ID
     * @param int $service_id Service ID
     * @return bool True on success, false on failure
     */
    public function removeByServiceId($user_id, $service_id) {
        $user_id = intval($user_id);
        $service_id = intval($service_id);
        
        $sql = "DELETE FROM cart WHERE user_id = $user_id AND service_id = $service_id";
        return $this->db_query($sql);
    }
    
    /**
     * Clear all items from user's cart
     * 
     * @param int $user_id User ID
     * @return bool True on success, false on failure
     */
    public function clearCart($user_id) {
        $user_id = intval($user_id);
        $sql = "DELETE FROM cart WHERE user_id = $user_id";
        return $this->db_query($sql);
    }
    
    /**
     * Validate cart items availability before checkout
     * 
     * @param int $user_id User ID
     * @return array Array with 'valid' boolean and 'errors' array
     */
    public function validateCart($user_id) {
        $user_id = intval($user_id);
        $items = $this->getCartItems($user_id);
        $errors = [];
        
        if (!$items || empty($items)) {
            return ['valid' => false, 'errors' => ['Cart is empty']];
        }
        
        foreach ($items as $item) {
            // Check if service is still active
            if (!$item['service_id']) {
                $errors[] = $item['service_name'] . ' is no longer available';
                continue;
            }
            
            // Check stock for events
            if ($item['stock_quantity'] !== null && $item['stock_quantity'] < $item['quantity']) {
                $errors[] = $item['service_name'] . ' has insufficient stock (Available: ' . $item['stock_quantity'] . ')';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Get cart summary for display
     * 
     * @param int $user_id User ID
     * @return array Cart summary with totals
     */
    public function getCartSummary($user_id) {
        $items = $this->getCartItems($user_id);
        $subtotal = $this->getCartTotal($user_id);
        $tax_rate = 0.00; // Set tax rate if applicable
        $tax_amount = $subtotal * $tax_rate;
        $total = $subtotal + $tax_amount;
        
        return [
            'items' => $items ? $items : [],
            'item_count' => $this->getCartCount($user_id),
            'subtotal' => $subtotal,
            'tax_rate' => $tax_rate,
            'tax_amount' => $tax_amount,
            'total' => $total
        ];
    }
}
?>