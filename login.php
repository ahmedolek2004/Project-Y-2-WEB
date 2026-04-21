<?php
require_once __DIR__ . '/includes/autoload.php';
if (Middleware::isLoggedIn()) Middleware::redirectByRole();
$error = '';
$msg   = htmlspecialchars($_GET['msg'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user   = new User();
    $result = $user->login($_POST['email'] ?? '', $_POST['password'] ?? '');
    if ($result) {
        Middleware::redirectByRole();
    } else {
        $error = "Invalid email or password.";
    }
}
$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <h2>🏥 NHDS Login</h2>
    <?php if ($msg):   ?><div class="alert alert-info"><?= $msg ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required placeholder="you@example.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%">Login</button>
      <p class="text-center mt-2">No account? <a href="/register.php">Register</a></p>
    </form>
    <hr style="margin:1.5rem 0;border-color:var(--border)">
    <p class="text-muted" style="font-size:.8rem;text-align:center">
      Demo accounts — admin@nhds.com / doctor@nhds.com / patient@nhds.com<br>
      Password for all: <strong>password</strong>
    </p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
