<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Validator.php';

Permission::require('doctor');

$auth        = new Auth();
$doctorId    = $auth->getId();
$userModel   = new User();
$recordModel = new MedicalRecord();

$patients = $userModel->getUsersByRole('patient');
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $diagnosis = Validator::sanitize($_POST['diagnosis'] ?? '');
    $notes     = Validator::sanitize($_POST['notes'] ?? '');
    $visitDate = Validator::sanitize($_POST['visit_date'] ?? '');

    $v = new Validator();
    $v->required('patient_id', $patientId)
      ->required('diagnosis', $diagnosis)
      ->required('visit_date', $visitDate)
      ->date('visit_date', $visitDate);

    // Verify patient exists and has role=patient
    $validPatient = false;
    foreach ($patients as $p) {
        if ($p['id'] === $patientId) { $validPatient = true; break; }
    }

    if (!$validPatient) {
        $error = 'Please select a valid patient.';
    } elseif ($v->passes()) {
        $recordId = $recordModel->createRecord($patientId, $doctorId, $diagnosis, $notes, $visitDate);
        header("Location: /pages/doctor/view_record.php?id={$recordId}&msg=Record+created");
        exit;
    } else {
        $error = $v->firstError();
    }
}

$pageTitle = 'New Medical Record';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">New <span>Record</span></h1>
    <p class="page-subtitle">Add a medical record for a patient</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width:600px;">
    <form method="POST" action="">
        <div class="form-group">
            <label>Patient</label>
            <select name="patient_id" required>
                <option value="">— Select patient —</option>
                <?php foreach ($patients as $p): ?>
                <option value="<?= $p['id'] ?>" <?= (int)($_POST['patient_id'] ?? 0) === $p['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?> &lt;<?= htmlspecialchars($p['email']) ?>&gt;
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Visit Date</label>
            <input type="date" name="visit_date" value="<?= htmlspecialchars($_POST['visit_date'] ?? date('Y-m-d')) ?>" required>
        </div>
        <div class="form-group">
            <label>Diagnosis</label>
            <textarea name="diagnosis" placeholder="Describe the diagnosis…" required><?= htmlspecialchars($_POST['diagnosis'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Clinical Notes <span class="text-muted">(optional)</span></label>
            <textarea name="notes" placeholder="Additional notes, observations…"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
        </div>
        <div class="flex-gap mt-2">
            <button type="submit" class="btn btn-primary">Create Record</button>
            <a href="/pages/doctor/records.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
