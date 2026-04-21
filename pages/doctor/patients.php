<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('doctor');
$doctor   = new Doctor();
$patients = $doctor->getMyPatients();
$pageTitle = 'My Patients';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">👨‍👩‍👧 My <span>Patients</span></h1>
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Records</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($patients as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['email']) ?></td>
          <td><?= htmlspecialchars($p['phone'] ?? '—') ?></td>
          <td><?= $p['record_count'] ?></td>
          <td><a href="add_record.php?patient_id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">+ Record</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($patients)): ?>
        <tr><td colspan="5" class="text-center text-muted">No patients yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>