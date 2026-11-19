

<?php

require __DIR__ . '/../auth/check_login.php';
needLogin('student');

require __DIR__ . '/../connection/db.php';

$studentId = $_SESSION['student_id'];

$sql = "SELECT * FROM user WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

// $id = 8;
mysqli_stmt_bind_param($stmt, "i", $studentId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

$error = '';

if (isset($_POST['update_profile'])) {

    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];

    $sql = "UPDATE user SET first_name=?, last_name=?, email=?, phone=?, gender=?, dob=?, address=? WHERE id=?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "sssssssi",
        $firstName, $lastName, $email, $phone,
        $gender, $dob, $address, $studentId
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: stddashboard.php?updated=1");
        exit;
    } else {
        $error = "Profile not updated";
    }
}

// Fetch enrolled courses details
$sql = "SELECT course.* FROM enrollment
        JOIN course ON enrollment.course_id = course.id
        WHERE enrollment.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $studentId);
mysqli_stmt_execute($stmt);

$enrollmentResult = mysqli_stmt_get_result($stmt);

?>
<?php
$pageTitle = 'Student Dashboard';
$pageHeading = 'Welcome, ' . ($student['first_name'] ?? 'Student');
$pageSubtitle = 'Student Account';
$activePage = 'profile';
$studentPortal = true;
$bannerLead = 'Student Number: ' . ($student['reg_num'] ?? '');
$bannerTitle = 'Welcome';
$bannerText = 'Review your personal details and courses enrolled.';



require __DIR__ . '/headder.php';
?>
<?php if (isset($_GET['updated'])) { ?>
                    <div class="alert alert-success">
                        Profile updated successfully.
                    </div>
                <?php } ?>

                <?php if ($error != '') { ?>

                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php } ?>

                <!-- profile section---------------------------------------------------------------------------->
                <section id="profile" class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Personal Details</h2>
                        <form method="POST">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Student Number</label>
                                    <input class="form-control"
                                        value="<?= htmlspecialchars($student['reg_num']) ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="<?= htmlspecialchars($student['email']) ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control"
                                        value="<?= htmlspecialchars($student['first_name']) ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control"
                                        value="<?= htmlspecialchars($student['last_name']) ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="<?= htmlspecialchars($student['phone']) ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>

                                 <select name="gender" class="form-select">
                                        <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                        <option value="Other" <?= $student['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control"
                                        value="<?= htmlspecialchars($student['dob'] ?? '') ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" rows="3"
                                        class="form-control"><?= htmlspecialchars($student['address'] ?? '') ?></textarea>
                                </div>

                            </div>
                            <button type="submit" name="update_profile"
                                class="btn btn-primary mt-4">
                                Save Profile
                            </button>
                        </form>
                    </div>
                </section>

                <!-- ---------------------------------------------------------------------------------------------------------------------------- -->
                <!-- Enrolled courses -->
                                <section id="enrollments" class="card border-0 shadow-sm rounded-4">

    <div class="card-body p-4">
        <h2 class="h5 mb-3">My Courses</h2>
             <p class="text-secondary">
                    <?php $total_course = mysqli_num_rows($enrollmentResult) ?>
              Total Courses: <?= $total_course ?>
             </p>

        <?php if ($total_course > 0) : ?>
            <div class="row g-3">
                <?php while ($course = mysqli_fetch_assoc($enrollmentResult)) { ?>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">

                            <h5><?= htmlspecialchars($course['course_name']) ?></h5>
                            
                            <span class="badge bg-primary"> <?= htmlspecialchars($course['course_code']) ?> </span>
                            <p class="mt-2">  <?= htmlspecialchars($course['description'] ?? '') ?> </p>
                            <p> Duration: <?= htmlspecialchars($course['duration'] ?? '') ?> </p>

                            <p>  Fees: <?= htmlspecialchars($course['fees'] ?? '') ?> </p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php endif; ?>
    </div>
  </section>



        </main>
    </div>
</div>
</body>
</html>
