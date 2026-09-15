<?php
session_start();

// Redirect to login.php if the teacher is not logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$teacher_name = $_SESSION['teacher_name'] ?? 'Instructor';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - EduMart</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar -->
        <?php include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <!-- Topbar -->
            <?php include('topbar.php'); ?>

            <!-- Dashboard Main Content -->
            <div class="p-4">
                
                <!-- Welcome Hero Banner -->
                <div class="card border-0 text-white p-4 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #0d6efd, #0d5ddb);">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-3 mb-lg-0">
                            <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 mb-3 extra-small fw-semibold">
                                <i class="bi bi-stars me-1"></i> Instructor Control Panel
                            </span>
                            <h2 class="fw-bold mb-2">Good Day, <?= htmlspecialchars($teacher_name) ?>! 👋</h2>
                            <p class="mb-0 text-white-50 small" style="max-width: 550px;">
                                Here is a quick overview of your teaching schedule, pending student approvals, and active course stats for today.
                            </p>
                        </div>

                        <!-- Right Quick Live Meeting Card -->
                        <div class="col-lg-4">
                            <div class="p-3 text-white rounded-4 shadow-sm" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-danger rounded-pill px-2 py-1 extra-small"><i class="bi bi-broadcast me-1"></i> Next Class</span>
                                    <span class="extra-small text-white-50">Today 02:00 PM</span>
                                </div>
                                <h6 class="fw-bold text-white mb-1">Software Architecture</h6>
                                <p class="extra-small text-white-50 mb-3">SE-2026 Batch • Module 04</p>
                                <a href="classes.php" class="btn btn-light text-primary w-100 fw-bold rounded-pill btn-sm py-2 shadow-sm">
                                    <i class="bi bi-camera-video-fill me-2"></i> Start Live Session
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Cards Summary -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted extra-small fw-semibold text-uppercase mb-1">Assigned Courses</p>
                                    <h3 class="fw-bold text-dark mb-0">04</h3>
                                </div>
                                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                                    <i class="bi bi-journal-bookmark fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted extra-small fw-semibold text-uppercase mb-1">Total Students</p>
                                    <h3 class="fw-bold text-dark mb-0">248</h3>
                                </div>
                                <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                                    <i class="bi bi-people fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted extra-small fw-semibold text-uppercase mb-1">Upcoming Classes</p>
                                    <h3 class="fw-bold text-dark mb-0">02</h3>
                                </div>
                                <div class="p-3 bg-info bg-opacity-10 text-info rounded-4">
                                    <i class="bi bi-calendar-event fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted extra-small fw-semibold text-uppercase mb-1">Pending Slips</p>
                                    <h3 class="fw-bold text-danger mb-0">02</h3>
                                </div>
                                <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4">
                                    <i class="bi bi-receipt fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Grid Section: Summaries & Quick Management -->
                <div class="row g-4 mb-4">
                    
                    <!-- Left Column: Today's Class Schedule & Active Courses -->
                    <div class="col-lg-8">
                        
                        <!-- Today's Schedule Card -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-calendar-week me-2 text-primary"></i>Today's Class Schedule</h5>
                                    <p class="text-muted extra-small mb-0">Sessions assigned for today</p>
                                </div>
                                <a href="classes.php" class="btn btn-light btn-sm rounded-pill fw-semibold text-primary extra-small">View All →</a>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-light border-start border-4 border-primary">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 text-center" style="min-width: 60px;">
                                            <span class="fw-bold d-block lh-1">02:00</span>
                                            <span class="extra-small text-uppercase">PM</span>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Advanced Software Architecture</h6>
                                            <span class="extra-small text-muted">BSc in Software Engineering • Room A</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-success rounded-pill px-3 py-2">Upcoming</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-light border-start border-4 border-secondary">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 bg-secondary bg-opacity-10 text-secondary rounded-3 text-center" style="min-width: 60px;">
                                            <span class="fw-bold d-block lh-1">04:30</span>
                                            <span class="extra-small text-uppercase">PM</span>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Data Science Fundamentals</h6>
                                            <span class="extra-small text-muted">Diploma in Data Science • Lab 02</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Scheduled</span>
                                </div>
                            </div>
                        </div>

                        <!-- Active Assigned Courses Summary -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-journal-text me-2 text-success"></i>Assigned Modules Overview</h5>
                                    <p class="text-muted extra-small mb-0">Active student enrollment breakdown</p>
                                </div>
                                <a href="courses.php" class="btn btn-light btn-sm rounded-pill fw-semibold text-primary extra-small">Manage Courses →</a>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-4 bg-white">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">SE-2026</span>
                                            <span class="extra-small text-muted fw-bold"><i class="bi bi-people me-1"></i>120 Students</span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Software Engineering</h6>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: 75%;"></div>
                                        </div>
                                        <span class="extra-small text-muted mt-1 d-block">75% Module Completed</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-3 border rounded-4 bg-white">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill">DS-2026</span>
                                            <span class="extra-small text-muted fw-bold"><i class="bi bi-people me-1"></i>85 Students</span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Data Science & AI</h6>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: 45%;"></div>
                                        </div>
                                        <span class="extra-small text-muted mt-1 d-block">45% Module Completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Pending Verification & Recent Activity -->
                    <div class="col-lg-4">
                        
                        <!-- Pending Verification Approvals Card -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-1 text-warning"></i> Pending Verification</h6>
                                    <span class="extra-small text-muted">Bank Slip Reviews</span>
                                </div>
                                <span class="badge bg-danger rounded-pill">2 Pending</span>
                            </div>

                            <div class="d-flex flex-column gap-3 mb-3">
                                <!-- Pending Item 1 -->
                                <div class="p-3 rounded-4 bg-light d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name=Kasun+Perera&background=ffc107&color=000" class="rounded-circle" width="36" height="36" alt="">
                                        <div>
                                            <p class="fw-bold text-dark mb-0 extra-small">Kasun Perera</p>
                                            <span class="text-muted extra-small">STU-1002</span>
                                        </div>
                                    </div>
                                    <a href="student.php" class="btn btn-sm btn-outline-warning rounded-pill extra-small px-3">Review Slip</a>
                                </div>

                                <!-- Pending Item 2 -->
                                <div class="p-3 rounded-4 bg-light d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name=Ruwan+Gamage&background=ffc107&color=000" class="rounded-circle" width="36" height="36" alt="">
                                        <div>
                                            <p class="fw-bold text-dark mb-0 extra-small">Ruwan Gamage</p>
                                            <span class="text-muted extra-small">STU-1005</span>
                                        </div>
                                    </div>
                                    <a href="student.php" class="btn btn-sm btn-outline-warning rounded-pill extra-small px-3">Review Slip</a>
                                </div>
                            </div>

                            <a href="student.php" class="btn btn-light w-100 rounded-pill text-dark fw-semibold extra-small">Go to Student Portal →</a>
                        </div>

                        <!-- Quick System Notice / Announcements -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bell me-2 text-info"></i>Instructor Announcements</h6>
                            
                            <div class="timeline-item pb-3 mb-3 border-bottom">
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill extra-small mb-1">Exam Notice</span>
                                <p class="extra-small text-dark fw-semibold mb-1">Mid-Semester Exam Paper Submission</p>
                                <span class="extra-small text-muted">Deadline: Aug 10, 2026</span>
                            </div>

                            <div class="timeline-item">
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill extra-small mb-1">System Update</span>
                                <p class="extra-small text-dark fw-semibold mb-1">Zoom Integration v2 Active</p>
                                <span class="extra-small text-muted">Updated today at 09:00 AM</span>
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