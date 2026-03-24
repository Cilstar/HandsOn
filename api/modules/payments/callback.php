<?php
/**
 * M-Pesa Payment Callback API
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

// Get callback data (from M-Pesa)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

try {
    $db = getDB();
    
    // Parse M-Pesa callback
    // In production, this would be the actual M-Pesa response
    $stk_callback = $input['Body']['stkCallback'] ?? $input;
    
    $checkout_request_id = $stk_callback['CheckoutRequestID'] ?? '';
    $result_code = $stk_callback['ResultCode'] ?? '0';
    $result_desc = $stk_callback['ResultDesc'] ?? 'Success';
    
    // Find payment by transaction ID
    $stmt = $db->prepare("SELECT * FROM payments WHERE transaction_id = ?");
    $stmt->execute([$checkout_request_id]);
    $payment = $stmt->fetch();
    
    if (!$payment) {
        // For mock payments, try to find by partial match
        $stmt = $db->prepare("SELECT * FROM payments WHERE transaction_id LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$checkout_request_id . '%']);
        $payment = $stmt->fetch();
    }
    
    if (!$payment) {
        http_response_code(API_NOT_FOUND);
        echo json_encode(['error' => 'Payment not found']);
        exit;
    }
    
    // Update payment status based on result
    if ($result_code === '0') {
        $status = 'completed';
        
        // Get callback metadata for amount and phone
        $callback_metadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
        $mpesa_amount = null;
        $mpesa_phone = null;
        $mpesa_transaction_id = null;
        
        foreach ($callback_metadata as $item) {
            if ($item['Name'] === 'Amount') {
                $mpesa_amount = $item['Value'];
            } elseif ($item['Name'] === 'PhoneNumber') {
                $mpesa_phone = $item['Value'];
            } elseif ($item['Name'] === 'MpesaReceiptNumber') {
                $mpesa_transaction_id = $item['Value'];
            }
        }
        
        // Update payment with M-Pesa details
        $stmt = $db->prepare("UPDATE payments SET 
            status = ?, 
            phone_number = COALESCE(?, phone_number)
            WHERE id = ?");
        
        $stmt->execute([
            $status,
            $mpesa_phone,
            $payment['id']
        ]);
        
    } else {
        $status = 'failed';
        
        $stmt = $db->prepare("UPDATE payments SET status = ? WHERE id = ?");
        $stmt->execute([$status, $payment['id']]);
    }
    
    http_response_code(API_SUCCESS);
    echo json_encode([
        'message' => 'Payment callback processed',
        'status' => $status
    ]);
    
} catch (PDOException $e) {
    error_log("Payment Callback Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to process callback']);
}
