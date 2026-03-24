<?php
/**
 * User Registration API
 * HandsOn - Location-based skilled worker platform
 */

// Turn off error display for API
error_reporting(0);
ini_set('display_errors', 0);

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

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

// Validate required fields
$required_fields = ['name', 'email', 'phone', 'password', 'role'];
$errors = [];

foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        $errors[] = ucfirst($field) . ' is required';
    }
}

// Validate role
$valid_roles = ['customer', 'worker'];
if (isset($input['role']) && !in_array($input['role'], $valid_roles)) {
    $errors[] = 'Invalid role. Must be customer or worker';
}

// Validate email format
if (isset($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

// Validate phone format (Kenyan phone)
if (isset($input['phone'])) {
    $phone = preg_replace('/[^0-9]/', '', $input['phone']);
    if (strlen($phone) < 10 || strlen($phone) > 12) {
        $errors[] = 'Invalid phone number format';
    } else {
        $input['phone'] = $phone;
    }
}

// Validate password length
if (isset($input['password']) && strlen($input['password']) < 6) {
    $errors[] = 'Password must be at least 6 characters';
}

if (!empty($errors)) {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['errors' => $errors]);
    exit;
}

try {
    $db = getDB();
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([strtolower($input['email'])]);
    if ($stmt->fetch()) {
        http_response_code(API_BAD_REQUEST);
        echo json_encode(['error' => 'Email already registered']);
        exit;
    }
    
    // Check if phone already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$input['phone']]);
    if ($stmt->fetch()) {
        http_response_code(API_BAD_REQUEST);
        echo json_encode(['error' => 'Phone number already registered']);
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($input['password'], PASSWORD_BCRYPT, ['cost' => 10]);
    
    // Insert user
    $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        sanitize_input($input['name']),
        strtolower($input['email']),
        $input['phone'],
        $hashed_password,
        $input['role']
    ]);
    
    $user_id = $db->lastInsertId();
    
    // If worker, create worker profile
    if ($input['role'] === 'worker') {
        $stmt = $db->prepare("INSERT INTO worker_profiles (user_id, category, experience, availability) VALUES (?, ?, ?, 'offline')");
        $stmt->execute([
            $user_id,
            sanitize_input($input['category'] ?? 'technician'),
            sanitize_input($input['experience'] ?? '1-2_years')
        ]);
        
        // Notify admins of new worker
        $stmt = $db->prepare("SELECT id FROM users WHERE role = 'admin'");
        $admins = $stmt->fetchAll();
        foreach ($admins as $admin) {
            create_notification(
                $admin['id'],
                'new_worker',
                'New Worker Registration',
                sanitize_input($input['name']) . ' registered as a ' . ($input['category'] ?? 'technician'),
                ['worker_id' => $user_id]
            );
        }
    }
    
    // Generate token (simple approach - in production use JWT)
    $token = bin2hex(random_bytes(32));
    
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_role'] = $input['role'];
    $_SESSION['user_name'] = $input['name'];
    $_SESSION['token'] = $token;
    
    // Generate CSRF token
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
    
    http_response_code(API_CREATED);
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'user' => [
            'id' => $user_id,
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'role' => $input['role']
        ],
        'token' => $token,
        'csrf_token' => $csrf_token
    ]);
    
} catch (PDOException $e) {
    error_log("Registration Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Registration failed. Please try again.']);
}

/**
 * Sanitize input
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
