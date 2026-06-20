<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once '_layout.php';
requireAdmin();

// Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del_id'])) {
    $pdo->prepare("DELETE FROM contact_submissions WHERE id=?")->execute([(int)$_POST['del_id']]);
    header('Location: contacts.php'); exit;
}
// Bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bulk_ids'])) {
    $ids = array_map('intval', (array)$_POST['bulk_ids']);
    if ($ids) {
        $in = implode(',', $ids);
        $pdo->exec("DELETE FROM contact_submissions WHERE id IN ($in)");
    }
    header('Location: contacts.php'); exit;
}
// Mark status
if (isset($_GET['status_id'], $_GET['status_val'])) {
    $allowed = ['new','read','replied'];
    if (in_array($_GET['status_val'], $allowed)) {
        $pdo->prepare("UPDATE contact_submissions SET status=? WHERE id=?")->execute([$_GET['status_val'], (int)$_GET['status_id']]);
    }
    header('Location: contacts.php'); exit;
}

// View single
$viewId = (int)($_GET['view'] ?? 0);
$viewItem = null;
if ($viewId) {
    $stmt = $pdo->prepare("SELECT * FROM contact_submissions WHERE id=?");
    $stmt->execute([$viewId]);
    $viewItem = $stmt->fetch();
    if ($viewItem && $viewItem['status'] === 'new') {
        $pdo->prepare("UPDATE contact_submissions SET status='read' WHERE id=?")->execute([$viewId]);
        $viewItem['status'] = 'read';
    }
}

