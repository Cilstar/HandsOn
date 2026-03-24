<?php
/**
 * Get Current User API
 * HandsOn - Location-based skilled worker platform
 */

// Turn off error display for API
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/constants.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(API_UNAUTHORIZED);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

try {
    $db = getDB();
    
    // Get user details
    $stmt = $db->prepare("SELECT id, name, email, phone, role, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(API_NOT_FOUND);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    
    // Get worker profile if worker
    $worker_profile = null;
    if ($user['role'] === 'worker') {
        $stmt = $db->prepare("SELECT * FROM worker_profiles WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $worker_profile = $stmt->fetch();
    }
    
    http_response_code(API_SUCCESS);
    echo json_encode([
        'user' => $user,
        'worker_profile' => $worker_profile,
        'token' => $_SESSION['token'] ?? null
    ]);
    
} catch (PDOException $e) {
    error_log("User Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to get user data']);
}
