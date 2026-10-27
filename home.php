<?php
session_start();
require "config/database.php";
require "includes/auth.php";
require "includes/functions.php";
require_login();
$page_title = "News Feed — Connect";
$user = user_by_id($conn, (int)$_SESSION['user_id']);
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
  $like = "%$q%";
  $stmt = $conn->prepare("SELECT p.*,u.name,u.profile_image FROM posts p JOIN users u ON u.id=p.user_id WHERE p.content LIKE ? OR u.name LIKE ? ORDER BY p.created_at DESC");
  $stmt->bind_param("ss", $like, $like);
} else $stmt = $conn->prepare("SELECT p.*,u.name,u.profile_image FROM posts p JOIN users u ON u.id=p.user_id ORDER BY p.created_at DESC");
$stmt->execute();
$posts = $stmt->get_result();
require "includes/header.php";
?>
<div class="feed-layout">
  <aside class="sidebar left-sidebar glass-card">
    <a class="side-link active" href="home.php"><span>⌂</span> News Feed</a>
    <a class="side-link" href="profile.php"><span>◎</span> My Profile</a>
    <a class="side-link" href="friends.php"><span>♧</span> Friends</a>
    <a class="side-link" href="edit_profile.php"><span>⚙</span> Settings</a>
    <div class="side-divider"></div><a class="side-link logout" href="logout.php"><span>↪</span> Log out</a>
  </aside>
  <section class="feed">
    <div class="feed-heading">
      <div><span class="eyebrow">YOUR SPACE</span>
        <h1>News Feed</h1>
      </div><button class="btn small" onclick="document.getElementById('postText').focus()">＋ Create</button>
    </div>
    <form class="composer glass-card" action="create_post.php" method="post" enctype="multipart/form-data">
      <div class="composer-row">
        <div class="avatar"><span><?= e(initials($user['name'])) ?></span></div><textarea id="postText" name="content" placeholder="What's on your mind, <?= e(explode(' ', $user['name'])[0]) ?>?"></textarea>
      </div>
      <div class="composer-bottom"><label class="upload-btn">▧ Photo <input type="file" name="image" accept="image/*" hidden></label><button class="btn primary small" type="submit">Post</button></div>
    </form>
    <?php if (!$posts->num_rows): ?><div class="empty glass-card">
        <div>✦</div>
        <h3>No posts yet</h3>
        <p>Be the first to share something.</p>
      </div><?php endif; ?>
    <?php while ($post = $posts->fetch_assoc()):
      $likeStmt = $conn->prepare("SELECT COUNT(*) c FROM likes WHERE post_id=?");
      $likeStmt->bind_param("i", $post['id']);
      $likeStmt->execute();
      $likes = (int)$likeStmt->get_result()->fetch_assoc()['c'];
      $mineStmt = $conn->prepare("SELECT id FROM likes WHERE post_id=? AND user_id=?");
      $mineStmt->bind_param("ii", $post['id'], $_SESSION['user_id']);
      $mineStmt->execute();
      $liked = $mineStmt->get_result()->num_rows > 0;
      $comments = $conn->prepare("SELECT c.comment,u.name FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=? ORDER BY c.created_at ASC");
      $comments->bind_param("i", $post['id']);
      $comments->execute();
      $commentRows = $comments->get_result();
    ?>
      <article class="post glass-card">
        <div class="post-head">
          <div class="avatar"><span><?= e(initials($post['name'])) ?></span></div>
          <div class="post-author"><b><?= e($post['name']) ?></b><small><?= e(date("d M Y, h:i A", strtotime($post['created_at']))) ?> • Public</small></div>
        </div>
        <?php if ($post['content'] !== ''): ?><p class="post-content"><?= nl2br(e($post['content'])) ?></p><?php endif; ?>
        <?php if ($post['image']): ?><img class="post-image" src="<?= e($post['image']) ?>" alt="Post image"><?php endif; ?>
        <div class="post-stats"><span>♥ <?= $likes ?> Likes</span><span><?= $commentRows->num_rows ?> Comments</span></div>
        <div class="post-actions">
          <form action="like.php" method="post"><input type="hidden" name="post_id" value="<?= $post['id'] ?>"><button class="action-btn <?= $liked ? 'liked' : '' ?>">♥ <?= $liked ? 'Liked' : 'Like' ?></button></form><button class="action-btn" onclick="this.closest('.post').querySelector('.comment-input').focus()">◌ Comment</button>
        </div>
        <div class="comments"><?php while ($c = $commentRows->fetch_assoc()): ?><div class="comment"><b><?= e($c['name']) ?></b><span><?= e($c['comment']) ?></span></div><?php endwhile; ?>
          <form class="comment-form" action="comment.php" method="post"><input type="hidden" name="post_id" value="<?= $post['id'] ?>"><input class="comment-input" name="comment" required placeholder="Write a comment..."><button>→</button></form>
        </div>
      </article>
    <?php endwhile; ?>
  </section>
  <aside class="sidebar right-sidebar">
    <div class="side-widget glass-card"><span class="eyebrow">QUICK NOTE</span>
      <h3>Make it yours.</h3>
      <p>Upload a profile photo and share your first image post to personalize your space.</p><a href="edit_profile.php" class="text-link">Edit profile →</a>
    </div>
    <div class="side-widget glass-card"><span class="eyebrow">PROJECT</span>
      <h3>Distance is not the factor anymore</h3>
      <p>Why not connect when you can do from the click of a finger?</p>
    </div>
  </aside>
</div>
<?php require "includes/footer.php"; ?>