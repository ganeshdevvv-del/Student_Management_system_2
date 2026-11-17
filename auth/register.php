<?php
require __DIR__ . '/../connection/db.php';

$error = '';
$form = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'gender' => '',
    'dob' => '',
    'address' => '',
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $field => $value) {
        $form[$field] = trim($_POST[$field] ?? '');
    }
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($form['first_name'] === '' || $form['last_name'] === '' || $form['email'] === '' || $password === '')
         {
        $error = 'Please fill all required fields.';
    } 
     elseif ($password !== $confirmPassword) {
        $error = 'Passwords not match.';
    }
     else {
        // $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $hashPassword = $password;
        $phone = $form['phone'] !== '' ? $form['phone'] : null;
        $gender = $form['gender'] !== '' ? $form['gender'] : null;
        $dob = $form['dob'] !== '' ? $form['dob'] : null;
        $address = $form['address'] !== '' ? $form['address'] : null;
        $studentRoleId = 2;

        $sql = "INSERT INTO user
                (first_name, last_name, email, password, phone, gender, dob, address, role_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssssssi', $form['first_name'], $form['last_name'], $form['email'], 
                                  $hashPassword, 
                                  $phone, $gender, $dob, $address, $studentRoleId
                                  );
            if (mysqli_stmt_execute($stmt)) {
                header('Location: ../dashboard/student.php?created=1');
                exit;
            }

            // $error = mysqli_stmt_errno($stmt) == 1062 ? "Email already register" : "Registeration failed";
            if (mysqli_stmt_errno($stmt) == 1062) { 
                 $error = "Email already registered."; // 1062 mySQL duplicate record error. 
              }
            else {
                $error = "Registration failed.";
                } 
             mysqli_stmt_close($stmt); 
        } 
        else {
            $error = 'Could not prepare the registration. Please try again.';
        }
    }
     }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f6f8; color: #252a34; }
    </style>
    <link rel="stylesheet" href="style.css">
</head>
<body class="py-5">
<main class="container" style="max-width: 720px;">
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-4">
            <h1 class="h4 mb-2">Add Student</h1>
            <p class="text-secondary">Fields marked with * are required.</p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First name *</label>
                        <input id="first_name" type="text" name="first_name" class="form-control" maxlength="150" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last name </label>
                        <input id="last_name" type="text" name="last_name" class="form-control" maxlength="150" >
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email *</label>
                        <input id="email" type="email" name="email" class="form-control" maxlength="150" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input id="phone" type="text" name="phone" class="form-control" maxlength="20" >
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password *</label>
                        <input id="password" type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirm password *</label>
                        <input id="confirm_password" type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" name="gender" class="form-select">
                             <option value="">Select gender</option>
                             <option value="Male">Male</option>
                             <option value="Female">Female</option>
                             <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="dob" class="form-label">Date of birth</label>
                        <input id="dob" type="date" name="dob" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="address" class="form-label">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Register</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <a href="../dashboard/student.php" class="btn btn-link">Back</a>
                </div>
            </form>
        </div>
    </div>
</main>
</body>
</html>
