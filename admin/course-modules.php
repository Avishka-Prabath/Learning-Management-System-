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

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 1;

// Fetch Course Info
$stmtCourse = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmtCourse->bind_param("i", $course_id);
$stmtCourse->execute();
$courseRes = $stmtCourse->get_result();
$courseData = $courseRes ? $courseRes->fetch_assoc() : null;

// Fetch All Modules for this specific Course
$modulesQuery = "SELECT m.*, t.full_name AS instructor_name, t.title AS instructor_title 
                FROM modules m 
                LEFT JOIN teachers t ON m.teacher_id = t.id 
                WHERE m.course_id = ? 
                ORDER BY m.semester ASC, m.id ASC";
$stmtMod = $conn->prepare($modulesQuery);
$stmtMod->bind_param("i", $course_id);
$stmtMod->execute();
$modulesRes = $stmtMod->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($courseData['course_name'] ?? 'BSc (Hons) in Cyber Security') ?> - Subjects</title>
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
        .module-card {
            border: none !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04) !important;
        }
        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
        }
        .module-banner {
            height: 115px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            position: relative;
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
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <a href="modules-list.php" class="btn btn-white btn-sm rounded-pill mb-2 px-3 border shadow-sm fw-semibold text-secondary extra-small">
                            <i class="bi bi-arrow-left me-1"></i> Back to Courses
                        </a>
                        <h3 class="fw-bold text-dark mb-1"><?= htmlspecialchars($courseData['course_name'] ?? 'BSc (Hons) in Cyber Security') ?></h3>
                        <p class="text-secondary small mb-0">
                            Course Code: <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-2 ms-1 fw-bold"><?= htmlspecialchars($courseData['course_code'] ?? 'CS-2026') ?></span> 
                            <span class="mx-2">•</span> Subject Modules: <strong class="text-dark"><?= $modulesRes ? $modulesRes->num_rows : 0 ?> Modules</strong>
                        </p>
                    </div>
                </div>

                <!-- Saegis LMS Style Grid Cards View -->
                <div class="row g-4">
                    <?php if ($modulesRes && $modulesRes->num_rows > 0): ?>
                        <?php while ($mod = $modulesRes->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card module-card h-100 overflow-hidden">
                                    
                                    <!-- LMS Banner Header -->
                                    <div class="module-banner p-3 text-center" style="background-color: <?= $mod['banner_color'] ?? '#0066ff' ?>;">
                                        <div class="text-center px-2">
                                            <span class="badge bg-white bg-opacity-25 text-white mb-1 extra-small"><?= htmlspecialchars($mod['module_code']) ?></span>
                                            <h6 class="fw-bold mb-0 text-white text-truncate" title="<?= htmlspecialchars($mod['module_name']) ?>">
                                                <?= htmlspecialchars($mod['module_name']) ?>
                                            </h6>
                                        </div>
                                    </div>

                                    <!-- Card Content Body -->
                                    <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="text-muted extra-small fw-bold text-uppercase"><?= htmlspecialchars($mod['semester']) ?></span>
                                                <span class="dropdown">
                                                    <a href="edit-teacher.php?id=<?= intval($mod['teacher_id'] ?? 0) ?>" class="text-muted" title="Assign Lecturer">
                                                        <i class="bi bi-gear-fill"></i>
                                                    </a>
                                                </span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-3" style="min-height: 42px; font-size: 0.95rem;">
                                                <?= htmlspecialchars($mod['module_name']) ?>
                                            </h6>
                                            
                                            <p class="text-secondary extra-small mb-1">
                                                <i class="bi bi-person-badge text-primary me-1"></i> 
                                                Lecturer: <strong class="text-dark"><?= htmlspecialchars(($mod['instructor_title'] ?? '') . ' ' . ($mod['instructor_name'] ?? 'Unassigned')) ?></strong>
                                            </p>
                                        </div>

                                        <!-- Progress Bar & Actions -->
                                        <div class="pt-3 border-top mt-3">
                                            <div class="d-flex justify-content-between extra-small fw-semibold mb-1">
                                                <span class="text-muted">Completion Progress</span>
                                                <span class="text-primary"><?= $mod['completion_pct'] ?? 0 ?>% complete</span>
                                            </div>
                                            <div class="progress mb-3" style="height: 5px;">
                                                <div class="progress-bar bg-primary" style="width: <?= $mod['completion_pct'] ?? 0 ?>%"></div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center pt-1">
                                                <span class="extra-small text-muted">Module ID: #<?= $mod['id'] ?></span>
                                                <a href="edit-teacher.php?id=<?= intval($mod['teacher_id'] ?? 0) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 extra-small fw-semibold">
                                                    <i class="bi bi-pencil-square me-1"></i> Assign Lecturer
                                                </a>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card border-0 rounded-4 p-5 text-center bg-white">
                                <i class="bi bi-journal-x fs-1 text-muted mb-2"></i>
                                <h6 class="fw-bold text-dark mb-1">No Modules Found</h6>
                                <p class="text-muted small mb-0">No subject modules assigned for this course yet.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>