<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('patient');
$patient = new Patient();
$user    = $patient->getCurrentUser();
$error   = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $patient->updateProfile($_POST);
    $result === true ? $success = "Profile updated!" : $error = $result;
    $user = $patient->getCurrentUser();
}
$pageTitle = 'My Profile';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">👤 My <span>Profile</span></h1>
<div class="card" style="max-width:520px">
  <?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Full Name *</label>
      <input type="text" name="name" required value="<?= htmlspecialchars($user['name']) ?>">
    </div>
    <div class="form-group">
      <label>Email *</label>
      <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">
    </div>
    <div class="form-group">
      <label>Phone</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
    </div>
    <hr style="margin:1.25rem 0;border-color:var(--border)">
    <p class="text-muted mb-1" style="font-size:.85rem">Leave blank to keep current password</p>
    <div class="form-group">
      <label>Current Password</label>
      <input type="password" name="current_password">
    </div>
    <div class="form-group">
      <label>New Password (min 8 chars)</label>
      <input type="password" name="new_password" minlength="8">
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>