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

// Fixed Course ID for Data Science = 2
$course_id = 2;

// Auto-seeding Data Science Subjects
$checkMod = $conn->prepare("SELECT COUNT(*) AS total FROM modules WHERE course_id = ?");
$checkMod->bind_param("i", $course_id);
$checkMod->execute();
$totalCount = $checkMod->get_result()->fetch_assoc()['total'];

if ($totalCount == 0) {
    $insStmt = $conn->prepare("INSERT INTO modules (course_id, module_code, module_name, semester, teacher_id, completion_pct, banner_color) VALUES (?, ?, ?, ?, NULL, 0, ?)");
    
    $dsSubjects = [
        ['DS 1101', 'Python Programming for Data Science', 'Semester 01', '#0284c7'],
        ['DS 1102', 'Linear Algebra & Statistics', 'Semester 01', '#059669'],
        ['DS 1201', 'Data Structures & Algorithms', 'Semester 02', '#0d6efd'],
        ['DS 1202', 'Big Data Architecture & Databases', 'Semester 02', '#d97706'],
        ['DS 2101', 'Machine Learning Fundamentals', 'Semester 03', '#7c3aed'],
        ['DS 2102', 'Data Visualization & Analytics', 'Semester 03', '#dc2626'],
        ['DS 2201', 'Deep Learning & Neural Networks', 'Semester 04', '#0284c7'],
        ['DS 3101', 'Natural Language Processing (NLP)', 'Semester 05', '#059669']
    ];

    foreach ($dsSubjects as $ds) {
        $insStmt->bind_param("issss", $course_id, $ds[0], $ds[1], $ds[2], $ds[3]);
        $insStmt->execute();
    }
    $insStmt->close();
}

// Fetch Modules
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
    <title>BSc (Hons) in Data Science & AI - Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: #f4f6f9 !important; }
        .module-card { border: none; border-radius: 12px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
        .module-banner { height: 115px; border-top-left-radius: 12px; border-top-right-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; }
    </style>
</head>
<body>

    <div class="admin-layout-wrapper">
        <?php include('admin-sidebar.php'); ?>

        <div class="main-wrapper">
            <?php include('topbar.php'); ?>

            <div class="content-area p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <a href="modules-list.php" class="btn btn-white btn-sm rounded-pill mb-2 px-3 border shadow-sm extra-small fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i> Back to All Courses
                        </a>
                        <h3 class="fw-bold text-dark mb-1">BSc (Hons) in Data Science & AI</h3>
                        <p class="text-secondary small mb-0">Course Code: <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-2">DS-2026</span></p>
                    </div>
                </div>

                <div class="row g-4">
                    <?php if ($modulesRes && $modulesRes->num_rows > 0): ?>
                        <?php while ($mod = $modulesRes->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card module-card h-100 overflow-hidden">
                                    <div class="module-banner p-3 text-center" style="background-color: <?= $mod['banner_color'] ?>;">
                                        <div>
                                            <span class="badge bg-white bg-opacity-25 text-white mb-1 extra-small"><?= htmlspecialchars($mod['module_code']) ?></span>
                                            <h6 class="fw-bold mb-0 text-white"><?= htmlspecialchars($mod['module_name']) ?></h6>
                                        </div>
                                    </div>
                                    <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                                        <div>
                                            <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1"><?= htmlspecialchars($mod['semester']) ?></span>
                                            <h6 class="fw-bold text-dark mb-3"><?= htmlspecialchars($mod['module_name']) ?></h6>
                                            <p class="text-secondary extra-small mb-1">
                                                Lecturer: <strong><?= htmlspecialchars(($mod['instructor_title'] ?? '') . ' ' . ($mod['instructor_name'] ?? 'Unassigned')) ?></strong>
                                            </p>
                                        </div>
                                        <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center">
                                            <span class="extra-small text-muted">#<?= $mod['id'] ?></span>
                                            
                                            <!-- DIRECT LINK TO LECTURE CONTENT PAGE -->
                                            <a href="module-details.php?id=<?= $mod['id'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 extra-small fw-semibold shadow-sm">
                                                <i class="bi bi-journal-text me-1"></i> View Content
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>