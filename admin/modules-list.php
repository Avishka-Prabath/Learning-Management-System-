<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists('../db.php')) {
    include_once('../db.php');
} elseif (file_exists('db.php')) {
    include_once('db.php');
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch All Degree Courses
$coursesQuery = "SELECT c.*, t.full_name AS instructor_name, t.title AS instructor_title 
                FROM courses c 
                LEFT JOIN teachers t ON c.teacher_id = t.id 
                ORDER BY c.course_name ASC";
$coursesRes = $conn->query($coursesQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Modules - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">

    <style>
        html, body {
            background-color: #f4f6f9 !important;
            color: #1a202c;
        }
        .main-wrapper {
            background-color: #f4f6f9 !important;
        }
        .btn-theme-blue {
            background-color: #0066ff !important;
            border-color: #0066ff !important;
            color: #ffffff !important;
        }
        .btn-theme-blue:hover {
            background-color: #0052cc !important;
            border-color: #0052cc !important;
            color: #ffffff !important;
        }
        .card-custom-clean {
            background-color: #ffffff !important;
            border: none !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03) !important;
        }
    </style>
</head>
<body>

    <div class="admin-layout-wrapper">
        <!-- Sidebar -->
        <?php include('admin-sidebar.php'); ?>

        <div class="main-wrapper">
            <!-- Topbar -->
            <?php include('topbar.php'); ?>

            <div class="content-area p-4" style="background-color: #f4f6f9;">
                
                <!-- Page Header -->
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>Course Academic Modules
                    </h3>
                    <p class="text-secondary small mb-0">Select a course to view and manage its academic subject modules.</p>
                </div>

                <!-- Degree Course Cards Grid -->
                <div class="row g-4">
                    <?php if ($coursesRes && $coursesRes->num_rows > 0): ?>
                        <?php while ($course = $coursesRes->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card card-custom-clean h-100 overflow-hidden">
                                    
                                    <div class="p-3 bg-light d-flex justify-content-between align-items-center border-bottom">
                                        <span class="badge bg-primary rounded-pill px-3 py-1.5 extra-small fw-bold">
                                            <?= htmlspecialchars($course['course_code'] ?? 'CS-2026') ?>
                                        </span>
                                        <span class="text-secondary extra-small fw-semibold">
                                            <i class="bi bi-person-badge me-1"></i> 
                                            <?= htmlspecialchars(($course['instructor_title'] ?? '') . ' ' . ($course['instructor_name'] ?? 'Not Assigned')) ?>
                                        </span>
                                    </div>

                                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($course['course_name']) ?></h5>
                                            <p class="text-muted small mb-3">
                                                <?= htmlspecialchars($course['description'] ?? 'Manage subject modules, lecture notes, and curriculum syllabus.') ?>
                                            </p>
                                        </div>

                                        <!-- Card Footer Actions with Dynamic Page Routing -->
                                        <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                            <span class="small text-secondary fw-semibold">Category: <?= htmlspecialchars($course['category'] ?? 'computing') ?></span>
                                            
                                            <?php 
                                                // Routing logic to dedicated degree pages
                                                $courseNameLower = strtolower($course['course_name']);
                                                $courseCodeLower = strtolower($course['course_code'] ?? '');
                                                
                                                $targetPage = "cyber-security-modules.php"; // Default
                                                
                                                if (strpos($courseNameLower, 'business') !== false || strpos($courseCodeLower, 'bm') !== false) {
                                                    $targetPage = "business-management-modules.php";
                                                } elseif (strpos($courseNameLower, 'data science') !== false) {
                                                    $targetPage = "data-science-modules.php";
                                                } elseif (strpos($courseNameLower, 'software') !== false) {
                                                    $targetPage = "software-engineering-modules.php";
                                                } elseif (strpos($courseNameLower, 'information') !== false || strpos($courseCodeLower, 'ict') !== false) {
                                                    $targetPage = "ict-modules.php";
                                                }
                                            ?>
                                            
                                            <a href="<?= $targetPage ?>?course_id=<?= $course['id'] ?>" class="btn btn-theme-blue btn-sm rounded-pill px-3 shadow-sm fw-semibold">
                                                <i class="bi bi-eye-fill me-1"></i> View Modules
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>