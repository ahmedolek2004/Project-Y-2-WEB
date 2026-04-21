<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('doctor');
$doctor = new Doctor();
$stats  = $doctor->getDashboardStats();
$pageTitle = 'Doctor Dashboard';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">🩺 Doctor <span>Dashboard</span></h1>
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-number"><?= $stats['overview']['patient_count'] ?></div>
    <div class="stat-label">My Patients</div>
  </div>
  <div class="stat-card">
    <div class="stat-number"><?= $stats['overview']['record_count'] ?></div>
    <div class="stat-label">Records Created</div>
  </div>
</div>
<div class="card">
  <div class="card-title">🕐 Recent Records</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Patient</th><th>Diagnosis</th><th>Visit Date</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($stats['recent_records'] as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['patient_name']) ?></td>
          <td><?= htmlspecialchars(substr($r['diagnosis'], 0, 60)) ?>...</td>
          <td><?= date('d M Y', strtotime($r['visit_date'])) ?></td>
          <td>
            <a href="edit_record.php?id=<?= $r['id'] ?>" class="btn btn-info btn-sm">Edit</a>
            <a href="add_prescription.php?record_id=<?= $r['id'] ?>" class="btn btn-success btn-sm">+ Rx</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($stats['recent_records'])): ?>
        <tr><td colspan="4" class="text-center text-muted">No records yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>