<?php
/**
 * Notifications API - Mark as read
 * HandsOn - Location-based skilled worker platform
 */

header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/auth.php';

// Handle preflight
add_cors_headers();
handle_cors_preflight();

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Require authentication
$user = require_auth();

// Get input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$notification_id = $input['id'] ?? null;
$mark_all = $input['mark_all'] ?? false;

try {
    $db = getDB();
    
    if ($mark_all) {
        // Mark all as read
        $stmt = $db->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ? AND is_read = FALSE");
        $stmt->execute([$user['user_id']]);
        $affected = $stmt->rowCount();
    } elseif ($notification_id) {
        // Mark specific notification as read
        $stmt = $db->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?");
        $stmt->execute([$notification_id, $user['user_id']]);
        $affected = $stmt->rowCount();
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Notification ID required']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'updated' => $affected
    ]);
    
} catch (PDOException $e) {
    error_log("Mark Read Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update notification']);
}
