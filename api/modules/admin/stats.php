<?php
/**
 * Admin Dashboard Stats API
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

// Check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(API_UNAUTHORIZED);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = getDB();
    
    // Get total users
    $stmt = $db->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as customers,
        SUM(CASE WHEN role = 'worker' THEN 1 ELSE 0 END) as workers,
        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins
    FROM users WHERE is_active = 1");
    $users = $stmt->fetch();
    
    // Get verified workers
    $stmt = $db->query("SELECT COUNT(*) as verified FROM worker_profiles WHERE is_verified = 1");
    $verified = $stmt->fetch();
    
    // Get total jobs
    $stmt = $db->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM job_requests");
    $jobs = $stmt->fetch();
    
    // Get total payments
    $stmt = $db->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_payments,
        SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount,
        SUM(CASE WHEN status = 'completed' THEN commission_amount ELSE 0 END) as total_commission
    FROM payments");
    $payments = $stmt->fetch();
    
    // Get pending worker verifications
    $stmt = $db->query("SELECT COUNT(*) as pending FROM worker_profiles WHERE is_verified = 0");
    $pending_verifications = $stmt->fetch();
    
    // Get recent reviews requiring admin attention
    $stmt = $db->query("SELECT COUNT(*) as low_ratings FROM reviews WHERE admin_reviewed = 0 AND rating < 3");
    $low_ratings = $stmt->fetch();
    
    // Get jobs by category
    $stmt = $db->query("SELECT category, COUNT(*) as count FROM job_requests GROUP BY category");
    $jobs_by_category = $stmt->fetchAll();
    
    // Get top workers by jobs completed
    $stmt = $db->query("
        SELECT u.name, wp.category, wp.rating_avg, wp.review_count, COUNT(j.id) as jobs_completed
        FROM users u
        INNER JOIN worker_profiles wp ON u.id = wp.user_id
        LEFT JOIN job_requests j ON u.id = j.worker_id AND j.status = 'completed'
        WHERE u.role = 'worker'
        GROUP BY u.id
        ORDER BY jobs_completed DESC
        LIMIT 5
    ");
    $top_workers = $stmt->fetchAll();
    
    // Get recent jobs
    $stmt = $db->query("
        SELECT j.id, j.title, j.category, j.status, j.created_at, 
               cu.name as customer_name, wo.name as worker_name
        FROM job_requests j
        INNER JOIN users cu ON j.customer_id = cu.id
        LEFT JOIN users wo ON j.worker_id = wo.id
        ORDER BY j.created_at DESC
        LIMIT 10
    ");
    $recent_jobs = $stmt->fetchAll();
    
    // Get monthly stats (last 6 months)
    $stmt = $db->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as jobs,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
        FROM job_requests
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month
    ");
    $monthly_stats = $stmt->fetchAll();
    
    // Get average rating
    $stmt = $db->query("SELECT AVG(rating_avg) as avg_rating FROM worker_profiles WHERE rating_avg > 0");
    $avg_rating = $stmt->fetch();
    
    http_response_code(API_SUCCESS);
    echo json_encode([
        'success' => true,
        'users' => $users,
        'verified_workers' => $verified['verified'],
        'jobs' => $jobs,
        'payments' => $payments,
        'pending_verifications' => $pending_verifications['pending'],
        'low_ratings' => $low_ratings['low_ratings'],
        'jobs_by_category' => $jobs_by_category,
        'top_workers' => $top_workers,
        'recent_jobs' => $recent_jobs,
        'monthly_stats' => $monthly_stats,
        'average_rating' => $avg_rating['avg_rating'] ?? 0
    ]);
    
} catch (PDOException $e) {
    error_log("Admin Stats Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to fetch stats']);
}
