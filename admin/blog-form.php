<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once '_layout.php';
requireAdmin();

$id   = (int)($_GET['id'] ?? 0);
$post = [];
$isEdit = false;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) { header('Location: blogs.php'); exit; }
    $isEdit = true;
}

$errors = [];
$success = '';

function uploadImage(string $field, string $old = ''): string {
    if (empty($_FILES[$field]['name'])) return $old;
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) return $old;
    $allowed = ['image/jpeg','image/png','image/webp'];
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowed)) return $old;
    if ($file['size'] > 5 * 1024 * 1024) return $old;
    $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid('blog_', true) . '.' . strtolower($ext);
    $dest = UPLOAD_BASE . $name;
    if (!is_dir(UPLOAD_BASE)) mkdir(UPLOAD_BASE, 0755, true);
    if (move_uploaded_file($file['tmp_name'], $dest)) return UPLOAD_URL . $name;
    return $old;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $slug     = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author   = trim($_POST['author'] ?? 'Acture Team');
    $tags     = trim($_POST['tags'] ?? '');
    $excerpt  = trim($_POST['excerpt'] ?? '');
    $body     = $_POST['body_html'] ?? '';
    $published = isset($_POST['published']) ? 1 : 0;

    $metaTitle    = trim($_POST['meta_title'] ?? '');
    $metaDesc     = trim($_POST['meta_desc'] ?? '');
    $metaKeywords = trim($_POST['meta_keywords'] ?? '');
    $ogImage      = trim($_POST['og_image_url'] ?? '');
    $canonical    = trim($_POST['canonical_url'] ?? '');
    $schema       = trim($_POST['schema_script'] ?? '');

    if (!$title)  $errors[] = 'Post title is required.';
    if (!$slug)   $errors[] = 'Slug is required.';
    $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $slug)));

    $coverImg    = uploadImage('cover_image',     $isEdit ? $post['cover_image'] : '');
    $featuredImg = uploadImage('featured_image',  $isEdit ? $post['featured_image'] : '');
    $titleImg    = uploadImage('title_image',     $isEdit ? $post['title_image'] : '');
    $longImg     = uploadImage('long_card_image', $isEdit ? $post['long_card_image'] : '');
    $bigImg      = uploadImage('big_card_image',  $isEdit ? $post['big_card_image'] : '');

    if (empty($errors)) {
        try {
            if ($isEdit) {
                $stmt = $pdo->prepare("UPDATE blogs SET title=?,slug=?,category=?,author=?,tags=?,cover_image=?,featured_image=?,title_image=?,long_card_image=?,big_card_image=?,excerpt=?,body_html=?,meta_title=?,meta_desc=?,meta_keywords=?,og_image_url=?,canonical_url=?,schema_script=?,published=? WHERE id=?");
                $stmt->execute([$title,$slug,$category,$author,$tags,$coverImg,$featuredImg,$titleImg,$longImg,$bigImg,$excerpt,$body,$metaTitle,$metaDesc,$metaKeywords,$ogImage,$canonical,$schema,$published,$id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO blogs (title,slug,category,author,tags,cover_image,featured_image,title_image,long_card_image,big_card_image,excerpt,body_html,meta_title,meta_desc,meta_keywords,og_image_url,canonical_url,schema_script,published) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->execute([$title,$slug,$category,$author,$tags,$coverImg,$featuredImg,$titleImg,$longImg,$bigImg,$excerpt,$body,$metaTitle,$metaDesc,$metaKeywords,$ogImage,$canonical,$schema,$published]);
                $id = (int)$pdo->lastInsertId();
            }
            setFlash('success', $isEdit ? 'Post updated successfully.' : 'Post created successfully.');
            header("Location: blog-form.php?id=$id");
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
    // Restore for re-render
    $post = compact('title','slug','category','author','tags','excerpt','body_html','published','metaTitle','metaDesc','metaKeywords','ogImage','canonical','schema','coverImg','featuredImg','titleImg','longImg','bigImg');
    $post['body_html'] = $body;
    $post['cover_image'] = $coverImg;
    $post['featured_image'] = $featuredImg;
    $post['title_image'] = $titleImg;
    $post['long_card_image'] = $longImg;
    $post['big_card_image'] = $bigImg;
    $post['meta_title'] = $metaTitle;
    $post['meta_desc'] = $metaDesc;
    $post['meta_keywords'] = $metaKeywords;
    $post['og_image_url'] = $ogImage;
    $post['canonical_url'] = $canonical;
    $post['schema_script'] = $schema;
}

$flashMsg = flash('success');
$pageTitle = $isEdit ? 'Edit Post' : 'New Post';

$quillCss = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css">';
admin_head($pageTitle, $quillCss);
?>
<div class="page-actions">
  <div class="page-actions-left">
    <a href="blogs.php" class="btn btn-outline btn-sm">← All Posts</a>
    <?php if ($isEdit && !empty($post['slug'])): ?>
    <a href="../blog-details.php?slug=<?= urlencode($post['slug']) ?>" target="_blank" class="btn btn-link btn-sm">View Post ↗</a>
    <?php endif; ?>
  </div>
  <div class="page-actions-left">
    <?php if ($isEdit): ?>
    <form method="POST" action="blog-delete.php" onsubmit="return confirm('Permanently delete this post?')">
      <input type="hidden" name="id" value="<?= $id ?>">
      <button class="btn btn-danger btn-sm">Delete Post</button>
    </form>
    <?php endif; ?>
    <button form="tplForm" type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Post' : 'Save Post' ?></button>
  </div>
</div>

<?php if ($flashMsg): ?><div class="alert alert-success"><?= h($flashMsg) ?></div><?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-error"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<form id="tplForm" method="POST" enctype="multipart/form-data">
<input type="hidden" name="body_html" id="bodyHtmlField" value="<?= h($post['body_html'] ?? '') ?>">

<div class="tab-bar">
  <button type="button" class="tab-btn active" onclick="switchTab(this,'tabContent')">Post Content</button>
  <button type="button" class="tab-btn" onclick="switchTab(this,'tabSEO')">SEO Settings</button>
</div>

<!-- ── POST CONTENT TAB ── -->
<div id="tabContent" class="tab-pane active">
  <div class="form-row">
    <div class="form-group">
      <label class="form-label">Post Title <span class="req">*</span></label>
      <input type="text" name="title" class="form-control" value="<?= h($post['title'] ?? '') ?>" required oninput="autoSlug(this.value)">
    </div>
    <div class="form-group">
      <label class="form-label">Slug <span class="req">*</span></label>
      <input type="text" name="slug" id="slugField" class="form-control" value="<?= h($post['slug'] ?? '') ?>" required>
      <div class="form-hint">URL-friendly identifier, e.g. best-marketing-tips</div>
    </div>
  </div>

  <div class="form-row-3">
    <div class="form-group">
      <label class="form-label">Category</label>
      <input type="text" name="category" class="form-control" value="<?= h($post['category'] ?? '') ?>" placeholder="e.g. Branding, AI, Marketing">
    </div>
    <div class="form-group">
      <label class="form-label">Author</label>
      <input type="text" name="author" class="form-control" value="<?= h($post['author'] ?? 'Acture Team') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Tags</label>
      <input type="text" name="tags" class="form-control" value="<?= h($post['tags'] ?? '') ?>" placeholder="tag1, tag2, tag3">
    </div>
  </div>

  <!-- Images -->
  <div class="form-group">
    <label class="form-label">Images</label>
    <div class="img-grid">
      <?php
      $imgFields = [
        'cover_image'     => 'Cover Image',
        'featured_image'  => 'Featured Image',
        'title_image'     => 'Blog Title Image',
        'long_card_image' => 'Long Card Image',
        'big_card_image'  => 'Big Card Image',
      ];
      foreach ($imgFields as $fname => $flabel):
        $val = $post[$fname] ?? '';
      ?>
      <div class="img-item">
        <label><?= $flabel ?></label>
        <div class="img-preview" onclick="document.getElementById('<?= $fname ?>').click()">
          <?php if ($val): ?>
            <img id="prev_<?= $fname ?>" src="<?= h($val) ?>" alt="">
          <?php else: ?>
            <div class="ph" id="prev_<?= $fname ?>">Click to upload<br>JPG/PNG/WebP · 5MB max</div>
          <?php endif; ?>
        </div>
        <input type="file" id="<?= $fname ?>" name="<?= $fname ?>" accept="image/jpeg,image/png,image/webp" style="display:none" onchange="previewImg(this,'<?= $fname ?>')">
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="form-group">
    <label class="form-label">Excerpt <span style="font-size:9px;color:#444">(shown in blog listing)</span></label>
    <textarea name="excerpt" class="form-control" rows="3" placeholder="Short summary shown in blog listing…"><?= h($post['excerpt'] ?? '') ?></textarea>
  </div>

  <!-- Content Editor -->
  <div class="form-group">
    <label class="form-label">Content</label>
    <div class="editor-toggle">
      <button type="button" class="editor-toggle-btn active" id="btnVisual">Visual</button>
      <button type="button" class="editor-toggle-btn" id="btnCode">HTML / Code</button>
    </div>
    <div id="paneVisual">
      <div id="quillEditor"></div>
    </div>
    <div id="paneCode">
      <textarea id="htmlEditor" placeholder="Paste raw HTML here…"></textarea>
    </div>
  </div>

  <div class="form-group" style="display:flex;align-items:center;gap:12px;padding:14px;background:#111;border:1px solid #1c1c1c;border-radius:4px">
    <input type="checkbox" id="published" name="published" <?= !empty($post['published']) ? 'checked' : '' ?> style="width:16px;height:16px;accent-color:#fff;cursor:pointer">
    <label for="published" style="font-size:13px;cursor:pointer">Publish immediately</label>
    <span style="font-size:11px;color:#444">(uncheck to save as draft)</span>
  </div>
</div>

<!-- ── SEO TAB ── -->
<div id="tabSEO" class="tab-pane">
  <div class="form-group">
    <label class="form-label">Meta Title <span style="font-size:9px;color:#444">(leave blank to use post title)</span></label>
    <input type="text" name="meta_title" class="form-control" value="<?= h($post['meta_title'] ?? '') ?>" placeholder="Recommended: 50–60 characters" maxlength="120">
    <div class="form-hint">Recommended: 50–60 characters</div>
  </div>
  <div class="form-group">
    <label class="form-label">Meta Description</label>
    <textarea name="meta_desc" class="form-control" rows="3" placeholder="150–160 characters recommended" maxlength="320"><?= h($post['meta_desc'] ?? '') ?></textarea>
    <div class="form-hint">150–160 characters recommended</div>
  </div>
  <div class="form-row">
    <div class="form-group">
      <label class="form-label">Meta Keywords</label>
      <input type="text" name="meta_keywords" class="form-control" value="<?= h($post['meta_keywords'] ?? '') ?>" placeholder="keyword1, keyword2, keyword3">
    </div>
    <div class="form-group">
      <label class="form-label">OG Image URL <span style="font-size:9px;color:#444">(defaults to cover image)</span></label>
      <input type="text" name="og_image_url" class="form-control" value="<?= h($post['og_image_url'] ?? '') ?>" placeholder="https://…">
    </div>
  </div>
  <div class="form-group">
    <label class="form-label">Canonical URL <span style="font-size:9px;color:#444">(leave blank for auto)</span></label>
    <input type="text" name="canonical_url" class="form-control" value="<?= h($post['canonical_url'] ?? '') ?>" placeholder="https://acturemedia.com/blog-details.php?slug=…">
  </div>
  <div class="form-group">
    <label class="form-label">Schema / Structured Data Script</label>
    <textarea name="schema_script" class="form-control" rows="8" style="font-family:'Courier New',monospace;font-size:12px" placeholder='<script type="application/ld+json">{ ... }</script>'><?= h($post['schema_script'] ?? '') ?></textarea>
    <div class="form-hint">Paste your full JSON-LD &lt;script&gt; block here.</div>
  </div>
</div>

</form>

<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
// ── Quill setup ──
const quill = new Quill('#quillEditor', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ header: [1,2,3,false] }],
      ['bold','italic','underline','strike'],
      [{ color: [] },{ background: [] }],
      [{ align: [] }],
      [{ list: 'ordered' },{ list: 'bullet' }],
      ['blockquote','code-block'],
      ['link','image'],
      ['clean']
    ]
  }
});
const existingHtml = document.getElementById('bodyHtmlField').value;
if (existingHtml.trim()) quill.clipboard.dangerouslyPasteHTML(existingHtml);

