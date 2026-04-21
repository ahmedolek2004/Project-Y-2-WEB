<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('patient');
$patient       = new Patient();
$prescriptions = $patient->getMyPrescriptions();
$pageTitle = 'My Prescriptions';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">💊 My <span>Prescriptions</span></h1>
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Date</th><th>Doctor</th><th>Diagnosis</th><th>Medication</th><th>Dosage</th><th>Instructions</th></tr></thead>
      <tbody>
        <?php foreach ($prescriptions as $p): ?>
        <tr>
          <td><?= date('d M Y', strtotime($p['visit_date'])) ?></td>
          <td><?= htmlspecialchars($p['doctor_name']) ?></td>
          <td><?= htmlspecialchars(substr($p['diagnosis'],0,40)) ?>...</td>
          <td><strong><?= htmlspecialchars($p['medication_name']) ?></strong></td>
          <td><?= htmlspecialchars($p['dosage']) ?></td>
          <td><?= htmlspecialchars($p['instructions'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($prescriptions)): ?>
        <tr><td colspan="6" class="text-center text-muted">No prescriptions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>