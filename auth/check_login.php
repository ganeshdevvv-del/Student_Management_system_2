<?php
session_start();

function needLogin($requiredRole = "")
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        header("Location: /auth/login.php");
        exit;
    }
    $role = $_SESSION['user_role'];

    if ($requiredRole != null && $role != $requiredRole) {
        
        if ($role == 'student') {
            header("Location: /stddashboard.php");
        } else {
            header("Location: /dashboard/admin.php");
        }
        exit;
    }
}