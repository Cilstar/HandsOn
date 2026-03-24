<?php
/**
 * List Jobs API
 * HandsOn - Location-based skilled worker platform
 */

// Turn off error display for API
error_reporting(0);
ini_set('display_errors', 0);

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

// Check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(API_UNAUTHORIZED);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = getDB();
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_role'];
    
    // Get filter parameters
    $status = $_GET['status'] ?? null;
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = min(50, max(1, intval($_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;
    
    if ($user_role === 'customer') {
        // Get customer's jobs
        $sql = "SELECT 
            j.id,
            j.category,
            j.title,
            j.description,
            j.address,
            j.status,
            j.scheduled_date,
            j.scheduled_time,
            j.created_at,
            j.updated_at,
            u.name as worker_name,
            u.phone as worker_phone,
            wp.category as worker_category,
            wp.rating_avg as worker_rating
        FROM job_requests j
        LEFT JOIN users u ON j.worker_id = u.id
        LEFT JOIN worker_profiles wp ON j.worker_id = wp.user_id
        WHERE j.customer_id = ?";
        
        $params = [$user_id];
        
        if ($status) {
            $sql .= " AND j.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY j.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
    } elseif ($user_role === 'worker') {
        // Get worker's jobs
        $sql = "SELECT 
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
            u.name as customer_name,
            u.phone as customer_phone
        FROM job_requests j
        INNER JOIN users u ON j.customer_id = u.id
        WHERE j.worker_id = ?";
        
        $params = [$user_id];
        
        if ($status) {
            $sql .= " AND j.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY j.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
    } else {
        // Admin - get all jobs
        $sql = "SELECT 
            j.id,
            j.category,
            j.title,
            j.description,
            j.address,
            j.status,
            j.scheduled_date,
            j.scheduled_time,
            j.created_at,
            j.updated_at,
            cu.name as customer_name,
            cu.phone as customer_phone,
            wo.name as worker_name,
            wo.phone as worker_phone
        FROM job_requests j
        INNER JOIN users cu ON j.customer_id = cu.id
        LEFT JOIN users wo ON j.worker_id = wo.id";
        
        $params = [];
        
        if ($status) {
            $sql .= " WHERE j.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY j.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll();
    
    // Get total count
    $count_sql = str_replace('SELECT 
            j.id,
            j.category,
            j.title,
            j.description,
            j.address,
            j.status,
            j.scheduled_date,
            j.scheduled_time,
            j.created_at,
            j.updated_at,', 'SELECT COUNT(*) as total FROM', $sql);
    $count_sql = preg_replace('/LIMIT \? OFFSET \?/', '', $count_sql);
    
    $stmt = $db->prepare($count_sql);
    $stmt->execute(array_slice($params, 0, -2));
    $total = $stmt->fetch()['total'];
    
    http_response_code(API_SUCCESS);
    echo json_encode([
        'success' => true,
        'jobs' => $jobs,
        'count' => count($jobs),
        'total' => $total,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("List Jobs Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to fetch jobs']);
}
