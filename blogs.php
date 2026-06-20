<?php
require_once 'includes/db.php';

// Featured: latest 3 published
$featured = $pdo->query("SELECT * FROM blogs WHERE published=1 ORDER BY created_at DESC LIMIT 3")->fetchAll();
// All published for grid
$allPosts = $pdo->query("SELECT * FROM blogs WHERE published=1 ORDER BY created_at DESC")->fetchAll();
// Distinct categories
$cats = $pdo->query("SELECT DISTINCT category FROM blogs WHERE published=1 AND category != '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

function blogUrl(array $p): string {
    return 'blog-details.php?slug=' . urlencode($p['slug']);
}
function blogImg(array $p, string ...$fields): string {
    foreach ($fields as $f) {
        if (!empty($p[$f])) return h($p[$f]);
    }
    return 'images/h1-blog-img-1.webp';
}
function blogDate(string $d): string {
    return date('d - m - Y', strtotime($d));
}
function slugClass(string $cat): string {
    return strtolower(preg_replace('/[^a-z0-9]/i', '-', $cat));
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Insights, ideas and trends from Acture Media">
  <title>Acture Media | Blogs</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/acture-icons.css">
  <link rel="stylesheet" href="css/meanmenu.css">
  <link rel="stylesheet" href="css/swiper.min.css">
  <link rel="stylesheet" href="css/venobox.min.css">
  <link rel="stylesheet" href="css/main.css">
  <?php if (!empty($_GET['sub'])): ?>
  <style>.nl-msg{display:block !important}</style>
  <?php endif; ?>
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
            <li class="has-dropdown"><a href="#">Resources</a>
              <ul class="sub-menu"><li><a href="./blogs.php">Blogs</a></li><li><a href="./case-studies.php">Case Studies</a></li></ul>
            </li>
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
        <div class="inner_top_gap"></div>

        <!-- Newsletter success notice (hidden by default, shown via CSS when ?sub=1) -->
        <div class="nl-msg" style="display:none;background:#2ecc71;color:#000;text-align:center;padding:10px 20px;font-size:13px;font-weight:500">
          <?= $_GET['sub'] === '1' ? 'Thank you for subscribing!' : 'Something went wrong. Please try again.' ?>
        </div>

        <!-- Blog Featured Section -->
        <section class="tj_blog_featured container-space">
          <div class="container-fluid"><div class="row"><div class="col">
            <div class="section_heading">
              <h6 class="sec_subtitle tj-fade">[ <span>Ideas &amp; Trends</span> ]</h6>
              <h1 class="sec_title tj-reveal-line">Tips and insights</h1>
            </div>
            <div class="tj_blog_featured_wrapper">
            <?php if (empty($featured)): ?>
              <p style="color:#666;padding:20px 0">No blog posts published yet. Check back soon.</p>
            <?php else: ?>
              <?php foreach ($featured as $i => $p): ?>
              <article class="tj_blog_item tj_blog_item_standard tj-fade" data-direction="<?= $i===0?'left':'right' ?>">
                <a href="<?= blogUrl($p) ?>" class="blog_image"><img src="<?= blogImg($p,'featured_image','cover_image','big_card_image') ?>" alt="<?= h($p['title']) ?>"></a>
                <div class="blog_content">
                  <div class="blog_meta">
                    <div class="blog_meta_item categories"><a href="blogs.php?cat=<?= urlencode($p['category']) ?>" class="category"><?= h($p['category'] ?: 'General') ?></a></div>
                    <div class="blog_meta_item date"><span><?= blogDate($p['created_at']) ?></span></div>
                  </div>
                  <?php $tag = $i===0 ? 'h2' : 'h3'; ?>
                  <<?= $tag ?> class="blog_title"><a class="reveal-hover-text" href="<?= blogUrl($p) ?>"><?= h($p['title']) ?></a></<?= $tag ?>>
                  <div class="blog_desc"><p><?= h($p['excerpt'] ?: '') ?></p></div>
                  <a href="<?= blogUrl($p) ?>" class="tj_icon_btn text-underline-btn <?= $i===0?'light-btn':'' ?> blog_btn">
                    <div class="icon_btn"><span><i class="tji-arrow-right"></i><i class="tji-arrow-right"></i></span></div>
                    <span class="underline-text">Read more</span>
                  </a>
                </div>
              </article>
              <?php endforeach; ?>
            <?php endif; ?>
            </div>
          </div></div></div>
        </section>

        <!-- Blog Grid Section -->
        <section class="tj_blog_grid_area container-space tj_filter section-divider fix">
          <div class="container-fluid"><div class="row"><div class="col">
            <div class="tj_blog_grid_header d-flex flex-wrap align-items-center justify-content-between gap-4">
              <div class="section_heading"><h2 class="sec_title tj-reveal-line">All Post</h2></div>
              <div class="tj_filter_btn_group category-wrap">
                <button data-filter="*" class="tj_filter_btn category flip-text-wrap active"><span class="flip-text">All</span></button>
                <?php foreach ($cats as $cat): ?>
                <button data-filter=".<?= slugClass($cat) ?>" class="tj_filter_btn category flip-text-wrap"><span class="flip-text"><?= h($cat) ?></span></button>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="tj_blog_grid tj_filter_item_wrapper">
            <?php foreach ($allPosts as $p):
              $cls = slugClass($p['category'] ?? '');
            ?>
            <article class="tj_blog_item tj_blog_item_standard tj_filter_item <?= h($cls) ?>">
              <a href="<?= blogUrl($p) ?>" class="blog_image"><img src="<?= blogImg($p,'long_card_image','cover_image','featured_image') ?>" alt="<?= h($p['title']) ?>"></a>
              <div class="blog_content">
                <div class="blog_meta">
                  <div class="blog_meta_item categories"><a href="blogs.php?cat=<?= urlencode($p['category']) ?>" class="category"><?= h($p['category'] ?: 'General') ?></a></div>
                  <div class="blog_meta_item date"><span><?= blogDate($p['created_at']) ?></span></div>
                </div>
                <h3 class="blog_title"><a class="reveal-hover-text" href="<?= blogUrl($p) ?>"><?= h($p['title']) ?></a></h3>
                <div class="blog_desc"><p><?= h($p['excerpt'] ?: '') ?></p></div>
                <a href="<?= blogUrl($p) ?>" class="tj_icon_btn text-underline-btn blog_btn">
                  <div class="icon_btn"><span><i class="tji-arrow-right"></i><i class="tji-arrow-right"></i></span></div>
                  <span class="underline-text">Read more</span>
                </a>
              </div>
            </article>
            <?php endforeach; ?>
            <?php if (empty($allPosts)): ?>
              <p style="color:#666;padding:20px 0">No posts published yet.</p>
            <?php endif; ?>
            </div>
          </div></div></div>
        </section>

        <!-- CTA Section -->
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

  <script src="js/jquery.min.js"></script><script src="js/bootstrap.bundle.min.js"></script><script src="js/gsap.min.js"></script><script src="js/gsap-scroll-trigger.min.js"></script><script src="js/gsap-scroll-smoother.js"></script><script src="js/gsap-scroll-to-plugin.min.js"></script><script src="js/gsap-split-text.min.js"></script><script src="js/gsap-custom-easc.min.js"></script><script src="js/meanmenu.js"></script><script src="js/swiper.min.js"></script><script src="js/magiccursor.js"></script><script src="js/venobox.min.js"></script><script src="js/three.js"></script><script src="js/hover-effect.umd.js"></script><script src="js/webgl.js"></script><script src="js/imagesloaded-pkgd.js"></script><script src="js/isotope.pkgd.min.js"></script><script src="js/preloader.js"></script><script src="js/window-shape-animation.js"></script><script src="js/gsap-custom-animations.js"></script><script src="js/main.js"></script>
</body>
</html>
