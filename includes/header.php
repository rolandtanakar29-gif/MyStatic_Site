<?php
require_once __DIR__ . '/functions.php';
$flash = get_flash();
?>
<!doctype html>
<html lang="en" data-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title ?? 'Connect — Social Feed') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>
    document.documentElement.dataset.theme = localStorage.getItem('theme') || 'light';
  </script>
</head>

<body>
  <div class="ambient ambient-one"></div>
  <div class="ambient ambient-two"></div>
  <header class="topbar">
    <div class="brand-wrap">
      <a class="brand" href="<?= isset($_SESSION['user_id']) ? 'home.php' : 'index.php' ?>"><span class="brand-mark">C</span><span>Connect</span></a>
    </div>
    <?php if (!empty($_SESSION['user_id'])): ?>
      <form class="searchbar" action="home.php" method="get"><span>⌕</span><input name="q" placeholder="Search Connect" value="<?= e($_GET['q'] ?? '') ?>"></form>
      <nav class="top-actions">
        <a class="icon-btn" href="home.php" title="Home">⌂</a>
        <a class="icon-btn" href="friends.php" title="Friends">♧</a>
        <button class="icon-btn" type="button" id="themeToggle" title="Toggle theme">◐</button>
        <a class="avatar mini" href="profile.php"><span><?= e(initials($_SESSION['user_name'] ?? 'User')) ?></span></a>
      </nav>
    <?php endif; ?>
  </header>
  <?php if ($flash): ?><div class="toast <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
  <main class="page-shell">