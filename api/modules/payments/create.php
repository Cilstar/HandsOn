<?php
/**
 * Create Payment API (Mock M-Pesa)
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

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(API_UNAUTHORIZED);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

// Validate required fields
if (empty($input['job_id']) || empty($input['amount'])) {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Job ID and amount are required']);
    exit;
}

try {
    $db = getDB();
    $customer_id = $_SESSION['user_id'];
    
    // Get job details
    $stmt = $db->prepare("SELECT * FROM job_requests WHERE id = ? AND customer_id = ?");
    $stmt->execute([$input['job_id'], $customer_id]);
    $job = $stmt->fetch();
    
    if (!$job) {
        http_response_code(API_NOT_FOUND);
        echo json_encode(['error' => 'Job not found or unauthorized']);
        exit;
    }
    
    // Check if payment already exists
    $stmt = $db->prepare("SELECT * FROM payments WHERE job_id = ? AND status = 'completed'");
    $stmt->execute([$input['job_id']]);
    if ($stmt->fetch()) {
        http_response_code(API_BAD_REQUEST);
        echo json_encode(['error' => 'Payment already completed for this job']);
        exit;
    }
    
    // Calculate commission
    $amount = floatval($input['amount']);
    $commission = ($amount * COMMISSION_RATE) / 100;
    
    // Generate transaction ID
    $transaction_id = 'TXN' . date('YmdHis') . rand(1000, 9999);
    
    // Create payment record
    $stmt = $db->prepare("INSERT INTO payments 
        (job_id, customer_id, worker_id, amount, commission_amount, status, transaction_id, payment_method, phone_number) 
        VALUES (?, ?, ?, ?, ?, 'pending', ?, 'mpesa', ?)");
    
    $stmt->execute([
        $input['job_id'],
        $customer_id,
        $job['worker_id'],
        $amount,
        $commission,
        $transaction_id,
        $input['phone_number'] ?? ''
    ]);
    
    $payment_id = $db->lastInsertId();
    
    // Mock M-Pesa STK Push simulation
    // In production, this would call the Daraja API
    $mock_stk_response = [
        'MerchantRequestID' => 'MERCHANT' . time(),
        'CheckoutRequestID' => $transaction_id,
        'ResponseCode' => '0',
        'ResponseDescription' => 'Success. Request accepted for processing',
        'CustomerMessage' => 'Payment request sent to your phone'
    ];
    
    // Update payment status to initiated
    $stmt = $db->prepare("UPDATE payments SET status = 'initiated' WHERE id = ?");
    $stmt->execute([$payment_id]);
    
    http_response_code(API_CREATED);
    echo json_encode([
        'message' => 'Payment request initiated',
        'payment' => [
            'id' => $payment_id,
            'job_id' => $input['job_id'],
            'amount' => $amount,
            'commission' => $commission,
            'transaction_id' => $transaction_id,
            'status' => 'initiated'
        ],
        'mpesa_response' => $mock_stk_response,
        'note' => 'This is a mock payment. In production, an STK push would be sent to the customer phone.'
    ]);
    
} catch (PDOException $e) {
    error_log("Create Payment Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to initiate payment']);
}
