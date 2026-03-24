<?php
/**
 * Job Detail API
 * HandsOn - Location-based skilled worker platform
 */

require_once '../../config/database.php';
require_once '../../config/constants.php';

// Enable CORS and handle preflight
add_cors_headers();
handle_cors_preflight();

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get job ID
$job_id = $_GET['id'] ?? null;

if (!$job_id) {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Job ID is required']);
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

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

try {
    $db = getDB();
    
    // Get job details
    $stmt = $db->prepare("SELECT 
        j.id,
        j.category,
        j.title,
        j.description,
        j.address,
        j.latitude,
        j.longitude,
        j.status,
        j.scheduled_date,
        j.scheduled_time,
        j.created_at,
        j.updated_at,
        cu.id as customer_id,
        cu.name as customer_name,
        cu.phone as customer_phone,
        cu.profile_photo as customer_photo,
        wo.id as worker_id,
        wo.name as worker_name,
        wo.phone as worker_phone,
        wo.profile_photo as worker_photo,
        wp.category as worker_category,
        wp.rating_avg as worker_rating,
        wp.photo as worker_profile_photo
    FROM job_requests j
    INNER JOIN users cu ON j.customer_id = cu.id
    LEFT JOIN users wo ON j.worker_id = wo.id
    LEFT JOIN worker_profiles wp ON wo.id = wp.user_id
    WHERE j.id = ?");
    
    $stmt->execute([$job_id]);
    $job = $stmt->fetch();
    
    if (!$job) {
        http_response_code(API_NOT_FOUND);
        echo json_encode(['error' => 'Job not found']);
        exit;
    }
    
    // Check authorization
    if ($user_role === 'customer' && $job['customer_id'] != $user_id) {
        http_response_code(API_FORBIDDEN);
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
    
    if ($user_role === 'worker' && $job['worker_id'] != $user_id) {
        // Workers can only view jobs they are assigned to or available jobs
        http_response_code(API_FORBIDDEN);
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
    
    // Get payment info if exists
    $stmt = $db->prepare("SELECT * FROM payments WHERE job_id = ?");
    $stmt->execute([$job_id]);
    $payment = $stmt->fetch();
    
    // Get reviews if job is completed
    $reviews = null;
    if ($job['status'] === 'completed') {
        $stmt = $db->prepare("SELECT * FROM reviews WHERE job_id = ?");
        $stmt->execute([$job_id]);
        $reviews = $stmt->fetch();
    }
    
    http_response_code(API_SUCCESS);
    echo json_encode([
        'success' => true,
        'job' => $job,
        'payment' => $payment,
        'reviews' => $reviews
    ]);
    
} catch (PDOException $e) {
    error_log("Job Detail Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to fetch job details']);
}
