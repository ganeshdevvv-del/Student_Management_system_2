<?php
session_start();

function needLogin($requiredRole = "")
{
    // The app can be hosted at the web root or inside a subdirectory.
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $appBase = preg_replace('#/(?:dashboard|auth)(?:/.*)?$#', '', $scriptName);
    $appBase = rtrim($appBase ?? '', '/');

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        header('Location: ' . $appBase . '/login.php');
        exit;
    }
    $role = $_SESSION['user_role'];

    if ($requiredRole != null && $role != $requiredRole) {

        if ($role == 'student') {
            header('Location: ' . $appBase . '/dashboard/stddashboard.php');
        } else {
            header('Location: ' . $appBase . '/dashboard/admin.php');
        }
        exit;
    }
}
