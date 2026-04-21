<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/Prescription.php';

Permission::require('patient');

$auth      = new Auth();
$patientId = $auth->getId();
$rxModel   = new Prescription();

// SECURITY: This query filters strictly by patient_id = session user
$prescriptions = $rxModel->getPrescriptionsByPatient($patientId);

$pageTitle = 'My Prescriptions';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">My <span>Prescriptions</span></h1>
    <p class="page-subtitle"><?= count($prescriptions) ?> prescription<?= count($prescriptions) !== 1 ? 's' : '' ?> on record</p>
</div>

<?php if (empty($prescriptions)): ?>
<div class="card" style="text-align:center; padding:3rem;">
    <div style="font-size:2rem; color:var(--text-muted); margin-bottom:1rem;">⊕</div>
    <p class="text-muted">No prescriptions found.</p>
</div>
<?php else: ?>
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Visit Date</th>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Instructions</th>
                    <th>Prescribing Doctor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $rx): ?>
                <tr>
                    <td><?= date('M j, Y', strtotime($rx['visit_date'])) ?></td>
                    <td><strong><?= htmlspecialchars($rx['medication_name']) ?></strong></td>
                    <td><span class="badge badge-amber"><?= htmlspecialchars($rx['dosage']) ?></span></td>
                    <td class="text-muted"><?= htmlspecialchars($rx['instructions']) ?></td>
                    <td>Dr. <?= htmlspecialchars($rx['doctor_name']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
