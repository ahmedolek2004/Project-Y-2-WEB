<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('patient');
$patient = new Patient();
$stats   = $patient->getDashboardStats();
$user    = $patient->getCurrentUser();
$pageTitle = 'My Dashboard';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">🏠 My <span>Dashboard</span></h1>
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-number"><?= $stats['record_count'] ?></div>
    <div class="stat-label">Medical Records</div>
  </div>
  <div class="stat-card">
    <div class="stat-number"><?= $stats['presc_count'] ?></div>
    <div class="stat-label">Prescriptions</div>
  </div>
</div>
<?php if ($stats['last_visit']): ?>
<div class="card">
  <div class="card-title">🕐 Last Visit</div>
  <p><strong>Date:</strong> <?= date('d M Y', strtotime($stats['last_visit']['visit_date'])) ?></p>
  <p><strong>Doctor:</strong> <?= htmlspecialchars($stats['last_visit']['doctor_name']) ?></p>
  <p><strong>Diagnosis:</strong> <?= htmlspecialchars($stats['last_visit']['diagnosis']) ?></p>
</div>
<?php endif; ?>
<div class="d-flex gap-1 mt-2">
  <a href="records.php"       class="btn btn-primary">📋 My Records</a>
  <a href="prescriptions.php" class="btn btn-success">💊 Prescriptions</a>
  <a href="timeline.php"      class="btn btn-info">📅 Timeline</a>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>