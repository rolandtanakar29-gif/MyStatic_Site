<?php
session_start();
require "config/database.php";
require "includes/auth.php";
require "includes/functions.php";
require_login();
$uid = (int)$_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['friend_id'])) {
    $fid = (int)$_POST['friend_id'];
    $action = $_POST['action'];
    if ($action === 'send') {
        $s = $conn->prepare("INSERT INTO friends(sender_id,receiver_id,status) VALUES(?,?,'pending')");
        $s->bind_param("ii", $uid, $fid);
        $s->execute();
    } elseif ($action === 'accept') {
        $s = $conn->prepare("UPDATE friends SET status='accepted' WHERE sender_id=? AND receiver_id=?");
        $s->bind_param("ii", $fid, $uid);
        $s->execute();
    } elseif ($action === 'reject') {
        $s = $conn->prepare("DELETE FROM friends WHERE sender_id=? AND receiver_id=?");
        $s->bind_param("ii", $fid, $uid);
        $s->execute();
    }
    flash("success", "Friendship updated.");
    redirect("friends.php");
}
$people = $conn->query("SELECT id,name,profile_image FROM users WHERE id<>" . intval($uid) . " ORDER BY name");
$page_title = "Friends — Connect";
require "includes/header.php";
?>
<div class="friends-page">
    <div class="feed-heading">
        <div><span class="eyebrow">YOUR CIRCLE</span>
            <h1>Friends</h1>
        </div>
    </div>
    <div class="people-grid"><?php while ($p = $people->fetch_assoc()):
                                    $st = $conn->prepare("SELECT sender_id,receiver_id,status FROM friends WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) LIMIT 1");
                                    $st->bind_param("iiii", $uid, $p['id'], $p['id'], $uid);
                                    $st->execute();
                                    $rel = $st->get_result()->fetch_assoc();
                                ?>
            <div class="person-card glass-card">
                <div class="avatar xl"><?php if ($p['profile_image']): ?><img src="<?= e($p['profile_image']) ?>" alt=""><?php else: ?><span><?= e(initials($p['name'])) ?></span><?php endif; ?></div>
                <h3><?= e($p['name']) ?></h3>
                <p>Connect member</p>
                <?php if (!$rel): ?><form method="post"><input type="hidden" name="friend_id" value="<?= $p['id'] ?>"><button class="btn primary small" name="action" value="send">＋ Add friend</button></form>
                <?php elseif ($rel['status'] === 'pending' && $rel['receiver_id'] === $uid): ?><div class="button-row">
                        <form method="post"><input type="hidden" name="friend_id" value="<?= $p['id'] ?>"><button class="btn primary small" name="action" value="accept">Accept</button></form>
                        <form method="post"><input type="hidden" name="friend_id" value="<?= $p['id'] ?>"><button class="btn small" name="action" value="reject">Reject</button></form>
                    </div>
                <?php elseif ($rel['status'] === 'accepted'): ?><span class="status-pill">✓ Friends</span><?php else: ?><span class="status-pill">Pending</span><?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php require "includes/footer.php"; ?>