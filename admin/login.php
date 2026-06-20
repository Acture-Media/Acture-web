<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

if (isAdmin()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_id'] = $user['id'];
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login — Acture Media</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#0d0d0d;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
a{color:inherit;text-decoration:none}
.card{width:100%;max-width:360px;background:#141414;border:1px solid #222;border-radius:8px;padding:36px}
.logo{text-align:center;margin-bottom:28px}
.logo img{height:30px}
.logo-text{font-size:14px;font-weight:600;letter-spacing:2px;display:none}
.heading{font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:#444;text-align:center;margin-bottom:24px}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.8px;color:#555;margin-bottom:7px}
.form-control{width:100%;background:#0d0d0d;border:1px solid #252525;color:#fff;padding:10px 13px;font-size:13px;border-radius:4px;transition:border-color .15s;font-family:inherit}
.form-control:focus{outline:none;border-color:#444}
.btn{width:100%;padding:11px;background:#fff;color:#000;border:none;font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;border-radius:4px;margin-top:8px;transition:background .15s}
.btn:hover{background:#ddd}
.alert{padding:11px 14px;border-radius:4px;margin-bottom:16px;font-size:12px;background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.25);color:#e74c3c}
.setup-hint{font-size:11px;color:#333;text-align:center;margin-top:16px}
.setup-hint a{color:#555;border-bottom:1px solid #333}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <img src="../images/custom/acture-logo-light.png" alt="Acture Media" onerror="this.style.display='none';document.querySelector('.logo-text').style.display='block'">
    <div class="logo-text">ACTURE MEDIA</div>
  </div>
  <p class="heading">Admin Panel</p>
  <?php if ($error): ?>
    <div class="alert"><?= h($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label class="form-label">Username</label>
      <input class="form-control" type="text" name="username" autocomplete="username" required>
    </div>
    <div class="form-group">
      <label class="form-label">Password</label>
      <input class="form-control" type="password" name="password" autocomplete="current-password" required>
    </div>
    <button type="submit" class="btn">Sign In</button>
  </form>
  <p class="setup-hint">First time? <a href="setup.php">Run setup</a> to create tables &amp; admin account.</p>
</div>
</body>
</html>
