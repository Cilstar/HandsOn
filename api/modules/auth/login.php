<?php
/**
 * User Login API - HandsOn
 */

// Turn off error display for API
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';

// Enable CORS and handle preflight
add_cors_headers();
handle_cors_preflight();

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get input JSON or POST
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

// Validate input
if (empty($input['email']) || empty($input['password'])) {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

// Try login
try {
    $db = getDB();
    
    $login_field = filter_var($input['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    
    $stmt = $db->prepare("SELECT * FROM users WHERE {$login_field} = ? AND is_active = 1");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($input['password'], $user['password'])) {
        http_response_code(API_UNAUTHORIZED);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }
    
    // Session + token
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    $token = bin2hex(random_bytes(32));
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['token'] = $token;
    $_SESSION['login_time'] = time();
    
    // Optional worker profile
    $worker_profile = null;
    if ($user['role'] === 'worker') {
        $stmt = $db->prepare("SELECT * FROM worker_profiles WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $worker_profile = $stmt->fetch();
    }
    
    // Generate CSRF token
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
    
    http_response_code(API_SUCCESS);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'is_verified' => (bool)$user['is_verified']
        ],
        'worker_profile' => $worker_profile,
        'token' => $token,
        'csrf_token' => $csrf_token
    ]);

} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Login failed. Please try again.']);
}