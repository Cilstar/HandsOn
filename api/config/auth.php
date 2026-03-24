<?php
/**
 * Authentication Middleware
 * HandsOn - Location-based skilled worker platform
 */

require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/database.php';

/**
 * Require user authentication
 */
function require_auth() {
    add_cors_headers();
    handle_cors_preflight();
    
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'user_role' => $_SESSION['user_role'],
        'user_name' => $_SESSION['user_name'] ?? '',
        'user_email' => $_SESSION['user_email'] ?? ''
    ];
}

/**
 * Require specific role
 */
function require_role($allowed_roles) {
    $user = require_auth();
    
    if (!in_array($user['user_role'], $allowed_roles)) {
        http_response_code(403);
        echo json_encode(['error' => 'Insufficient permissions']);
        exit;
    }
    
    return $user;
}

/**
 * Generate JWT-like token (simple version for now)
 */
function generate_api_token($user_id) {
    $token = bin2hex(random_bytes(32));
    $expires = time() + (7 * 24 * 60 * 60); // 7 days
    
    // Store token in database (for production, use proper JWT)
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO api_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $token, date('Y-m-d H:i:s', $expires)]);
    } catch (PDOException $e) {
        error_log("Token generation error: " . $e->getMessage());
    }
    
    return $token;
}

/**
 * Validate API token
 */
function validate_api_token($token) {
    if (empty($token)) {
        return null;
    }
    
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT user_id FROM api_tokens WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        
        if ($result) {
            return $result['user_id'];
        }
    } catch (PDOException $e) {
        error_log("Token validation error: " . $e->getMessage());
    }
    
    return null;
}

/**
 * Get current user from session or token
 */
function get_current_user() {
    session_start();
    
    if (isset($_SESSION['user_id'])) {
        return [
            'user_id' => $_SESSION['user_id'],
            'user_role' => $_SESSION['user_role'],
            'user_name' => $_SESSION['user_name'] ?? '',
            'user_email' => $_SESSION['user_email'] ?? ''
        ];
    }
    
    // Check for API token
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s+(.+)/i', $auth_header, $matches)) {
        $user_id = validate_api_token($matches[1]);
        if ($user_id) {
            $db = getDB();
            $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if ($user) {
                return [
                    'user_id' => $user['id'],
                    'user_role' => $user['role'],
                    'user_name' => $user['name'],
                    'user_email' => $user['email']
                ];
            }
        }
    }
    
    return null;
}
