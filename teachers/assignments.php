<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include db.php from the root folder
require_once '../db.php'; 

// Instructor Login Check
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 1. AUTO-CREATE TABLE IF NOT EXISTS TO PREVENT FATAL SQL ERROR
$conn->query("CREATE TABLE IF NOT EXISTS assignment_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    student_id INT NOT NULL,
    assignment_key VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Search and Filter Setup
$search_term = trim($_GET['search'] ?? '');

// 2. FETCH ALL STUDENT ASSIGNMENT SUBMISSIONS WITH STUDENT & MODULE DETAILS
$querySubmissions = "
    SELECT 
        sub.id AS submission_id,
        sub.assignment_key,
        sub.file_path,
        sub.submitted_at,
        s.student_id AS campus_student_id,
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        s.email AS student_email,
        m.module_code,
        m.module_name,
        c.course_name,
        c.course_code
    FROM assignment_submissions sub
    INNER JOIN students s ON sub.student_id = s.id
    INNER JOIN modules m ON sub.module_id = m.id
    LEFT JOIN courses c ON m.course_id = c.id
    WHERE 1=1
";

if (!empty($search_term)) {
    $searchEsc = $conn->real_escape_string($search_term);
    $querySubmissions .= " AND (s.student_id LIKE '%$searchEsc%' OR s.first_name LIKE '%$searchEsc%' OR s.last_name LIKE '%$searchEsc%' OR m.module_code LIKE '%$searchEsc%')";
}

$querySubmissions .= " ORDER BY sub.submitted_at DESC";
$submissionsResult = $conn->query($querySubmissions);
$totalSubmissions = $submissionsResult ? $submissionsResult->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Submissions - Teacher Portal</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- External Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: #f4f6f9; color: #1a202c; }
        .extra-small { font-size: 0.75rem; }
        .card-custom { border: none; border-radius: 16px; background: #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    </style>
</head>
<body>

    <div class="d-flex">
        <!-- Sidebar Component -->
        <?php if (file_exists('instructor-sidebar.php')) include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100 d-flex flex-column">
            <!-- Topbar Component -->
            <?php if (file_exists('topbar.php')) include('topbar.php'); ?>

            <!-- Main Content Area -->
            <div class="p-4 flex-grow-1">
                
                <!-- Page Header -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">Student Assignment Submissions</h3>
                        <p class="text-secondary small mb-0">Review and download all answer sheets submitted by students.</p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold extra-small">
                            <i class="bi bi-file-earmark-check-fill me-1"></i> Total Submissions: <?= $totalSubmissions ?>
                        </span>
                    </div>
                </div>

                <!-- Search & Filter Bar -->
                <div class="card card-custom p-3 mb-4">
                    <form method="GET" action="assignments.php">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6 col-lg-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                                        <i class="bi bi-search text-muted small"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-pill extra-small py-2" placeholder="Search by Student ID, Name or Module Code..." value="<?= htmlspecialchars($search_term) ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary rounded-pill btn-sm w-100 fw-bold extra-small">
                                    Filter Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Student Submissions Data Table -->
                <div class="card card-custom p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted extra-small text-uppercase">
                                    <th>Student Details</th>
                                    <th>Course & Module</th>
                                    <th>Assignment Name</th>
                                    <th>Submission Time</th>
                                    <th class="text-end">Answer File</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if ($submissionsResult && $submissionsResult->num_rows > 0): ?>
                                    <?php while($sub = $submissionsResult->fetch_assoc()): ?>
                                        <?php 
                                            $formatted_key = ucwords(str_replace('_', ' ', $sub['assignment_key']));
                                        ?>
                                        <tr>
                                            <!-- Student Name & Campus ID -->
                                            <td>
                                                <div class="d-flex align-items-center gap-2.5">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                        <i class="bi bi-person-fill fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-0 small"><?= htmlspecialchars($sub['student_name']) ?></h6>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold extra-small" style="font-size: 0.68rem;">
                                                            <?= htmlspecialchars($sub['campus_student_id']) ?>
                                                        </span>
                                                        <span class="text-muted extra-small d-block"><?= htmlspecialchars($sub['student_email']) ?></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Course & Module Code -->
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-10 text-dark border extra-small mb-1">
                                                    <?= htmlspecialchars($sub['module_code']) ?>
                                                </span>
                                                <span class="text-dark fw-bold d-block extra-small"><?= htmlspecialchars($sub['module_name']) ?></span>
                                            </td>

                                            <!-- Assignment Slot Title -->
                                            <td>
                                                <span class="fw-semibold text-danger extra-small">
                                                    <i class="bi bi-journal-check me-1"></i><?= htmlspecialchars($formatted_key) ?>
                                                </span>
                                            </td>

                                            <!-- Submitted Time -->
                                            <td>
                                                <span class="text-muted extra-small">
                                                    <i class="bi bi-clock me-1"></i><?= date('M d, Y - h:i A', strtotime($sub['submitted_at'])) ?>
                                                </span>
                                            </td>

                                            <!-- Download Solution File -->
                                            <td class="text-end">
                                                <a href="../uploads/submissions/<?= htmlspecialchars($sub['file_path']) ?>" download target="_blank" class="btn btn-sm btn-success rounded-pill px-3 extra-small fw-bold shadow-sm">
                                                    <i class="bi bi-download me-1"></i> Download Solution
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                            No student assignment submissions found yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>