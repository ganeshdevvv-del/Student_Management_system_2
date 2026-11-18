<?php
session_start();

function needLogin($requiredRole = "")
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        header("Location: /ganesh/manage/auth/login.php");
        exit;
    }
    $role = $_SESSION['user_role'];

    if ($requiredRole != null && $role != $requiredRole) {
        
        if ($role == 'student') 
            {
            header("Location: /ganesh/manage/stddashboard.php");
        } 
        else {
            header("Location: /ganesh/manage/dashboard/admin.php");
        }
        exit;
    }
}