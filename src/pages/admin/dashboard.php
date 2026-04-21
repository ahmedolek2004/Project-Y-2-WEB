<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/Prescription.php';

Permission::require('admin');

$userModel   = new User();
$recordModel = new MedicalRecord();
$rxModel     = new Prescription();

$totalDoctors   = $userModel->countByRole('doctor');
$totalPatients  = $userModel->countByRole('patient');
$totalRecords   = $recordModel->countAll();
$totalRx        = $rxModel->countAll();
$recentUsers    = $userModel->getAllUsers();

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Admin <span>Dashboard</span></h1>
    <p class="page-subtitle">System overview · <?= date('F j, Y') ?></p>
</div>

<div class="stats-grid">
    <div class="stat-card" style="--stat-color: var(--role-admin)">
        <div class="stat-value"><?= $totalDoctors ?></div>
        <div class="stat-label">Doctors</div>
    </div>
    <div class="stat-card" style="--stat-color: var(--role-patient)">
        <div class="stat-value"><?= $totalPatients ?></div>
        <div class="stat-label">Patients</div>
    </div>
    <div class="stat-card" style="--stat-color: var(--accent-blue)">
        <div class="stat-value"><?= $totalRecords ?></div>
        <div class="stat-label">Medical Records</div>
    </div>
    <div class="stat-card" style="--stat-color: var(--accent-amber)">
        <div class="stat-value"><?= $totalRx ?></div>
        <div class="stat-label">Prescriptions</div>
    </div>
</div>

<div class="card">
    <div class="card-title flex-between">
        <span>◎ All Users</span>
        <a href="/pages/admin/add_user.php" class="btn btn-primary btn-sm">⊕ Add User</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentUsers as $u): ?>
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
                        <div class="flex-gap">
                            <a href="/pages/admin/users.php" class="btn btn-secondary btn-sm">View All</a>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <a href="/pages/admin/delete_user.php?id=<?= $u['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete <?= htmlspecialchars($u['name']) ?>?')">Delete</a>
                            <?php else: ?>
                            <span class="text-muted" style="font-size:0.72rem;">You</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
