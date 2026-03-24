<?php
/**
 * Create Job Request API
 * HandsOn - Location-based skilled worker platform
 */

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../../config/notifications.php';

// Enable CORS and handle preflight
add_cors_headers();
handle_cors_preflight();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(API_UNAUTHORIZED);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

// Validate required fields
$required_fields = ['category', 'title', 'description', 'address'];
$errors = [];

foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        $errors[] = ucfirst($field) . ' is required';
    }
}

// Validate category
$valid_categories = ['plumber', 'electrician', 'carpenter', 'cleaner', 'painter', 'technician'];
if (isset($input['category']) && !in_array($input['category'], $valid_categories)) {
    $errors[] = 'Invalid category';
}

if (!empty($errors)) {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['errors' => $errors]);
    exit;
}

try {
    $db = getDB();
    
    $customer_id = $_SESSION['user_id'];
    $customer_name = $_SESSION['user_name'];
    
    // Insert job request
    $stmt = $db->prepare("INSERT INTO job_requests 
        (customer_id, worker_id, category, title, description, address, latitude, longitude, scheduled_date, scheduled_time, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    
    $stmt->execute([
        $customer_id,
        $input['worker_id'] ?? null,
        sanitize_input($input['category']),
        sanitize_input($input['title']),
        sanitize_input($input['description']),
        sanitize_input($input['address']),
        $input['latitude'] ?? null,
        $input['longitude'] ?? null,
        $input['scheduled_date'] ?? null,
        $input['scheduled_time'] ?? null
    ]);
    
    $job_id = $db->lastInsertId();
    
    // If worker_id is specified, notify the worker
    if (!empty($input['worker_id'])) {
        $worker_id = $input['worker_id'];
        
        // Get worker details
        $stmt = $db->prepare("SELECT name FROM users WHERE id = ?");
        $stmt->execute([$worker_id]);
        $worker = $stmt->fetch();
        
        // Create notification for worker
        create_notification(
            $worker_id,
            'new_job',
            'New Job Request',
            "You have a new {$input['category']} job request from {$customer_name}",
            ['job_id' => $job_id, 'category' => $input['category']]
        );
    }
    
    http_response_code(API_CREATED);
    echo json_encode([
        'success' => true,
        'message' => 'Job request created successfully',
        'job_id' => $job_id
    ]);
    
} catch (PDOException $e) {
    error_log("Create Job Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to create job request']);
}

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
