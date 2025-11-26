<?php
/**
 * Payment Class
 * Manages payment transactions and records
 */

require_once(dirname(__FILE__).'/../settings/db_class.php');

class Payment extends db_connection {
    
    /**
     * Record a payment transaction
     * 
     * @param array $payment_data Payment details
     * @return mixed Payment ID on success, false on failure
     */
    public function recordPayment($payment_data) {
        // Get single connection for all operations
        $conn = $this->db_conn();
        
        $order_id = intval($payment_data['order_id']);
        $gateway = mysqli_real_escape_string($conn, $payment_data['payment_gateway']);
        $reference = mysqli_real_escape_string($conn, $payment_data['transaction_reference']);
        $amount = floatval($payment_data['amount']);
        $currency = mysqli_real_escape_string($conn, $payment_data['currency']);
        $status = mysqli_real_escape_string($conn, $payment_data['payment_status']);
        $channel = isset($payment_data['payment_channel']) ? mysqli_real_escape_string($conn, $payment_data['payment_channel']) : null;
        $email = isset($payment_data['customer_email']) ? mysqli_real_escape_string($conn, $payment_data['customer_email']) : null;
        $phone = isset($payment_data['customer_phone']) ? mysqli_real_escape_string($conn, $payment_data['customer_phone']) : null;
        $response = isset($payment_data['gateway_response']) ? mysqli_real_escape_string($conn, $payment_data['gateway_response']) : null;
        
        $sql = "INSERT INTO payments (order_id, payment_gateway, transaction_reference, amount, currency, 
                payment_status, payment_channel, customer_email, customer_phone, gateway_response, date_completed) 
                VALUES ($order_id, '$gateway', '$reference', $amount, '$currency', '$status', " .
                ($channel ? "'$channel'" : "NULL") . ", " .
                ($email ? "'$email'" : "NULL") . ", " .
                ($phone ? "'$phone'" : "NULL") . ", " .
                ($response ? "'$response'" : "NULL") . ", NOW())";
        
        if (mysqli_query($conn, $sql)) {
            $payment_id = mysqli_insert_id($conn);
            error_log("Payment recorded successfully - ID: $payment_id, Reference: $reference");
            return $payment_id;
        } else {
            $error = mysqli_error($conn);
            error_log("Payment recording failed: $error");
            error_log("SQL: $sql");
            return false;
        }
    }
    
    /**
     * Get payment by transaction reference
     */
    public function getPaymentByReference($reference) {
        $reference = mysqli_real_escape_string($this->db_conn(), $reference);
        $sql = "SELECT * FROM payments WHERE transaction_reference = '$reference'";
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get payment by order ID
     */
    public function getPaymentByOrderId($order_id) {
        $order_id = intval($order_id);
        $sql = "SELECT * FROM payments WHERE order_id = $order_id ORDER BY date_initiated DESC";
        return $this->db_fetch_one($sql);
    }
}
?>
