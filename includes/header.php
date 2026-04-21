<?php
// header.php — called after autoload.php
$role     = $_SESSION['user_role'] ?? '';
$userName = htmlspecialchars($_SESSION['user_name'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHDS — <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<nav class="navbar">
    <a href="/" class="navbar-brand">🏥 NHDS</a>
    <div class="navbar-links">
        <?php if ($role === 'admin'): ?>
            <a href="/pages/admin/dashboard.php">Dashboard</a>
            <a href="/pages/admin/users.php">Users</a>
            <a href="/pages/admin/add_user.php">Add User</a>
        <?php elseif ($role === 'doctor'): ?>
            <a href="/pages/doctor/dashboard.php">Dashboard</a>
            <a href="/pages/doctor/patients.php">My Patients</a>
            <a href="/pages/doctor/add_record.php">Add Record</a>
            <a href="/pages/doctor/search.php">Search</a>
        <?php elseif ($role === 'patient'): ?>
            <a href="/pages/patient/dashboard.php">Dashboard</a>
            <a href="/pages/patient/records.php">My Records</a>
            <a href="/pages/patient/prescriptions.php">Prescriptions</a>
            <a href="/pages/patient/timeline.php">Timeline</a>
            <a href="/pages/patient/profile.php">Profile</a>
        <?php endif; ?>
        <?php if ($role): ?>
            <span class="nav-user">👤 <?= $userName ?></span>
            <a href="/logout.php" class="btn-logout">Logout</a>
        <?php endif; ?>
    </div>
</nav>
<main class="container">
