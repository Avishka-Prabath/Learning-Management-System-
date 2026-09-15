<?php
session_start();
include_once('../db.php');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = "";

// Action handlers (Approve / Reject)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = trim($_GET['action']);
    
    if ($id > 0) {
        if ($action === 'approve') {
            $conn->query("UPDATE enrollments SET status = 'Approved' WHERE id = $id");
            $message = "<div class='alert alert-success rounded-3 shadow-sm border-0 mb-4'><i class='bi bi-check-circle-fill me-2'></i>Campus application approved successfully!</div>";
        } elseif ($action === 'reject') {
            $conn->query("UPDATE enrollments SET status = 'Rejected' WHERE id = $id");
            $message = "<div class='alert alert-danger rounded-3 shadow-sm border-0 mb-4'><i class='bi bi-exclamation-triangle-fill me-2'></i>Campus application has been rejected.</div>";
        }
    }
}

// Fetch all enrollment requests joining with courses table only
$enrollResult = $conn->query("
    SELECT 
        e.*, 
        c.course_name, 
        c.course_code 
    FROM enrollments e 
    LEFT JOIN courses c ON e.course_id = c.id 
    ORDER BY e.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Student Enrollments - Admin Panel</title>
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
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Campus Applications & Enrollments</h3>
                    <p class="text-secondary small mb-0">Review student applications, verify educational details, and manage course admissions.</p>
                </div>

                <?php if(!empty($message)) echo $message; ?>

                <!-- Table Card Full Width -->
                <div class="card card-custom p-4">
                    <div class="table-responsive w-100">
                        <table class="table table-hover align-middle mb-0 w-100">
                            <thead>
                                <tr>
                                    <th>Applicant Details</th>
                                    <th>NIC / Contact</th>
                                    <th>Course & Intake</th>
                                    <th>Guardian Contact</th>
                                    <th>Submission Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if ($enrollResult && $enrollResult->num_rows > 0): ?>
                                    <?php while ($row = $enrollResult->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['full_name'] ?? '') ?></h6>
                                                    <span class="badge bg-light text-dark border extra-small me-1"><?= htmlspecialchars($row['gender'] ?? 'N/A') ?></span>
                                                    <span class="text-muted extra-small"><i class="bi bi-mortarboard me-1"></i><?= htmlspecialchars($row['qualification'] ?? 'N/A') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="fw-semibold text-dark d-block extra-small"><i class="bi bi-card-heading me-1 text-primary"></i><?= htmlspecialchars($row['nic'] ?? 'N/A') ?></span>
                                                    <span class="text-muted extra-small d-block"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($row['phone'] ?? '') ?></span>
                                                    <span class="text-muted extra-small"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($row['email'] ?? '') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <?php if(!empty($row['course_code'])): ?>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 extra-small fw-semibold"><?= htmlspecialchars($row['course_code']) ?></span>
                                                    <?php endif; ?>
                                                    <span class="text-dark fw-bold extra-small d-block mt-1"><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></span>
                                                    <span class="text-secondary extra-small"><i class="bi bi-clock-history me-1"></i><?= htmlspecialchars(($row['study_mode'] ?? '') . ' | ' . ($row['intake'] ?? '')) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="fw-semibold text-dark d-block extra-small"><?= htmlspecialchars($row['guardian_name'] ?? 'N/A') ?> (<?= htmlspecialchars($row['guardian_relation'] ?? 'Guardian') ?>)</span>
                                                    <span class="text-muted extra-small"><i class="bi bi-telephone-fill me-1"></i><?= htmlspecialchars($row['guardian_phone'] ?? 'N/A') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted extra-small"><?= !empty($row['created_at']) ? date('Y-M-d H:i', strtotime($row['created_at'])) : 'N/A' ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                    $status = $row['status'] ?? 'Pending';
                                                    if ($status === 'Approved' || $status === 'Active'): 
                                                ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 extra-small fw-semibold">Approved</span>
                                                <?php elseif ($status === 'Rejected' || $status === 'Cancelled'): ?>
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1 extra-small fw-semibold">Rejected</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 extra-small fw-semibold">Pending Approval</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <?php if (($row['status'] ?? 'Pending') === 'Pending'): ?>
                                                    <a href="student-enrollments.php?action=approve&id=<?= $row['id'] ?>" class="btn btn-sm btn-success rounded-3 me-1 px-3" title="Approve Request"><i class="bi bi-check-circle me-1"></i> Approve</a>
                                                    <a href="student-enrollments.php?action=reject&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger rounded-3 px-3" title="Reject Request"><i class="bi bi-x-circle me-1"></i> Reject</a>
                                                <?php else: ?>
                                                    <a href="student-enrollments.php?action=approve&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-3 p-1 px-2 me-1" title="Change to Approved"><i class="bi bi-check"></i></a>
                                                    <a href="student-enrollments.php?action=reject&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-3 p-1 px-2" title="Change to Rejected"><i class="bi bi-x"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-folder-x fs-2 opacity-50 d-block mb-2"></i>
                                            No campus applications submitted yet.
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