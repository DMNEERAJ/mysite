<?php
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Settings;
use App\Services\CartService;

/** @var string $content */
$seo = $seo ?? ['title' => Settings::get('brand_name', 'Store'), 'description' => '', 'canonical' => url('/'), 'image' => url('assets/img/og-default.svg'), 'type' => 'website', 'noindex' => false, 'jsonld' => [], 'site_name' => Settings::get('brand_name', 'Store')];
$brand = Settings::get('brand_name', 'Aurora');
$primary = Settings::get('brand_primary_color', '#111111');
$accent = Settings::get('brand_accent_color', '#b45309');
$logo = Settings::get('logo_path', '');
$mainMenu = [];
$footerMenu = [];
try {
    $mainMenu = Database::all("SELECT * FROM menus WHERE menu = 'main' AND is_active = 1 ORDER BY sort_order, id");
    $footerMenu = Database::all("SELECT * FROM menus WHERE menu = 'footer' AND is_active = 1 ORDER BY sort_order, id");
} catch (Throwable) {
}
$cartCount = 0;
try { $cartCount = CartService::count(); } catch (Throwable) {
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($seo['title']) ?></title>
<meta name="description" content="<?= e($seo['description']) ?>">
<link rel="canonical" href="<?= e($seo['canonical']) ?>">
<?php if ($seo['noindex']): ?><meta name="robots" content="noindex, nofollow"><?php endif; ?>
<meta property="og:type" content="<?= e($seo['type']) ?>">
<meta property="og:title" content="<?= e($seo['title']) ?>">
<meta property="og:description" content="<?= e($seo['description']) ?>">
<meta property="og:url" content="<?= e($seo['canonical']) ?>">
<meta property="og:image" content="<?= e($seo['image']) ?>">
<meta property="og:site_name" content="<?= e($seo['site_name']) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($seo['title']) ?>">
<meta name="twitter:description" content="<?= e($seo['description']) ?>">
<meta name="twitter:image" content="<?= e($seo['image']) ?>">
<link rel="alternate" hreflang="x-default" href="<?= e($seo['canonical']) ?>">
<link rel="alternate" hreflang="en" href="<?= e($seo['canonical']) ?>">
<!-- Favicon set — files in /assets/img/ (favicon.ico bhi site root me). Badalna ho to wahi files replace karo + ?v= badhao. -->
<link rel="icon" href="<?= url('assets/img/favicon.svg?v=2') ?>" type="image/svg+xml">
<link rel="icon" href="<?= url('assets/img/favicon-32.png?v=2') ?>" sizes="32x32" type="image/png">
<link rel="icon" href="<?= url('assets/img/favicon-16.png?v=2') ?>" sizes="16x16" type="image/png">
<link rel="apple-touch-icon" href="<?= url('assets/img/apple-touch-icon.png?v=2') ?>">
<link rel="stylesheet" href="<?= url('assets/css/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= url('assets/css/bootstrap-icons.css') ?>">
<link rel="stylesheet" href="<?= url('assets/css/store.css') ?>">
<link rel="stylesheet" href="<?= url('assets/css/theme.css') ?>">
<style>
:root{--brand-primary:<?= e($primary) ?>;--brand-accent:<?= e($accent) ?>;}
</style>
<?php foreach ($seo['jsonld'] as $ld): ?>
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endforeach; ?>
<?php $gtmId = trim((string)Settings::get('gtm_id', '')); $ga4Id = trim((string)Settings::get('ga4_id', '')); ?>
<?php if ($gtmId !== ''): ?>
<!-- Google Tag Manager (free) — configure in Admin → Settings → SEO Controls -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= e($gtmId) ?>');</script>
<?php elseif ($ga4Id !== ''): ?>
<!-- Google Analytics 4 (free) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga4Id) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= e($ga4Id) ?>');</script>
<?php endif; ?>
</head>
<body>
<?php if ($gtmId !== ''): ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= e($gtmId) ?>" height="0" width="0" style="display:none;visibility:hidden" title="gtm"></iframe></noscript>
<?php endif; ?>
<?php if (Settings::get('announcement_text', '') !== ''): ?>
<div class="announcement-bar text-center"><?= e(Settings::get('announcement_text', '')) ?></div>
<?php endif; ?>

