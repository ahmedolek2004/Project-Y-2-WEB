<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('admin');
$admin = new Admin();
$stats = $admin->getDashboardStats();
$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">📊 Admin <span>Dashboard</span></h1>
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-number"><?= $stats['users']['total'] ?></div>
    <div class="stat-label">Total Users</div>
  </div>
  <div class="stat-card">
    <div class="stat-number"><?= $stats['users']['doctors'] ?></div>
    <div class="stat-label">Doctors</div>
  </div>
  <div class="stat-card">
    <div class="stat-number"><?= $stats['users']['patients'] ?></div>
    <div class="stat-label">Patients</div>
  </div>
  <div class="stat-card">
    <div class="stat-number"><?= $stats['records'] ?></div>
    <div class="stat-label">Medical Records</div>
  </div>
  <div class="stat-card">
    <div class="stat-number"><?= $stats['prescriptions'] ?></div>
    <div class="stat-label">Prescriptions</div>
  </div>
</div>
<div class="card">
  <div class="card-title">🕐 Recently Added Users</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Role</th><th>Joined</th></tr></thead>
      <tbody>
        <?php foreach ($stats['recent_users'] as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><span class="badge badge-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
          <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
