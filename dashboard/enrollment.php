
<?php
require __DIR__ . '/../auth/check_login.php';
needLogin('admin');

require __DIR__ . '/../connection/db.php';

// Get students and courses
$students = mysqli_query($conn, "SELECT * FROM user WHERE role_id = 2");
$courses = mysqli_query($conn, "SELECT * FROM course");

// Save enrollment
if (isset($_POST['save'])) {

    $user_id = (int)$_POST['user_id'];
    $course_ids = $_POST['course_ids'] ?? [];

    // Delete old enrollments
    $stmt = mysqli_prepare($conn, "DELETE FROM enrollment WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // Insert new enrollments
    $stmt = mysqli_prepare($conn, "INSERT INTO enrollment (user_id, course_id, price) SELECT ?, id, fees FROM course WHERE id = ?");

    foreach ($course_ids as $course_id) {
        $course_id = (int)$course_id;
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $course_id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: enrollment.php?saved=1");
    exit;
}

// Search enrollments
$search = $_GET['search'] ?? '';
$searchValue = "%$search%";

$sql = "SELECT u.id, u.reg_num, u.first_name, u.last_name, u.email, c.course_name
        FROM user u
        LEFT JOIN enrollment e ON u.id = e.user_id
        LEFT JOIN course c ON e.course_id = c.id
        WHERE u.role_id = 2
        AND (u.first_name LIKE ? OR u.last_name LIKE ?
        OR u.reg_num LIKE ? OR u.email LIKE ?)
        ORDER BY u.id";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $searchValue, $searchValue, $searchValue, $searchValue);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

// Organize enrollments
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

    if ($row['course_name']) {
        $enrollments[$id]['courses'][] = $row['course_name'];
    }
}
?>

<?php
$pageTitle = 'Student Enrollment';
$pageHeading = 'Student Enrollment';
$pageSubtitle = 'Administration';
$activePage = 'enrollments';
$bannerTitle = 'Manage Enrollments';
$bannerText = 'Enroll students in available courses.';
require __DIR__ . '/headder.php';
?>

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

                    <!-- search options.............. -->
                    <div class="table-responsive">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                        <h5 class="mb-0">All Students</h5>
                        <form method="GET" class="d-flex gap-2">
                            <input type="search" name="search" class="form-control"
                                placeholder="Search by student name..."
                                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="enrollment.php" class="btn btn-outline-secondary">Clear</a>
                        </form>
                    </div>

                    <!-- displat the table  -->
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
                                <?php if (!$enrollments): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary py-4">No students found.</td>
                                    </tr>
                                <?php endif; ?>
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
