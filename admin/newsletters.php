<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once '_layout.php';
requireAdmin();

// Unsubscribe action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unsub_id'])) {
    $pdo->prepare("UPDATE newsletter_subscribers SET status='unsubscribed' WHERE id=?")->execute([(int)$_POST['unsub_id']]);
    header('Location: newsletters.php?msg=unsubscribed');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del_id'])) {
    $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id=?")->execute([(int)$_POST['del_id']]);
    header('Location: newsletters.php?msg=deleted');
    exit;
}

$filter = $_GET['status'] ?? 'active';
$where  = $filter === 'all' ? '' : "WHERE status = " . $pdo->quote($filter);
$subs   = $pdo->query("SELECT * FROM newsletter_subscribers $where ORDER BY subscribed_at DESC")->fetchAll();
$total  = $pdo->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status='active'")->fetchColumn();

admin_head('Newsletter Subscribers');
?>
<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success"><?= $_GET['msg'] === 'deleted' ? 'Subscriber deleted.' : 'Subscriber unsubscribed.' ?></div>
<?php endif; ?>

<div class="page-actions">
  <div class="page-actions-left">
    <form method="GET" style="display:flex;gap:6px">
      <select name="status" class="form-control" style="width:130px;padding:7px 11px;font-size:12px">
        <option value="active" <?= $filter==='active'?'selected':'' ?>>Active</option>
        <option value="unsubscribed" <?= $filter==='unsubscribed'?'selected':'' ?>>Unsubscribed</option>
        <option value="all" <?= $filter==='all'?'selected':'' ?>>All</option>
      </select>
      <button type="submit" class="btn btn-outline btn-sm">Filter</button>
    </form>
  </div>
  <span style="font-size:12px;color:#444"><?= $total ?> active subscribers</span>
</div>

<div class="card">
  <div class="table-wrap">
  <?php if (empty($subs)): ?>
    <div style="padding:36px;text-align:center;color:#333;font-size:13px">No subscribers yet.</div>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Email</th><th>Status</th><th>Subscribed</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($subs as $s): ?>
    <tr>
      <td style="color:#333;font-size:12px"><?= $s['id'] ?></td>
      <td><?= h($s['email']) ?></td>
      <td><span class="badge <?= $s['status']==='active' ? 'badge-published' : 'badge-draft' ?>"><?= h($s['status']) ?></span></td>
      <td style="color:#555;font-size:12px"><?= date('d M Y, H:i', strtotime($s['subscribed_at'])) ?></td>
      <td>
        <div class="actions">
          <?php if ($s['status'] === 'active'): ?>
          <form method="POST" onsubmit="return confirm('Unsubscribe this email?')">
            <input type="hidden" name="unsub_id" value="<?= $s['id'] ?>">
            <button class="btn btn-outline btn-sm">Unsub</button>
          </form>
          <?php endif; ?>
          <form method="POST" onsubmit="return confirm('Delete permanently?')">
            <input type="hidden" name="del_id" value="<?= $s['id'] ?>">
            <button class="btn btn-danger btn-sm">Del</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
  </div>
</div>
<?php admin_foot(); ?>
