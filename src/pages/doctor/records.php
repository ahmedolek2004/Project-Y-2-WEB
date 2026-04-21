<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';

Permission::require('doctor');

$auth        = new Auth();
$doctorId    = $auth->getId();
$recordModel = new MedicalRecord();
$records     = $recordModel->getRecordsByDoctor($doctorId);

$success = $_GET['msg']   ?? '';
$error   = $_GET['error'] ?? '';

$pageTitle = 'My Records';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header flex-between">
    <div>
        <h1 class="page-title">My <span>Records</span></h1>
        <p class="page-subtitle"><?= count($records) ?> record<?= count($records) !== 1 ? 's' : '' ?> across all patients</p>
    </div>
    <a href="/pages/doctor/add_record.php" class="btn btn-primary">⊕ New Record</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <?php if (empty($records)): ?>
        <div class="text-muted" style="padding:2rem 0; text-align:center;">
            No records yet. <a href="/pages/doctor/add_record.php" style="color:var(--accent-teal)">Create your first →</a>
        </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Visit Date</th>
                    <th>Patient</th>
                    <th>Diagnosis</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= date('M j, Y', strtotime($r['visit_date'])) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($r['patient_name']) ?></strong>
                        <div class="text-muted"><?= htmlspecialchars($r['patient_email']) ?></div>
                    </td>
                    <td><?= htmlspecialchars(mb_strimwidth($r['diagnosis'], 0, 70, '…')) ?></td>
                    <td>
                        <div class="flex-gap">
                            <a href="/pages/doctor/view_record.php?id=<?= $r['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                            <a href="/pages/doctor/edit_record.php?id=<?= $r['id'] ?>" class="btn btn-blue btn-sm">Edit</a>
                            <a href="/pages/doctor/delete_record.php?id=<?= $r['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this record?')">Delete</a>
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
