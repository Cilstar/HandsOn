<?php
/**
 * Create Review API
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
if (empty($input['job_id']) || empty($input['rating'])) {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Job ID and rating are required']);
    exit;
}

// Validate rating
if ($input['rating'] < MIN_RATING || $input['rating'] > MAX_RATING) {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Rating must be between 1 and 5']);
    exit;
}

try {
    $db = getDB();
    $customer_id = $_SESSION['user_id'];
    $customer_name = $_SESSION['user_name'];
    
    // Get job details
    $stmt = $db->prepare("SELECT * FROM job_requests WHERE id = ? AND customer_id = ? AND status = 'completed'");
    $stmt->execute([$input['job_id'], $customer_id]);
    $job = $stmt->fetch();
    
    if (!$job) {
        http_response_code(API_NOT_FOUND);
        echo json_encode(['error' => 'Job not found or not completed']);
        exit;
    }
    
    // Check if review already exists
    $stmt = $db->prepare("SELECT id FROM reviews WHERE job_id = ? AND customer_id = ?");
    $stmt->execute([$input['job_id'], $customer_id]);
    if ($stmt->fetch()) {
        http_response_code(API_BAD_REQUEST);
        echo json_encode(['error' => 'You have already reviewed this job']);
        exit;
    }
    
    // Create review
    $stmt = $db->prepare("INSERT INTO reviews 
        (job_id, customer_id, worker_id, rating, review_text, admin_reviewed) 
        VALUES (?, ?, ?, ?, ?, ?)");
    
    $admin_reviewed = ($input['rating'] < 3) ? 1 : 0;
    
    $stmt->execute([
        $input['job_id'],
        $customer_id,
        $job['worker_id'],
        $input['rating'],
        sanitize_input($input['review_text'] ?? ''),
        $admin_reviewed
    ]);
    
    $review_id = $db->lastInsertId();
    
    // Update worker rating
    $stmt = $db->prepare("UPDATE worker_profiles SET 
        rating_avg = (
            SELECT AVG(rating) FROM reviews WHERE worker_id = ?
        ),
        review_count = (
            SELECT COUNT(*) FROM reviews WHERE worker_id = ?
        )
        WHERE user_id = ?");
    
    $stmt->execute([$job['worker_id'], $job['worker_id'], $job['worker_id']]);
    
    // Notify worker of new review
    if ($job['worker_id']) {
        $rating_text = $input['rating'] == 5 ? '5 stars' : ($input['rating'] >= 4 ? '4 stars' : ($input['rating'] >= 3 ? '3 stars' : 'low rating'));
        create_notification(
            $job['worker_id'],
            'new_review',
            'New Review Received',
            "You received a {$rating_text} review from {$customer_name}",
            ['job_id' => $input['job_id'], 'rating' => $input['rating']]
        );
    }
    
    http_response_code(API_CREATED);
    echo json_encode([
        'success' => true,
        'message' => 'Review submitted successfully',
        'review_id' => $review_id
    ]);
    
} catch (PDOException $e) {
    error_log("Create Review Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to submit review']);
}

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
