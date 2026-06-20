<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once '_layout.php';
requireAdmin();

// Ensure settings table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `site_settings` (
    `key`        VARCHAR(100) NOT NULL PRIMARY KEY,
    `value`      TEXT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function getSetting(PDO $pdo, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT value FROM site_settings WHERE `key` = ?");
    $stmt->execute([$key]);
    return $stmt->fetchColumn() ?: $default;
}
function saveSetting(PDO $pdo, string $key, string $value): void {
    $pdo->prepare("INSERT INTO site_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?")->execute([$key,$value,$value]);
}

$success = $error = '';

// Test SMTP
if (isset($_POST['test_smtp'])) {
    require_once dirname(__DIR__) . '/includes/mailer.php';
    $testEmail = trim($_POST['test_email'] ?? getSetting($pdo,'smtp_from_email'));
    $ok = sendMail($pdo, $testEmail, 'Test', 'SMTP Test — Acture Admin', '<p>SMTP is working correctly.</p>');
    $success = $ok ? "Test email sent to <b>$testEmail</b>" : 'Failed — check SMTP credentials and logs.';
    if (!$ok) $error = $success; $success = $ok ? $success : '';
}

// Save settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['test_smtp'])) {
    $fields = ['smtp_host','smtp_port','smtp_username','smtp_password','smtp_encryption','smtp_from_email','smtp_from_name','smtp_notify_email','site_name','site_tagline'];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) saveSetting($pdo, $f, trim($_POST[$f]));
    }
    $success = 'Settings saved.';
}

// Load current settings
$s = [];
foreach (['smtp_host','smtp_port','smtp_username','smtp_password','smtp_encryption','smtp_from_email','smtp_from_name','smtp_notify_email','site_name','site_tagline'] as $k) {
    $s[$k] = getSetting($pdo, $k);
}

admin_head('Settings');
?>
<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

<form method="POST">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">

<!-- SMTP Card -->
<div class="card">
  <div class="card-header"><h2>SMTP / Email Settings</h2></div>
  <div class="card-body">
    <div class="form-group">
      <label class="form-label">SMTP Host</label>
      <input type="text" name="smtp_host" class="form-control" value="<?= h($s['smtp_host']) ?>" placeholder="smtp.gmail.com">
      <div class="form-hint">e.g. smtp.gmail.com · smtp.sendgrid.net · mail.yourdomain.com</div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Port</label>
        <input type="number" name="smtp_port" class="form-control" value="<?= h($s['smtp_port'] ?: '587') ?>" placeholder="587">
        <div class="form-hint">587 (TLS) · 465 (SSL) · 25</div>
      </div>
      <div class="form-group">
        <label class="form-label">Encryption</label>
        <select name="smtp_encryption" class="form-control">
          <option value="tls"  <?= $s['smtp_encryption']==='tls'||!$s['smtp_encryption']?'selected':'' ?>>TLS (recommended)</option>
          <option value="ssl"  <?= $s['smtp_encryption']==='ssl'?'selected':'' ?>>SSL</option>
          <option value="none" <?= $s['smtp_encryption']==='none'?'selected':'' ?>>None</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">SMTP Username</label>
      <input type="text" name="smtp_username" class="form-control" value="<?= h($s['smtp_username']) ?>" placeholder="your@email.com" autocomplete="off">
    </div>
    <div class="form-group">
      <label class="form-label">SMTP Password</label>
      <input type="password" name="smtp_password" class="form-control" value="<?= h($s['smtp_password']) ?>" placeholder="••••••••" autocomplete="new-password">
      <div class="form-hint">For Gmail use an App Password, not your login password.</div>
    </div>
    <div class="form-group">
      <label class="form-label">From Email</label>
      <input type="email" name="smtp_from_email" class="form-control" value="<?= h($s['smtp_from_email']) ?>" placeholder="info@acturemedia.com">
    </div>
    <div class="form-group">
      <label class="form-label">From Name</label>
      <input type="text" name="smtp_from_name" class="form-control" value="<?= h($s['smtp_from_name'] ?: 'Acture Media') ?>" placeholder="Acture Media">
    </div>
    <div class="form-group">
      <label class="form-label">Notify Email <span style="font-size:9px;color:#444">(receives contact form submissions)</span></label>
      <input type="email" name="smtp_notify_email" class="form-control" value="<?= h($s['smtp_notify_email']) ?>" placeholder="info@acturemedia.com">
    </div>
  </div>
</div>

<!-- Site & Test Card -->
<div style="display:flex;flex-direction:column;gap:16px">
  <div class="card">
    <div class="card-header"><h2>Site Settings</h2></div>
    <div class="card-body">
      <div class="form-group">
        <label class="form-label">Site Name</label>
        <input type="text" name="site_name" class="form-control" value="<?= h($s['site_name'] ?: 'Acture Media') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Tagline</label>
        <input type="text" name="site_tagline" class="form-control" value="<?= h($s['site_tagline']) ?>" placeholder="We Build What Works.">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Test SMTP Connection</h2></div>
    <div class="card-body">
      <p style="font-size:12px;color:#555;margin-bottom:14px">Save settings first, then send a test email to confirm SMTP is working.</p>
      <div class="form-group">
        <label class="form-label">Send test to</label>
        <input type="email" name="test_email" class="form-control" value="<?= h($s['smtp_notify_email'] ?: $s['smtp_from_email']) ?>" placeholder="test@example.com">
      </div>
      <button type="submit" name="test_smtp" class="btn btn-outline" style="width:100%">Send Test Email</button>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Admin Account</h2></div>
    <div class="card-body">
      <p style="font-size:12px;color:#555;margin-bottom:14px">Change your admin password.</p>
      <div class="form-group">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" id="newPw" class="form-control" placeholder="Leave blank to keep current" autocomplete="new-password">
      </div>
      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirmPw" class="form-control" placeholder="Repeat new password">
      </div>
      <button type="submit" name="change_password" class="btn btn-outline" style="width:100%" onclick="return checkPw()">Change Password</button>
    </div>
  </div>
</div>

</div>

<div style="margin-top:20px;display:flex;gap:10px">
  <button type="submit" class="btn btn-primary">Save All Settings</button>
  <a href="index.php" class="btn btn-outline">Cancel</a>
</div>
</form>

<script>
function checkPw(){
  const a=document.getElementById('newPw').value,b=document.getElementById('confirmPw').value;
  if(a&&a!==b){alert('Passwords do not match.');return false;}
  return true;
}
</script>
<?php
// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $newPw = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($newPw && $newPw === $confirm) {
        $hash = password_hash($newPw, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE admin_users SET password_hash=? WHERE id=?")->execute([$hash, $_SESSION['admin_id']]);
        echo '<script>alert("Password changed successfully.");window.location="settings.php";</script>';
    }
}
admin_foot();
?>
