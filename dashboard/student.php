
<?php
require __DIR__ . '/../auth/check_login.php';
needLogin('admin');

require __DIR__ . '/../connection/db.php';

$search = $_GET['search'] ?? '';
$perPage = 10;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
    'options' => ['default' => 1, 'min_range' => 1],
]);
$searchValue = "%$search%";
//paggination for display table
$countSql = "SELECT COUNT(*) AS total FROM user WHERE role_id = 2 AND (first_name LIKE ? OR last_name LIKE ?)";
$countStmt = mysqli_prepare($conn, $countSql);
mysqli_stmt_bind_param($countStmt, "ss", $searchValue, $searchValue);
mysqli_stmt_execute($countStmt);
$filteredStudentCount = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($countStmt))['total'];
$totalPages = max(1, (int) ceil($filteredStudentCount / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

// search the student
$sql = "SELECT * FROM user WHERE role_id = 2 AND (first_name LIKE ? OR last_name LIKE ?) ORDER BY reg_num ASC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssii", $searchValue, $searchValue, $perPage, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$sql = "SELECT COUNT(*) AS total FROM user WHERE role_id = 2";

$countResult = mysqli_query($conn, $sql);
$studentCount = mysqli_fetch_assoc($countResult)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Directory</title>
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
        <aside class="col-md-2 sidebar p-3">
            <h4 class="mb-4">🎓 Student</h4>
            <nav class="nav flex-column gap-2">
                <a class=" rounded p-2" href="admin.php">▦  Admin Dashboard</a>
                <a class="rounded p-2 active" href="student.php">Student Details</a>
                <a href="course.php" class="rounded p-2 " >▤ Courses </a>
                <a class="rounded p-2" href="enrollment.php">☑ Enrollments</a>
                <!-- <a class="rounded p-2" href="#">⚙ Settings</a> -->
                <a class="rounded p-2 mt-auto text-danger" href="/auth/logout.php">⏻ Logout</a>
            </nav>
        </aside>

        <main class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary mb-1">Administration</p>
                    <h3>Student Directory</h3>
                </div>
                <a href="../auth/register.php" class="btn btn-primary btn-sm rounded-pill px-3">
                    + Add Student
                </a>
            </div>

            <div class="directory-banner text-white rounded-4 p-4 mb-4">
                <h4>Manage your student community</h4>
                <p class="mb-0">View all registered students.</p>
            </div>

            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">Student updated successfully!</div>
            <?php endif; ?>

            <?php if (isset($_GET['created'])): ?>
                <div class="alert alert-success">Student added successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">Student deleted successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Student could not be saved or deleted.</div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <p class="text-secondary mb-1">Total Students</p>
                    <h3><?= $studentCount ?></h3>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                        <h5 class="mb-0">All Students</h5>
                        <form method="GET" class="d-flex gap-2">
                            <input type="search" name="search" class="form-control"
                                placeholder="Search by student name..."
                                value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="student.php" class="btn btn-outline-secondary">Clear</a>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Reg. Number</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>DOB</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) == 0): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-secondary py-4">
                                            No students found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php while ($student = mysqli_fetch_assoc($result)): ?>
                      <tr>
                         <td><?= htmlspecialchars($student['reg_num'] ?? '') ?></td>
                         <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                              <td><?= htmlspecialchars($student['email']) ?></td>
                           <td><?= htmlspecialchars($student['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($student['gender'] ?? '') ?></td>
                         <td><?= htmlspecialchars($student['dob'] ?? '') ?></td>
                        <td><?= htmlspecialchars($student['address'] ?? '') ?></td>
                      <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-primary"
                                                    href="edit/stdedit.php?id=<?= (int)$student['id'] ?>">
                                                    Manage
                                                </a>
                                            </div>
                        </td>
                                    </tr>              
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?search=<?= $search ?>&page=<?= $page - 1 ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= $search ?>&page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>    
            <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?search=<?= $search ?>&page=<?= $page + 1 ?>">Next</a>
            </li>

        </ul>
    </nav>
<?php endif; ?>

                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
