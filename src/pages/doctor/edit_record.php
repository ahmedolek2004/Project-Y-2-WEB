<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/Validator.php';

Permission::require('doctor');

$auth        = new Auth();
$doctorId    = $auth->getId();
$recordModel = new MedicalRecord();

$recordId = (int)($_GET['id'] ?? 0);
$record   = $recordModel->getRecordForDoctor($recordId, $doctorId);

if (!$record) {
    header('Location: /pages/doctor/records.php?error=Record+not+found');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosis = Validator::sanitize($_POST['diagnosis'] ?? '');
    $notes     = Validator::sanitize($_POST['notes'] ?? '');

    $v = new Validator();
    $v->required('diagnosis', $diagnosis);

    if ($v->passes()) {
        $recordModel->updateDiagnosis($recordId, $doctorId, $diagnosis, $notes);
        header("Location: /pages/doctor/view_record.php?id={$recordId}&msg=Record+updated");
        exit;
    } else {
        $error = $v->firstError();
    }
}

$pageTitle = 'Edit Record';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Edit <span>Record</span></h1>
    <p class="page-subtitle">Patient: <?= htmlspecialchars($record['patient_name']) ?> · <?= date('M j, Y', strtotime($record['visit_date'])) ?></p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width:600px;">
    <form method="POST" action="">
        <div class="form-group">
            <label>Diagnosis</label>
            <textarea name="diagnosis" required><?= htmlspecialchars($_POST['diagnosis'] ?? $record['diagnosis']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Clinical Notes</label>
            <textarea name="notes"><?= htmlspecialchars($_POST['notes'] ?? $record['notes']) ?></textarea>
        </div>
        <div class="flex-gap mt-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="/pages/doctor/view_record.php?id=<?= $recordId ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
