
<?php
session_start();
require __DIR__ . '/../connection/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $sql = "SELECT u.*, r.role_name FROM `user` u 
            JOIN user_role r ON u.role_id = r.id 
            WHERE u.email = ? LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        
        if ($user &&(password_verify($password, $user['password']) || $password === $user['password']))
         {
            $role = strtolower($user['role_name']);
            
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_role'] = $role;

            if ($role === 'admin') {
                header("Location: ../dashboard/admin.php");
                exit;
            } 
            elseif ($role === 'student') {
                $_SESSION['student_id'] = (int)$user['id'];
                header("Location: ../stddashboard.php");
                exit;
            } 
            else {
                $error = "Invalid user";
            }
        } 
        else {
            $error = "wrong email or password";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Database query failed";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <main class="container login-container">
        <h2>Welcome Back</h2>
        <p><?= htmlspecialchars($error) ?></p>
        <form method="POST">

            <div class="form-grid">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-actions">
                    <button type="submit">Login</button>
                </div>

            </div>
        </form>

    </main>
</body>
</html>
