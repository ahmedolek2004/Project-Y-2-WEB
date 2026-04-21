<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/Prescription.php';
require_once __DIR__ . '/../../classes/Validator.php';

Permission::require('doctor');

$auth        = new Auth();
$doctorId    = $auth->getId();
$recordModel = new MedicalRecord();
$rxModel     = new Prescription();

$recordId = (int)($_GET['id'] ?? 0);
$record   = $recordModel->getRecordForDoctor($recordId, $doctorId);

if (!$record) {
    header('Location: /pages/doctor/records.php?error=Record+not+found+or+access+denied');
    exit;
}

$prescriptions = $rxModel->getPrescriptionsByRecord($recordId);
$error   = '';
$success = $_GET['msg'] ?? '';

// Handle add prescription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_rx'])) {
    $medName      = Validator::sanitize($_POST['medication_name'] ?? '');
    $dosage       = Validator::sanitize($_POST['dosage'] ?? '');
    $instructions = Validator::sanitize($_POST['instructions'] ?? '');

    $v = new Validator();
    $v->required('medication_name', $medName)
      ->required('dosage', $dosage)
      ->required('instructions', $instructions);

    if ($v->passes()) {
        $rxModel->addPrescription($recordId, $medName, $dosage, $instructions);
        header("Location: /pages/doctor/view_record.php?id={$recordId}&msg=Prescription+added");
        exit;
    } else {
        $error = $v->firstError();
    }
    $prescriptions = $rxModel->getPrescriptionsByRecord($recordId);
}

$pageTitle = 'View Record';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header flex-between">
    <div>
        <h1 class="page-title">Medical <span>Record</span></h1>
        <p class="page-subtitle">Patient: <?= htmlspecialchars($record['patient_name']) ?> · <?= date('F j, Y', strtotime($record['visit_date'])) ?></p>
    </div>
    <div class="flex-gap">
        <a href="/pages/doctor/edit_record.php?id=<?= $recordId ?>" class="btn btn-blue">Edit Record</a>
        <a href="/pages/doctor/records.php" class="btn btn-secondary">← Back</a>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="grid-2">
    <div>
        <div class="card mb-2">
            <div class="card-title">◎ Diagnosis</div>
            <p style="color:var(--text-primary); line-height:1.7;"><?= nl2br(htmlspecialchars($record['diagnosis'])) ?></p>
            <?php if ($record['notes']): ?>
            <div class="mt-2">
                <label>Clinical Notes</label>
                <p class="text-muted" style="margin-top:4px; line-height:1.7;"><?= nl2br(htmlspecialchars($record['notes'])) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Prescriptions list -->
        <div class="card">
            <div class="card-title">⊕ Prescriptions (<?= count($prescriptions) ?>)</div>
            <?php if (empty($prescriptions)): ?>
                <p class="text-muted">No prescriptions yet.</p>
            <?php else: ?>
                <?php foreach ($prescriptions as $rx): ?>
                <div class="rx-item">
                    <div>
                        <div class="rx-name"><?= htmlspecialchars($rx['medication_name']) ?></div>
                        <div class="rx-dosage"><?= htmlspecialchars($rx['dosage']) ?></div>
                        <div class="rx-instr"><?= htmlspecialchars($rx['instructions']) ?></div>
                    </div>
                    <a href="/pages/doctor/delete_rx.php?id=<?= $rx['id'] ?>&record_id=<?= $recordId ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete this prescription?')">✕</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Prescription form -->
    <div class="card" style="align-self:start;">
        <div class="card-title">⊕ Add Prescription</div>
        <form method="POST" action="">
            <div class="form-group">
                <label>Medication Name</label>
                <input type="text" name="medication_name" placeholder="e.g. Amoxicillin" required>
            </div>
            <div class="form-group">
                <label>Dosage</label>
                <input type="text" name="dosage" placeholder="e.g. 500mg twice daily" required>
            </div>
            <div class="form-group">
                <label>Instructions</label>
                <textarea name="instructions" placeholder="Take with food, complete full course…" required></textarea>
            </div>
            <button type="submit" name="add_rx" class="btn btn-primary">Add Prescription</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
