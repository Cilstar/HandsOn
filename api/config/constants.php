<?php
/**
 * Application Constants
 * HandsOn - Location-based skilled worker platform
 */

// Application info
define('APP_NAME', 'HandsOn');
define('APP_TAGLINE', 'Connect with Verified Skilled Workers');
define('APP_URL', 'http://localhost/hands-on');

// API response codes
define('API_SUCCESS', 200);
define('API_CREATED', 201);
define('API_BAD_REQUEST', 400);
define('API_UNAUTHORIZED', 401);
define('API_FORBIDDEN', 403);
define('API_NOT_FOUND', 404);
define('API_SERVER_ERROR', 500);

// Worker categories
define('WORKER_CATEGORIES', [
    'plumber' => 'Plumber',
    'electrician' => 'Electrician',
    'carpenter' => 'Carpenter',
    'cleaner' => 'Cleaner',
    'painter' => 'Painter',
    'technician' => 'Technician'
]);

// Experience levels
define('EXPERIENCE_LEVELS', [
    '1-2_years' => '1-2 years',
    '3-5_years' => '3-5 years',
    '5_plus_years' => '5+ years'
]);

// Availability statuses
define('AVAILABILITY_STATUSES', [
    'available' => 'Available',
    'busy' => 'Busy',
    'offline' => 'Offline'
]);

// Job statuses
define('JOB_STATUSES', [
    'pending' => 'Pending',
    'accepted' => 'Accepted',
    'in_progress' => 'In Progress',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
    'rejected' => 'Rejected'
]);

// Payment statuses
define('PAYMENT_STATUSES', [
    'pending' => 'Pending',
    'initiated' => 'Initiated',
    'completed' => 'Completed',
    'failed' => 'Failed',
    'refunded' => 'Refunded'
]);

// User roles
define('USER_ROLES', [
    'customer' => 'Customer',
    'worker' => 'Worker',
    'admin' => 'Admin'
]);

// Commission rate (percentage)
define('COMMISSION_RATE', 10);

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../../uploads/profiles/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Session settings
define('SESSION_NAME', 'HANDSON_SESSION');
define('SESSION_LIFETIME', 86400); // 24 hours

// Rating constants
define('MIN_RATING', 1);
define('MAX_RATING', 5);

/**
 * Add CORS headers to response
 */
function add_cors_headers() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token, X-Requested-With');
    header('Access-Control-Max-Age: 86400');
    header('Content-Type: application/json');
}

/**
 * Handle CORS preflight request
 */
function handle_cors_preflight() {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

/**
 * Send JSON response
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

/**
 * Send error response
 */
function send_error($message, $status_code = 400) {
    json_response(['error' => $message], $status_code);
}

/**
 * Validate required fields
 */
function validate_required($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            errors[] = ucfirst($field) . ' is required';
        }
    }
    return $errors;
}
