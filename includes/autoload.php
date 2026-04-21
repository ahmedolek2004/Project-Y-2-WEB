<?php
// ============================================================
//  includes/autoload.php
//  Load all classes + start session
// ============================================================
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Middleware.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/Doctor.php';
require_once __DIR__ . '/../classes/Patient.php';
require_once __DIR__ . '/../classes/MedicalRecord.php';
require_once __DIR__ . '/../classes/Prescription.php';
