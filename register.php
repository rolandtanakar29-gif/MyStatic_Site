<?php
session_start();
require "config/database.php";
require "includes/functions.php";
if (!empty($_SESSION['user_id'])) redirect("home.php");
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || ($email === '' && $phone === '') || strlen($password) < 6) {
        $error = "Enter your name, email or phone, and a password of at least 6 characters.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE (email<>'' AND email=?) OR (phone<>'' AND phone=?)");
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        if ($stmt->get_result()->num_rows) $error = "That email or phone number is already registered.";
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users(name,email,phone,password) VALUES(?,?,?,?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hash);
            if ($stmt->execute()) {
                flash("success", "Account created. Welcome to Connect!");
                redirect("login.php");
            }   
            $error = "Could not create the account.";
        }
    }
}
$page_title = "Create Account — Connect";
require "includes/header.php";
?>
<div class="auth-layout">
    <section class="auth-intro"><span class="eyebrow">JOIN CONNECT</span>
        <h1>Your people.<br>Your moments.</h1>
        <p>Create a profile, share images, interact with posts and grow your circle.</p>
    </section>
    <form class="auth-card glass-card" method="post">
        <h2>Create account</h2>
        <p class="muted">It only takes a minute.</p>
        <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <label>Full name<input name="name" required value="<?= e($_POST['name'] ?? '') ?>"></label>
        <label>Email address <span class="muted">(optional)</span><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>"></label>
        <label>Phone number <span class="muted">(optional)</span><input name="phone" value="<?= e($_POST['phone'] ?? '') ?>"></label>
        <label>Password<input type="password" name="password" minlength="6" required></label>
        <button class="btn primary full" type="submit">Create account →</button>
        <p class="center muted">Already a member? <a href="login.php">Sign in</a></p>
    </form>
</div>
<?php require "includes/footer.php"; ?>