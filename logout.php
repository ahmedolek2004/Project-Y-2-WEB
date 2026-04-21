<?php
require_once __DIR__ . '/includes/autoload.php';
session_unset();
session_destroy();
header("Location: /login.php?msg=Logged+out+successfully");
exit;
