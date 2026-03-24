<?php
/**
 * Admin Worker Management API
 * HandsOn - Location-based skilled worker platform
 */

header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/constants.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check authentication
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(API_UNAUTHORIZED);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = getDB();
    
    // GET - List workers (pending verification)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $status = $_GET['status'] ?? 'pending';
        
        if ($status === 'pending') {
            $stmt = $db->query("SELECT 
                u.id as user_id,
                u.name,
                u.email,
                u.phone,
                u.created_at,
                wp.id as profile_id,
                wp.category,
                wp.experience,
                wp.bio,
                wp.hourly_rate,
                wp.rating_avg,
                wp.review_count
            FROM users u
            INNER JOIN worker_profiles wp ON u.id = wp.user_id
            WHERE wp.is_verified = 0
            ORDER BY u.created_at DESC");
        } else {
            $stmt = $db->query("SELECT 
                u.id as user_id,
                u.name,
                u.email,
                u.phone,
                u.created_at,
                wp.id as profile_id,
                wp.category,
                wp.experience,
                wp.bio,
                wp.hourly_rate,
                wp.is_verified,
                wp.rating_avg,
                wp.review_count
            FROM users u
            INNER JOIN worker_profiles wp ON u.id = wp.user_id
            ORDER BY u.created_at DESC");
        }
        
        $workers = $stmt->fetchAll();
        
        http_response_code(API_SUCCESS);
        echo json_encode([
            'workers' => $workers,
            'count' => count($workers)
        ]);
        exit;
    }
    
    // PUT - Verify/Suspend worker
    if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_POST;
        }
        
        if (empty($input['worker_id']) || empty($input['action'])) {
            http_response_code(API_BAD_REQUEST);
            echo json_encode(['error' => 'Worker ID and action are required']);
            exit;
        }
        
        $worker_id = $input['worker_id'];
        $action = $input['action'];
        
        if ($action === 'verify') {
            $stmt = $db->prepare("UPDATE worker_profiles SET is_verified = 1 WHERE user_id = ?");
            $stmt->execute([$worker_id]);
            
            // Log admin action
            $stmt = $db->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id) VALUES (?, 'verify_worker', 'worker', ?)");
            $stmt->execute([$_SESSION['user_id'], $worker_id]);
            
            $message = 'Worker verified successfully';
            
        } elseif ($action === 'suspend') {
            $stmt = $db->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
            $stmt->execute([$worker_id]);
            
            // Log admin action
            $stmt = $db->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id) VALUES (?, 'suspend_worker', 'worker', ?)");
            $stmt->execute([$_SESSION['user_id'], $worker_id]);
            
            $message = 'Worker suspended successfully';
            
        } elseif ($action === 'activate') {
            $stmt = $db->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
            $stmt->execute([$worker_id]);
            
            // Log admin action
            $stmt = $db->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id) VALUES (?, 'activate_worker', 'worker', ?)");
            $stmt->execute([$_SESSION['user_id'], $worker_id]);
            
            $message = 'Worker activated successfully';
            
        } else {
            http_response_code(API_BAD_REQUEST);
            echo json_encode(['error' => 'Invalid action']);
            exit;
        }
        
        http_response_code(API_SUCCESS);
        echo json_encode(['message' => $message]);
        exit;
    }
    
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid request method']);
    
} catch (PDOException $e) {
    error_log("Admin Worker Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to process request']);
}
