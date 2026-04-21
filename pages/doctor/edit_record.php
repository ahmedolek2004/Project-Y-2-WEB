<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('doctor');
$doctor = new Doctor();
$id     = (int)($_GET['id'] ?? 0);
$record = $doctor->getRecord($id);
if (!$record) { http_response_code(403); die("Record not found or access denied."); }
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $doctor->updateDiagnosis($id, $_POST);
    $result === true ? $success = "Record updated!" : $error = $result;
    $record = $doctor->getRecord($id);
}
$pageTitle = 'Edit Record';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">✏️ Edit <span>Record</span></h1>
<div class="card" style="max-width:560px">
  <p class="text-muted mb-2">Patient: <strong><?= htmlspecialchars($record['patient_name']) ?></strong>
  | Visit: <?= date('d M Y', strtotime($record['visit_date'])) ?></p>
  <?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Diagnosis *</label>
      <textarea name="diagnosis" required><?= htmlspecialchars($record['diagnosis']) ?></textarea>
    </div>
    <div class="form-group">
      <label>Notes</label>
      <textarea name="notes"><?= htmlspecialchars($record['notes'] ?? '') ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="add_prescription.php?record_id=<?= $id ?>" class="btn btn-success">+ Prescription</a>
    <a href="patients.php" class="btn btn-info">Back</a>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>