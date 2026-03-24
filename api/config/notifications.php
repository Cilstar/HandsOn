<?php
/**
 * Notification Helper Functions
 * HandsOn - Location-based skilled worker platform
 */

require_once __DIR__ . '/database.php';

/**
 * Create a notification for a user
 */
function create_notification($user_id, $type, $title, $message, $data = null) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO notifications (user_id, type, title, message, data) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id,
            $type,
            $title,
            $message,
            $data ? json_encode($data) : null
        ]);
        return $db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Create notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Notify worker of new job request
 */
function notify_worker_new_job($worker_id, $job_id, $customer_name, $category) {
    $title = 'New Job Request';
    $message = "You have a new $category job request from $customer_name";
    $data = ['job_id' => $job_id, 'type' => 'new_job'];
    
    return create_notification($worker_id, 'new_job', $title, $message, $data);
}

/**
 * Notify customer of job status change
 */
function notify_customer_job_status($customer_id, $job_id, $status, $worker_name = null) {
    $titles = [
        'accepted' => 'Job Accepted',
        'in_progress' => 'Work Started',
        'completed' => 'Job Completed',
        'cancelled' => 'Job Cancelled',
        'rejected' => 'Job Declined'
    ];
    
    $messages = [
        'accepted' => "Your job has been accepted by " . ($worker_name ?? 'the worker'),
        'in_progress' => "The worker has started working on your job",
        'completed' => "Your job has been marked as completed",
        'cancelled' => "Your job has been cancelled",
        'rejected' => "The worker has declined your job request"
    ];
    
    $title = $titles[$status] ?? 'Job Update';
    $message = $messages[$status] ?? 'Your job status has been updated';
    $data = ['job_id' => $job_id, 'status' => $status];
    
    return create_notification($customer_id, 'job_status', $title, $message, $data);
}

/**
 * Notify customer of new review
 */
function notify_customer_review($customer_id, $job_id, $worker_name, $rating) {
    $title = 'New Review';
    $message = "$worker_name left you a $rating-star review";
    $data = ['job_id' => $job_id, 'type' => 'review'];
    
    return create_notification($customer_id, 'review', $title, $message, $data);
}

/**
 * Notify worker of payment received
 */
function notify_worker_payment($worker_id, $job_id, $amount) {
    $title = 'Payment Received';
    $message = "You received KSh " . number_format($amount) . " for a completed job";
    $data = ['job_id' => $job_id, 'type' => 'payment', 'amount' => $amount];
    
    return create_notification($worker_id, 'payment', $title, $message, $data);
}

/**
 * Notify admin of new worker registration
 */
function notify_admin_new_worker($admin_id, $worker_id, $worker_name, $category) {
    $title = 'New Worker Registration';
    $message = "$worker_name registered as a $category. Verification required.";
    $data = ['worker_id' => $worker_id, 'type' => 'new_worker'];
    
    return create_notification($admin_id, 'new_worker', $title, $message, $data);
}
