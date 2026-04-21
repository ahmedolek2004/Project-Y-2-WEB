<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Validator.php';

Permission::require('patient');

$auth      = new Auth();
$patientId = $auth->getId();
$userModel = new User();

$user    = $userModel->findById($patientId);
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = Validator::sanitize($_POST['name'] ?? '');
    $email    = Validator::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';

    $v = new Validator();
    $v->required('name', $name)
      ->required('email', $email)
      ->email('email', $email);

    if ($password !== '') {
        $v->minLength('password', $password, 8);
        if ($password !== $confirm) {
            $error = 'Passwords do not match.';
        }
    }

    if (!$error && $v->passes()) {
        // Check email uniqueness (excluding self)
        $existing = $userModel->findByEmail($email);
        if ($existing && $existing['id'] !== $patientId) {
            $error = 'That email is already in use.';
        } else {
            $userModel->updateProfile($patientId, $name, $email, $password ?: null);
            // Update session name
            $_SESSION['user_name'] = $name;
            $success = 'Profile updated successfully.';
            $user = $userModel->findById($patientId);
        }
    } elseif (!$error) {
        $error = $v->firstError();
    }
}

$pageTitle = 'My Profile';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">My <span>Profile</span></h1>
    <p class="page-subtitle">Update your personal information</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card" style="max-width:520px;">
    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div style="border-top: 1px solid var(--border); padding-top: 1.25rem; margin-top: 0.5rem;">
            <div class="text-muted mb-2" style="font-size:0.75rem;">Leave password fields blank to keep your current password.</div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" placeholder="••••••••" minlength="8">
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm" placeholder="••••••••">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
