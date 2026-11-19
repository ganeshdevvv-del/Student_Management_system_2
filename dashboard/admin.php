
<?php
require __DIR__ . '/../auth/check_login.php';
needLogin('admin');

require __DIR__ . '/../connection/db.php';

#select query
$sql = "SELECT
        (SELECT COUNT(*) FROM user where role_id = 2) AS total_students,
        (SELECT COUNT(*) FROM course) as total_courses" ;

$result = mysqli_query($conn, $sql);

$stats = mysqli_fetch_assoc($result);
?>
<?php
$pageTitle = 'Admin Dashboard';
$pageHeading = 'Dashboard';
$pageSubtitle = '';
$activePage = 'admin';
$bannerLead = 'Academic Year 2026-2029';
$bannerTitle = 'Welcome to the ADMIN Dashboard';
$bannerText = 'View courses & Enroll Students.';
$headerAction = ['href' => '../auth/newAdmin.php', 'label' => '+ Add Admin'];
$bannerActions = [
    ['href' => 'student.php', 'label' => 'View overview', 'class' => 'btn btn-light rounded-pill px-4'],
    ['href' => 'enrollment.php', 'label' => 'Manage enrollments', 'class' => 'btn btn-outline-light rounded-pill px-4 ms-2'],
];

require __DIR__ . '/headder.php';
?>

<?php if (isset($_GET['created'])): ?>
    <div class="alert alert-success">Admin added successfully.</div>
<?php endif; ?>
<section id="overview" class="row g-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <!-- <div class="stat-icon bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center mb-4">♙</div> -->

                            <p class="text-secondary mb-1">Total Students</p>
                            <h3 class="h4 mb-0"> <?=(int)$stats["total_students"]?> </h3>
                            
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <!-- <div class="stat-icon bg-success-subtle rounded-circle d-flex align-items-center justify-content-center mb-4">▤</div> -->
                            <p class="text-secondary mb-1">Courses</p>

                            <h3 class="h4 mb-0"> <?=(int)$stats["total_courses"]?> </h3>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>
</body>
</html>
