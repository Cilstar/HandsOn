<?php
/**
 * Notifications API - List user notifications
 * HandsOn - Location-based skilled worker platform
 */

header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/auth.php';

// Handle preflight
add_cors_headers();
handle_cors_preflight();

// Require authentication
$user = require_auth();

try {
    $db = getDB();
    
    // Get unread count
    $stmt = $db->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = FALSE");
    $stmt->execute([$user['user_id']]);
    $unread_count = $stmt->fetch()['unread'];
    
    // Get notifications
    $stmt = $db->prepare("
        SELECT id, type, title, message, data, is_read, created_at 
        FROM notifications 
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 50
    ");
    $stmt->execute([$user['user_id']]);
    $notifications = $stmt->fetchAll();
    
    // Decode JSON data
    foreach ($notifications as &$notif) {
        if ($notif['data']) {
            $notif['data'] = json_decode($notif['data'], true);
        }
    }
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unread_count
    ]);
    
} catch (PDOException $e) {
    error_log("Notifications Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch notifications']);
}
