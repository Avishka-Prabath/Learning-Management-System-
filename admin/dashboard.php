<?php
include_once('../db.php');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch dynamic counts
$totalTeachers = $conn->query("SELECT COUNT(*) AS count FROM teachers")->fetch_assoc()['count'] ?? 0;
$totalStudents = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'] ?? 0;
$totalCourses  = $conn->query("SELECT COUNT(*) AS count FROM courses")->fetch_assoc()['count'] ?? 0;
$totalPendingEnrollments = $conn->query("SELECT COUNT(*) AS count FROM enrollments WHERE payment_status = 'Pending'")->fetch_assoc()['count'] ?? 0;

// Fetch recent students
$recentStudents = $conn->query("SELECT * FROM students ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EduMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../admin/style.css">
</head>
<body class="bg-light">

    <div class="admin-layout-wrapper">
        <!-- Admin Sidebar -->
        <?php include('admin-sidebar.php'); ?>

        <div class="main-wrapper">
            <!-- Admin Topbar -->
            <?php include('topbar.php'); ?>

            <div class="p-4">
                
                <!-- Hero Banner Card -->
                <div class="card border-0 rounded-4 p-4 mb-4 text-white shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #0d52f6 0%, #003ebd 100%);">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 mb-2 extra-small fw-semibold">
                                System Administrator Panel
                            </span>
                            <h2 class="fw-bold mb-2">Welcome Back, Admin! 👋</h2>
                            <p class="mb-0 opacity-75 small">Here is a quick overview of system activity, teacher/student registrations, and course schedules for today.</p>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                            <div class="bg-white bg-opacity-10 backdrop-blur p-3 rounded-4 border border-white border-opacity-10 text-start d-inline-block" style="min-width: 240px;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge rounded-pill extra-small text-white" style="background-color: #ff3b30;">Live Control</span>
                                    <span class="extra-small opacity-75">Academic Year 2026</span>
                                </div>
                                <h6 class="fw-bold mb-1">System Health: Good</h6>
                                <a href="announcement.php" class="btn btn-light btn-sm w-100 rounded-pill fw-bold style="color: #0d52f6;" mt-2">
                                    <i class="bi bi-megaphone-fill me-1"></i> Post Announcement
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stat Cards Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted extra-small fw-bold text-uppercase">Total Instructors</span>
                                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $totalTeachers ?></h3>
                                </div>
                                <div class="p-3 rounded-4 fs-4" style="background-color: #e8f0fe; color: #0d52f6;">
                                    <i class="bi bi-person-workspace"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted extra-small fw-bold text-uppercase">Total Students</span>
                                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $totalStudents ?></h3>
                                </div>
                                <div class="p-3 rounded-4 fs-4" style="background-color: #e6f4ea; color: #137333;">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted extra-small fw-bold text-uppercase">Active Modules</span>
                                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $totalCourses ?></h3>
                                </div>
                                <div class="p-3 rounded-4 fs-4" style="background-color: #e8f0fe; color: #0d52f6;">
                                    <i class="bi bi-book-half"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted extra-small fw-bold text-uppercase">Pending Approvals</span>
                                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $totalPendingEnrollments ?></h3>
                                </div>
                                <div class="p-3 rounded-4 fs-4" style="background-color: #fef7e0; color: #b06000;">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Row -->
                <div class="row g-4">
                    <!-- Recent Activity Section -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="bi bi-activity me-2" style="color: #0d52f6;"></i>Recent System Activity
                                </h5>
                                <a href="#" class="text-decoration-none extra-small fw-bold" style="color: #0d52f6;">View All <i class="bi bi-arrow-right"></i></a>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                <?php if ($recentStudents->num_rows > 0): ?>
                                    <?php while ($student = $recentStudents->fetch_assoc()): ?>
                                        <div class="p-3 bg-light rounded-4 border-start border-4 border-success d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                                                    <i class="bi bi-person-check-fill"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0">New Student Registration</h6>
                                                    <span class="text-muted extra-small"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?> registered on <?= date('Y-M-d', strtotime($student['created_at'])) ?></span>
                                                </div>
                                            </div>
                                            <span class="badge bg-white text-muted border extra-small"><?= htmlspecialchars($student['student_id']) ?></span>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center text-muted py-3 small">No recent student registrations.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Side Panel -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <h5 class="fw-bold text-dark mb-3">
                                <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Actions
                            </h5>
                            <div class="d-flex flex-column gap-2">
                                <a href="add-teacher.php" class="btn btn-light text-start p-3 rounded-3 border d-flex align-items-center justify-content-between hover-bg">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-plus-fill fs-5" style="color: #0d52f6;"></i>
                                        <span class="fw-semibold text-dark small">Add New Instructor</span>
                                    </div>
                                    <i class="bi bi-chevron-right extra-small text-muted"></i>
                                </a>

                                <a href="add-student.php" class="btn btn-light text-start p-3 rounded-3 border d-flex align-items-center justify-content-between hover-bg">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-badge-fill text-success fs-5"></i>
                                        <span class="fw-semibold text-dark small">Register New Student</span>
                                    </div>
                                    <i class="bi bi-chevron-right extra-small text-muted"></i>
                                </a>

                                <a href="courses.php" class="btn btn-light text-start p-3 rounded-3 border d-flex align-items-center justify-content-between hover-bg">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-book-half fs-5" style="color: #0d52f6;"></i>
                                        <span class="fw-semibold text-dark small">Manage Modules</span>
                                    </div>
                                    <i class="bi bi-chevron-right extra-small text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>