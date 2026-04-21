<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/User.php';

Permission::require('admin');

$userModel = new User();
$users     = $userModel->getAllUsers();

$success = $_GET['msg']   ?? '';
$error   = $_GET['error'] ?? '';

$pageTitle = 'User Management';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header flex-between">
    <div>
        <h1 class="page-title">User <span>Management</span></h1>
        <p class="page-subtitle"><?= count($users) ?> total users</p>
    </div>
    <a href="/pages/admin/add_user.php" class="btn btn-primary">⊕ Add User</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="badge <?= $u['role'] === 'admin' ? 'badge-purple' : ($u['role'] === 'doctor' ? 'badge-blue' : 'badge-teal') ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <a href="/pages/admin/delete_user.php?id=<?= $u['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Permanently delete <?= htmlspecialchars($u['name']) ?>?')">
                           Delete
                        </a>
                        <?php else: ?>
                        <span class="badge badge-teal">You</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
