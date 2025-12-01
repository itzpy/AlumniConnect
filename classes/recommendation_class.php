<?php
/**
 * AI-Powered Recommendation Engine
 * Provides personalized service recommendations based on:
 * - User's purchase history
 * - User's browsing/cart behavior
 * - Similar user preferences (collaborative filtering)
 * - Service category affinity
 * - Price range matching
 * 
 * @category E-Commerce AI
 * @package  AlumniConnect
 */

require_once(dirname(__FILE__).'/../settings/db_class.php');

class Recommendation extends db_connection {
    
    /**
     * Get personalized recommendations for a user
     * 
     * @param int $user_id User ID
     * @param int $limit Number of recommendations to return
     * @param array $exclude_ids Service IDs to exclude (e.g., already in cart)
     * @return array Recommended services with scores
     */
    public function getRecommendations($user_id, $limit = 6, $exclude_ids = []) {
        $user_id = intval($user_id);
        
        // Get user's purchase history for profiling
        $user_profile = $this->buildUserProfile($user_id);
        
        // If user has no history, return popular/trending items
        if (empty($user_profile['purchased_types']) && empty($user_profile['purchased_categories'])) {
            return $this->getTrendingServices($limit, $exclude_ids);
        }
        
        // Get recommendations based on user profile
        $recommendations = $this->calculateRecommendations($user_id, $user_profile, $limit, $exclude_ids);
        
        return $recommendations;
    }
    
    /**
     * Build a user profile based on their behavior
     * 
     * @param int $user_id User ID
     * @return array User profile with preferences
     */
    private function buildUserProfile($user_id) {
        $profile = [
            'purchased_types' => [],
            'purchased_categories' => [],
            'avg_price' => 0,
            'price_range' => ['min' => 0, 'max' => 1000],
            'cart_types' => [],
            'total_orders' => 0
        ];
        
        // Get purchase history
        $sql = "SELECT s.service_type, s.price, oi.quantity
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.order_id
                JOIN services s ON oi.service_id = s.service_id
                WHERE o.user_id = $user_id AND o.payment_status = 'paid'
                ORDER BY o.order_date DESC
                LIMIT 50";
        
        $purchases = $this->db_fetch_all($sql);
        
        if ($purchases && count($purchases) > 0) {
            $total_spent = 0;
            $prices = [];
            
            foreach ($purchases as $item) {
                // Track service types
                $type = $item['service_type'];
                if (!isset($profile['purchased_types'][$type])) {
                    $profile['purchased_types'][$type] = 0;
                }
                $profile['purchased_types'][$type] += $item['quantity'];
                
                // Track price preferences
                $prices[] = floatval($item['price']);
                $total_spent += floatval($item['price']) * $item['quantity'];
            }
            
            $profile['total_orders'] = count($purchases);
            $profile['avg_price'] = $total_spent / array_sum(array_column($purchases, 'quantity'));
            $profile['price_range'] = [
                'min' => min($prices) * 0.5,
                'max' => max($prices) * 1.5
            ];
        }
        
        // Get current cart items to understand intent
        $cart_sql = "SELECT s.service_type 
                     FROM cart c 
                     JOIN services s ON c.service_id = s.service_id 
                     WHERE c.user_id = $user_id";
        $cart_items = $this->db_fetch_all($cart_sql);
        
        if ($cart_items) {
            foreach ($cart_items as $item) {
                $profile['cart_types'][] = $item['service_type'];
            }
        }
        
        return $profile;
    }
    
