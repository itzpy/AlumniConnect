<?php
/**
 * Service Class
 * Manages all services/products in the platform including job postings,
 * mentorship sessions, event tickets, and premium features
 * 
 * @category E-Commerce
 * @package  AlumniConnect
 * @author   Alumni Connect Team
 */

require_once(dirname(__FILE__).'/../settings/db_class.php');

class Service extends db_connection {
    
    /**
     * Add a new service to the platform
     * 
     * @param array $service_data Associative array containing service details
     * @return mixed Service ID on success, false on failure
     */
    public function addService($service_data) {
        // Extract service data
        $service_name = mysqli_real_escape_string($this->db_conn(), $service_data['service_name']);
        $service_type = mysqli_real_escape_string($this->db_conn(), $service_data['service_type']);
        $description = mysqli_real_escape_string($this->db_conn(), $service_data['description']);
        $price = floatval($service_data['price']);
        $duration = isset($service_data['duration']) ? intval($service_data['duration']) : NULL;
        $provider_id = isset($service_data['provider_id']) ? intval($service_data['provider_id']) : NULL;
        $category = isset($service_data['category']) ? mysqli_real_escape_string($this->db_conn(), $service_data['category']) : NULL;
        $location = isset($service_data['location']) ? mysqli_real_escape_string($this->db_conn(), $service_data['location']) : NULL;
        $stock_quantity = isset($service_data['stock_quantity']) ? intval($service_data['stock_quantity']) : NULL;
        $image_url = isset($service_data['image_url']) ? mysqli_real_escape_string($this->db_conn(), $service_data['image_url']) : NULL;
        
        // Prepare SQL query
        $sql = "INSERT INTO services (service_name, service_type, description, price, duration, 
                provider_id, category, location, stock_quantity, image_url) 
                VALUES ('$service_name', '$service_type', '$description', $price, ";
        
        $sql .= ($duration !== NULL) ? "$duration, " : "NULL, ";
        $sql .= ($provider_id !== NULL) ? "$provider_id, " : "NULL, ";
        $sql .= ($category !== NULL) ? "'$category', " : "NULL, ";
        $sql .= ($location !== NULL) ? "'$location', " : "NULL, ";
        $sql .= ($stock_quantity !== NULL) ? "$stock_quantity, " : "NULL, ";
        $sql .= ($image_url !== NULL) ? "'$image_url')" : "NULL)";
        
        // Execute query
        if ($this->db_query($sql)) {
            return mysqli_insert_id($this->db_conn());
        }
        return false;
    }
    
