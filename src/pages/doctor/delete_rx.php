<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/Prescription.php';

Permission::require('doctor');

$auth     = new Auth();
$doctorId = $auth->getId();
$rxModel  = new Prescription();

$rxId     = (int)($_GET['id']        ?? 0);
$recordId = (int)($_GET['record_id'] ?? 0);

$rxModel->deletePrescription($rxId, $doctorId);
header("Location: /pages/doctor/view_record.php?id={$recordId}&msg=Prescription+removed");
exit;
