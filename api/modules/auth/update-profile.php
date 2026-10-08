<?php
/**
 * Update User Profile API
 * HandsOn - Location-based skilled worker platform
 */

// Turn off error display for API
error_reporting(0);
ini_set('display_errors', 0);

require_once '../../config/database.php';
require_once '../../config/constants.php';

// Enable CORS and handle preflight
header('Content-Type: application/json');
add_cors_headers();
handle_cors_preflight();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$user_id = $_SESSION['user_id'];

try {
    $db = getDB();
    
    // Get current user data
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(API_NOT_FOUND);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    
    $response = ['success' => true, 'message' => 'Profile updated successfully'];
    
    // Handle file upload (profile photo)
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_photo'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowed_types)) {
            http_response_code(API_BAD_REQUEST);
            echo json_encode(['error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.']);
            exit;
        }
        
        // Check file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            http_response_code(API_BAD_REQUEST);
            echo json_encode(['error' => 'File too large. Maximum size is 2MB.']);
            exit;
        }
        
        // Create uploads directory if it doesn't exist
        $upload_dir = __DIR__ . '/../../../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $photo_url = '/handsonapplication/uploads/' . $filename;
            
            // Update user profile photo
            $stmt = $db->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
            $stmt->execute([$photo_url, $user_id]);
            
            // Also update worker profile photo if worker
            if ($user['role'] === 'worker') {
                $stmt = $db->prepare("UPDATE worker_profiles SET photo = ? WHERE user_id = ?");
                $stmt->execute([$photo_url, $user_id]);
            }
            
            $response['profile_photo'] = $photo_url;
        }
    }
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Update user basic info (name, phone)
    $user_updates = [];
    $user_params = [];
    
    if (isset($input['name']) && !empty(trim($input['name']))) {
        $user_updates[] = 'name = ?';
        $user_params[] = sanitize_input($input['name']);
    }
    
    if (isset($input['phone']) && !empty(trim($input['phone']))) {
        // Validate phone format
        $phone = preg_replace('/[^0-9]/', '', $input['phone']);
        if (strlen($phone) >= 10) {
            $user_updates[] = 'phone = ?';
            $user_params[] = $phone;
        }
    }
    
    if (!empty($user_updates)) {
        $user_params[] = $user_id;
        $sql = "UPDATE users SET " . implode(', ', $user_updates) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($user_params);
    }
    
    // Update worker profile specific fields
    if ($user['role'] === 'worker') {
        $worker_updates = [];
        $worker_params = [];
        
        if (isset($input['bio'])) {
            $worker_updates[] = 'bio = ?';
            $worker_params[] = sanitize_input($input['bio']);
        }
        
        if (isset($input['category'])) {
            $worker_updates[] = 'category = ?';
            $worker_params[] = sanitize_input($input['category']);
        }
        
        if (isset($input['experience'])) {
            $worker_updates[] = 'experience = ?';
            $worker_params[] = sanitize_input($input['experience']);
        }
        
        if (isset($input['hourly_rate'])) {
            $worker_updates[] = 'hourly_rate = ?';
            $worker_params[] = floatval($input['hourly_rate']);
        }
        
        if (isset($input['service_radius'])) {
            $worker_updates[] = 'service_radius = ?';
            $worker_params[] = intval($input['service_radius']);
        }
        
        if (isset($input['availability'])) {
            $valid_availability = ['available', 'busy', 'offline'];
            if (in_array($input['availability'], $valid_availability)) {
                $worker_updates[] = 'availability = ?';
                $worker_params[] = $input['availability'];
            }
        }
        
        // Location updates
        if (isset($input['latitude']) && isset($input['longitude'])) {
            $worker_updates[] = 'latitude = ?';
            $worker_params[] = floatval($input['latitude']);
            $worker_updates[] = 'longitude = ?';
            $worker_params[] = floatval($input['longitude']);
        }
        
        if (!empty($worker_updates)) {
            $worker_params[] = $user_id;
            $sql = "UPDATE worker_profiles SET " . implode(', ', $worker_updates) . " WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($worker_params);
        }
    }
    
    // Get updated user data
    $stmt = $db->prepare("SELECT id, name, email, phone, role, profile_photo FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $updated_user = $stmt->fetch();
    
    $response['user'] = $updated_user;
    
    // Get worker profile if worker
    if ($user['role'] === 'worker') {
        $stmt = $db->prepare("SELECT * FROM worker_profiles WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $worker_profile = $stmt->fetch();
        $response['worker_profile'] = $worker_profile;
    }
    
    http_response_code(API_SUCCESS);
    echo json_encode($response);
    
} catch (PDOException $e) {
    error_log("Update Profile Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to update profile']);
}

/**
 * Sanitize input
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
