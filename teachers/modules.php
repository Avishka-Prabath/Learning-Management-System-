<?php
session_start();

// Redirect to login.php if the teacher is not logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../db.php';

// Get the logged-in lecturer's ID
$current_teacher_id = intval($_SESSION['teacher_id'] ?? 0);

// Query that fetches the subject modules for the instructor's degree
$query = "SELECT 
            m.*,
            c.course_name,
            c.course_code,
            t.full_name AS instructor_name,
            t.title AS instructor_title
          FROM modules m
          INNER JOIN courses c ON m.course_id = c.id
          LEFT JOIN teachers t ON c.teacher_id = t.id
          WHERE c.teacher_id = ? OR m.teacher_id = ?
          ORDER BY m.semester ASC, m.id ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $current_teacher_id, $current_teacher_id);
$stmt->execute();
$modulesResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Academic Modules - Instructor Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">

    <style>
        html, body {
            background-color: #f4f6f9 !important;
            color: #1a202c;
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

    <div class="d-flex">
        <!-- Sidebar -->
        <?php include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100" style="background-color: #f4f6f9;">
            <!-- Topbar -->
            <?php include('topbar.php'); ?>

            <div class="content-area p-4">
                
                <!-- Page Header -->
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">
                            <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>My Assigned Modules
                        </h3>
                        <p class="text-secondary small mb-0">Academic subject modules assigned to your degree program. Manage lecture notes and study resources.</p>
                    </div>
                </div>

                <!-- Saegis LMS Style Grid Cards View -->
                <div class="row g-4">
                    <?php if ($modulesResult && $modulesResult->num_rows > 0): ?>
                        <?php while ($mod = $modulesResult->fetch_assoc()): ?>
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
                                                <span class="text-muted"><i class="bi bi-gear-fill"></i></span>
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
                                                <span class="text-muted">Completion</span>
                                                <span class="text-primary"><?= $mod['completion_pct'] ?? 0 ?>% complete</span>
                                            </div>
                                            <div class="progress mb-3" style="height: 5px;">
                                                <div class="progress-bar bg-primary" style="width: <?= $mod['completion_pct'] ?? 0 ?>%"></div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center pt-1">
                                                <span class="extra-small text-muted">Module ID: #<?= $mod['id'] ?></span>
                                                
                                                <!-- DIRECT LINK TO LECTURE CONTENT PAGE -->
                                                <a href="module-details.php?id=<?= $mod['id'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 extra-small fw-semibold shadow-sm">
    <i class="bi bi-journal-text me-1"></i> View Content
</a>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- If No Modules Assigned -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                                <i class="bi bi-journal-x fs-1 text-muted mb-2"></i>
                                <h6 class="fw-bold text-dark mb-1">No Modules Assigned Yet</h6>
                                <p class="text-muted small mb-0">You currently have no subject modules assigned to your degree profile. Please contact System Administrator.</p>
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