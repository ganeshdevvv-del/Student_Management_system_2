<?php
require __DIR__ . '/../../auth/check_login.php';
needLogin('admin');
require __DIR__ . '/../../connection/db.php';
$message = '';
$messageType = 'danger';
    $studentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$studentId) {
        header('Location: ../student.php?error=1');
        exit;
    }

    //------------------------------------------------------------------------------------------------------------------
    # Delete student from record.............
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $sql = 'DELETE FROM user WHERE id = ? AND role_id = 2';
            $delete = mysqli_prepare($conn,$sql );
            mysqli_stmt_bind_param($delete, 'i', $studentId);
            if (mysqli_stmt_execute($delete) && mysqli_stmt_affected_rows($delete) === 1) {
                header('Location: ../student.php?deleted=1');
                exit;
            }
            $message = 'Student could not be deleted.';
        }
        
        elseif (isset($_POST['save'])) {
            $selectedCourseIds = $_POST['course_ids'] ?? [];
            if (!is_array($selectedCourseIds)) {
                $selectedCourseIds = [];
            }
            $selectedCourseIds = array_values(array_unique(array_filter(array_map('intval', $selectedCourseIds))));
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $gender = trim($_POST['gender'] ?? '');
            $dob = trim($_POST['dob'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if ($firstName === '' || $lastName === '' || $email === '') {
                $message = 'First name, last name, and email are required.';
            } 


            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Please enter a valid email address.';
            } 
            
            else {
                $sql = 'UPDATE user SET first_name = ?, last_name = ?, email = ?, phone = ?, gender = ?, dob = ?, address = ? 
                WHERE id = ? AND role_id = 2';
                $update = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($update, 'sssssssi', $firstName, $lastName, $email, $phone, $gender, $dob, $address, $studentId);
                mysqli_begin_transaction($conn);
                try {
                    mysqli_stmt_execute($update);

                    $deleteEnrollments = mysqli_prepare($conn, 'DELETE FROM enrollment WHERE user_id = ?');
                    mysqli_stmt_bind_param($deleteEnrollments, 'i', $studentId);
                    mysqli_stmt_execute($deleteEnrollments);

                    $addEnrollment = mysqli_prepare($conn, 'INSERT INTO enrollment (user_id, course_id, price) SELECT ?, id, fees FROM course WHERE id = ?');
                    mysqli_stmt_bind_param($addEnrollment, 'ii', $studentId, $courseId);
                    foreach ($selectedCourseIds as $courseId) {
                        mysqli_stmt_execute($addEnrollment);
                    }

                    mysqli_commit($conn);
                    header('Location: ../student.php?updated=1');
                    exit;
                } catch (Throwable $exception) {
                    mysqli_rollback($conn);
                    $message = 'Student and course changes could not be saved.';
                }
            }
        }
    }
    $sql = 'SELECT * FROM user WHERE id = ? AND role_id = 2 LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $studentId);
    mysqli_stmt_execute($stmt);
    $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$student) {
        header('Location: ../student.php?error=1');
        exit;
    }

    $courses = "";

    $courses = mysqli_query($conn, 'SELECT id, course_code, course_name, fees FROM course ORDER BY course_name');
    if (!isset($selectedCourseIds)) {
        $selectedCourseIds = [];
        $enrolled = mysqli_prepare($conn, 'SELECT course_id FROM enrollment WHERE user_id = ?');
        mysqli_stmt_bind_param($enrolled, 'i', $studentId);
        mysqli_stmt_execute($enrolled);
        $enrolledResult = mysqli_stmt_get_result($enrolled);
        while ($enrollment = mysqli_fetch_assoc($enrolledResult)) {
            $selectedCourseIds[] = (int)$enrollment['course_id'];
        }
    }
function fieldValue($record, $key) {
    return htmlspecialchars($record[$key] ?? '', ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5" style="max-width: 760px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-secondary mb-1">Student Directory</p>
            <h2 class="mb-0">Manage Student</h2>
        </div>
        <a href="../student.php" class="btn btn-outline-secondary">Back to students</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    
        <form method="POST" class="card border-0 shadow-sm p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First name *</label>
                    <input id="first_name" name="first_name" class="form-control" required value="<?= fieldValue($student, 'first_name') ?>">
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last name *</label>
                    <input id="last_name" name="last_name" class="form-control" required value="<?= fieldValue($student, 'last_name') ?>">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input id="email" name="email" type="email" class="form-control" required value="<?= fieldValue($student, 'email') ?>">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input id="phone" name="phone" class="form-control" value="<?= fieldValue($student, 'phone') ?>">
                </div>
                <div class="col-md-6">
                    <label for="gender" class="form-label">Gender</label>
                    <input id="gender" name="gender" class="form-control" value="<?= fieldValue($student, 'gender') ?>">
                </div>
                <div class="col-md-6">
                    <label for="dob" class="form-label">Date of birth</label>
                    <input id="dob" name="dob" type="date" class="form-control" value="<?= fieldValue($student, 'dob') ?>">
                </div>
                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3"><?= fieldValue($student, 'address') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Courses</label>

                    <?php if (mysqli_num_rows($courses) === 0): ?>
                        <p class="text-secondary">No courses are available.</p>
                    <?php else: ?>
                        <div id="course-list">
                        <?php while ($courseOption = mysqli_fetch_assoc($courses)): ?>
                            <div class="form-check course-option">
                                <input class="form-check-input" type="checkbox" name="course_ids[]"
                                    id="course_<?= (int)$courseOption['id'] ?>" value="<?= (int)$courseOption['id'] ?>"
                                    <?= in_array((int)$courseOption['id'], $selectedCourseIds, true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="course_<?= (int)$courseOption['id'] ?>">
                                    <?= htmlspecialchars($courseOption['course_name'] . ' (' . $courseOption['course_code'] . ')', ENT_QUOTES, 'UTF-8') ?>
                                </label>
                            </div>
                            
                        <?php endwhile; ?>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-3" id="course-pagination">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="course-prev">Previous</button>
                            <span id="course-page" class="small text-secondary"></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="course-next">Next</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4"> 
                <button type="submit" name="save" class="btn btn-primary">Save changes</button>
                <button type="submit" name="delete" class="btn btn-outline-danger">Delete student</button>
            </div>
        </form>
</main>
<script>
    const courses = document.querySelectorAll('.course-option');
    let page = 0;
    const limit = 10;

    function showPage() {
        let totalPages = Math.ceil(courses.length / limit);

        courses.forEach((course, index) => {
            course.hidden = index < page * limit || index >= (page + 1) * limit;
        });

        document.getElementById('course-page').textContent = `Page ${page + 1} of ${totalPages}`;
        document.getElementById('course-prev').disabled = page === 0;
        document.getElementById('course-next').disabled = page === totalPages - 1;
    }

    document.getElementById('course-prev').onclick = () => {
        page--;
        showPage();
    };

    document.getElementById('course-next').onclick = () => {
        page++;
        showPage();
    };

    showPage();
</script>
</body>
</html>
