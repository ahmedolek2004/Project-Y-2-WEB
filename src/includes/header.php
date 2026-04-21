<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$currentRole = $_SESSION['user_role'] ?? '';
$currentName = $_SESSION['user_name'] ?? '';

// Nav links per role
$navLinks = [
    'admin' => [
        ['href' => '/pages/admin/dashboard.php',  'icon' => '◈', 'label' => 'Dashboard'],
        ['href' => '/pages/admin/users.php',       'icon' => '◎', 'label' => 'Users'],
        ['href' => '/pages/admin/add_user.php',    'icon' => '⊕', 'label' => 'Add User'],
        ['href' => '/pages/admin/search.php',      'icon' => '◉', 'label' => 'Search'],
    ],
    'doctor' => [
        ['href' => '/pages/doctor/dashboard.php',  'icon' => '◈', 'label' => 'Dashboard'],
        ['href' => '/pages/doctor/records.php',    'icon' => '◎', 'label' => 'Records'],
        ['href' => '/pages/doctor/add_record.php', 'icon' => '⊕', 'label' => 'New Record'],
        ['href' => '/pages/admin/search.php',      'icon' => '◉', 'label' => 'Search'],
    ],
    'patient' => [
        ['href' => '/pages/patient/dashboard.php',  'icon' => '◈', 'label' => 'Dashboard'],
        ['href' => '/pages/patient/history.php',    'icon' => '◎', 'label' => 'My History'],
        ['href' => '/pages/patient/prescriptions.php','icon' => '⊕', 'label' => 'Prescriptions'],
        ['href' => '/pages/patient/profile.php',    'icon' => '◉', 'label' => 'Profile'],
    ],
];

$links = $navLinks[$currentRole] ?? [];
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?= $pageTitle ?? 'MediCore System' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php if ($currentRole): ?>
<nav class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-content">
            <div class="brand-icon">✚</div>
            <div class="brand-text">
                <span class="brand-name">MediCore</span>
                <span class="brand-role"><?= htmlspecialchars(ucfirst($currentRole)) ?></span>
            </div>
        </div>
        <button class="sidebar-toggle" aria-label="Toggle sidebar" title="Collapse/Expand sidebar">◄</button>
    </div>
    <div class="sidebar-user">
        <div class="user-avatar"><?= strtoupper(substr($currentName, 0, 1)) ?></div>
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($currentName) ?></span>
            <span class="user-role-badge role-<?= $currentRole ?>"><?= ucfirst($currentRole) ?></span>
        </div>
    </div>
    <ul class="nav-list">
        <?php foreach ($links as $link): 
            $isActive = basename($link['href']) === $currentPage ? 'active' : '';
        ?>
        <li class="nav-item">
            <a href="<?= $link['href'] ?>" class="nav-link <?= $isActive ?>">
                <span class="nav-icon"><?= $link['icon'] ?></span>
                <span><?= $link['label'] ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <div class="sidebar-footer">
        <a href="/includes/logout.php" class="logout-btn">
            <span>⏻</span> Sign Out
        </a>
    </div>
</nav>
<main class="main-content">
<?php else: ?>
<main class="auth-wrapper">
<?php endif; ?>

