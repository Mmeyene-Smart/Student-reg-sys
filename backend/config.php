<?php
// backend/config.php

// Enable error reporting but keep it internal, don't output to screen to avoid breaking JSON
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Ensure we don't send any output before headers
ob_start();

// Determine environment
$is_localhost = (php_sapi_name() === 'cli' || $_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

if ($is_localhost) {
    // Local Development Settings
    header("Access-Control-Allow-Origin: *");
    
    $host = '127.0.0.1';
    $db_name = 'u678570154_student_reg_sy';
    $username = 'root';
    $password = '';
} else {
    // Production Settings (cPanel)
    header("Access-Control-Allow-Origin: https://app.elthomppoly.edu.ng");
    
    $host = 'localhost';
    $db_name = 'u678570154_student_reg_sy';
    $username = 'u678570154_root';
    $password = 'Isaac@1982~1982';
}

header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, ngrok-skip-browser-warning");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight request
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    ob_end_clean();
    exit();
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Attempt to connect without DB name to create it (Only for Localhost usually)
    if ($is_localhost) {
        try {
            $conn = new PDO("mysql:host=$host", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
            $conn->exec($sql);
            $conn->exec("USE $db_name");
            
            // Re-connect
            $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e2) {
            http_response_code(500);
            ob_end_clean(); // Clean buffer
            echo json_encode(["message" => "Database connection failed: " . $e2->getMessage()]);
            exit();
        }
    } else {
        // In production, just fail if connection fails
        http_response_code(500);
        ob_end_clean();
        echo json_encode(["message" => "Database connection failed."]);
        exit();
    }
}

// Clear any accidental output
ob_end_clean();
?>
