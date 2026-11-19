<?php
session_start();
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

$appBase = preg_replace('#/auth(?:/.*)?$#', '', $scriptName);

$appBase = rtrim($appBase ?? '', '/');

session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');
header('Location: ' . $appBase . '/login.php');
exit;
