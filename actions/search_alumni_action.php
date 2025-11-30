<?php
/**
 * Search Alumni Action
 * Returns alumni matching search filters
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once '../classes/alumni_class.php';

$alumni = new AlumniProfile();

// Get filter parameters
$filters = [];

if (!empty($_GET['name'])) {
    $filters['name'] = trim($_GET['name']);
}

if (!empty($_GET['major'])) {
    $filters['major'] = trim($_GET['major']);
}

if (!empty($_GET['year'])) {
    $filters['graduation_year'] = intval($_GET['year']);
}

if (!empty($_GET['industry'])) {
    $filters['industry'] = trim($_GET['industry']);
}

if (!empty($_GET['location'])) {
    $filters['location'] = trim($_GET['location']);
}

if (!empty($_GET['company'])) {
    $filters['company'] = trim($_GET['company']);
}

if (isset($_GET['mentorship']) && $_GET['mentorship'] == '1') {
    $filters['available_for_mentorship'] = 1;
}

try {
    $results = $alumni->searchAlumni($filters);
    
    echo json_encode([
        'success' => true,
        'count' => count($results),
        'alumni' => $results
    ]);
} catch (Exception $e) {
    error_log("Alumni search error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Search failed'
    ]);
}
?>
