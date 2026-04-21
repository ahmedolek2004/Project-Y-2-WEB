<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Validator.php';

$auth = new Auth();
if ($auth->isLoggedIn()) {
    header('Location: /index.php'); exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = Validator::sanitize($_POST['name'] ?? '');
    $email    = Validator::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    $v = new Validator();
    $v->required('name', $name)
      ->required('email', $email)
      ->email('email', $email)
      ->required('password', $password)
      ->minLength('password', $password, 8);

    if ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif ($v->passes()) {
        $result = $auth->register($name, $email, $password, 'patient');
        if ($result['success']) {
            $success = 'Account created! You can now sign in.';
        } else {
            $error = $result['message'];
        }
    } else {
        $error = $v->firstError();
    }
}

$pageTitle = 'MediCore — Register';
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
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Patient Registration</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Jane Smith" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="you@email.com" required>
            </div>
            <div class="form-group">
                <label>Password <span class="text-muted">(min 8 chars)</span></label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Create Account →</button>
        </form>

        <div class="auth-links">
            Already registered? <a href="/index.php">Sign in</a>
        </div>
    </div>
</main>
</body>
</html>
