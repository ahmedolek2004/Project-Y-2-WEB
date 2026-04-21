<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';

Permission::require('doctor');

$auth        = new Auth();
$doctorId    = $auth->getId();
$recordModel = new MedicalRecord();

$patientCount   = $recordModel->countDoctorPatients($doctorId);
$recentRecords  = $recordModel->getDoctorRecentRecords($doctorId, 8);
$totalRecords   = count($recordModel->getRecordsByDoctor($doctorId));

$pageTitle = 'Doctor Dashboard';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Good <?= date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') ?>, <span>Dr. <?= htmlspecialchars(explode(' ', $auth->getName())[0]) ?></span></h1>
    <p class="page-subtitle"><?= date('l, F j, Y') ?></p>
</div>

<div class="stats-grid">
    <div class="stat-card" style="--stat-color: var(--role-doctor)">
        <div class="stat-value"><?= $patientCount ?></div>
        <div class="stat-label">My Patients</div>
    </div>
    <div class="stat-card" style="--stat-color: var(--accent-teal)">
        <div class="stat-value"><?= $totalRecords ?></div>
        <div class="stat-label">Total Records</div>
    </div>
</div>

<div class="card">
    <div class="card-title flex-between">
        <span>◎ Recent Records</span>
        <a href="/pages/doctor/add_record.php" class="btn btn-primary btn-sm">⊕ New Record</a>
    </div>
    <?php if (empty($recentRecords)): ?>
        <div class="text-muted" style="padding:1.5rem 0;">No records yet. <a href="/pages/doctor/add_record.php" style="color:var(--accent-teal)">Add your first record →</a></div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Diagnosis</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentRecords as $r): ?>
                <tr>
                    <td><?= date('M j, Y', strtotime($r['visit_date'])) ?></td>
                    <td><strong><?= htmlspecialchars($r['patient_name']) ?></strong></td>
                    <td><?= htmlspecialchars(mb_strimwidth($r['diagnosis'], 0, 60, '…')) ?></td>
                    <td>
                        <div class="flex-gap">
                            <a href="/pages/doctor/view_record.php?id=<?= $r['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                            <a href="/pages/doctor/edit_record.php?id=<?= $r['id'] ?>" class="btn btn-blue btn-sm">Edit</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
