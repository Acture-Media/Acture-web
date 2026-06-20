<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u574372436_actureus');
define('DB_USER', 'u574372436_actureus');
define('DB_PASS', 'ActureUS@123');
define('UPLOAD_BASE', dirname(__DIR__) . '/uploads/blogs/');
define('UPLOAD_URL', '/uploads/blogs/');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed. Please configure includes/db.php');
}
