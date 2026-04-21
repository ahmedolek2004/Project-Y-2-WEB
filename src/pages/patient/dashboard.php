<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/Prescription.php';

Permission::require('patient');

$auth        = new Auth();
$patientId   = $auth->getId();
$recordModel = new MedicalRecord();
$rxModel     = new Prescription();

$records        = $recordModel->getRecordsByPatient($patientId);
$prescriptions  = $rxModel->getPrescriptionsByPatient($patientId);
$recentRecords  = array_slice($records, 0, 3);

$pageTitle = 'My Dashboard';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Hello, <span><?= htmlspecialchars(explode(' ', $auth->getName())[0]) ?></span></h1>
    <p class="page-subtitle">Your health overview · <?= date('F j, Y') ?></p>
</div>

<div class="stats-grid">
    <div class="stat-card" style="--stat-color: var(--role-patient)">
        <div class="stat-value"><?= count($records) ?></div>
        <div class="stat-label">Medical Visits</div>
    </div>
    <div class="stat-card" style="--stat-color: var(--accent-amber)">
        <div class="stat-value"><?= count($prescriptions) ?></div>
        <div class="stat-label">Prescriptions</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-title flex-between">
            <span>◎ Recent Visits</span>
            <a href="/pages/patient/history.php" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <?php if (empty($recentRecords)): ?>
            <p class="text-muted">No medical records yet.</p>
        <?php else: ?>
            <?php foreach ($recentRecords as $r): ?>
            <div style="padding: 10px 0; border-bottom: 1px solid var(--border);">
                <div style="font-size:0.7rem; color:var(--accent-teal); text-transform:uppercase; letter-spacing:0.1em;">
                    <?= date('M j, Y', strtotime($r['visit_date'])) ?>
                </div>
                <div style="color:var(--text-primary); margin: 3px 0;"><?= htmlspecialchars(mb_strimwidth($r['diagnosis'], 0, 60, '…')) ?></div>
                <div class="text-muted">Dr. <?= htmlspecialchars($r['doctor_name']) ?></div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-title flex-between">
            <span>⊕ Active Prescriptions</span>
            <a href="/pages/patient/prescriptions.php" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <?php if (empty($prescriptions)): ?>
            <p class="text-muted">No prescriptions.</p>
        <?php else: ?>
            <?php foreach (array_slice($prescriptions, 0, 3) as $rx): ?>
            <div class="rx-item">
                <div>
                    <div class="rx-name"><?= htmlspecialchars($rx['medication_name']) ?></div>
                    <div class="rx-dosage"><?= htmlspecialchars($rx['dosage']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
