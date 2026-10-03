<?php
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Guard;
use App\Core\Settings;

/** @var string $content */
$user = $user ?? \App\Core\Auth::admin() ?? ['name' => 'Guest', 'role' => 'staff'];
$brand = Settings::get('brand_name', 'Aurora');
$unreadNotifications = [];
try {
    $unreadNotifications = Database::all('SELECT * FROM notifications WHERE is_read = 0 ORDER BY id DESC LIMIT 8');
} catch (Throwable) {
}
$pendingCount = 0;
try {
    $pendingCount = (int)Database::val("SELECT (SELECT COUNT(*) FROM articles WHERE status = 'pending_review') + (SELECT COUNT(*) FROM media WHERE source = 'ai' AND status = 'pending')");
} catch (Throwable) {
}

$nav = [
    'dashboard' => ['Dashboard', '/admin', 'speedometer2'],
    'products' => ['Products', '/admin/products', 'bag'],
    'categories' => ['Categories', '/admin/categories', 'grid'],
    'orders' => ['Orders', '/admin/orders', 'receipt'],
    'customers' => ['Customers', '/admin/customers', 'people'],
    'articles' => ['Articles', '/admin/articles', 'journal-text'],
    'ai' => ['AI Content', '/admin/ai', 'stars'],
    'media' => ['Media Library', '/admin/media', 'images'],
    'marketing' => ['Coupons', '/admin/coupons', 'ticket-perforated'],
    'menus' => ['Menus', '/admin/menus', 'list-nested'],
    'pages' => ['Pages', '/admin/pages', 'file-earmark-text'],
    'comments' => ['Comments', '/admin/comments', 'chat-dots'],
    'seo' => ['Redirects (SEO)', '/admin/redirects', 'signpost-split'],
    'seohealth' => ['SEO Health', '/admin/seo-health', 'heart-pulse'],
    'newsletter' => ['Newsletter', '/admin/newsletter', 'envelope-heart'],
    'users.view' => ['Users', '/admin/users', 'person-gear'],
    'backups' => ['Backups', '/admin/backups', 'cloud-arrow-down'],
    'logs' => ['Activity & Messages', '/admin/logs', 'clock-history'],
];
$settingsTabs = [
    'settings.general' => ['General', '/admin/settings/general'],
    'settings.brand' => ['Brand Guidelines', '/admin/settings/brand'],
    'settings.ai' => ['AI Configuration', '/admin/settings/ai'],
    'settings.payments' => ['Payments', '/admin/settings/payments'],
    'settings.shipping' => ['Shipping & Tax', '/admin/settings/shipping'],
    'settings.mail' => ['Email', '/admin/settings/mail'],
    'settings.seo' => ['SEO Controls', '/admin/settings/seo'],
];
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($seoTitle ?? 'Admin') ?> — <?= e($brand) ?> Admin</title>
<!-- Favicon set — files in /assets/img/ (favicon.ico bhi site root me). Badalna ho to wahi files replace karo + ?v= badhao. -->
<link rel="icon" href="<?= url('assets/img/favicon.svg?v=2') ?>" type="image/svg+xml">
<link rel="icon" href="<?= url('assets/img/favicon-32.png?v=2') ?>" sizes="32x32" type="image/png">
<link rel="icon" href="<?= url('assets/img/favicon-16.png?v=2') ?>" sizes="16x16" type="image/png">
<link rel="apple-touch-icon" href="<?= url('assets/img/apple-touch-icon.png?v=2') ?>">
<link rel="stylesheet" href="<?= url('assets/css/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= url('assets/css/bootstrap-icons.css') ?>">
<link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>">
</head>
<body class="admin-body">
<div class="admin-wrapper">
  <aside class="admin-sidebar">
    <div class="sidebar-brand">
      <span class="brand-mark"><?= e(mb_strtoupper(mb_substr($brand, 0, 1))) ?></span>
      <div><div class="brand-name"><?= e($brand) ?></div><small>Admin Panel</small></div>
    </div>
    <nav class="sidebar-nav">
      <?php foreach ($nav as $perm => [$label, $href, $icon]):
          if (!Guard::can($user, $perm)) continue;
          $active = $currentPath === $href || ($href !== '/admin' && str_starts_with($currentPath, $href)); ?>
      <a href="<?= url($href) ?>" class="sidebar-link<?= $active ? ' active' : '' ?>">
        <i class="bi bi-<?= e($icon) ?>"></i> <?= e($label) ?>
        <?php if ($perm === 'ai' && $pendingCount > 0): ?><span class="badge-count"><?= $pendingCount ?></span><?php endif; ?>
      </a>
      <?php endforeach; ?>

      <div class="sidebar-heading">Settings</div>
      <?php foreach ($settingsTabs as $perm => [$label, $href]):
          if (!Guard::can($user, $perm)) continue; ?>
      <a href="<?= url($href) ?>" class="sidebar-link<?= $currentPath === $href ? ' active' : '' ?>"><i class="bi bi-gear"></i> <?= e($label) ?></a>
      <?php endforeach; ?>

      <a href="<?= url('/') ?>" target="_blank" class="sidebar-link mt-3"><i class="bi bi-box-arrow-up-right"></i> View Website</a>
      <form method="post" action="<?= url('/admin/logout') ?>" class="mt-1"><?= Csrf::field() ?>
        <button type="submit" class="sidebar-link sidebar-logout"><i class="bi bi-box-arrow-left"></i> Sign Out</button>
      </form>
    </nav>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle" aria-label="Toggle menu"><i class="bi bi-list"></i></button>
      <div class="topbar-title"><?= e($seoTitle ?? 'Dashboard') ?></div>
      <div class="ms-auto d-flex align-items-center gap-3">
        <div class="dropdown">
          <a class="topbar-icon position-relative" href="#" data-bs-toggle="dropdown" aria-label="Notifications">
            <i class="bi bi-bell"></i>
            <?php if ($unreadNotifications): ?><span class="dot"></span><?php endif; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-end notification-menu">
            <?php if (!$unreadNotifications): ?><span class="dropdown-item-text text-muted small">No new notifications.</span><?php endif; ?>
            <?php foreach ($unreadNotifications as $n): ?>
              <a class="dropdown-item notification-item" href="<?= e($n['link'] ? $n['link'] : '#') ?>">
                <strong><?= e($n['title']) ?></strong>
                <?php if ($n['body']): ?><small class="d-block text-muted"><?= e(str_limit($n['body'], 90)) ?></small><?php endif; ?>
              </a>
            <?php endforeach; ?>
            <?php if ($unreadNotifications): ?>
            <form method="post" action="<?= url('/admin/notifications/read') ?>"><?= Csrf::field() ?>
              <button class="dropdown-item text-center small text-primary">Mark all as read</button></form>
            <?php endif; ?>
          </div>
        </div>
        <span class="topbar-user"><i class="bi bi-person-circle me-1"></i><?= e($user['name']) ?> <small class="text-muted">(<?= e(str_replace('_', ' ', $user['role'])) ?>)</small></span>
      </div>
    </header>

    <div class="admin-content">
      <?php foreach (['success', 'error', 'info'] as $flashType):
          foreach (get_flashes($flashType) as $msg): ?>
      <div class="alert alert-<?= $flashType === 'error' ? 'danger' : ($flashType === 'info' ? 'info' : 'success') ?> alert-dismissible fade show">
        <?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endforeach; endforeach; ?>
      <?= $content ?>
    </div>
  </div>
</div>
<script src="<?= url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= url('assets/js/admin.js') ?>"></script>
</body>
</html>
