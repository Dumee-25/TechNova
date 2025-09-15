<?php
// Admin configuration file
session_start();

// Define root path
define('ROOT_PATH', dirname(dirname(__FILE__)));

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'tech_news_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if admin is logged in for protected pages
$protected_pages = ['dashboard.php', 'manage-news.php', 'add-news.php', 'edit-news.php', 'manage-users.php'];

if (in_array(basename($_SERVER['PHP_SELF']), $protected_pages) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
?>