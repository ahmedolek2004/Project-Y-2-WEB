<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/User.php';

Permission::require('admin');

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: /pages/admin/users.php?error=Invalid+user+ID');
    exit;
}

if (!Permission::can('delete_user', ['user_id' => $id])) {
    header('Location: /pages/admin/users.php?error=You+cannot+delete+yourself');
    exit;
}

$userModel = new User();
$user = $userModel->findById($id);

if (!$user) {
    header('Location: /pages/admin/users.php?error=User+not+found');
    exit;
}

$userModel->deleteUser($id);
header('Location: /pages/admin/users.php?msg=' . urlencode($user['name'] . ' has been deleted'));
exit;
