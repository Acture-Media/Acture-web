<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once '_layout.php';
requireAdmin();

$totalPosts    = $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
$publishedPosts = $pdo->query("SELECT COUNT(*) FROM blogs WHERE published=1")->fetchColumn();
$draftPosts    = $pdo->query("SELECT COUNT(*) FROM blogs WHERE published=0")->fetchColumn();
$subscribers   = $pdo->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status='active'")->fetchColumn();
$contacts      = $pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE status='new'")->fetchColumn();
$recentPosts   = $pdo->query("SELECT id, title, category, published, created_at FROM blogs ORDER BY created_at DESC LIMIT 5")->fetchAll();

admin_head('Dashboard');
?>
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label">Total Blog Posts</div>
    <div class="stat-value"><?= $totalPosts ?></div>
    <div class="stat-sub"><?= $publishedPosts ?> published · <?= $draftPosts ?> drafts</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Newsletter Subscribers</div>
    <div class="stat-value"><?= $subscribers ?></div>
    <div class="stat-sub">active subscribers</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">New Contact Messages</div>
    <div class="stat-value"><?= $contacts ?></div>
    <div class="stat-sub">unread submissions</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2>Recent Blog Posts</h2>
    <a href="blog-form.php" class="btn btn-primary btn-sm">+ New Post</a>
  </div>
  <div class="table-wrap">
    <?php if (empty($recentPosts)): ?>
    <div style="padding:28px;text-align:center;color:#333;font-size:13px">No blog posts yet. <a href="blog-form.php" style="color:#666">Create your first post →</a></div>
    <?php else: ?>
    <table>
      <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($recentPosts as $p): ?>
        <tr>
          <td class="td-title truncate"><a href="blog-form.php?id=<?= $p['id'] ?>"><?= h($p['title']) ?></a></td>
          <td style="color:#666"><?= h($p['category'] ?: '—') ?></td>
          <td><span class="badge <?= $p['published'] ? 'badge-published' : 'badge-draft' ?>"><?= $p['published'] ? 'Published' : 'Draft' ?></span></td>
          <td style="color:#555;font-size:12px"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
          <td><a href="blog-form.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>
<?php admin_foot(); ?>
