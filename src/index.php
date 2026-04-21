<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Validator.php';

$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    $role = $auth->getRole();
    header("Location: /pages/{$role}/dashboard.php");
    exit;
}

$error = '';
$success = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validator();
    $email    = Validator::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $v->required('email', $email)
      ->email('email', $email)
      ->required('password', $password);

    if ($v->passes()) {
        $result = $auth->login($email, $password);
        if ($result['success']) {
            header("Location: /pages/{$result['role']}/dashboard.php");
            exit;
        }
        $error = $result['message'];
    } else {
        $error = $v->firstError();
    }
}

$pageTitle = 'MediCore — Sign In';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<main class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-logo">✚</span>
            <h1 class="auth-title">MediCore</h1>
            <p class="auth-subtitle">Medical Records System</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="you@hospital.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Sign In →</button>
        </form>

        <div class="auth-links">
            New patient? <a href="/pages/register.php">Create an account</a>
        </div>

        <div class="text-muted mt-3" style="font-size:0.7rem; border-top:1px solid var(--border); padding-top:1rem; margin-top:1rem;">
            
        </div>
    </div>
</main>
<script src="/assets/js/mobile-menu.js" defer></script>
</body>
</html>
