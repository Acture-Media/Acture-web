<?php
// Run this ONCE to create tables and default admin account.
// Delete or restrict access to this file after setup.
define('DB_HOST', 'localhost');
define('DB_NAME', 'u574372436_actureus');
define('DB_USER', 'u574372436_actureus');
define('DB_PASS', 'ActureUS@123');

$log = [];

try {
    // Connect without selecting a DB so we can CREATE DATABASE
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");
    $log[] = "✓ Database `" . DB_NAME . "` ready.";

    $pdo->exec("CREATE TABLE IF NOT EXISTS `blogs` (
        `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title`         VARCHAR(500) NOT NULL,
        `slug`          VARCHAR(500) NOT NULL,
        `category`      VARCHAR(200) DEFAULT '',
        `author`        VARCHAR(200) DEFAULT 'Acture Team',
        `tags`          TEXT,
        `cover_image`   VARCHAR(500) DEFAULT '',
        `featured_image` VARCHAR(500) DEFAULT '',
        `title_image`   VARCHAR(500) DEFAULT '',
        `long_card_image` VARCHAR(500) DEFAULT '',
        `big_card_image` VARCHAR(500) DEFAULT '',
        `excerpt`       TEXT,
        `body_html`     LONGTEXT,
        `meta_title`    VARCHAR(500) DEFAULT '',
        `meta_desc`     TEXT,
        `meta_keywords` VARCHAR(500) DEFAULT '',
        `og_image_url`  VARCHAR(500) DEFAULT '',
        `canonical_url` VARCHAR(500) DEFAULT '',
        `schema_script` TEXT,
        `published`     TINYINT(1) DEFAULT 0,
        `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `slug` (`slug`(191))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $log[] = "✓ Table `blogs` ready.";

    $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
        `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `email`        VARCHAR(255) NOT NULL,
        `subscribed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `status`       ENUM('active','unsubscribed') DEFAULT 'active',
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $log[] = "✓ Table `newsletter_subscribers` ready.";

    $pdo->exec("CREATE TABLE IF NOT EXISTS `contact_submissions` (
        `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `full_name`    VARCHAR(255) DEFAULT '',
        `email`        VARCHAR(255) DEFAULT '',
        `phone`        VARCHAR(60) DEFAULT '',
        `company`      VARCHAR(255) DEFAULT '',
        `message`      TEXT,
        `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `status`       ENUM('new','read','replied') DEFAULT 'new'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $log[] = "✓ Table `contact_submissions` ready.";

    $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_users` (
        `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `username`      VARCHAR(100) NOT NULL,
        `password_hash` VARCHAR(255) NOT NULL,
        `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $log[] = "✓ Table `admin_users` ready.";

    $pdo->exec("CREATE TABLE IF NOT EXISTS `site_settings` (
        `key`        VARCHAR(100) NOT NULL PRIMARY KEY,
        `value`      TEXT,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $log[] = "✓ Table `site_settings` ready.";

    // Default admin — change password immediately after setup
    $defaultUser = 'admin';
    $defaultPass = 'Acture@2026';
    $hash = password_hash($defaultPass, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO `admin_users` (`username`, `password_hash`) VALUES (?, ?)");
    $stmt->execute([$defaultUser, $hash]);
    if ($stmt->rowCount() > 0) {
        $log[] = "✓ Default admin created — username: <b>admin</b> / password: <b>Acture@2026</b> — <span style='color:#e74c3c'>change this immediately!</span>";
    } else {
        $log[] = "ℹ Admin user already exists.";
    }

} catch (PDOException $e) {
    $log[] = "<span style='color:#e74c3c'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Setup — Acture Admin</title>
<style>
body{font-family:monospace;background:#0d0d0d;color:#aaa;padding:40px;line-height:1.8;font-size:14px}
h1{color:#fff;margin-bottom:20px;font-size:18px}
.log{background:#111;border:1px solid #222;border-radius:6px;padding:20px;margin-bottom:20px}
a{color:#7ec8e3}
</style>
</head>
<body>
<h1>Acture Media — Database Setup</h1>
<div class="log">
<?php foreach ($log as $l) echo "<div>$l</div>"; ?>
</div>
<p><a href="login.php">→ Go to Admin Login</a></p>
<p style="margin-top:16px;font-size:12px;color:#333">Delete or restrict this file after setup.</p>
</body>
</html>
