<?php
/**
 * Database Configuration
 * HandsOn - Location-based skilled worker platform
 */

// Turn off error display for API
error_reporting(0);
ini_set('display_errors', 0);

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'handson_db');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // Create PDO connection
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

/**
 * Get database instance
 */
function getDB() {
    global $pdo;
    return $pdo;
}

/**
 * Close database connection
 */
function closeDB() {
    global $pdo;
    $pdo = null;
}
