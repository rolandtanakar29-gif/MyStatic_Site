<?php
session_start();
require "config/database.php";
require "includes/auth.php";
require_login();
$post_id = (int)($_POST['post_id'] ?? 0);
$uid = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM likes WHERE user_id=? AND post_id=?");
$stmt->bind_param("ii", $uid, $post_id);
$stmt->execute();
if ($stmt->get_result()->num_rows) {
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id=? AND post_id=?");
    $stmt->bind_param("ii", $uid, $post_id);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("INSERT INTO likes(user_id,post_id) VALUES(?,?)");
    $stmt->bind_param("ii", $uid, $post_id);
    $stmt->execute();
}
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'home.php'));
exit;
