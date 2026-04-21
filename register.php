<?php
require_once __DIR__ . '/includes/autoload.php';
if (Middleware::isLoggedIn()) Middleware::redirectByRole();
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user   = new User();
    $result = $user->register($_POST);
    if ($result === true) {
        $success = "Account created successfully! You can now login.";
    } else {
        $error = $result;
    }
}
$pageTitle = 'Register';
include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <h2>🏥 Create Account</h2>
    <?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Full Name *</label>
        <input type="text" name="name" required placeholder="Your full name">
      </div>
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" required placeholder="you@example.com">
      </div>
      <div class="form-group">
        <label>Password * (min 8 chars)</label>
        <input type="password" name="password" required minlength="8">
      </div>
      <div class="form-group">
        <label>Role *</label>
        <select name="role" required>
          <option value="">— Select Role —</option>
          <option value="doctor">Doctor</option>
          <option value="patient">Patient</option>
        </select>
      </div>
      <div class="form-group">
        <label>Phone (optional)</label>
        <input type="text" name="phone" placeholder="01XXXXXXXXX">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%">Register</button>
      <p class="text-center mt-2">Already have an account? <a href="/login.php">Login</a></p>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