    /**
     * Calculate recommendation scores for services
     * 
     * @param int $user_id User ID
     * @param array $profile User profile
     * @param int $limit Number of results
     * @param array $exclude_ids IDs to exclude
     * @return array Scored and sorted recommendations
     */
    private function calculateRecommendations($user_id, $profile, $limit, $exclude_ids) {
        $exclude_clause = '';
        if (!empty($exclude_ids)) {
            $exclude_ids = array_map('intval', $exclude_ids);
            $exclude_clause = "AND s.service_id NOT IN (" . implode(',', $exclude_ids) . ")";
        }
        
        // Get services user hasn't purchased
        $purchased_sql = "SELECT DISTINCT oi.service_id 
                          FROM order_items oi 
                          JOIN orders o ON oi.order_id = o.order_id 
                          WHERE o.user_id = $user_id";
        $purchased = $this->db_fetch_all($purchased_sql);
        $purchased_ids = $purchased ? array_column($purchased, 'service_id') : [];
        
        $exclude_purchased = '';
        if (!empty($purchased_ids)) {
            $exclude_purchased = "AND s.service_id NOT IN (" . implode(',', $purchased_ids) . ")";
        }
        
        // Get all active services
        $sql = "SELECT s.*, 
                (SELECT COUNT(*) FROM order_items oi2 
                 JOIN orders o2 ON oi2.order_id = o2.order_id 
                 WHERE oi2.service_id = s.service_id AND o2.payment_status = 'paid') as purchase_count
                FROM services s 
                WHERE s.is_active = 1 
                $exclude_clause 
                $exclude_purchased
                ORDER BY purchase_count DESC";
        
        $services = $this->db_fetch_all($sql);
        
        if (!$services) {
            return [];
        }
        
        // Score each service
        $scored_services = [];
        foreach ($services as $service) {
            $score = $this->calculateServiceScore($service, $profile);
            $service['recommendation_score'] = $score;
            $service['recommendation_reason'] = $this->getRecommendationReason($service, $profile);
            $scored_services[] = $service;
        }
        
        // Sort by score descending
        usort($scored_services, function($a, $b) {
            return $b['recommendation_score'] <=> $a['recommendation_score'];
        });
        
        // Return top results
        return array_slice($scored_services, 0, $limit);
    }
    
    /**
     * Calculate a recommendation score for a service
     * 
     * @param array $service Service data
     * @param array $profile User profile
     * @return float Score between 0-100
     */
    private function calculateServiceScore($service, $profile) {
        $score = 0;
        $weights = [
            'type_match' => 35,      // Service type matches purchase history
            'price_match' => 20,      // Price is in user's comfort range
            'popularity' => 25,       // Overall popularity
            'cart_affinity' => 15,    // Related to items in cart
            'recency_boost' => 5      // Newer services get slight boost
        ];
        
        // Type match scoring
        $service_type = $service['service_type'];
        if (isset($profile['purchased_types'][$service_type])) {
            $type_frequency = $profile['purchased_types'][$service_type];
            $total_purchases = array_sum($profile['purchased_types']);
            $type_score = ($type_frequency / max($total_purchases, 1)) * $weights['type_match'];
            $score += $type_score;
        }
        
        // Price match scoring
        $price = floatval($service['price']);
        if ($profile['avg_price'] > 0) {
            $price_diff = abs($price - $profile['avg_price']);
            $price_tolerance = $profile['avg_price'] * 0.5;
            if ($price_diff <= $price_tolerance) {
                $price_score = (1 - ($price_diff / max($price_tolerance, 1))) * $weights['price_match'];
                $score += $price_score;
            }
        } else {
            // New user - neutral price score
            $score += $weights['price_match'] * 0.5;
        }
        
        // Popularity scoring (normalized)
        $purchase_count = intval($service['purchase_count']);
        $popularity_score = min($purchase_count / 10, 1) * $weights['popularity'];
        $score += $popularity_score;
        
        // Cart affinity scoring
        if (in_array($service_type, $profile['cart_types'])) {
            $score += $weights['cart_affinity'];
        }
        
        // Recency boost for newer services
        if (isset($service['date_created'])) {
            $days_old = (time() - strtotime($service['date_created'])) / 86400;
            if ($days_old < 30) {
                $score += $weights['recency_boost'] * (1 - $days_old / 30);
            }
        }
        
        return round($score, 2);
    }
    
    /**
     * Generate a human-readable reason for the recommendation
     * 
     * @param array $service Service data
     * @param array $profile User profile
     * @return string Reason text
     */
    private function getRecommendationReason($service, $profile) {
        $type = $service['service_type'];
        $type_labels = [
            'mentorship' => 'mentorship sessions',
            'event' => 'events',
            'job_posting' => 'job opportunities',
            'premium' => 'premium services'
        ];
        
        $type_label = $type_labels[$type] ?? 'services';
        
        // Check why this was recommended
        if (isset($profile['purchased_types'][$type]) && $profile['purchased_types'][$type] > 0) {
            return "Based on your interest in $type_label";
        }
        
        if (in_array($type, $profile['cart_types'])) {
            return "Similar to items in your cart";
        }
        
        if (intval($service['purchase_count']) > 5) {
            return "Popular among alumni";
        }
        
        if (isset($service['date_created'])) {
            $days_old = (time() - strtotime($service['date_created'])) / 86400;
            if ($days_old < 14) {
                return "New on Alumni Connect";
            }
        }
        
        return "Recommended for you";
    }
    