// Filters & search
$search  = trim($_GET['q'] ?? '');
$filter  = $_GET['status'] ?? 'all';
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$where  = [];
$params = [];
if ($search) {
    $where[] = "(full_name LIKE ? OR email LIKE ? OR company LIKE ? OR message LIKE ?)";
    $like = "%$search%";
    array_push($params, $like, $like, $like, $like);
}
if ($filter !== 'all') { $where[] = "status = ?"; $params[] = $filter; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM contact_submissions $whereSQL");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM contact_submissions $whereSQL ORDER BY submitted_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$submissions = $stmt->fetchAll();

$newCount = $pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE status='new'")->fetchColumn();

admin_head('Contact Submissions');
?>

<?php if ($viewItem): ?>
<!-- ── Single View ── -->
<div class="card" style="margin-bottom:20px">
  <div class="card-header">
    <h2>Message #<?= $viewItem['id'] ?> — <?= h($viewItem['full_name']) ?></h2>
    <div style="display:flex;gap:8px">
      <a href="?status_id=<?= $viewItem['id'] ?>&status_val=replied" class="btn btn-outline btn-sm">Mark Replied</a>
      <a href="contacts.php" class="btn btn-outline btn-sm">← Back</a>
    </div>
  </div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;margin-bottom:20px">
      <div><div class="form-label">Name</div><div style="font-size:14px"><?= h($viewItem['full_name']) ?></div></div>
      <div><div class="form-label">Email</div><div style="font-size:14px"><a href="mailto:<?= h($viewItem['email']) ?>" style="color:#7ec8e3"><?= h($viewItem['email']) ?></a></div></div>
      <div><div class="form-label">Phone</div><div style="font-size:14px"><?= h($viewItem['phone'] ?: '—') ?></div></div>
      <div><div class="form-label">Company</div><div style="font-size:14px"><?= h($viewItem['company'] ?: '—') ?></div></div>
    </div>
    <div class="form-label">Message</div>
    <div style="background:#0d0d0d;border:1px solid #1c1c1c;border-radius:4px;padding:16px;font-size:14px;line-height:1.7;white-space:pre-wrap;color:#ccc"><?= h($viewItem['message']) ?></div>
    <div style="margin-top:10px;font-size:11px;color:#444">Received: <?= date('d M Y, H:i', strtotime($viewItem['submitted_at'])) ?> · Status: <b><?= h($viewItem['status']) ?></b></div>
    <div style="margin-top:16px;display:flex;gap:8px">
      <a href="mailto:<?= h($viewItem['email']) ?>?subject=Re: Your message to Acture Media" class="btn btn-primary btn-sm">Reply via Email</a>
      <form method="POST" onsubmit="return confirm('Delete?')" style="display:inline">
        <input type="hidden" name="del_id" value="<?= $viewItem['id'] ?>">
        <button class="btn btn-danger btn-sm">Delete</button>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ── Search / Filter Bar ── -->
<div class="page-actions">
  <div class="page-actions-left" style="flex-wrap:wrap;gap:6px">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center">
      <input type="text" name="q" class="form-control" placeholder="Search name, email, company…" value="<?= h($search) ?>" style="width:220px;padding:7px 11px;font-size:12px">
      <select name="status" class="form-control" style="width:120px;padding:7px 11px;font-size:12px">
        <option value="all"     <?= $filter==='all'?'selected':'' ?>>All</option>
        <option value="new"     <?= $filter==='new'?'selected':'' ?>>New</option>
        <option value="read"    <?= $filter==='read'?'selected':'' ?>>Read</option>
        <option value="replied" <?= $filter==='replied'?'selected':'' ?>>Replied</option>
      </select>
      <button type="submit" class="btn btn-outline btn-sm">Filter</button>
      <?php if ($search || $filter !== 'all'): ?>
        <a href="contacts.php" class="btn btn-link btn-sm">Clear</a>
      <?php endif; ?>
    </form>
  </div>
  <span style="font-size:12px;color:#444"><?= $newCount ?> unread · <?= $total ?> total</span>
</div>

<!-- ── Table ── -->
<div class="card">
  <div class="table-wrap">
  <?php if (empty($submissions)): ?>
    <div style="padding:40px;text-align:center;color:#333;font-size:13px">No submissions found.</div>
  <?php else: ?>
  <form method="POST" id="bulkForm">
  <table>
    <thead>
      <tr>
        <th><input type="checkbox" id="checkAll" style="accent-color:#fff;cursor:pointer"></th>
        <th>#</th><th>Name</th><th>Email</th><th>Company</th><th>Status</th><th>Date</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($submissions as $s): ?>
    <tr style="<?= $s['status']==='new'?'background:#15150f':'' ?>">
      <td><input type="checkbox" name="bulk_ids[]" value="<?= $s['id'] ?>" style="accent-color:#fff;cursor:pointer"></td>
      <td style="color:#333;font-size:12px"><?= $s['id'] ?></td>
      <td style="font-weight:<?= $s['status']==='new'?600:400 ?>"><?= h($s['full_name']) ?></td>
      <td style="color:#666;font-size:12px"><a href="mailto:<?= h($s['email']) ?>" style="color:#7ec8e3"><?= h($s['email']) ?></a></td>
      <td style="color:#555;font-size:12px"><?= h($s['company'] ?: '—') ?></td>
      <td>
        <span class="badge <?= $s['status']==='new'?'badge-published':($s['status']==='replied'?'':'badge-draft') ?>">
          <?= $s['status']==='new'?'🔵 New':($s['status']==='replied'?'✓ Replied':'Read') ?>
        </span>
      </td>
      <td style="color:#444;font-size:12px;white-space:nowrap"><?= date('d M Y', strtotime($s['submitted_at'])) ?></td>
      <td>
        <div class="actions">
          <a href="?view=<?= $s['id'] ?>" class="btn btn-outline btn-sm">View</a>
          <?php if ($s['status'] !== 'replied'): ?>
          <a href="?status_id=<?= $s['id'] ?>&status_val=replied" class="btn btn-link btn-sm" title="Mark replied">✓</a>
          <?php endif; ?>
          <button type="submit" name="del_id" value="<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Del</button>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Bulk actions + pagination -->
  <div style="padding:12px 14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;border-top:1px solid #1c1c1c">
    <div style="display:flex;align-items:center;gap:8px">
      <span style="font-size:11px;color:#444">Bulk:</span>
      <button type="submit" name="bulk_action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete selected?')">Delete Selected</button>
    </div>
    <?php if ($pages > 1): ?>
    <div style="display:flex;gap:4px;align-items:center">
      <?php if ($page > 1): ?>
        <a href="?q=<?= urlencode($search) ?>&status=<?= h($filter) ?>&page=<?= $page-1 ?>" class="btn btn-outline btn-sm">←</a>
      <?php endif; ?>
      <?php for ($i = max(1,$page-2); $i <= min($pages,$page+2); $i++): ?>
        <a href="?q=<?= urlencode($search) ?>&status=<?= h($filter) ?>&page=<?= $i ?>" class="btn btn-sm <?= $i===$page?'btn-primary':'btn-outline' ?>"><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($page < $pages): ?>
        <a href="?q=<?= urlencode($search) ?>&status=<?= h($filter) ?>&page=<?= $page+1 ?>" class="btn btn-outline btn-sm">→</a>
      <?php endif; ?>
      <span style="font-size:11px;color:#333;margin-left:4px">of <?= $pages ?></span>
    </div>
    <?php endif; ?>
  </div>
  </form>
  <?php endif; ?>
  </div>
</div>

<script>
document.getElementById('checkAll')?.addEventListener('change', function(){
  document.querySelectorAll('input[name="bulk_ids[]"]').forEach(c => c.checked = this.checked);
});
</script>
<?php admin_foot(); ?>
