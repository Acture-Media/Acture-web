<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: blogs.php'); exit; }

$id = (int)($_POST['id'] ?? 0);
if ($id) {
    $pdo->prepare("DELETE FROM blogs WHERE id = ?")->execute([$id]);
    setFlash('success', 'Post deleted.');
}
header('Location: blogs.php');
exit;
