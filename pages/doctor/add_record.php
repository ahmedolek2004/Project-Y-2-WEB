<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('doctor');
$doctor   = new Doctor();
$error    = $success = '';
$patients = $doctor->getAllPatients();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $doctor->addRecord($_POST);
    $result === true ? $success = "Record added successfully!" : $error = $result;
}
$pageTitle = 'Add Medical Record';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">📋 Add Medical <span>Record</span></h1>
<div class="card" style="max-width:560px">
  <?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Patient *</label>
      <select name="patient_id" required>
        <option value="">— Select Patient —</option>
        <?php foreach ($patients as $p): ?>
        <option value="<?= $p['id'] ?>" <?= (($_GET['patient_id'] ?? '') == $p['id'] ? 'selected' : '') ?>>
          <?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['email']) ?>)
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Diagnosis *</label>
      <textarea name="diagnosis" required></textarea>
    </div>
    <div class="form-group">
      <label>Notes</label>
      <textarea name="notes"></textarea>
    </div>
    <div class="form-group">
      <label>Visit Date *</label>
      <input type="date" name="visit_date" required value="<?= date('Y-m-d') ?>">
    </div>
    <button type="submit" class="btn btn-primary">Save Record</button>
    <a href="patients.php" class="btn btn-info">Back</a>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>