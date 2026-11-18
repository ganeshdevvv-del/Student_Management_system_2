
seperate  student dashboard....................
<?php
require __DIR__ . '/connection/db.php';

$student_id = $_SESSION["student_id"];

$sql = "SELECT * FROM user WHERE id = ?";
$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt, "s", $student_id);

mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if(isset($_POST["updateProfile"])){
    $first_name = trim($_POST['first_name']??"");
    $last_name = trim($_POST['last_name']??"");
    $email = trim ($_POST['email']??"");
    $phone = trim($_POST['phone']??"");
    $gender =trim($_POST["gender"]??'');
    $dob = trim($_POST['dob'])??"";
    $address = trim($_POST["address"]??"");

}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f6f8;
            color: #252a34;
        }
        .sidebar {
            min-height: 100vh;
            background: white;
        }
        .sidebar a {
            color: #626875;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #fff1e6;
            color: #d58b53;
        }
        .banner {
            background: linear-gradient(110deg, #d6a168, #5c9c7c);
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar-->
            <aside class="col-md-3 col-lg-2 sidebar p-3">
                <h4 class="mb-4">Student Portal</h4>
                <nav class="nav flex-column gap-2">
                    <a class="rounded p-2 active" href="#profile">My Profile</a>
                </nav>
            </aside>
            <main class="col-md-9 col-lg-10 p-3 p-lg-4">
                <header class="mb-4">
                    <p class="text-secondary mb-1">Student Account</p>
                    <h1 class="h3">
                        Welcome, NAME..........
                    </h1>
                </header>
                <!-- Banner -->
                <section class="banner text-white rounded-4 p-4 mb-4">
                    <p>Student Number:   </p>
                    <h2 class="h4">Wellcome</h2>
                    <p>Review your personal details and courses enrolled.</p>
                </section>

<!-- ------------------------------------------------------------------------------------------------------ -->
                <!-- Profile section -->
                <section id="profile" class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Personal Details</h2>
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Student Number</label>
                                    <input class="form-control"
                                        value="<?="" ?>" readonly>
                         </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="<?= ""?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control"
                                        value="<?= ""?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control"
                                        value="<?= "" ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="<?= ""?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>

                                    <select name="gender" class="form-select">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control"
                                        value="<?= ""?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" rows="3"
                                        class="form-control"></textarea>
                                </div>
                         </div>

                            <button type="submit" name="update_profile"
                                class="btn btn-primary mt-4">
                                Save Profile
                            </button>
                        </form>
                    </div>
                </section>
<!-- -------------------------------------------------------------------------------------- -->
                <section>

                    //display enrollement details
                    
                </section>

            </main>
        </div>
    </div>
</body>
</html>
         