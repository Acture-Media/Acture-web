<?php
require_once 'includes/db.php';

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$back  = $_SERVER['HTTP_REFERER'] ?? 'index.php';

if (!$email) {
    header('Location: ' . $back . '?sub=error');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE status='active'");
    $stmt->execute([$email]);
    header('Location: ' . $back . '?sub=1');
} catch (PDOException $e) {
    header('Location: ' . $back . '?sub=error');
}
exit;