    /**
     * Get all active services with optional filtering
     * 
     * @param string $service_type Optional filter by service type
     * @param string $category Optional filter by category
     * @param string $search_term Optional search term for name/description
     * @param float $min_price Optional minimum price filter
     * @param float $max_price Optional maximum price filter
     * @return array|false Array of services or false on failure
     */
    public function getAllServices($service_type = null, $category = null, $search_term = null, $min_price = null, $max_price = null) {
        // Base query
        $sql = "SELECT s.*, u.first_name, u.last_name 
                FROM services s 
                LEFT JOIN users u ON s.provider_id = u.user_id 
                WHERE s.is_active = 1";
        
        // Apply filters
        if ($service_type) {
            $service_type = mysqli_real_escape_string($this->db_conn(), $service_type);
            $sql .= " AND s.service_type = '$service_type'";
        }
        
        if ($category) {
            $category = mysqli_real_escape_string($this->db_conn(), $category);
            $sql .= " AND s.category = '$category'";
        }
        
        if ($search_term) {
            $search_term = mysqli_real_escape_string($this->db_conn(), $search_term);
            $sql .= " AND (s.service_name LIKE '%$search_term%' OR s.description LIKE '%$search_term%')";
        }
        
        if ($min_price !== null) {
            $min_price = floatval($min_price);
            $sql .= " AND s.price >= $min_price";
        }
        
        if ($max_price !== null) {
            $max_price = floatval($max_price);
            $sql .= " AND s.price <= $max_price";
        }
        
        $sql .= " ORDER BY s.date_created DESC";
        
        // Execute query
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Get a single service by ID
     * 
     * @param int $service_id Service ID
     * @return array|false Service details or false if not found
     */
    public function getServiceById($service_id) {
        $service_id = intval($service_id);
        $sql = "SELECT s.*, u.first_name, u.last_name, u.email 
                FROM services s 
                LEFT JOIN users u ON s.provider_id = u.user_id 
                WHERE s.service_id = $service_id";
        
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Update service details
     * 
     * @param int $service_id Service ID to update
     * @param array $update_data Array of fields to update
     * @return bool True on success, false on failure
     */
    public function updateService($service_id, $update_data) {
        $service_id = intval($service_id);
        $updates = [];
        
        // Build update string
        foreach ($update_data as $key => $value) {
            if (in_array($key, ['service_name', 'description', 'category', 'location', 'image_url'])) {
                $value = mysqli_real_escape_string($this->db_conn(), $value);
                $updates[] = "$key = '$value'";
            } elseif (in_array($key, ['price', 'duration', 'provider_id', 'stock_quantity', 'is_active'])) {
                $updates[] = "$key = " . ($value !== null ? (is_numeric($value) ? $value : "'$value'") : 'NULL');
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE services SET " . implode(', ', $updates) . " WHERE service_id = $service_id";
        return $this->db_query($sql);
    }
    
    /**
     * Delete (deactivate) a service
     * 
     * @param int $service_id Service ID to delete
     * @return bool True on success, false on failure
     */
    public function deleteService($service_id) {
        $service_id = intval($service_id);
        // Soft delete by setting is_active to 0
        $sql = "UPDATE services SET is_active = 0 WHERE service_id = $service_id";
        return $this->db_query($sql);
    }
    
    /**
     * Get all unique categories for a service type
     * 
     * @param string $service_type Service type to get categories for
     * @return array|false Array of categories or false on failure
     */
    public function getCategories($service_type = null) {
        $sql = "SELECT DISTINCT category FROM services WHERE is_active = 1 AND category IS NOT NULL";
        
        if ($service_type) {
            $service_type = mysqli_real_escape_string($this->db_conn(), $service_type);
            $sql .= " AND service_type = '$service_type'";
        }
        
        $sql .= " ORDER BY category ASC";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Check if service has available stock (for events)
     * 
     * @param int $service_id Service ID
     * @param int $quantity Quantity requested
     * @return bool True if available, false otherwise
     */
    public function checkAvailability($service_id, $quantity = 1) {
        $service_id = intval($service_id);
        $quantity = intval($quantity);
        
        $sql = "SELECT stock_quantity FROM services WHERE service_id = $service_id AND is_active = 1";
        $result = $this->db_fetch_one($sql);
        
        if (!$result) {
            return false;
        }
        
        // If stock_quantity is NULL, it's unlimited (like mentorship sessions)
        if ($result['stock_quantity'] === null) {
            return true;
        }
        
        return $result['stock_quantity'] >= $quantity;
    }
    
    /**
     * Update stock quantity (for event tickets)
     * 
     * @param int $service_id Service ID
     * @param int $quantity_change Amount to decrease (negative) or increase (positive)
     * @return bool True on success, false on failure
     */
    public function updateStock($service_id, $quantity_change) {
        $service_id = intval($service_id);
        $quantity_change = intval($quantity_change);
        
        $sql = "UPDATE services 
                SET stock_quantity = stock_quantity + $quantity_change 
                WHERE service_id = $service_id AND stock_quantity IS NOT NULL";
        
        return $this->db_query($sql);
    }
    
    /**
     * Get services by provider (alumni who created them)
     * 
     * @param int $provider_id User ID of the provider
     * @return array|false Array of services or false on failure
     */
    public function getServicesByProvider($provider_id) {
        $provider_id = intval($provider_id);
        $sql = "SELECT * FROM services 
                WHERE provider_id = $provider_id 
                ORDER BY date_created DESC";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Get service statistics
     * 
     * @return array Statistics about services
     */
    public function getServiceStats() {
        $sql = "SELECT 
                    service_type,
                    COUNT(*) as total_services,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_services,
                    AVG(price) as average_price,
                    MIN(price) as min_price,
                    MAX(price) as max_price
                FROM services
                GROUP BY service_type";
        
        return $this->db_fetch_all($sql);
    }
}
?>
