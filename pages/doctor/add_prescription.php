<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('doctor');
$doctor   = new Doctor();
$recordId = (int)($_GET['record_id'] ?? $_POST['record_id'] ?? 0);
$record   = $doctor->getRecord($recordId);
if (!$record) { http_response_code(403); die("Access denied."); }
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $doctor->addPrescription($_POST);
    $result === true ? $success = "Prescription added!" : $error = $result;
}
$pageTitle = 'Add Prescription';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">💊 Add <span>Prescription</span></h1>
<div class="card" style="max-width:520px">
  <p class="text-muted mb-2">For: <strong><?= htmlspecialchars($record['patient_name']) ?></strong></p>
  <?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
  <form method="POST">
    <input type="hidden" name="record_id" value="<?= $recordId ?>">
    <div class="form-group">
      <label>Medication Name *</label>
      <input type="text" name="medication_name" required>
    </div>
    <div class="form-group">
      <label>Dosage *</label>
      <input type="text" name="dosage" required placeholder="e.g. 500mg twice daily">
    </div>
    <div class="form-group">
      <label>Instructions</label>
      <textarea name="instructions"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Add Prescription</button>
    <a href="edit_record.php?id=<?= $recordId ?>" class="btn btn-info">Back</a>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>