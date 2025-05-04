<?php
// Ensure session is started at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debugging: Check if session is working
if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = 'Session is working';
}

// Database connection settings
$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $db = new PDO($dsn, $user, $pass);
    // Enable PDO error mode for better debugging
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $ex) {
    echo '<p>Database Connection Error: ' . $ex->getMessage() . '</p>';
    exit;
}

// Generate a new CSRF token for every request
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>