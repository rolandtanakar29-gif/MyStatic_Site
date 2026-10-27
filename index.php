<?php
session_start();
if (!empty($_SESSION['user_id'])) {
  header("Location: home.php");
  exit;
}
$page_title = "Connect — Welcome";
require "includes/header.php";
?>
<section class="landing">
  <div class="hero-copy">
    <span class="eyebrow">SOCIAL • SIMPLE • SMART</span>
    <h1>Share your world.<br><span>Stay connected.</span></h1>
    <p>The World is right in your hands, connect and communicate now.</p>
    <div class="hero-actions"><a class="btn primary" href="register.php">Create account</a><a class="btn ghost" href="login.php">Sign in</a></div>
    <div class="feature-row">
      <div><b>01</b><span>Post photos</span></div>
      <div><b>02</b><span>Meet friends</span></div>
      <div><b>03</b><span>Join the feed</span></div>
    </div>
  </div>
  <div class="hero-card glass-card">
    <div class="mock-top"><span></span><span></span><span></span></div>
    <div class="mock-profile">
      <div class="avatar large"><span>RK</span></div>
      <div><b>Roland N Tanakar</b><small>Just now • Public</small></div>
    </div>
    <p class="mock-text">Hello everyone! ✨ Building something meaningful, one post at a time.</p>
    <div class="mock-image"><span>YOUR STORY</span></div>
    <div class="mock-stats"><span>♡ 128 Likes</span><span>24 Comments</span></div>
  </div>
</section>
<?php require "includes/footer.php"; ?>