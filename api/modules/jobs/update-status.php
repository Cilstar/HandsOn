<?php
/**
 * Update Job Status API
 * HandsOn - Location-based skilled worker platform
 */

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../../config/notifications.php';

// Enable CORS and handle preflight
add_cors_headers();
handle_cors_preflight();

// Only allow PUT/PATCH requests
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
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
if (empty($input['job_id']) || empty($input['status'])) {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Job ID and status are required']);
    exit;
}

// Valid status transitions
$valid_statuses = ['pending', 'accepted', 'in_progress', 'completed', 'cancelled', 'rejected'];
if (!in_array($input['status'], $valid_statuses)) {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

try {
    $db = getDB();
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_role'];
    $user_name = $_SESSION['user_name'];
    $job_id = $input['job_id'];
    $new_status = $input['status'];
    
    // Get job details
    $stmt = $db->prepare("SELECT * FROM job_requests WHERE id = ?");
    $stmt->execute([$job_id]);
    $job = $stmt->fetch();
    
    if (!$job) {
        http_response_code(API_NOT_FOUND);
        echo json_encode(['error' => 'Job not found']);
        exit;
    }
    
    // Check authorization
    $authorized = false;
    
    if ($user_role === 'admin') {
        $authorized = true;
    } elseif ($user_role === 'worker' && $job['worker_id'] == $user_id) {
        // Worker can only accept, reject, or update to in_progress/completed
        $worker_allowed = ['accepted', 'rejected', 'in_progress', 'completed'];
        $authorized = in_array($new_status, $worker_allowed);
    } elseif ($user_role === 'customer' && $job['customer_id'] == $user_id) {
        // Customer can only cancel
        $authorized = ($new_status === 'cancelled');
    }
    
    if (!$authorized) {
        http_response_code(API_FORBIDDEN);
        echo json_encode(['error' => 'Not authorized to update this job']);
        exit;
    }
    
    // Update job status
    $stmt = $db->prepare("UPDATE job_requests SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_status, $job_id]);
    
    // If accepted, update worker availability to busy
    if ($new_status === 'accepted' && $user_role === 'worker') {
        $stmt = $db->prepare("UPDATE worker_profiles SET availability = 'busy' WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }
    
    // If completed or cancelled, update worker availability to available
    if (in_array($new_status, ['completed', 'cancelled', 'rejected']) && $job['worker_id']) {
        $stmt = $db->prepare("UPDATE worker_profiles SET availability = 'available' WHERE user_id = ?");
        $stmt->execute([$job['worker_id']]);
    }
    
    // Create notification for customer
    $customer_id = $job['customer_id'];
    $status_messages = [
        'accepted' => "Your job has been accepted",
        'in_progress' => 'Work has started on your job',
        'completed' => 'Your job has been marked as completed',
        'cancelled' => 'Your job has been cancelled',
        'rejected' => 'The worker declined your job request'
    ];
    
    if (isset($status_messages[$new_status])) {
        create_notification(
            $customer_id,
            'job_status',
            'Job Status Update',
            $status_messages[$new_status],
            ['job_id' => $job_id, 'status' => $new_status]
        );
    }
    
    http_response_code(API_SUCCESS);
    echo json_encode([
        'success' => true,
        'message' => 'Job status updated successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Update Job Status Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to update job status']);
}
