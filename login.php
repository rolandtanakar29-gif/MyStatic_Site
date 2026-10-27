<?php
session_start();
require "config/database.php";
require "includes/functions.php";
if (!empty($_SESSION['user_id'])) redirect("home.php");
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT id,name,password FROM users WHERE email=? OR phone=? LIMIT 1");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        redirect("home.php");
    } else $error = "Invalid email/phone number or password.";
}
$page_title = "Login — Connect";
require "includes/header.php";
?>
<div class="auth-layout">
    <section class="auth-intro"><span class="eyebrow">WELCOME BACK</span>
        <h1>Good to see<br><span>you again.</span></h1>
        <p>Your feed is waiting. Sign in to continue where you left off.</p>
    </section>
    <form class="auth-card glass-card" method="post">
        <h2>Sign in</h2>
        <p class="muted">Use your email address or phone number.</p>
        <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <label>Email or phone number<input name="identifier" required autofocus></label><label>Password<input type="password" name="password" required></label>
        <button class="btn primary full" type="submit">Login →</button>
        <p class="center muted">New here? <a href="register.php">Create an account</a></p>
    </form>
</div>
<?php require "includes/footer.php"; ?>