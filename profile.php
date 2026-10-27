<?php
session_start();
require "config/database.php";
require "includes/auth.php";
require "includes/functions.php";
require_login();
$id = (int)($_GET['id'] ?? $_SESSION['user_id']);
$profile = user_by_id($conn, $id);
if (!$profile) redirect("home.php");
$stmt = $conn->prepare("SELECT p.*,u.name FROM posts p JOIN users u ON u.id=p.user_id WHERE p.user_id=? ORDER BY p.created_at DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$posts = $stmt->get_result();
$page_title = e($profile['name']) . " — Connect";
require "includes/header.php";
?>
<section class="profile-page">
    <div class="profile-cover">
        <div class="cover-glow"></div>
    </div>
    <div class="profile-header glass-card">
        <div class="avatar profile-avatar"><?= $profile['profile_image'] ? '<img src="' . e($profile['profile_image']) . '" alt="Profile">' : '<span>' . e(initials($profile['name'])) . '</span>' ?></div>
        <div class="profile-info">
            <h1><?= e($profile['name']) ?></h1>
            <p>Member since <?= e(date("F Y", strtotime($profile['created_at']))) ?></p>
        </div><a class="btn small" href="edit_profile.php">Edit profile</a>
    </div>
    <div class="profile-grid">
        <section>
            <h2 class="section-title">Posts</h2>
            <?php while ($p = $posts->fetch_assoc()): ?><article class="post glass-card">
                    <div class="post-head">
                        <div class="avatar"><span><?= e(initials($p['name'])) ?></span></div>
                        <div class="post-author"><b><?= e($p['name']) ?></b><small><?= e(date("d M Y, h:i A", strtotime($p['created_at']))) ?></small></div>
                    </div><?php if ($p['content']): ?><p class="post-content"><?= nl2br(e($p['content'])) ?></p><?php endif; ?><?php if ($p['image']): ?><img class="post-image" src="<?= e($p['image']) ?>" alt="Post image"><?php endif; ?>
                </article><?php endwhile; ?>
        </section>
        <aside class="profile-side glass-card"><span class="eyebrow">ABOUT</span>
            <h3><?= e($profile['name']) ?></h3>
            <p><?= e($profile['email'] ?: ($profile['phone'] ?: 'Connect member')) ?></p>
        </aside>
    </div>
</section>
<?php require "includes/footer.php"; ?>