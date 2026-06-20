<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once '_layout.php';
requireAdmin();

$search  = trim($_GET['q'] ?? '');
$filter  = $_GET['status'] ?? 'all';
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$where   = [];
$params  = [];
if ($search) { $where[] = "(title LIKE ? OR category LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($filter === 'published') { $where[] = "published = 1"; }
if ($filter === 'draft')     { $where[] = "published = 0"; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = (int)$pdo->prepare("SELECT COUNT(*) FROM blogs $whereSQL")->execute($params) ? 0 : 0;
$stmt  = $pdo->prepare("SELECT COUNT(*) FROM blogs $whereSQL");
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT id, title, slug, category, author, published, created_at FROM blogs $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$posts = $stmt->fetchAll();

$flash = flash('success');
admin_head('Blog Posts');
?>
<?php if ($flash): ?><div class="alert alert-success"><?= h($flash) ?></div><?php endif; ?>
<div class="page-actions">
  <div class="page-actions-left">
    <form method="GET" style="display:flex;gap:8px">
      <input type="text" name="q" class="form-control" placeholder="Search posts…" value="<?= h($search) ?>" style="width:200px;padding:7px 11px;font-size:12px">
      <select name="status" class="form-control" style="width:120px;padding:7px 11px;font-size:12px">
        <option value="all" <?= $filter==='all'?'selected':'' ?>>All</option>
        <option value="published" <?= $filter==='published'?'selected':'' ?>>Published</option>
        <option value="draft" <?= $filter==='draft'?'selected':'' ?>>Drafts</option>
      </select>
      <button type="submit" class="btn btn-outline btn-sm">Filter</button>
      <?php if ($search||$filter!=='all'): ?><a href="blogs.php" class="btn btn-link btn-sm">Clear</a><?php endif; ?>
    </form>
  </div>
  <a href="blog-form.php" class="btn btn-primary">+ New Post</a>
</div>

<div class="card">
  <div class="table-wrap">
  <?php if (empty($posts)): ?>
    <div style="padding:40px;text-align:center;color:#333;font-size:13px">No posts found. <a href="blog-form.php" style="color:#555">Create one →</a></div>
  <?php else: ?>
  <table>
    <thead>
      <tr><th>#</th><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($posts as $p): ?>
    <tr>
      <td style="color:#333;font-size:12px"><?= $p['id'] ?></td>
      <td class="td-title">
        <a href="blog-form.php?id=<?= $p['id'] ?>" class="truncate" style="display:block;max-width:280px" title="<?= h($p['title']) ?>"><?= h($p['title']) ?></a>
        <div style="font-size:10px;color:#3a3a3a;margin-top:3px"><?= h($p['slug']) ?></div>
      </td>
      <td style="color:#666;font-size:12px"><?= h($p['category'] ?: '—') ?></td>
      <td style="color:#555;font-size:12px"><?= h($p['author']) ?></td>
      <td><span class="badge <?= $p['published'] ? 'badge-published' : 'badge-draft' ?>"><?= $p['published'] ? 'Published' : 'Draft' ?></span></td>
      <td style="color:#444;font-size:12px;white-space:nowrap"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
      <td>
        <div class="actions">
          <a href="blog-form.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
          <a href="../blog-details.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="btn btn-link btn-sm" title="View">↗</a>
          <form method="POST" action="blog-delete.php" onsubmit="return confirm('Delete this post?')">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm">Del</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php if ($pages > 1): ?>
  <div style="padding:16px 14px;display:flex;gap:6px;align-items:center;border-top:1px solid #1c1c1c">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a href="?q=<?= urlencode($search) ?>&status=<?= h($filter) ?>&page=<?= $i ?>" class="btn btn-sm <?= $i===$page?'btn-primary':'btn-outline' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <span style="font-size:11px;color:#333;margin-left:8px"><?= $total ?> total</span>
  </div>
  <?php endif; ?>
  <?php endif; ?>
  </div>
</div>
<?php admin_foot(); ?>
