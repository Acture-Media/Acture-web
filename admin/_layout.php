<?php
function admin_head(string $title, string $extra = ''): void { ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($title) ?> — Acture Admin</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#0d0d0d;color:#fff;min-height:100vh}
a{color:inherit;text-decoration:none}
/* Layout */
.admin-wrap{display:flex;min-height:100vh}
.sidebar{width:230px;background:#000;border-right:1px solid #1c1c1c;display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;overflow-y:auto;z-index:200;transition:transform .25s ease}
.sidebar-logo{padding:22px 20px 18px;border-bottom:1px solid #1c1c1c}
.sidebar-logo img{height:28px}
.sidebar-nav{flex:1;padding:12px 0}
.sidebar-section{padding:14px 20px 4px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:#3a3a3a}
.sidebar-nav a{display:flex;align-items:center;gap:10px;padding:9px 20px;color:#666;font-size:12px;letter-spacing:.3px;transition:all .15s}
.sidebar-nav a svg{width:15px;height:15px;flex-shrink:0}
.sidebar-nav a:hover,.sidebar-nav a.active{color:#fff;background:#111}
.sidebar-footer{padding:16px 20px;border-top:1px solid #1c1c1c}
.sidebar-footer a{color:#3a3a3a;font-size:12px;display:flex;align-items:center;gap:8px;transition:color .15s}
.sidebar-footer a:hover{color:#fff}
.admin-main{margin-left:230px;flex:1;min-height:100vh}
.admin-topbar{background:#000;border-bottom:1px solid #1c1c1c;padding:14px 28px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.admin-topbar h1{font-size:16px;font-weight:500;letter-spacing:.3px}
.topbar-actions{display:flex;align-items:center;gap:10px}
.admin-content{padding:28px}
/* Hamburger */
.hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:4px;background:none;border:none}
.hamburger span{display:block;width:20px;height:1.5px;background:#fff;transition:all .25s}
/* Overlay */
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:150;opacity:0;transition:opacity .25s}
.sidebar-overlay.visible{opacity:1}
/* Cards */
.card{background:#141414;border:1px solid #222;border-radius:6px}
.card-header{padding:18px 22px;border-bottom:1px solid #222;display:flex;align-items:center;justify-content:space-between}
.card-header h2{font-size:12px;font-weight:500;text-transform:uppercase;letter-spacing:1px;color:#888}
.card-body{padding:22px}
/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-bottom:28px}
.stat-card{background:#141414;border:1px solid #222;border-radius:6px;padding:22px}
.stat-label{font-size:10px;text-transform:uppercase;letter-spacing:1px;color:#555;margin-bottom:8px}
.stat-value{font-size:34px;font-weight:600;line-height:1}
.stat-sub{font-size:11px;color:#444;margin-top:6px}
/* Table */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead tr{border-bottom:1px solid #222}
thead th{padding:11px 14px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:#555;white-space:nowrap;font-weight:500}
tbody tr{border-bottom:1px solid #1a1a1a;transition:background .1s}
tbody tr:hover{background:#181818}
tbody td{padding:13px 14px;font-size:13px}
.td-title{font-weight:500;max-width:300px}
.td-title a{color:#fff}
.td-title a:hover{opacity:.7}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
.badge-published{background:rgba(46,204,113,.12);color:#2ecc71}
.badge-draft{background:rgba(255,255,255,.06);color:#555}
/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:none;cursor:pointer;font-size:12px;font-weight:500;letter-spacing:.3px;transition:all .15s;border-radius:4px;line-height:1}
.btn-primary{background:#fff;color:#000}
.btn-primary:hover{background:#ddd}
.btn-outline{background:transparent;color:#fff;border:1px solid #2a2a2a}
.btn-outline:hover{border-color:#555}
.btn-danger{background:rgba(231,76,60,.12);color:#e74c3c;border:1px solid rgba(231,76,60,.25)}
.btn-danger:hover{background:rgba(231,76,60,.22)}
.btn-sm{padding:5px 11px;font-size:11px}
.btn-link{background:transparent;border:none;color:#666;font-size:12px;cursor:pointer;padding:5px}
.btn-link:hover{color:#fff}
.actions{display:flex;align-items:center;gap:4px}
/* Forms */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.8px;color:#666;margin-bottom:7px}
.form-label .req{color:#e74c3c}
.form-control{width:100%;background:#0d0d0d;border:1px solid #252525;color:#fff;padding:9px 12px;font-size:13px;border-radius:4px;transition:border-color .15s;font-family:inherit}
.form-control:focus{outline:none;border-color:#444}
textarea.form-control{resize:vertical;min-height:90px}
.form-hint{font-size:10px;color:#444;margin-top:5px}
/* Tabs */
.tab-bar{display:flex;border-bottom:1px solid #222;margin-bottom:22px}
.tab-btn{padding:11px 22px;font-size:12px;background:transparent;border:none;color:#444;cursor:pointer;border-bottom:2px solid transparent;transition:all .15s;letter-spacing:.3px}
.tab-btn:hover{color:#aaa}
.tab-btn.active{color:#fff;border-bottom-color:#fff}
.tab-pane{display:none}
.tab-pane.active{display:block}
/* Editor */
.editor-toggle{display:flex;gap:4px;margin-bottom:10px}
.editor-toggle-btn{padding:5px 13px;font-size:11px;background:#111;border:1px solid #222;color:#555;cursor:pointer;border-radius:3px;transition:all .15s}
.editor-toggle-btn.active{background:#fff;color:#000;border-color:#fff}
#paneCode{display:none}
#htmlEditor{width:100%;background:#080808;border:1px solid #222;color:#7ec8e3;padding:12px;font-family:'Courier New',monospace;font-size:12px;min-height:400px;border-radius:4px;resize:vertical}
/* Quill dark overrides */
.ql-toolbar.ql-snow{border-color:#252525 !important;background:#111;border-radius:4px 4px 0 0}
.ql-container.ql-snow{border-color:#252525 !important;border-radius:0 0 4px 4px}
.ql-toolbar.ql-snow .ql-stroke{stroke:#777 !important}
.ql-toolbar.ql-snow .ql-fill{fill:#777 !important}
.ql-toolbar.ql-snow button:hover .ql-stroke,.ql-toolbar.ql-snow button.ql-active .ql-stroke{stroke:#fff !important}
.ql-toolbar.ql-snow .ql-picker-label{color:#777 !important}
.ql-toolbar.ql-snow .ql-picker-options{background:#1a1a1a;border-color:#252525}
.ql-editor{min-height:380px;color:#fff;background:#111;font-size:14px}
.ql-editor.ql-blank::before{color:#333}
/* Images grid */
.img-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:14px}
.img-item label{display:block;font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px}
.img-preview{width:100%;aspect-ratio:16/9;background:#0d0d0d;border:1px dashed #252525;border-radius:4px;overflow:hidden;display:flex;align-items:center;justify-content:center;margin-bottom:7px;cursor:pointer;transition:border-color .15s}
.img-preview:hover{border-color:#444}
.img-preview img{width:100%;height:100%;object-fit:cover}
.img-preview .ph{font-size:10px;color:#333;text-align:center;padding:8px}
/* Alerts */
.alert{padding:12px 16px;border-radius:4px;margin-bottom:18px;font-size:13px}
.alert-success{background:rgba(46,204,113,.09);border:1px solid rgba(46,204,113,.25);color:#2ecc71}
.alert-error{background:rgba(231,76,60,.09);border:1px solid rgba(231,76,60,.25);color:#e74c3c}
/* Login */
.login-wrap{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;background:#0d0d0d}
.login-card{width:100%;max-width:380px;background:#141414;border:1px solid #222;border-radius:8px;padding:38px}
.login-logo{text-align:center;margin-bottom:30px}
.login-logo img{height:32px}
.login-title{font-size:13px;text-transform:uppercase;letter-spacing:1px;color:#555;text-align:center;margin-bottom:24px}
/* Misc */
.page-actions{display:flex;align-items:center;gap:10px;margin-bottom:22px;justify-content:space-between}
.page-actions-left{display:flex;align-items:center;gap:8px}
.text-muted{color:#444}
.mt-2{margin-top:8px}
.mt-3{margin-top:16px}
.truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:260px}

/* ── Mobile Responsive ── */
@media(max-width:768px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:translateX(0)}
  .admin-main{margin-left:0}
  .hamburger{display:flex}
  .admin-topbar{padding:12px 16px}
  .admin-content{padding:16px}
  .stats-grid{grid-template-columns:1fr 1fr}
  .form-row,.form-row-3{grid-template-columns:1fr}
  .img-grid{grid-template-columns:1fr 1fr}
  table{font-size:11px}
  tbody td{padding:10px 8px}
  thead th{padding:8px}
  .td-title{max-width:140px}
  .admin-topbar h1{font-size:13px}
  /* Settings 2-col → 1-col on mobile */
  div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr !important}
}
@media(max-width:480px){
  .stats-grid{grid-template-columns:1fr}
  .img-grid{grid-template-columns:1fr}
  .page-actions{flex-direction:column;align-items:flex-start}
  .btn-sm{padding:6px 10px}
}
</style>
<?= $extra ?>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="admin-wrap">
<nav class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <a href="index.php"><img src="../images/custom/acture-logo-light.png" alt="Acture Media" onerror="this.style.display='none';this.nextSibling.style.display='block'"><span style="display:none;font-size:14px;font-weight:600;letter-spacing:1px">ACTURE MEDIA</span></a>
  </div>
  <div class="sidebar-nav">
    <div class="sidebar-section">Main</div>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>
    <div class="sidebar-section">Content</div>
    <a href="blogs.php" class="<?= in_array(basename($_SERVER['PHP_SELF']), ['blogs.php','blog-form.php']) ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      Blog Posts
    </a>
    <div class="sidebar-section">Inbox</div>
    <a href="newsletters.php" class="<?= basename($_SERVER['PHP_SELF']) === 'newsletters.php' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Newsletter
    </a>
    <a href="contacts.php" class="<?= basename($_SERVER['PHP_SELF']) === 'contacts.php' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Contact Submissions
    </a>
    <div class="sidebar-section">Site</div>
    <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      Settings
    </a>
    <a href="../index.php" target="_blank">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      View Site
    </a>
  </div>
  <div class="sidebar-footer">
    <a href="logout.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Logout
    </a>
  </div>
</nav>
<div class="admin-main">
  <div class="admin-topbar">
    <div style="display:flex;align-items:center;gap:14px">
      <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
      <h1><?= h($title) ?></h1>
    </div>
    <div class="topbar-actions">
      <span style="font-size:11px;color:#444">[ <span style="color:#666"><?= h($_SESSION['admin_username'] ?? 'Admin') ?></span> ]</span>
      <a href="settings.php" style="font-size:11px;color:#333;margin-left:4px" title="Settings">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      </a>
    </div>
  </div>
  <div class="admin-content">
<?php }

function admin_foot(): void { ?>
  </div>
</div>
</div>
<script>
(function(){
  var btn  = document.getElementById('hamburger');
  var sb   = document.getElementById('sidebar');
  var ov   = document.getElementById('sidebarOverlay');
  if(!btn) return;
  function open(){sb.classList.add('open');ov.style.display='block';requestAnimationFrame(function(){ov.classList.add('visible')});}
  function close(){sb.classList.remove('open');ov.classList.remove('visible');setTimeout(function(){ov.style.display='none'},250);}
  btn.addEventListener('click',function(){sb.classList.contains('open')?close():open();});
  ov.addEventListener('click',close);
  // Close on nav link click (mobile)
  sb.querySelectorAll('a').forEach(function(a){a.addEventListener('click',function(){if(window.innerWidth<=768)close();});});
})();
</script>
</body>
</html>
<?php }
