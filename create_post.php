<?php
session_start();
require "config/database.php";
require "includes/auth.php";
require "includes/functions.php";
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect("home.php");
$content = trim($_POST['content'] ?? '');
$image = null;
try {
  $image = upload_image($_FILES['image'] ?? [], __DIR__ . '/uploads/posts/', 'uploads/posts/');
  if ($content === '' && !$image) {
    flash("error", "Write something or choose an image.");
    redirect("home.php");
  }
  $stmt = $conn->prepare("INSERT INTO posts(user_id,content,image) VALUES(?,?,?)");
  $stmt->bind_param("iss", $_SESSION['user_id'], $content, $image);
  $stmt->execute();
  flash("success", "Your post is live!");
  redirect("home.php");
} catch (Throwable $e) {
  flash("error", $e->getMessage());
  redirect("home.php");
}
