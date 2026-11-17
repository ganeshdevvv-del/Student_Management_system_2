
<?php
require __DIR__ . '/../connection/db.php';

// get students..........
$students = mysqli_query($conn, "SELECT * FROM user WHERE role_id = 2");

// Get course....................
$courses = mysqli_query($conn, "SELECT * FROM course");


if (isset($_POST['save'])) {
    $user_id = (int)$_POST['user_id'];
    $course_ids = $_POST['course_ids'] ?? [];

    # Delete old enrollments
    $sql = "DELETE FROM enrollment WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // updste enrollment 
    $sql = "INSERT INTO enrollment (user_id, course_id, price) SELECT ?, id, fees 
    FROM course 
    WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    foreach ($course_ids as $course_id) {
        $course_id = (int)$course_id;
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $course_id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: enrollment.php?saved=1");
    exit;
}
// Get enrollment details
$sql = "SELECT u.id, u.reg_num, u.first_name, u.last_name, u.email, c.course_name FROM user u
        LEFT JOIN enrollment e ON u.id = e.user_id
        LEFT JOIN course c ON e.course_id = c.id
        WHERE u.role_id = 2
        ORDER BY u.id";

$result = mysqli_query($conn, $sql);

$enrollments = [];

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];

    if (!isset($enrollments[$id])) {
        $enrollments[$id] = [
            'reg_num' => $row['reg_num'],
            'name' => $row['first_name'] . ' ' . $row['last_name'],
            'email' => $row['email'],
            'courses' => []
        ];
    }
    if ($row['course_name'] != NULL) {
        $enrollments[$id]['courses'][] = $row['course_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f6f8; }
        .sidebar { min-height: 100vh; background: white; }
        .sidebar a { color: #626875; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active {
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
                <a href="admin.php" class="p-2 rounded">Admin Dashboard</a>
                <a href="student.php" class="p-2 rounded">Student Profile</a>
                <a href="course.php" class="p-2 rounded">Courses</a>
                <a href="enrollment.php" class="p-2 rounded active">Enroll Student</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="col-md-10 p-4">
            <p class="text-secondary mb-1">Administration</p>
            <h3 class="mb-4">Student Enrollment</h3>

            <!-- Banner -->
            <div class="directory-banner text-white rounded-4 p-4 mb-4">
                <h4>Manage Enrollments</h4>
                <p class="mb-0">Enroll students in available courses.</p>
            </div>

            <?php if (isset($_GET['saved'])): ?>
                <div class="alert alert-success">Enrollment saved successfully!</div>
            <?php endif; ?>

            <!-- Enrollment Form -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Student Enrollment</h5>
                    <form method="POST">
                        <label class="form-label">Select Student</label>
                        <select name="user_id" class="form-select mb-3" required>
                            <option value="">Choose Student</option>
                            <?php while ($student = mysqli_fetch_assoc($students)): ?>
                                <option value="<?= $student['id'] ?>">
                                    <?= htmlspecialchars($student['reg_num'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                        <label class="form-label">Select Courses</label>
                        <?php while ($course = mysqli_fetch_assoc($courses)): ?>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input"
                                    name="course_ids[]"
                                    value="<?= $course['id'] ?>">
                                <label class="form-check-label">
                                    <?= htmlspecialchars($course['course_name']) ?>
                                    (<?= htmlspecialchars($course['course_code']) ?>)
                                </label>
                            </div>
                        <?php endwhile; ?>

                        <button type="submit" name="save" class="btn btn-primary btn-sm mt-3">
                            Save Enrollment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Enrollment Table -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Student Enrollments</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Reg Number</th>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Courses</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrollments as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['reg_num'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($student['name']) ?></td>
                                        <td><?= htmlspecialchars($student['email']) ?></td>
                                        <td><?= htmlspecialchars(implode(', ', $student['courses']) ?: 'No courses') ?></td>
                                    </tr>
                                <?php endforeach; ?>
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
