<?php
require __DIR__ . '/../connection/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = "admin";
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $role = 1;

    if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    }
     else {
        // $hashPassword = password_hash($password, PASSWORD_DEFAULT);
           $hashPassword = $password;

        $sql = "INSERT INTO user (first_name, last_name, email, password, role_id)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssi', $firstName, $lastName, $email, $hashPassword, $role);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: ../dashboard/admin.php?created=1');
                exit;
            }

            $error = mysqli_stmt_errno($stmt) === 1062
                ? 'That email address is already registered.'
                : 'Could not add the admin. Please try again.';
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f6f8; color: #252a34; }
    </style>
</head>
<body class="py-5">
<main class="container" style="max-width: 600px;">
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Add Admin</h1>

            <form method="POST">
                <div class="mb-3">
                    <label for="first_name" class="form-label">Name</label>
                    <input id="first_name" type="text" name="first_name" class="form-control" maxlength="150">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" class="form-control" maxlength="150">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Add Admin</button>  
                <a href="../dashboard/admin.php" class="btn btn-link">Back</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
