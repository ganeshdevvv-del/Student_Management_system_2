
<?php

// Default page settings
$pageTitle = $pageTitle ?? 'Dashboard';
$pageHeading = $pageHeading ?? $pageTitle;
$pageSubtitle = $pageSubtitle ?? 'Administration';

$bannerTitle = $bannerTitle ?? '';
$bannerText = $bannerText ?? '';
$bannerLead = $bannerLead ?? '';

$activePage = $activePage ?? '';
$studentPortal = $studentPortal ?? false;

$headerAction = $headerAction ?? null;
$bannerActions = $bannerActions ?? [];


// Navigation links
if ($studentPortal) {
    $navigation = [
        ['profile', '#profile', 'My Profile']
    ];
} else {
    $navigation = [
        ['admin', 'admin.php', 'Admin Dashboard'],
        ['students', 'student.php', 'Student Profile'],
        ['courses', 'course.php', 'Courses'],
        ['enrollments', 'enrollment.php', 'Enrollments']
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
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

        .sidebar a:hover,
        .sidebar a.active {
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
        <!-- Sidebar -->
        <aside class="<?= $studentPortal ? 'col-md-3 col-lg-2' : 'col-md-2' ?> sidebar p-3">
            <h4 class="mb-4">
                <?= $studentPortal ? 'Student Portal' : 'Student' ?>
            </h4>
            <nav class="nav flex-column gap-2">
                <?php foreach ($navigation as $item): ?>
                    <?php
                        $key = $item[0];
                        $href = $item[1];
                        $label = $item[2];
                    ?>
                    <a href="<?= htmlspecialchars($href) ?>"
                       class="rounded p-2 <?= $activePage === $key ? 'active' : '' ?>">
                        <?= htmlspecialchars($label) ?>
                    </a>
                <?php endforeach; ?>
                <a href="../auth/logout.php" class="rounded p-2 text-danger">
                    Logout
                </a>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="<?= $studentPortal ? 'col-md-9 col-lg-10' : 'col-md-10' ?> p-3 p-lg-4">

            <!-- Page Header -->
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <?php if ($pageSubtitle != ''): ?>
                        <p class="text-secondary mb-1">
                            <?= htmlspecialchars($pageSubtitle) ?>
                        </p>
                    <?php endif; ?>
                    <h1 class="h3">
                        <?= htmlspecialchars($pageHeading) ?>
                    </h1>
                </div>


                <!-- Header Button -->
                <?php if ($headerAction): ?>
                    <a href="<?= htmlspecialchars($headerAction['href']) ?>"
                       class="<?= htmlspecialchars($headerAction['class'] ?? 'btn btn-primary btn-sm') ?>">
                        <?= htmlspecialchars($headerAction['label']) ?>
                    </a>
                <?php endif; ?>
            </header>


            <!-- Banner -->
            <?php if ($bannerTitle != '' || $bannerText != '' || $bannerLead != ''): ?>
                <section class="banner text-white rounded-4 p-4 p-lg-5 mb-4">
                    <?php if ($bannerLead != ''): ?>
                        <p><?= htmlspecialchars($bannerLead) ?></p>
                    <?php endif; ?>
                    <?php if ($bannerTitle != ''): ?>
                        <h2 class="h4">
                            <?= htmlspecialchars($bannerTitle) ?>
                        </h2>
                    <?php endif; ?>

                    <?php if ($bannerText != ''): ?>
                        <p><?= htmlspecialchars($bannerText) ?></p>
                    <?php endif; ?>

                    <!-- Banner Buttons -->
                    <?php foreach ($bannerActions as $action): ?>
                        <a href="<?= htmlspecialchars($action['href']) ?>"
                           class="<?= htmlspecialchars($action['class']) ?>">
                            <?= htmlspecialchars($action['label']) ?>
                        </a>
                    <?php endforeach; ?>

                </section>
            <?php endif; ?>
