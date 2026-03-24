<?php
/**
 * Worker Profile API
 * HandsOn - Location-based skilled worker platform
 */

require_once '../../config/database.php';
require_once '../../config/constants.php';

// Enable CORS and handle preflight
add_cors_headers();
handle_cors_preflight();

try {
    $db = getDB();
    
    // GET request - get worker profile
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $worker_id = $_GET['id'] ?? null;
        
        if (!$worker_id) {
            http_response_code(API_BAD_REQUEST);
            echo json_encode(['error' => 'Worker ID is required']);
            exit;
        }
        
        $stmt = $db->prepare("SELECT 
            u.id as user_id,
            u.name,
            u.email,
            u.phone,
            u.profile_photo,
            wp.id as profile_id,
            wp.category,
            wp.experience,
            wp.bio,
            wp.photo,
            wp.latitude,
            wp.longitude,
            wp.service_radius,
            wp.hourly_rate,
            wp.availability,
            wp.is_verified,
            wp.rating_avg,
            wp.review_count,
            wp.created_at
        FROM users u
        INNER JOIN worker_profiles wp ON u.id = wp.user_id
        WHERE u.id = ? AND u.role = 'worker' AND u.is_active = 1");
        
        $stmt->execute([$worker_id]);
        $worker = $stmt->fetch();
        
        if (!$worker) {
            http_response_code(API_NOT_FOUND);
            echo json_encode(['error' => 'Worker not found']);
            exit;
        }
        
        // Get worker reviews
        $stmt = $db->prepare("SELECT 
            r.id,
            r.rating,
            r.review_text,
            r.created_at,
            u.name as customer_name
        FROM reviews r
        INNER JOIN users u ON r.customer_id = u.id
        WHERE r.worker_id = ?
        ORDER BY r.created_at DESC
        LIMIT 10");
        
        $stmt->execute([$worker_id]);
        $reviews = $stmt->fetchAll();
        
        http_response_code(API_SUCCESS);
        echo json_encode([
            'success' => true,
            'worker' => $worker,
            'reviews' => $reviews
        ]);
        exit;
    }
    
    // POST/PUT request - update worker profile
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
        // Check authentication
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'worker') {
            http_response_code(API_UNAUTHORIZED);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $user_id = $_SESSION['user_id'];
        
        // Validate input
        $allowed_categories = ['plumber', 'electrician', 'carpenter', 'cleaner', 'painter', 'technician'];
        $allowed_availability = ['available', 'busy', 'offline'];
        $allowed_experience = ['1-2_years', '3-5_years', '5_plus_years'];
        
        $errors = [];
        
        if (isset($input['category']) && !in_array($input['category'], $allowed_categories)) {
            $errors[] = 'Invalid category';
        }
        if (isset($input['availability']) && !in_array($input['availability'], $allowed_availability)) {
            $errors[] = 'Invalid availability status';
        }
        if (isset($input['experience']) && !in_array($input['experience'], $allowed_experience)) {
            $errors[] = 'Invalid experience level';
        }
        if (isset($input['hourly_rate']) && floatval($input['hourly_rate']) < 0) {
            $errors[] = 'Hourly rate must be positive';
        }
        if (isset($input['service_radius']) && intval($input['service_radius']) < 1) {
            $errors[] = 'Service radius must be at least 1 km';
        }
        
        if (!empty($errors)) {
            http_response_code(API_BAD_REQUEST);
            echo json_encode(['errors' => $errors]);
            exit;
        }
        
        // Update worker profile
        $stmt = $db->prepare("UPDATE worker_profiles SET 
            category = COALESCE(?, category),
            experience = COALESCE(?, experience),
            bio = COALESCE(?, bio),
            photo = COALESCE(?, photo),
            latitude = COALESCE(?, latitude),
            longitude = COALESCE(?, longitude),
            service_radius = COALESCE(?, service_radius),
            hourly_rate = COALESCE(?, hourly_rate),
            availability = COALESCE(?, availability)
        WHERE user_id = ?");
        
        $stmt->execute([
            $input['category'] ?? null,
            $input['experience'] ?? null,
            $input['bio'] ?? null,
            $input['photo'] ?? null,
            $input['latitude'] ?? null,
            $input['longitude'] ?? null,
            $input['service_radius'] ?? null,
            $input['hourly_rate'] ?? null,
            $input['availability'] ?? null,
            $user_id
        ]);
        
        // Also update user phone if provided
        if (isset($input['phone'])) {
            $stmt = $db->prepare("UPDATE users SET phone = ? WHERE id = ?");
            $stmt->execute([$input['phone'], $user_id]);
        }
        
        http_response_code(API_SUCCESS);
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
        exit;
    }
    
    // Method not allowed
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid request method']);
    
} catch (PDOException $e) {
    error_log("Worker Profile Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to process request']);
}
