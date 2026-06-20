<?php
require_once 'includes/db.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: blogs.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ? AND published = 1");
$stmt->execute([$slug]);
$post = $stmt->fetch();
if (!$post) { http_response_code(404); header('Location: blogs.php'); exit; }

// Related posts: same category, excluding current
$related = [];
if ($post['category']) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE published=1 AND category=? AND id != ? ORDER BY created_at DESC LIMIT 3");
    $stmt->execute([$post['category'], $post['id']]);
    $related = $stmt->fetchAll();
}

$metaTitle = $post['meta_title'] ?: $post['title'];
$metaDesc  = $post['meta_desc'] ?: $post['excerpt'];
$ogImage   = $post['og_image_url'] ?: $post['cover_image'];
$canonical = $post['canonical_url'] ?: 'https://acturemedia.com/blog-details.php?slug=' . urlencode($slug);
$readTime  = max(1, (int)ceil(str_word_count(strip_tags($post['body_html'])) / 200)) . ' minutes';
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($metaTitle) ?> | Acture Media</title>
  <?php if ($metaDesc): ?><meta name="description" content="<?= h($metaDesc) ?>"><?php endif; ?>
  <?php if ($post['meta_keywords']): ?><meta name="keywords" content="<?= h($post['meta_keywords']) ?>"><?php endif; ?>
  <link rel="canonical" href="<?= h($canonical) ?>">
  <?php if ($ogImage): ?>
  <meta property="og:image" content="<?= h($ogImage) ?>">
  <?php endif; ?>
  <meta property="og:title" content="<?= h($metaTitle) ?>">
  <?php if ($metaDesc): ?><meta property="og:description" content="<?= h($metaDesc) ?>"><?php endif; ?>
  <?php if ($post['schema_script']): echo $post['schema_script']; endif; ?>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/acture-icons.css">
  <link rel="stylesheet" href="css/meanmenu.css">
  <link rel="stylesheet" href="css/swiper.min.css">
  <link rel="stylesheet" href="css/venobox.min.css">
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <div class="tj-preloader">
    <svg viewBox="0 0 1000 1000" preserveAspectRatio="none"><path id="preloaderSvg" d="M0,1005S175,995,500,995s500,5,500,5V0H0Z"></path></svg>
    <div class="loading-container"><div class="loading-circle"></div><div id="loading-icon"><img src="images/custom/acture-logo-light.png" alt="Loading"></div></div>
    <div class="tj-preloader_bottom"><div class="loading_text">[ <span>Loading Please wait...</span> ]</div><div class="copyright_text">[ <span>©2026</span> ]</div></div>
  </div>

  <div class="tj-offcanvas-overlay"></div>
  <div class="tj-offcanvas">
    <div class="offcanvas_bg"></div>
    <div class="offcanvas_wrapper">
      <div class="offcanvas_top d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="offcanvas_logo"><a href="index.php" class="logo"><img src="images/custom/acture-logo-dark 1.png" alt="LOGO"></a></div>
        <button class="offcanvas_close"><span class="close-text">Close</span><span class="tj_sidebar_toggle"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span></button>
      </div>
      <div class="offcanvas_action d-none d-lg-block"><h5 class="greetings">Glad you're here! <img src="images/start.svg" alt=""></h5><a href="about.php" class="tj_text_btn">Know more us<i class="tji-arrow-right"></i></a></div>
      <div class="offcanvas_menu mobile_menu d-lg-none"></div>
      <div class="offcanvas_contact tj_contact address"><div class="sec_subtitle contact_title">[ <span>Location</span> ]</div><div class="contact_info">1102 Ajmera Sikova, Laal Bahadur Shastri Marg, Nityanand Nagar, Ghatkopar West, <br>Mumbai - 400086</div></div>
      <div class="offcanvas_contact tj_contact contact"><div class="sec_subtitle contact_title">[ <span>Contact</span> ]</div><a href="tel:+918082233227" class="contact_info">+918082233227</a><a href="mailto:info@acturemedia.com" class="contact_info">info@acturemedia.com</a></div>
      <ul class="offcanvas_socials tj_socials"><li><a href="https://facebook.com" target="_blank"><i class="tji-facebook"></i></a></li><li><a href="https://linkedin.com" target="_blank"><i class="tji-linkedin"></i></a></li><li><a href="https://instagram.com" target="_blank"><i class="tji-instagram"></i></a></li><li><a href="https://twitter.com" target="_blank"><i class="tji-x-twitter"></i></a></li></ul>
    </div>
  </div>

  <div class="tj_navigation_wrap_overlay"></div>
  <header class="tj-header header-1 header-absolute">
    <div class="container-fluid"><div class="row"><div class="col">
      <div class="tj-header_wrap">
        <div class="site_logo"><a class="logo" href="index.php"><img src="./images/custom/acture-logo-dark 1.png" alt="LOGO"></a></div>
        <div class="site_navigation d-none d-lg-inline-flex">
          <nav id="mobile-menu"><ul>
            <li><a href="./index.php">Home</a></li>
            <li><a href="./about.php">Discover us</a></li>
            <li class="has-dropdown"><a href="./services.php">Services</a>
              <ul class="sub-menu">
                <li><a href="./services.php">BRAND FOUNDATION STUDIO</a></li>
                <li><a href="./service-founder-authority.php">FOUNDER AUTHORITY ENGINES</a></li>
                <li><a href="./service-demand-pipeline.php">DEMAND PIPELINE SYSTEMS</a></li>
                <li><a href="./service-content-scale.php">CONTENT AT SCALE</a></li>
                <li><a href="./service-brand-storytelling.php">CONTENT &amp; BRAND STORYTELLING</a></li>
                <li><a href="./service-search-engine-optimization.php">SEO, BUT LET'S MAKE IT AI.</a></li>
                <li><a href="./service-digital-presence.php">DIGITAL PRESENCE ENGINEERING</a></li>
                <li><a href="./service-conversion-intelligence.php">CONVERSION INTELLIGENCE</a></li>
              </ul>
            </li>
            <li class="has-dropdown"><a href="#">Resources</a><ul class="sub-menu"><li><a href="./blogs.php">Blogs</a></li><li><a href="./case-studies.php">Case Studies</a></li></ul></li>
            <li><a href="./careers.php">Careers</a></li>
          </ul></nav>
        </div>
        <div class="tj-header_right">
          <a href="./ai-leap.php" class="tj_btn custom-button flip-text-wrap d-none d-md-inline-flex gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/></svg>
            <span class="flip-text">AI Leap</span>
          </a>
          <a href="contact.php" class="tj_btn bg-black flip-text-wrap d-none d-md-inline-flex"><span class="flip-text">Get in touch</span></a>
          <button class="tj_sidebar_toggle"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></button>
        </div>
      </div>
    </div></div></div>
  </header>

  <div class="has-smooth" id="has_smooth"></div>
  <div id="smooth-wrapper">
    <div id="smooth-content">
      <main>
        <section class="tj_blog_details container-space">
          <div class="inner_top_gap"></div>
          <div class="container-fluid">
            <div class="tj_blog_details_header row">
              <div class="tj_blog_details_header_left col-lg-4 tj-fade">
                <a href="blogs.php" class="tj_icon_btn text-underline-btn blog_btn flip-text-wrap">
                  <div class="icon_btn"><span><i class="tji-arrow-left"></i><i class="tji-arrow-left"></i></span></div>
                  <span class="underline-text flip-text">Back to blog</span>
                </a>
              </div>
              <div class="tj_blog_details_header_right col-lg-8 tj-fade">
                <div class="blog_meta_item categories"><a href="blogs.php?cat=<?= urlencode($post['category']) ?>" class="category"><?= h($post['category'] ?: 'General') ?></a></div>
                <h1 class="tj_blog_details_title title-anim"><?= h($post['title']) ?></h1>
                <div class="blog-date">
                  <div class="blog-date-item"><span class="blog-date-title">Date:</span> <?= date('d - m - Y', strtotime($post['created_at'])) ?></div>
                  <div class="blog-date-item"><span class="blog-date-title">Time to read:</span> <?= $readTime ?></div>
                  <div class="blog-date-item"><span class="blog-date-title">Author:</span> <?= h($post['author']) ?></div>
                </div>
              </div>
            </div>
            <div class="row row-gap-5 flex-column-reverse flex-lg-row">
              <div class="col-lg-4">
                <div class="tj_blog_sidebar">
                  <?php if (!empty($related)): ?>
                  <div class="tj_blog_related pt-0">
                    <span class="sec_subtitle tj-fade">[ <span>Related Post</span> ]</span>
                    <div class="tj_blog_grid tj-fade">
                    <?php foreach ($related as $r): ?>
                    <article class="tj_blog_item tj_blog_item_standard">
                      <a href="blog-details.php?slug=<?= urlencode($r['slug']) ?>" class="blog_image">
                        <img src="<?= h(!empty($r['cover_image']) ? $r['cover_image'] : 'images/h1-blog-img-1.webp') ?>" alt="<?= h($r['title']) ?>">
                      </a>
                      <div class="blog_content">
                        <div class="blog_meta">
                          <div class="blog_meta_item categories"><a href="blogs.php?cat=<?= urlencode($r['category']) ?>" class="category"><?= h($r['category'] ?: 'General') ?></a></div>
                          <div class="blog_meta_item date"><span><?= date('d - m - Y', strtotime($r['created_at'])) ?></span></div>
                        </div>
                        <div class="blog_title ff-heading fw-sbold"><a href="blog-details.php?slug=<?= urlencode($r['slug']) ?>"><?= h($r['title']) ?></a></div>
                        <div class="blog_desc"><p><?= h($r['excerpt'] ?: '') ?></p></div>
                        <a href="blog-details.php?slug=<?= urlencode($r['slug']) ?>" class="tj_icon_btn text-underline-btn blog_btn">
                          <div class="icon_btn"><span><i class="tji-arrow-right"></i><i class="tji-arrow-right"></i></span></div>
                          <span class="underline-text">Read more</span>
                        </a>
                      </div>
                    </article>
                    <?php endforeach; ?>
                    </div>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="tj_blog_details_body">
                  <?php if (!empty($post['title_image']) || !empty($post['cover_image'])): ?>
                  <div class="tj_blog_details_image tj-fade">
                    <img src="<?= h(!empty($post['title_image']) ? $post['title_image'] : $post['cover_image']) ?>" alt="<?= h($post['title']) ?>">
                  </div>
                  <?php endif; ?>
                  <div class="tj-fade blog-body-content">
                    <?= $post['body_html'] ?>
                  </div>
                  <?php if ($post['tags']): ?>
                  <div style="margin-top:24px;display:flex;flex-wrap:wrap;gap:8px">
                    <?php foreach (explode(',', $post['tags']) as $tag): if (!trim($tag)) continue; ?>
                    <span style="padding:4px 12px;border:1px solid #ddd;font-size:12px;border-radius:20px"><?= h(trim($tag)) ?></span>
                    <?php endforeach; ?>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="h1-cta-section container-space fix">
          <div class="bg_image" data-bg-image="./images/custom/cta-anim.gif"></div>
          <div class="container-fluid"><div class="row"><div class="col"><div class="h1_cta_wrapper">
            <h2 class="cta_title fs-120 tj-reveal-line">Let's talk!</h2>
            <div class="cta_buttons d-inline-flex flex-wrap tj-fade" data-direction="right">
              <a href="contact.php" class="tj_btn bg-black flip-text-wrap"><div class="name flip-text">Book a demo call</div></a>
              <a href="contact.php" class="tj_bordered_btn flip-text-wrap"><span class="flip-text">Make It Happen</span></a>
            </div>
          </div></div></div></div>
        </section>
      </main>

      <footer class="h1-footer-area footer-1 tj-theme-dark">
        <div class="h1_footer_widgets container-space"><div class="container-fluid"><div class="row"><div class="col">
          <div class="h1_footer_widgets_wrap">
            <div class="tj_footer_widget footer_info tj-fade" data-delay=".5">
              <div class="footer_logo"><a href="index.php" class="logo"><img src="images/custom/acture-logo-light.png" alt="Logo"></a></div>
              <div class="subscription_wrap">
                <div class="footer_desc">Subscribe to our newsletter and get the latest design inspiration.</div>
                <div class="footer_subscription">
                  <form action="newsletter.php" method="POST">
                    <input type="email" name="email" placeholder="Email*" required>
                    <button type="submit" class="icon_btn"><span><i class="tji-arrow-right"></i><i class="tji-arrow-right"></i></span></button>
                  </form>
                </div>
              </div>
            </div>
            <hr class="tj-fade" data-delay=".5">
            <div class="tj_footer_widget widget-nav-menu tj-fade" data-delay=".6"><h3 class="widget_title">[ <span>Company</span> ]</h3><ul><li><a class="flip-text-wrap" href="index.php"><span class="flip-text">Home</span></a></li><li><a class="flip-text-wrap" href="about.php"><span class="flip-text">About us</span></a></li><li><a class="flip-text-wrap" href="services.php"><span class="flip-text">Services</span></a></li><li><a class="flip-text-wrap" href="blogs.php"><span class="flip-text">Blog</span></a></li><li><a class="flip-text-wrap" href="contact.php"><span class="flip-text">Contact</span></a></li></ul></div>
            <hr class="tj-fade" data-delay=".6">
            <div class="tj_footer_widget widget-nav-menu tj-fade" data-delay=".7"><h3 class="widget_title">[ <span>Services</span> ]</h3><ul><li><a class="flip-text-wrap" href="services.php"><span class="flip-text">Brand Foundation Studio</span></a></li><li><a class="flip-text-wrap" href="service-digital-presence.php"><span class="flip-text">Digital Presence</span></a></li><li><a class="flip-text-wrap" href="service-conversion-intelligence.php"><span class="flip-text">Conversion Intelligence</span></a></li><li><a class="flip-text-wrap" href="service-brand-storytelling.php"><span class="flip-text">Brand Storytelling</span></a></li><li><a class="flip-text-wrap" href="service-demand-pipeline.php"><span class="flip-text">Demand Pipeline</span></a></li></ul></div>
            <hr class="tj-fade" data-delay=".7">
            <div class="tj_footer_widget footer_contact tj-fade" data-delay=".8">
              <div class="tj_footer_contact"><a href="tel:+918082233227" class="contact_link tj-random-char-blink">+91 80822 33227</a><a href="mailto:info@acturemedia.com" class="contact_link tj-random-char-blink">info@acturemedia.com</a></div>
              <ul class="tj_socials"><li><a href="https://facebook.com" target="_blank"><i class="tji-facebook"></i></a></li><li><a href="https://linkedin.com" target="_blank"><i class="tji-linkedin"></i></a></li><li><a href="https://instagram.com" target="_blank"><i class="tji-instagram"></i></a></li><li><a href="https://twitter.com" target="_blank"><i class="tji-x-twitter"></i></a></li></ul>
              <div class="tj_contact"><div class="sec_subtitle contact_title">[ <span>Location</span> ]</div><div class="contact_info">1102 Ajmera Sikova, Laal Bahadur Shastri Marg, Nityanand Nagar, Ghatkopar West, Mumbai - 400086</div></div>
            </div>
          </div>
        </div></div></div></div>
        <div class="h1_footer_bottom container-space"><div class="container-fluid"><div class="row"><div class="col"><div class="h1_footer_bottom_wrap">
          <nav class="tj_footer_nav"><ul><li><a href="policy-privacy.php">Policy &amp; privacy</a></li><li><a href="policy-privacy.php">Term &amp; condition</a></li></ul></nav>
          <div class="tj_copyright">©<span>2026</span> <a href="index.php" target="_blank">Acture media </a>| All right reserved.</div>
          <button id="back_to_top" type="button" class="tj_back_to_top">Back to top <span><i class="tji-arrow-up"></i><i class="tji-arrow-up"></i></span></button>
        </div></div></div></div></div>
      </footer>
    </div>
  </div>

  <script src="js/jquery.min.js"></script><script src="js/bootstrap.bundle.min.js"></script><script src="js/gsap.min.js"></script><script src="js/gsap-scroll-trigger.min.js"></script><script src="js/gsap-scroll-smoother.js"></script><script src="js/gsap-scroll-to-plugin.min.js"></script><script src="js/gsap-split-text.min.js"></script><script src="js/gsap-custom-easc.min.js"></script><script src="js/meanmenu.js"></script><script src="js/swiper.min.js"></script><script src="js/magiccursor.js"></script><script src="js/venobox.min.js"></script><script src="js/three.js"></script><script src="js/hover-effect.umd.js"></script><script src="js/webgl.js"></script><script src="js/window-shape-animation.js"></script><script src="js/preloader.js"></script><script src="js/imagesloaded-pkgd.js"></script><script src="js/isotope.pkgd.min.js"></script><script src="js/gsap-custom-animations.js"></script><script src="js/main.js"></script>
</body>
</html>
