<?php
require __DIR__ . '/../connection/db.php';

$type = $_GET['type'] ?? 'student';
$message = '';
$messageType = 'danger';

// for course...........................
if ($type === 'course') {
    $courseCode = $_GET['id'] ;
    if ($courseCode === '') {
        header('Location: course.php?error=1');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $sql = 'DELETE FROM course WHERE course_code = ?';
            $delete = mysqli_prepare($conn, $sql);
            if ($delete) {
                mysqli_stmt_bind_param($delete, 's', $courseCode);
            }
            if ($delete && mysqli_stmt_execute($delete) && mysqli_stmt_affected_rows($delete) === 1) {
                header('Location: course.php?deleted=1');
                exit;
            }
            $message = 'Course could not be deleted.';
        } elseif (isset($_POST['save'])) {
            $newCode = trim($_POST['course_code'] ?? '');
            $name = trim($_POST['course_name'] ?? '');
            $duration = trim($_POST['duration'] ?? '');
            $fees = filter_var($_POST['fees'] ?? '', FILTER_VALIDATE_FLOAT);

            if ($newCode === '' || $name === '' || $duration === '' || $fees === false || $fees < 0) {
                $message = 'Enter a course code, name, duration, and a valid non-negative fee.';
            } else {
                $sql = 'UPDATE course SET course_code = ?, course_name = ?, duration = ?, fees = ? WHERE course_code = ?';
                $update = mysqli_prepare($conn, $sql);
                if ($update) {
                    mysqli_stmt_bind_param($update, 'sssds', $newCode, $name, $duration, $fees, $courseCode);
                }
                if ($update && mysqli_stmt_execute($update)) {
                    header('Location: course.php?updated=1');
                    exit;
                }
                $message = 'Course could not be updated. Check that the course code is unique.';
            }
        }
    }
    $sql = 'SELECT course_code, course_name, duration, fees FROM course WHERE course_code = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql );
    mysqli_stmt_bind_param($stmt, 's', $courseCode);
    mysqli_stmt_execute($stmt);
    $course = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$course) {
        header('Location: course.php?error=1');
        exit;
    }
} else 
{
    $studentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$studentId) {
        header('Location: student.php?error=1');
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
                header('Location: student.php?deleted=1');
                exit;
            }
            $message = 'Student could not be deleted.';
        } elseif (isset($_POST['save'])) {
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
            } else {
                $sql = 'UPDATE user SET first_name = ?, last_name = ?, email = ?, phone = ?, gender = ?, dob = ?, address = ? WHERE id = ? AND role_id = 2';
                $update = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($update, 'sssssssi', $firstName, $lastName, $email, $phone, $gender, $dob, $address, $studentId);
                if (mysqli_stmt_execute($update) && mysqli_stmt_affected_rows($update) >= 0) {
                    header('Location: student.php?updated=1');
                    exit;
                }
                $message = 'Student Not update';
            }
        }
    }
    $sql = 'SELECT * FROM user WHERE id = ? AND role_id = 2 LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $studentId);
    mysqli_stmt_execute($stmt);
    $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$student) {
        header('Location: student.php?error=1');
        exit;
    }
}
//------------------------------------------------------------------------------------------------------------------
function fieldValue($record, $key) {
    return htmlspecialchars($record[$key] ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $type === 'course' ? 'Manage Course' : 'Manage Student' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5" style="max-width: 760px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-secondary mb-1"><?= $type === 'course' ? 'Course Directory' : 'Student Directory' ?></p>
            <h2 class="mb-0"><?= $type === 'course' ? 'Manage Course' : 'Manage Student' ?></h2>
        </div>
        <a href="<?= $type === 'course' ? 'course.php' : 'student.php' ?>" class="btn btn-outline-secondary">Back to <?= $type === 'course' ? 'courses' : 'students' ?></a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($type === 'course'): ?>
        <form method="POST" class="card border-0 shadow-sm p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="course_code" class="form-label">Course code *</label>
                    <input id="course_code" name="course_code" class="form-control" value="<?= fieldValue($course, 'course_code') ?>">
                </div>
                <div class="col-md-6">
                    <label for="course_name" class="form-label">Course name *</label>
                    <input id="course_name" name="course_name" class="form-control" value="<?= fieldValue($course, 'course_name') ?>">
                </div>
                <div class="col-md-6">
                    <label for="duration" class="form-label">Duration *</label>
                    <input id="duration" name="duration" class="form-control" value="<?= fieldValue($course, 'duration') ?>">
                </div>
                <div class="col-md-6">
                    <label for="fees" class="form-label">Fees *</label>
                    <input id="fees" name="fees" type="number" min="0" step="0.01" class="form-control" value="<?= fieldValue($course, 'fees') ?>">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" name="save" class="btn btn-primary">Save changes</button>
                <button type="submit" name="delete" class="btn btn-outline-danger">Delete course</button>
            </div>
        </form>
    <!-- --------------------------------------------------------------------------------------------------------------------------------------------------------------------- -->
    <?php else: ?>
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
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" name="save" class="btn btn-primary">Save changes</button>
                <button type="submit" name="delete" class="btn btn-outline-danger">Delete student</button>
            </div>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
