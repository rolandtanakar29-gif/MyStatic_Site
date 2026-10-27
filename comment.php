<?php
session_start();
require "config/database.php";
require "includes/auth.php";
require_login();
$post_id = (int)($_POST['post_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
if ($post_id && $comment !== '') {
    $stmt = $conn->prepare("INSERT INTO comments(user_id,post_id,comment) VALUES(?,?,?)");
    $stmt->bind_param("iis", $_SESSION['user_id'], $post_id, $comment);
    $stmt->execute();
}
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'home.php'));
exit;