const paneVisual = document.getElementById('paneVisual');
const paneCode   = document.getElementById('paneCode');
const htmlEditor = document.getElementById('htmlEditor');
const btnVisual  = document.getElementById('btnVisual');
const btnCode    = document.getElementById('btnCode');

btnCode.addEventListener('click', () => {
  htmlEditor.value = quill.root.innerHTML;
  paneVisual.style.display = 'none';
  paneCode.style.display   = '';
  btnCode.classList.add('active');
  btnVisual.classList.remove('active');
});
btnVisual.addEventListener('click', () => {
  quill.clipboard.dangerouslyPasteHTML(htmlEditor.value);
  paneCode.style.display   = 'none';
  paneVisual.style.display = '';
  btnVisual.classList.add('active');
  btnCode.classList.remove('active');
});
document.getElementById('tplForm').addEventListener('submit', () => {
  const isCode = paneCode.style.display !== 'none';
  document.getElementById('bodyHtmlField').value = isCode ? htmlEditor.value : quill.root.innerHTML;
});

// ── Tabs ──
function switchTab(btn, paneId) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById(paneId).classList.add('active');
}

// ── Auto slug ──
let slugEdited = <?= $isEdit ? 'true' : 'false' ?>;
document.getElementById('slugField').addEventListener('input', () => slugEdited = true);
function autoSlug(val) {
  if (slugEdited) return;
  document.getElementById('slugField').value = val.toLowerCase().replace(/[^a-z0-9\s\-]/g,'').trim().replace(/\s+/g,'-');
}

// ── Image preview ──
function previewImg(input, field) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const el = document.getElementById('prev_' + field);
    if (el.tagName === 'IMG') {
      el.src = e.target.result;
    } else {
      const img = document.createElement('img');
      img.id = 'prev_' + field;
      img.src = e.target.result;
      el.replaceWith(img);
    }
  };
  reader.readAsDataURL(file);
}
</script>
<?php admin_foot(); ?>
