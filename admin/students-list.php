<?php
session_start();
include_once('../db.php');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = "";

// Delete Student Logic
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if ($delete_id > 0) {
        $conn->query("DELETE FROM students WHERE id = $delete_id");
        $message = "<div class='alert alert-success rounded-3 border-0 shadow-sm mb-4'><i class='bi bi-check-circle-fill me-2'></i>Student record deleted successfully!</div>";
    }
}

// Fetch all registered students
$studentsQuery = $conn->query("
    SELECT 
        s.*, 
        c.course_name, 
        c.course_code 
    FROM students s 
    LEFT JOIN courses c ON s.course_id = c.id 
    ORDER BY s.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Student Management - Admin Panel</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Admin Main Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="admin-layout-wrapper">
        <!-- Sidebar Include -->
        <?php include('admin-sidebar.php'); ?>

        <!-- Main Area -->
        <div class="main-wrapper">
            <!-- Topbar Include -->
            <?php include('topbar.php'); ?>

            <div class="content-area">
                
                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">Campus Registered Students</h3>
                        <p class="text-secondary small mb-0">View registered students, update profile details, and manage official campus credentials.</p>
                    </div>
                    <!-- Add New Student Button -->
                    <a href="add-student.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm btn-sm">
                        <i class="bi bi-person-plus-fill me-1"></i> Register New Student
                    </a>
                </div>

                <?php if(!empty($message)) echo $message; ?>

                <!-- Table Card Full Width (Exact Screenshot UI Design) -->
                <div class="card card-custom p-4 border-0 shadow-sm rounded-4 bg-white">
                    <div class="table-responsive w-100">
                        <table class="table table-hover align-middle mb-0 w-100">
                            <thead>
                                <tr class="text-muted extra-small text-uppercase">
                                    <th>Applicant Details</th>
                                    <th>NIC / Contact</th>
                                    <th>Course & Intake</th>
                                    <th>Guardian Contact</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if ($studentsQuery && $studentsQuery->num_rows > 0): ?>
                                    <?php while ($student = $studentsQuery->fetch_assoc()): ?>
                                        <tr>
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
                                                <span class="text-muted extra-small"><?= !empty($student['created_at']) ? date('Y-M-d H:i', strtotime($student['created_at'])) : 'N/A' ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 extra-small fw-semibold">
                                                    <?= htmlspecialchars($student['status'] ?? 'Active') ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <!-- Edit Button: Navigates to add-student.php Form Page with edit_id -->
                                                <a href="add-student.php?edit_id=<?= $student['id'] ?>" class="btn btn-sm btn-outline-primary rounded-3 me-1 px-3 py-1" title="Edit Student">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </a>
                                                <!-- Delete Button -->
                                                <a href="students-list.php?delete=<?= $student['id'] ?>" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" onclick="return confirm('Are you sure you want to delete this student record?')" title="Delete Student">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-people fs-2 opacity-50 d-block mb-2"></i>
                                            No registered students found. Click "Register New Student" to add one.
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
</body>
</html>