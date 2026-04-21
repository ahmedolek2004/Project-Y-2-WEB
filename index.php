<?php
require_once __DIR__ . '/includes/autoload.php';
Middleware::isLoggedIn() ? Middleware::redirectByRole() : header("Location: /login.php");
exit;