    /**
     * Get trending/popular services for new users
     * 
     * @param int $limit Number of results
     * @param array $exclude_ids IDs to exclude
     * @return array Popular services
     */
    public function getTrendingServices($limit = 6, $exclude_ids = []) {
        $exclude_clause = '';
        if (!empty($exclude_ids)) {
            $exclude_ids = array_map('intval', $exclude_ids);
            $exclude_clause = "AND s.service_id NOT IN (" . implode(',', $exclude_ids) . ")";
        }
        
        $sql = "SELECT s.*, 
                (SELECT COUNT(*) FROM order_items oi 
                 JOIN orders o ON oi.order_id = o.order_id 
                 WHERE oi.service_id = s.service_id AND o.payment_status = 'paid') as purchase_count,
                (SELECT COUNT(*) FROM cart c WHERE c.service_id = s.service_id) as cart_count
                FROM services s 
                WHERE s.is_active = 1 $exclude_clause
                ORDER BY (purchase_count * 2 + cart_count) DESC, s.date_created DESC
                LIMIT $limit";
        
        $services = $this->db_fetch_all($sql);
        
        if ($services) {
            foreach ($services as &$service) {
                $service['recommendation_score'] = 50 + min(intval($service['purchase_count']) * 5, 50);
                $service['recommendation_reason'] = intval($service['purchase_count']) > 3 
                    ? "Trending on Alumni Connect" 
                    : "Popular choice";
            }
        }
        
        return $services ?: [];
    }
    
    /**
     * Get similar services to a specific service
     * 
     * @param int $service_id Service ID to find similar items for
     * @param int $limit Number of results
     * @return array Similar services
     */
    public function getSimilarServices($service_id, $limit = 4) {
        $service_id = intval($service_id);
        
        // Get the source service details
        $sql = "SELECT * FROM services WHERE service_id = $service_id";
        $source = $this->db_fetch_one($sql);
        
        if (!$source) {
            return [];
        }
        
        $type = $source['service_type'];
        $price = floatval($source['price']);
        $price_min = $price * 0.5;
        $price_max = $price * 1.5;
        
        // Find similar services
        $similar_sql = "SELECT s.*, 
                        ABS(s.price - $price) as price_diff,
                        (SELECT COUNT(*) FROM order_items oi 
                         JOIN orders o ON oi.order_id = o.order_id 
                         WHERE oi.service_id = s.service_id AND o.payment_status = 'paid') as purchase_count
                        FROM services s 
                        WHERE s.service_id != $service_id 
                        AND s.is_active = 1
                        AND (s.service_type = '$type' OR s.price BETWEEN $price_min AND $price_max)
                        ORDER BY 
                            CASE WHEN s.service_type = '$type' THEN 0 ELSE 1 END,
                            price_diff ASC,
                            purchase_count DESC
                        LIMIT $limit";
        
        $similar = $this->db_fetch_all($similar_sql);
        
        if ($similar) {
            foreach ($similar as &$service) {
                $service['recommendation_reason'] = $service['service_type'] === $type 
                    ? "Similar " . str_replace('_', ' ', $type)
                    : "You might also like";
            }
        }
        
        return $similar ?: [];
    }
    
    /**
     * Get "frequently bought together" recommendations
     * 
     * @param array $cart_service_ids Current cart service IDs
     * @param int $limit Number of results
     * @return array Services frequently bought with cart items
     */
    public function getFrequentlyBoughtTogether($cart_service_ids, $limit = 3) {
        if (empty($cart_service_ids)) {
            return [];
        }
        
        $cart_ids = array_map('intval', $cart_service_ids);
        $cart_ids_str = implode(',', $cart_ids);
        
        // Find services that appear in orders containing cart items
        $sql = "SELECT s.*, COUNT(DISTINCT o.order_id) as co_purchase_count
                FROM services s
                JOIN order_items oi ON s.service_id = oi.service_id
                JOIN orders o ON oi.order_id = o.order_id
                WHERE o.payment_status = 'paid'
                AND s.service_id NOT IN ($cart_ids_str)
                AND s.is_active = 1
                AND o.order_id IN (
                    SELECT DISTINCT o2.order_id 
                    FROM orders o2 
                    JOIN order_items oi2 ON o2.order_id = oi2.order_id 
                    WHERE oi2.service_id IN ($cart_ids_str)
                    AND o2.payment_status = 'paid'
                )
                GROUP BY s.service_id
                HAVING co_purchase_count >= 1
                ORDER BY co_purchase_count DESC
                LIMIT $limit";
        
        $services = $this->db_fetch_all($sql);
        
        if ($services) {
            foreach ($services as &$service) {
                $service['recommendation_reason'] = "Frequently bought together";
            }
        }
        
        return $services ?: [];
    }
}
?>
