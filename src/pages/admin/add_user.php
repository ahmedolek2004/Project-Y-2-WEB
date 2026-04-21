<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Validator.php';

Permission::require('admin');

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = Validator::sanitize($_POST['name'] ?? '');
    $email    = Validator::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = Validator::sanitize($_POST['role'] ?? '');

    $v = new Validator();
    $v->required('name', $name)
      ->required('email', $email)
      ->email('email', $email)
      ->required('password', $password)
      ->minLength('password', $password, 8)
      ->required('role', $role)
      ->inArray('role', $role, ['doctor', 'patient']);

    if ($v->passes()) {
        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $error = 'That email is already registered.';
        } else {
            $userModel->register($name, $email, $password, $role);
            $success = ucfirst($role) . ' account created for ' . htmlspecialchars($name) . '.';
        }
    } else {
        $error = $v->firstError();
    }
}

$pageTitle = 'Add User';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Add <span>User</span></h1>
    <p class="page-subtitle">Create a doctor or patient account</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card" style="max-width: 520px;">
    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Dr. Jane Smith" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="doctor@hospital.com" required>
        </div>
        <div class="form-group">
            <label>Password <span class="text-muted">(min 8 chars)</span></label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" required>
                <option value="">— Select role —</option>
                <option value="doctor"  <?= (($_POST['role'] ?? '') === 'doctor')  ? 'selected' : '' ?>>Doctor</option>
                <option value="patient" <?= (($_POST['role'] ?? '') === 'patient') ? 'selected' : '' ?>>Patient</option>
            </select>
        </div>
        <div class="flex-gap mt-2">
            <button type="submit" class="btn btn-primary">Create Account</button>
            <a href="/pages/admin/users.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
