<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/Prescription.php';

Permission::require('patient');

$auth        = new Auth();
$patientId   = $auth->getId();
$recordModel = new MedicalRecord();
$rxModel     = new Prescription();

// Timeline: sorted ascending by date (shows progression)
$timeline = $recordModel->getPatientTimeline($patientId);

$pageTitle = 'My Medical History';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Medical <span>History</span></h1>
    <p class="page-subtitle">Your treatment timeline · <?= count($timeline) ?> visit<?= count($timeline) !== 1 ? 's' : '' ?></p>
</div>

<?php if (empty($timeline)): ?>
<div class="card" style="text-align:center; padding:3rem;">
    <div style="font-size:2rem; color:var(--text-muted); margin-bottom:1rem;">◌</div>
    <p class="text-muted">No medical records found.</p>
</div>
<?php else: ?>
<div class="timeline">
    <?php foreach ($timeline as $r): ?>
    <div class="timeline-item">
        <div class="timeline-dot"></div>
        <div class="timeline-card">
            <div class="timeline-date">
                <?= date('F j, Y', strtotime($r['visit_date'])) ?>
            </div>
            <div class="timeline-diagnosis"><?= htmlspecialchars($r['diagnosis']) ?></div>
            <div class="timeline-doctor">Dr. <?= htmlspecialchars($r['doctor_name']) ?></div>

            <?php if ($r['notes']): ?>
            <p class="text-muted mt-1"><?= nl2br(htmlspecialchars($r['notes'])) ?></p>
            <?php endif; ?>

            <?php if ($r['prescription_count'] > 0): ?>
            <?php $rxList = $rxModel->getPrescriptionsByRecord($r['id']); ?>
            <div class="mt-2">
                <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--accent-amber); margin-bottom:6px;">
                    ⊕ <?= $r['prescription_count'] ?> Prescription<?= $r['prescription_count'] > 1 ? 's' : '' ?>
                </div>
                <?php foreach ($rxList as $rx): ?>
                <div class="rx-item">
                    <div>
                        <div class="rx-name"><?= htmlspecialchars($rx['medication_name']) ?></div>
                        <div class="rx-dosage"><?= htmlspecialchars($rx['dosage']) ?></div>
                        <div class="rx-instr"><?= htmlspecialchars($rx['instructions']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
