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

$module_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch Selected Module Info from Database
$stmtMod = $conn->prepare("SELECT m.*, c.course_name, t.full_name AS instructor_name, t.title AS instructor_title 
                          FROM modules m 
                          LEFT JOIN courses c ON m.course_id = c.id 
                          LEFT JOIN teachers t ON m.teacher_id = t.id 
                          WHERE m.id = ?");
$stmtMod->bind_param("i", $module_id);
$stmtMod->execute();
$modRes = $stmtMod->get_result();
$moduleData = $modRes ? $modRes->fetch_assoc() : null;

if (!$moduleData) {
    header("Location: modules-list.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($moduleData['module_name']) ?> - LMS Content</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">

    <style>
        html, body { background-color: #ffffff !important; color: #1a202c; }
        .main-wrapper { background-color: #ffffff !important; }
        .lms-breadcrumb { font-size: 0.85rem; color: #6c757d; }
        .lms-breadcrumb a { color: #6c757d; text-decoration: none; }
        .lms-breadcrumb a:hover { color: #0066ff; }
        .lesson-card {
            border: none;
            border-bottom: 1px solid #f1f3f5;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .pdf-icon-badge {
            width: 30px;
            height: 30px;
            background-color: #ffebee;
            color: #d32f2f;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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

            <div class="content-area p-4 p-md-5">
                
                <!-- Back Button & LMS Breadcrumb Navigation -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="lms-breadcrumb mb-0">
                        <a href="dashboard.php">Dashboard</a> / 
                        <a href="modules-list.php">My courses</a> / 
                        <span class="text-dark fw-semibold"><?= htmlspecialchars($moduleData['module_code']) ?></span>
                    </div>

                    <!-- BACK BUTTON -->
                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm fw-semibold extra-small">
                        <i class="bi bi-arrow-left me-1"></i> Back to Modules
                    </a>
                </div>

                <!-- Page Main Title -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($moduleData['module_name']) ?></h2>
                        <p class="text-muted small mb-0">
                            Course: <strong><?= htmlspecialchars($moduleData['course_name'] ?? 'Degree Program') ?></strong> 
                            <span class="mx-2">•</span> Lecturer: <strong><?= htmlspecialchars(($moduleData['instructor_title'] ?? '') . ' ' . ($moduleData['instructor_name'] ?? 'Unassigned')) ?></strong>
                        </p>
                    </div>
                </div>

                <!-- Section 1: Announcements -->
                <div class="mb-5">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-megaphone-fill text-warning fs-5"></i>
                        <a href="#" class="text-primary text-decoration-none fw-semibold">Announcements</a>
                    </div>
                </div>

                <!-- Section 2: Course Content / Theory Units -->
                <div class="mb-4">
                    <h4 class="fw-bold text-primary mb-4">Theory</h4>

                    <!-- Lesson Topic 1 -->
                    <div class="lesson-card">
                        <h5 class="fw-semibold text-dark mb-3">1. Introduction to <?= htmlspecialchars($moduleData['module_name']) ?></h5>
                        
                        <div class="d-flex align-items-center gap-3 p-2 rounded-3 hover-bg-light">
                            <div class="pdf-icon-badge">
                                <i class="bi bi-file-earmark-pdf-fill fs-6"></i>
                            </div>
                            <div>
                                <a href="#" class="text-primary text-decoration-none extra-small fw-semibold d-block">
                                    1. Introduction to <?= htmlspecialchars($moduleData['module_name']) ?> Presentation Slides
                                </a>
                                <button class="btn btn-sm btn-light border rounded-2 extra-small text-secondary mt-1 py-0.5 px-2" style="font-size: 0.72rem;">
                                    Mark as done
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Lesson Topic 2 -->
                    <div class="lesson-card">
                        <h5 class="fw-semibold text-dark mb-3">2. Fundamental Concepts & System Components</h5>
                        
                        <div class="d-flex align-items-center gap-3 p-2 rounded-3 hover-bg-light">
                            <div class="pdf-icon-badge">
                                <i class="bi bi-file-earmark-pdf-fill fs-6"></i>
                            </div>
                            <div>
                                <a href="#" class="text-primary text-decoration-none extra-small fw-semibold d-block">
                                    2. System Components, Services, System Calls & Process Notes
                                </a>
                                <button class="btn btn-sm btn-light border rounded-2 extra-small text-secondary mt-1 py-0.5 px-2" style="font-size: 0.72rem;">
                                    Mark as done
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Lesson Topic 3 -->
                    <div class="lesson-card">
                        <h5 class="fw-semibold text-dark mb-3">3. Advanced Topics & Practical Application</h5>
                        
                        <div class="d-flex align-items-center gap-3 p-2 rounded-3 hover-bg-light">
                            <div class="pdf-icon-badge">
                                <i class="bi bi-file-earmark-pdf-fill fs-6"></i>
                            </div>
                            <div>
                                <a href="#" class="text-primary text-decoration-none extra-small fw-semibold d-block">
                                    3. Practical Lab Guidelines & Exercises
                                </a>
                                <button class="btn btn-sm btn-light border rounded-2 extra-small text-secondary mt-1 py-0.5 px-2" style="font-size: 0.72rem;">
                                    Mark as done
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>