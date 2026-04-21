<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';

Permission::require('doctor');

$auth        = new Auth();
$doctorId    = $auth->getId();
$recordModel = new MedicalRecord();

$recordId = (int)($_GET['id'] ?? 0);

if (!$recordId) {
    header('Location: /pages/doctor/records.php?error=Invalid+record');
    exit;
}

$deleted = $recordModel->deleteRecord($recordId, $doctorId);

if ($deleted) {
    header('Location: /pages/doctor/records.php?msg=Record+deleted');
} else {
    header('Location: /pages/doctor/records.php?error=Could+not+delete+record');
}
exit;
