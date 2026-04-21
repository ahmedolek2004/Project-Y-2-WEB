<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('patient');
$patient = new Patient();
$records = $patient->getMyRecords();
$pageTitle = 'My Records';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">📋 My Medical <span>Records</span></h1>
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Visit Date</th><th>Doctor</th><th>Diagnosis</th><th>Notes</th></tr></thead>
      <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
          <td><?= date('d M Y', strtotime($r['visit_date'])) ?></td>
          <td><?= htmlspecialchars($r['doctor_name']) ?></td>
          <td><?= htmlspecialchars($r['diagnosis']) ?></td>
          <td><?= htmlspecialchars($r['notes'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($records)): ?>
        <tr><td colspan="4" class="text-center text-muted">No records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>