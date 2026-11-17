<?php

require __DIR__ . '/../connection/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $sql = "SELECT `user`.*, user_role.role_name 
            FROM `user`
            JOIN user_role ON `user`.role_id = user_role.id
            WHERE `user`.email = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
        
        $role = strtolower($user['role_name']);

        if ($role === 'admin') {
            header("Location: ../dashboard/admin.php");
            exit;
        } 
        elseif ($role === 'student') {
            header("Location: ../stddashboard.php");
            exit;
        } 
        else {
            $error = "Invalid user";
        }
    } 
    else {
        $error = "Wrong email or password";
    }
    mysqli_stmt_close($stmt);
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