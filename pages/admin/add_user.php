<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('admin');
$admin = new Admin();
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $admin->addUser($_POST);
    $result === true ? $success = "User added successfully!" : $error = $result;
}
$pageTitle = 'Add User';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">➕ Add <span>New User</span></h1>
<div class="card" style="max-width:520px">
  <?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Full Name *</label>
      <input type="text" name="name" required>
    </div>
    <div class="form-group">
      <label>Email *</label>
      <input type="email" name="email" required>
    </div>
    <div class="form-group">
      <label>Password * (min 8 chars)</label>
      <input type="password" name="password" required minlength="8">
    </div>
    <div class="form-group">
      <label>Role *</label>
      <select name="role" required>
        <option value="">— Select —</option>
        <option value="doctor">Doctor</option>
        <option value="patient">Patient</option>
      </select>
    </div>
    <div class="form-group">
      <label>Phone</label>
      <input type="text" name="phone">
    </div>
    <button type="submit" class="btn btn-primary">Add User</button>
    <a href="users.php" class="btn btn-info">Back</a>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
