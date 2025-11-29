<?php
/**
 * Get AI Recommendations Action
 * Returns personalized service recommendations for the user
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in', 'recommendations' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get parameters
$input = json_decode(file_get_contents('php://input'), true);
$type = isset($input['type']) ? $input['type'] : 'personalized';
$limit = isset($input['limit']) ? intval($input['limit']) : 6;
$exclude_ids = isset($input['exclude_ids']) ? $input['exclude_ids'] : [];
$service_id = isset($input['service_id']) ? intval($input['service_id']) : 0;
$cart_ids = isset($input['cart_ids']) ? $input['cart_ids'] : [];

require_once '../classes/recommendation_class.php';
$recommender = new Recommendation();

$recommendations = [];

switch ($type) {
    case 'similar':
        // Get similar services to a specific service
        if ($service_id > 0) {
            $recommendations = $recommender->getSimilarServices($service_id, $limit);
        }
        break;
        
    case 'frequently_bought':
        // Get frequently bought together
        if (!empty($cart_ids)) {
            $recommendations = $recommender->getFrequentlyBoughtTogether($cart_ids, $limit);
        }
        break;
        
    case 'trending':
        // Get trending/popular services
        $recommendations = $recommender->getTrendingServices($limit, $exclude_ids);
        break;
        
    case 'personalized':
    default:
        // Get personalized recommendations
        $recommendations = $recommender->getRecommendations($user_id, $limit, $exclude_ids);
        break;
}

// Format image URLs
foreach ($recommendations as &$rec) {
    if (!empty($rec['image'])) {
        // Check if it's already a full URL
        if (strpos($rec['image'], 'http') !== 0) {
            $rec['image_url'] = '../uploads/' . $rec['image'];
        } else {
            $rec['image_url'] = $rec['image'];
        }
    } else {
        $rec['image_url'] = null;
    }
    
    // Format price
    $rec['formatted_price'] = 'GHS ' . number_format(floatval($rec['price']), 2);
    
    // Format service type for display
    $type_labels = [
        'mentorship' => 'Mentorship',
        'event' => 'Event',
        'job_posting' => 'Job Posting',
        'premium' => 'Premium Service'
    ];
    $rec['type_label'] = $type_labels[$rec['service_type']] ?? ucfirst($rec['service_type']);
}

echo json_encode([
    'success' => true,
    'count' => count($recommendations),
    'recommendations' => $recommendations
]);
?>
