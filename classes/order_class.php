<?php
/**
 * Order Class
 * Manages customer orders, order items, and order tracking
 * Handles order creation, status updates, and order history
 * 
 * @category E-Commerce
 * @package  AlumniConnect
 * @author   Alumni Connect Team
 */

require_once(dirname(__FILE__).'/../settings/db_class.php');

class Order extends db_connection {
    
    /**
     * Create a new order from cart items
     * 
     * @param int $user_id User ID placing the order
     * @param array $billing_info Billing information array
     * @return mixed Order ID on success, false on failure
     */
    public function createOrder($user_id, $billing_info) {
        $user_id = intval($user_id);
        
        // Generate unique order number
        $order_number = $this->generateOrderNumber();
        
        // Get cart items and calculate totals
        require_once(dirname(__FILE__).'/cart_class.php');
        $cart = new Cart();
        $cart_summary = $cart->getCartSummary($user_id);
        
        if (empty($cart_summary['items'])) {
            return false;
        }
        
        // Get database connection
        $conn = $this->db_conn();
        
        // Escape billing info
        $billing_name = mysqli_real_escape_string($conn, $billing_info['name']);
        $billing_email = mysqli_real_escape_string($conn, $billing_info['email']);
        $billing_phone = mysqli_real_escape_string($conn, $billing_info['phone']);
        $notes = isset($billing_info['notes']) ? mysqli_real_escape_string($conn, $billing_info['notes']) : '';
        
        $subtotal = $cart_summary['subtotal'];
        $discount_amount = isset($billing_info['discount']) ? floatval($billing_info['discount']) : 0.00;
        $tax_amount = $cart_summary['tax_amount'];
        $final_amount = $subtotal - $discount_amount + $tax_amount;
        
        // Begin transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert order
            $sql = "INSERT INTO orders (order_number, user_id, total_amount, discount_amount, 
                    tax_amount, final_amount, billing_name, billing_email, billing_phone, notes) 
                    VALUES ('$order_number', $user_id, $subtotal, $discount_amount, 
                    $tax_amount, $final_amount, '$billing_name', '$billing_email', '$billing_phone', '$notes')";
            
            if (!mysqli_query($conn, $sql)) {
                $error = mysqli_error($conn);
                throw new Exception("Failed to create order: " . $error);
            }
            
            $order_id = mysqli_insert_id($conn);
            
            if (!$order_id) {
                throw new Exception("Failed to get order ID after insert");
            }
            
            // Insert order items
            foreach ($cart_summary['items'] as $item) {
                $service_id = intval($item['service_id']);
                $service_name = mysqli_real_escape_string($conn, $item['service_name']);
                $quantity = intval($item['quantity']);
                $unit_price = floatval($item['price']);
                $total_price = floatval($item['subtotal']);
                
                $selected_date = $item['selected_date'] ? "'" . $item['selected_date'] . "'" : 'NULL';
                $selected_time = $item['selected_time'] ? "'" . $item['selected_time'] . "'" : 'NULL';
                $special_requests = $item['special_requests'] ? "'" . mysqli_real_escape_string($conn, $item['special_requests']) . "'" : 'NULL';
                
                $item_sql = "INSERT INTO order_items (order_id, service_id, service_name, quantity, 
                            unit_price, total_price, selected_date, selected_time, special_requests) 
                            VALUES ($order_id, $service_id, '$service_name', $quantity, $unit_price, 
                            $total_price, $selected_date, $selected_time, $special_requests)";
                
                if (!mysqli_query($conn, $item_sql)) {
                    $error = mysqli_error($conn);
                    throw new Exception("Failed to add order item: " . $error);
                }
                
