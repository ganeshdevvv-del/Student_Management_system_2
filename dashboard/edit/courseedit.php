<?php
require __DIR__ . '/../../auth/check_login.php';
needLogin('admin');

require __DIR__ . '/../../connection/db.php';
$message = '';
$messageType = 'danger';
    $courseCode = $_GET['id'] ;
    if ($courseCode === '') {
        header('Location: ../course.php?error=1');
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
                header('Location: ../course.php?deleted=1');
                exit;
            }
            $message = 'Course could not be deleted.';
        }
        
        elseif (isset($_POST['save'])) {
            $newCode = trim($_POST['course_code'] ?? '');
            $name = trim($_POST['course_name'] ?? '');
            $duration = trim($_POST['duration'] ?? '');
            $fees = filter_var($_POST['fees'] ?? '', FILTER_VALIDATE_FLOAT);

            if ($newCode === '' || $name === '' || $duration === '' || $fees === false || $fees < 0) {
                $message = 'Enter the required fields & fees not in negative';
            } else {
                $sql = 'UPDATE course SET course_code = ?, course_name = ?, duration = ?, fees = ? 
                WHERE course_code = ?';
                $update = mysqli_prepare($conn, $sql);
                if ($update) {
                    mysqli_stmt_bind_param($update, 'sssds', $newCode, $name, $duration, $fees, $courseCode);
                }
                if ($update && mysqli_stmt_execute($update)) {
                    header('Location: ../course.php?updated=1');
                    exit;
                }
                $message = 'Course not be update';
            }
        }
    }
    $sql = 'SELECT course_code, course_name, duration, fees FROM course WHERE course_code = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql );
    mysqli_stmt_bind_param($stmt, 's', $courseCode);
    mysqli_stmt_execute($stmt);
    $course = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$course) {
        header('Location: ../course.php?error=1');
        exit;
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
    <title>Manage Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5" style="max-width: 760px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-secondary mb-1">Course Directory</p>
            <h2 class="mb-0">Manage Course</h2>
        </div>
        <a href="../course.php" class="btn btn-outline-secondary">Back to courses</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    
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
</main>
</body>
</html>