<header class="site-header sticky-top">
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('/') ?>">
        <?php if ($logo): ?><img src="<?= upload_url($logo) ?>" alt="<?= e($brand) ?> logo" class="brand-logo"><?php else: ?><span class="brand-mark"><?= e(mb_strtoupper(mb_substr($brand, 0, 1))) ?></span><?php endif; ?>
        <span class="brand-name"><?= e($brand) ?></span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Menu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= url('/shop') ?>">Shop</a></li>
          <?php foreach ($mainMenu as $mi): ?>
          <li class="nav-item"><a class="nav-link" href="<?= str_starts_with($mi['url'], 'http') ? e($mi['url']) : url($mi['url']) ?>"><?= e($mi['label']) ?></a></li>
          <?php endforeach; ?>
          <li class="nav-item"><a class="nav-link" href="<?= url('/blog') ?>">Blog</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= url('/about') ?>">About</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= url('/contact') ?>">Contact</a></li>
        </ul>
        <div class="d-flex align-items-center gap-3 header-actions">
          <form class="search-form d-none d-lg-flex" action="<?= url('/search') ?>" method="get" role="search">
            <input type="search" name="q" class="form-control form-control-sm" placeholder="Search products…" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search">
          </form>
          <a class="icon-link-item" href="<?= url('/account') ?>" title="Account"><i class="bi bi-person"></i></a>
          <a class="icon-link-item position-relative" href="<?= url('/cart') ?>" title="Cart">
            <i class="bi bi-bag"></i>
            <?php if ($cartCount > 0): ?><span class="cart-badge"><?= (int)$cartCount ?></span><?php endif; ?>
          </a>
        </div>
      </div>
    </div>
  </nav>
</header>

<main>
<?php
foreach (['success', 'error', 'info'] as $flashType):
    foreach (get_flashes($flashType) as $msg): ?>
<div class="container mt-3"><div class="alert alert-<?= $flashType === 'error' ? 'danger' : ($flashType === 'info' ? 'info' : 'success') ?> alert-dismissible fade show" role="alert">
  <?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div></div>
<?php endforeach; endforeach; ?>
<?= $content ?>
</main>

<footer class="site-footer mt-auto">
  <div class="container">
    <div class="row g-4 py-5">
      <div class="col-lg-4">
        <div class="footer-brand"><?= e($brand) ?></div>
        <p class="footer-about"><?= e(str_limit(Settings::get('about_short', 'Premium clothing, thoughtfully designed.'), 220)) ?></p>
        <div class="social-links">
          <?php if (Settings::get('social_instagram')): ?><a href="<?= e(Settings::get('social_instagram')) ?>" rel="noopener" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a><?php endif; ?>
          <?php if (Settings::get('social_facebook')): ?><a href="<?= e(Settings::get('social_facebook')) ?>" rel="noopener" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a><?php endif; ?>
          <?php if (Settings::get('social_x')): ?><a href="<?= e(Settings::get('social_x')) ?>" rel="noopener" target="_blank" aria-label="X"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
          <?php if (Settings::get('social_youtube')): ?><a href="<?= e(Settings::get('social_youtube')) ?>" rel="noopener" target="_blank" aria-label="YouTube"><i class="bi bi-youtube"></i></a><?php endif; ?>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-heading">Explore</h6>
        <ul class="footer-links">
          <li><a href="<?= url('/shop') ?>">Shop</a></li>
          <li><a href="<?= url('/blog') ?>">Blog</a></li>
          <li><a href="<?= url('/about') ?>">About Us</a></li>
          <li><a href="<?= url('/faq') ?>">FAQ</a></li>
          <li><a href="<?= url('/sitemap') ?>">Sitemap</a></li>
          <?php foreach ($footerMenu as $mi): ?><li><a href="<?= str_starts_with($mi['url'], 'http') ? e($mi['url']) : url($mi['url']) ?>"><?= e($mi['label']) ?></a></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="col-6 col-lg-3">
        <h6 class="footer-heading">Policies</h6>
        <ul class="footer-links">
          <li><a href="<?= url('/page/privacy-policy') ?>">Privacy Policy</a></li>
          <li><a href="<?= url('/page/terms') ?>">Terms &amp; Conditions</a></li>
          <li><a href="<?= url('/page/shipping-policy') ?>">Shipping Policy</a></li>
          <li><a href="<?= url('/page/return-policy') ?>">Return &amp; Refund Policy</a></li>
          <li><a href="<?= url('/contact') ?>">Contact Us</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <h6 class="footer-heading">Newsletter</h6>
        <p class="footer-about">Style notes, drops and offers — straight to your inbox.</p>
        <form action="<?= url('/newsletter/subscribe') ?>" method="post" class="newsletter-form" data-ajax-newsletter>
          <?= Csrf::field() ?>
          <div class="input-group">
            <input type="email" name="email" class="form-control" placeholder="you@email.com" required aria-label="Email address">
            <button class="btn btn-brand" type="submit">Join</button>
          </div>
          <small class="newsletter-msg"></small>
        </form>
      </div>
    </div>
    <div class="footer-bottom py-3 d-flex flex-wrap justify-content-between gap-2">
      <span>© <?= date('Y') ?> <?= e($brand) ?>. All rights reserved.</span>
      <span class="footer-contact"><?= e(Settings::get('contact_email', '')) ?></span>
    </div>
  </div>
</footer>

<script src="<?= url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= url('assets/js/store.js') ?>"></script>
<script src="<?= url('assets/js/motion.js') ?>"></script>
</body>
</html>
