<?php
require_once __DIR__ . '/../classes/Auth.php';
$auth = new Auth();
$auth->logout();
header('Location: /index.php?msg=You+have+been+signed+out');
exit;
