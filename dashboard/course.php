
<?php

require __DIR__ . '/../connection/db.php';

$error = "";

# add hte new course
if (isset($_POST['add_course'])) {

    $code = trim($_POST['course_code']);
    $name = trim($_POST['course_name']);
    $fees = $_POST['course_fees'];
    $start = trim($_POST['course_start']);
    $end = trim($_POST['course_end']);

    $duration = $start . "-" . $end;
    $sql = "INSERT INTO course
            (course_code, course_name, duration, fees) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssd", $code, $name, $duration, $fees);
    }
    if ($stmt && mysqli_stmt_execute($stmt)) {
        header("Location: course.php?saved=1"); // send GET Responce
        exit;
    } 
    else {
        $error = "Course could not be added. Check that the course code is unique and all fields are valid.";
    }
}

// DISPLAY COURSES
$sql = "";
$result = mysqli_query($conn, "SELECT * FROM course ORDER BY id");
$totalCourses = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f6f8;
        }
        .sidebar {
            min-height: 100vh;
            background: white;
        }
        .sidebar a {
            color: #626875;
            text-decoration: none;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background: #fff1e6;
            color: #d58b53;
        }
        .directory-banner {
            background: linear-gradient(110deg, #d6a168, #5c9c7c);
        }
    </style>
    
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <aside class="col-md-2 sidebar p-3">
            <h4 class="mb-4">🎓 Student</h4>

            <nav class="nav flex-column gap-2">
                <a href="admin.php" class="p-2 rounded">Admin Zone</a>
                <a href="student.php" class="p-2 rounded">Student Profile</a>
                <a href="course.php" class="p-2 rounded active">Course Directory</a>
                <a href="enrollment.php" class="p-2 rounded">Enrollments</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="col-md-10 p-4">
            <div class="mb-4">
                <p class="text-secondary mb-1">Administration</p>
                <h3>Course Management</h3>
            </div>
            <!-- Banner -->
            <div class="directory-banner text-white rounded-4 p-4 mb-4">
                <h4>Manage your courses</h4>
                <p class="mb-0">View and manage all available courses.</p>
            </div>

            <?php if (isset($_GET['saved'])): ?>
                <div class="alert alert-success">Course added successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">Course updated successfully!!</div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">Course deleted successfully!</div>
            <?php endif; ?>
            <!-- display total course -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <p class="text-secondary mb-1">Total Courses</p>
                    <h3><?= $totalCourses ?></h3>
                </div>
            </div>

            <!-- Add Course -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Add New Course</h5>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Course Code</label>
                                <input type="text" name="course_code" class="form-control" required>
                            
                        </div>
                            <div class="col-md-6">
                                <label class="form-label">Course Name</label>
                                <input type="text" name="course_name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Course Fess</label>
                                <input type="text" name="course_fees" class="form-control" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Start At</label>
                                <input type="text" name="course_start" class="form-control" placeholder="Jan" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">End At</label>
                                <input type="text" name="course_end" class="form-control" placeholder="Dec" required>
                            </div>
                        </div>

                        <button type="submit" name="add_course" class="btn btn-primary btn-sm mt-3">
                            + Add Course
                        </button>
                    </form>
                </div>
            </div>

            <!-- Display Course table  -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5>All Courses</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Course Fees</th>
                                    <th>Duration</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                        <?php while ($course = mysqli_fetch_assoc($result))
                             { ?>
                            <tr>
                                  <td><?= htmlspecialchars($course['course_code']) ?></td>
                                  <td><?= htmlspecialchars($course['course_name']) ?></td>
                                  <td><?= htmlspecialchars($course['fees']) ?></td>
                                 <td><?= htmlspecialchars($course['duration']) ?></td>
                                 <td>
                                     <a href="edit/courseedit.php?id=<?= urlencode($course['course_code']) ?>"
                                       class="btn btn-sm btn-primary">
                                            Manage
                                    </a>
                                        </td>
                            </tr>
                        <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>