                // Update stock for events (using same connection for transaction integrity)
                if ($item['stock_quantity'] !== null) {
                    $stock_sql = "UPDATE services 
                                 SET stock_quantity = stock_quantity - $quantity 
                                 WHERE service_id = $service_id AND stock_quantity IS NOT NULL";
                    
                    if (!mysqli_query($conn, $stock_sql)) {
                        $error = mysqli_error($conn);
                        throw new Exception("Failed to update stock: " . $error);
                    }
                }
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            return $order_id;
            
        } catch (Exception $e) {
            // Rollback on error
            mysqli_rollback($conn);
            // Log the error for debugging
            error_log("Order creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get order number by order ID
     * 
     * @param int $order_id Order ID
     * @return string|null Order number or null if not found
     */
    public function getOrderNumber($order_id) {
        $order_id = intval($order_id);
        $sql = "SELECT order_number FROM orders WHERE order_id = $order_id";
        
        $result = $this->db_fetch_one($sql);
        
        return $result ? $result['order_number'] : null;
    }
    
    /**
     * Generate unique order number
     * Format: ORD-YYYYMMDD-XXXX
     * 
     * @return string Order number
     */
    private function generateOrderNumber() {
        $date = date('Ymd');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return "ORD-$date-$random";
    }
    
    /**
     * Get order by ID
     * 
     * @param int $order_id Order ID
     * @return array|false Order details or false if not found
     */
    public function getOrderById($order_id) {
        $order_id = intval($order_id);
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email 
                FROM orders o 
                INNER JOIN users u ON o.user_id = u.user_id 
                WHERE o.order_id = $order_id";
        
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get order by order number
     * 
     * @param string $order_number Order number
     * @return array|false Order details or false if not found
     */
    public function getOrderByNumber($order_number) {
        $order_number = mysqli_real_escape_string($this->db_conn(), $order_number);
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email 
                FROM orders o 
                INNER JOIN users u ON o.user_id = u.user_id 
                WHERE o.order_number = '$order_number'";
        
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get order items for a specific order
     * 
     * @param int $order_id Order ID
     * @return array|false Array of order items or false
     */
    public function getOrderItems($order_id) {
        $order_id = intval($order_id);
        $sql = "SELECT * FROM order_items WHERE order_id = $order_id ORDER BY order_item_id";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Get all orders for a user
     * 
     * @param int $user_id User ID
     * @param string $status Optional filter by status
     * @return array|false Array of orders or false
     */
    public function getUserOrders($user_id, $status = null) {
        $user_id = intval($user_id);
        $sql = "SELECT * FROM orders WHERE user_id = $user_id";
        
        if ($status) {
            $status = mysqli_real_escape_string($this->db_conn(), $status);
            $sql .= " AND order_status = '$status'";
        }
        
        $sql .= " ORDER BY date_created DESC";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Update order status
     * 
     * @param int $order_id Order ID
     * @param string $status New status
     * @return bool True on success, false on failure
     */
    public function updateOrderStatus($order_id, $status) {
        $order_id = intval($order_id);
        $status = mysqli_real_escape_string($this->db_conn(), $status);
        
        $sql = "UPDATE orders SET order_status = '$status' WHERE order_id = $order_id";
        return $this->db_query($sql);
    }
    
    /**
     * Update payment status
     * 
     * @param int $order_id Order ID
     * @param string $payment_status New payment status
     * @param string $payment_method Payment method used
     * @param string $payment_reference Payment reference/transaction ID
     * @return bool True on success, false on failure
     */
    public function updatePaymentStatus($order_id, $payment_status, $payment_method = null, $payment_reference = null) {
        $order_id = intval($order_id);
        $payment_status = mysqli_real_escape_string($this->db_conn(), $payment_status);
        
        $sql = "UPDATE orders SET payment_status = '$payment_status'";
        
        if ($payment_method) {
            $payment_method = mysqli_real_escape_string($this->db_conn(), $payment_method);
            $sql .= ", payment_method = '$payment_method'";
        }
        
        if ($payment_reference) {
            $payment_reference = mysqli_real_escape_string($this->db_conn(), $payment_reference);
            $sql .= ", payment_reference = '$payment_reference'";
        }
        
        $sql .= " WHERE order_id = $order_id";
        
        return $this->db_query($sql);
    }
    
    /**
     * Update order item fulfillment status
     * 
     * @param int $order_item_id Order item ID
     * @param string $status New fulfillment status
     * @return bool True on success, false on failure
     */
    public function updateItemFulfillmentStatus($order_item_id, $status) {
        $order_item_id = intval($order_item_id);
        $status = mysqli_real_escape_string($this->db_conn(), $status);
        
        $sql = "UPDATE order_items SET fulfillment_status = '$status' WHERE order_item_id = $order_item_id";
        return $this->db_query($sql);
    }
    
    /**
     * Cancel order
     * 
     * @param int $order_id Order ID
     * @return bool True on success, false on failure
     */
    public function cancelOrder($order_id) {
        $order_id = intval($order_id);
        
        // Get order items to restore stock
        $items = $this->getOrderItems($order_id);
        
        if ($items) {
            require_once(dirname(__FILE__).'/service_class.php');
            $service = new Service();
            
            foreach ($items as $item) {
                // Restore stock for events
                $service_details = $service->getServiceById($item['service_id']);
                if ($service_details && $service_details['stock_quantity'] !== null) {
                    $service->updateStock($item['service_id'], $item['quantity']);
                }
            }
        }
        
        // Update order status
        $sql = "UPDATE orders SET order_status = 'cancelled' WHERE order_id = $order_id";
        return $this->db_query($sql);
    }
    
    /**
     * Get order statistics for a user
     * 
     * @param int $user_id User ID
     * @return array Order statistics
     */
    public function getUserOrderStats($user_id) {
        $user_id = intval($user_id);
        
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN order_status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    SUM(CASE WHEN payment_status = 'paid' THEN final_amount ELSE 0 END) as total_spent
                FROM orders
                WHERE user_id = $user_id";
        
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get all orders with filters (for admin)
     * 
     * @param array $filters Optional filters array
     * @return array|false Array of orders or false
     */
    public function getAllOrders($filters = []) {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email, u.user_role 
                FROM orders o 
                INNER JOIN users u ON o.user_id = u.user_id 
                WHERE 1=1";
        
        if (isset($filters['order_status'])) {
            $status = mysqli_real_escape_string($this->db_conn(), $filters['order_status']);
            $sql .= " AND o.order_status = '$status'";
        }
        
        if (isset($filters['payment_status'])) {
            $status = mysqli_real_escape_string($this->db_conn(), $filters['payment_status']);
            $sql .= " AND o.payment_status = '$status'";
        }
        
        if (isset($filters['start_date'])) {
            $date = mysqli_real_escape_string($this->db_conn(), $filters['start_date']);
            $sql .= " AND DATE(o.date_created) >= '$date'";
        }
        
        if (isset($filters['end_date'])) {
            $date = mysqli_real_escape_string($this->db_conn(), $filters['end_date']);
            $sql .= " AND DATE(o.date_created) <= '$date'";
        }
        
        $sql .= " ORDER BY o.date_created DESC";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Generate invoice for an order
     * 
     * @param int $order_id Order ID
     * @return mixed Invoice ID on success, false on failure
     */
    public function generateInvoice($order_id) {
        $order = $this->getOrderById($order_id);
        
        if (!$order) {
            return false;
        }
        
        // Generate invoice number
        $invoice_number = 'INV-' . date('Y') . '-' . str_pad($order_id, 4, '0', STR_PAD_LEFT);
        $invoice_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+30 days'));
        
        $sql = "INSERT INTO invoices (invoice_number, order_id, user_id, invoice_date, due_date, 
                subtotal, tax_amount, discount_amount, total_amount, invoice_status) 
                VALUES ('$invoice_number', $order_id, {$order['user_id']}, '$invoice_date', '$due_date', 
                {$order['total_amount']}, {$order['tax_amount']}, {$order['discount_amount']}, 
                {$order['final_amount']}, 'sent')";
        
        if ($this->db_query($sql)) {
            $invoice_id = mysqli_insert_id($this->db_conn());
            
            // Update invoice sent date
            $update_sql = "UPDATE invoices SET date_sent = NOW() WHERE invoice_id = $invoice_id";
            $this->db_query($update_sql);
            
            return $invoice_id;
        }
        
        return false;
    }
    
    /**
     * Get invoice by order ID
     * 
     * @param int $order_id Order ID
     * @return array|false Invoice details or false
     */
    public function getInvoiceByOrderId($order_id) {
        $order_id = intval($order_id);
        $sql = "SELECT * FROM invoices WHERE order_id = $order_id";
        
        return $this->db_fetch_one($sql);
    }
}
?>