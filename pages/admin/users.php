<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('admin');
$admin   = new Admin();
$success = $error = '';
if (isset($_GET['delete'])) {
    $result = $admin->deleteUser((int)$_GET['delete']);
    $result === true ? $success = "User deleted." : $error = $result;
}
$keyword = htmlspecialchars(trim($_GET['q'] ?? ''));
$users   = $keyword ? $admin->searchUsers($keyword) : $admin->getUsers();
$pageTitle = 'Manage Users';
include __DIR__ . '/../../includes/header.php';
?>
<div class="d-flex justify-between align-center mb-2">
  <h1 class="page-title">👥 Manage <span>Users</span></h1>
  <a href="/pages/admin/add_user.php" class="btn btn-primary">+ Add User</a>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<div class="card">
  <form method="GET" class="d-flex gap-1 mb-2">
    <input type="text" name="q" placeholder="Search by name or email..." value="<?= $keyword ?>" style="flex:1">
    <button class="btn btn-info">Search</button>
    <?php if ($keyword): ?><a href="users.php" class="btn btn-primary">Clear</a><?php endif; ?>
  </form>
  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Joined</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge badge-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
          <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
          <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td>
            <?php if ($u['id'] != $_SESSION['user_id']): ?>
            <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this user and all their data?')">Delete</a>
            <?php else: ?>
            <span class="text-muted" style="font-size:.8rem">You</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
        <tr><td colspan="7" class="text-center text-muted">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
