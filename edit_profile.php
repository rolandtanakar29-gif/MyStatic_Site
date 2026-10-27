<?php
session_start();
require "config/database.php";
require "includes/auth.php";
require "includes/functions.php";
require_login();
$user = user_by_id($conn, $_SESSION['user_id']);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $image = $user['profile_image'];
  try {
    $new = upload_image($_FILES['profile_image'] ?? [], __DIR__ . '/uploads/profiles/', 'uploads/profiles/');
    if ($new) $image = $new;
    $stmt = $conn->prepare("UPDATE users SET name=?,email=?,phone=?,profile_image=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $image, $_SESSION['user_id']);
    $stmt->execute();
    $_SESSION['user_name'] = $name;
    flash("success", "Profile updated.");
    redirect("profile.php");
  } catch (Throwable $e) {
    $error = $e->getMessage();
  }
}
$page_title = "Edit Profile — Connect";
require "includes/header.php";
?>
<div class="form-page">
  <form class="settings-card glass-card" method="post" enctype="multipart/form-data"><span class="eyebrow">PROFILE SETTINGS</span>
    <h1>Make your profile yours.</h1><?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <div class="profile-preview"><?php if ($user['profile_image']): ?><img src="<?= e($user['profile_image']) ?>" alt="Profile"><?php else: ?><span><?= e(initials($user['name'])) ?></span><?php endif; ?></div>
    <label>Full name<input name="name" required value="<?= e($user['name']) ?>"></label><label>Email<input type="email" name="email" value="<?= e($user['email']) ?>"></label><label>Phone<input name="phone" value="<?= e($user['phone']) ?>"></label><label>Profile picture<input type="file" name="profile_image" accept="image/*"></label><button class="btn primary" type="submit">Save changes</button>
  </form>
</div>
<?php require "includes/footer.php"; ?>