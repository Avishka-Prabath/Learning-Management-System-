<?php
session_start();
include_once('../db.php');

// Instructor Login Verification
if (!isset($_SESSION['teacher_id'])) {
    $current_teacher_id = $_SESSION['teacher_id'] ?? 1; // Default Testing Fallback
} else {
    $current_teacher_id = intval($_SESSION['teacher_id']);
}

// Instructor ට අදාළ Courses වල ඉන්න සියලුම Students ලා Fetch කිරීම (Direct Course ID එකෙන් හෝ Enrollments හරහා)
$query = "SELECT DISTINCT
            s.id AS student_db_id,
            s.*,
            c.course_name,
            c.course_code
          FROM students s
          INNER JOIN courses c ON s.course_id = c.id OR s.id IN (SELECT student_id FROM enrollments WHERE course_id = c.id)
          WHERE c.teacher_id = ?
          ORDER BY s.id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_teacher_id);
$stmt->execute();
$studentsResult = $stmt->get_result();

$totalStudents = $studentsResult->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolled Students - Teacher Portal</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar -->
        <?php include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <!-- Topbar -->
            <?php include('topbar.php'); ?>

            <div class="p-4">
                
                <!-- Page Header & Search Bar -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Enrolled Students</h4>
                        <p class="text-muted small mb-0">Students enrolled in courses assigned to your lectures.</p>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="position-relative" style="min-width: 280px;">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="studentSearchInput" class="form-control form-control-sm rounded-pill ps-5 pe-3 py-2 border-0 shadow-sm" placeholder="Search student details...">
                    </div>
                </div>

                <!-- Status Filter Tabs -->
                <ul class="nav nav-pills mb-3 gap-2 border-bottom pb-3">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill px-3 py-1 extra-small fw-semibold">
                            My Assigned Course Students <span class="badge bg-secondary rounded-pill ms-1"><?= $totalStudents ?></span>
                        </button>
                    </li>
                </ul>

                <!-- Students Table Card (Admin Design Format) -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="table-responsive w-100">
                        <table class="table table-hover align-middle mb-0 w-100" id="studentsTable">
                            <thead>
                                <tr class="text-muted extra-small text-uppercase">
                                    <th>Applicant Details</th>
                                    <th>NIC / Contact</th>
                                    <th>Course & Intake</th>
                                    <th>Guardian Contact</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if ($studentsResult && $studentsResult->num_rows > 0): ?>
                                    <?php while ($student = $studentsResult->fetch_assoc()): ?>
                                        <tr class="student-row">
                                            <td>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($student['full_name'] ?? (($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''))) ?></h6>
                                                    <span class="badge bg-light text-dark border extra-small me-1"><?= htmlspecialchars($student['gender'] ?? 'N/A') ?></span>
                                                    <span class="text-muted extra-small"><i class="bi bi-mortarboard me-1"></i><?= htmlspecialchars($student['qualification'] ?? 'N/A') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="fw-semibold text-dark d-block extra-small"><i class="bi bi-card-heading me-1 text-primary"></i><?= htmlspecialchars($student['nic'] ?? 'N/A') ?></span>
                                                    <span class="text-muted extra-small d-block"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($student['phone'] ?? '') ?></span>
                                                    <span class="text-muted extra-small"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($student['email'] ?? '') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <?php if(!empty($student['course_code'])): ?>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 extra-small fw-semibold"><?= htmlspecialchars($student['course_code']) ?></span>
                                                    <?php endif; ?>
                                                    <span class="text-dark fw-bold extra-small d-block mt-1"><?= htmlspecialchars($student['course_name'] ?? 'N/A') ?></span>
                                                    <span class="text-secondary extra-small"><i class="bi bi-clock-history me-1"></i><?= htmlspecialchars(($student['study_mode'] ?? 'Full-Time') . ' | ' . ($student['intake'] ?? '2026-Batch-01')) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="fw-semibold text-dark d-block extra-small"><?= htmlspecialchars($student['guardian_name'] ?? 'N/A') ?> (<?= htmlspecialchars($student['guardian_relation'] ?? 'Guardian') ?>)</span>
                                                    <span class="text-muted extra-small"><i class="bi bi-telephone-fill me-1"></i><?= htmlspecialchars($student['guardian_phone'] ?? 'N/A') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 extra-small fw-semibold">
                                                    <?= htmlspecialchars($student['status'] ?? 'Active') ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="tel:<?= htmlspecialchars($student['phone'] ?? '') ?>" class="btn btn-sm btn-outline-secondary rounded-3 me-1" title="Call Student">
                                                    <i class="bi bi-telephone"></i>
                                                </a>
                                                <a href="mailto:<?= htmlspecialchars($student['email'] ?? '') ?>" class="btn btn-sm btn-outline-primary rounded-3" title="Email Student">
                                                    <i class="bi bi-envelope"></i> Email
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-people fs-2 opacity-50 d-block mb-2"></i>
                                            No students are currently enrolled in your courses.
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Table Live Search Script
        document.getElementById('studentSearchInput')?.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#studentsTable tbody tr.student-row');
            
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>