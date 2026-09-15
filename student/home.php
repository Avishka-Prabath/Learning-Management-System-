<?php
require_once __DIR__ . '/../db.php';
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
$student_name = $_SESSION['student_name'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - EduMart LMS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        .welcome-card-bg {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border-radius: 20px;
        }
        .content-card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .content-card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }
        .extra-small {
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar Navigation -->
        <?php include('navbar.php'); ?>

        <!-- Main Content Area -->
        <div class="flex-grow-1 min-vh-100">
            <!-- Topbar Navigation -->
            <?php include('topbar.php'); ?>

            <div class="p-4">
                
                <!-- Smart Header Hero Section -->
                <div class="welcome-card-bg p-4 p-md-5 text-white mb-4 shadow-sm position-relative overflow-hidden">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold mb-3">
                                <i class="bi bi-stars me-1"></i> Active Semester
                            </span>
                            <h2 class="fw-bold mb-2">Welcome back, Student! 👋</h2>
                            <p class="text-white-50 mb-4">You have 3 upcoming assignments and a live lecture scheduled for today. Keep up the momentum!</p>
                            
                            <!-- Overall Progress Track -->
                            <div class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-25" style="max-width: 500px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-white fw-semibold">Overall Degree Completion</span>
                                    <span class="small text-white fw-bold">58%</span>
                                </div>
                                <div class="progress bg-white bg-opacity-25" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 58%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Live Upcoming Session Banner -->
                        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                            <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur border border-white border-opacity-25 text-start text-lg-end">
                                <span class="badge bg-danger rounded-pill px-3 py-1 mb-2">
                                    <i class="bi bi-broadcast me-1"></i> Upcoming Live Class
                                </span>
                                <h6 class="fw-bold text-white mb-1">Database Systems Discussion</h6>
                                <p class="small text-white-50 mb-3">Software Engineering • Today 03:00 PM</p>
                                <a href="https://zoom.us" target="_blank" class="btn btn-light rounded-pill px-4 btn-sm fw-bold text-primary w-100">
                                    <i class="bi bi-camera-video-fill me-1"></i> Join Zoom Meeting
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Overview Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white content-card-hover h-100">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 fs-4">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold mb-0">3</h3>
                                    <span class="text-muted small">Enrolled Modules</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white content-card-hover h-100">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 fs-4">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold mb-0">3</h3>
                                    <span class="text-muted small">Pending Tasks</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white content-card-hover h-100">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 fs-4">
                                    <i class="bi bi-play-btn-fill"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold mb-0">18 hrs</h3>
                                    <span class="text-muted small">Watch Time</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white content-card-hover h-100">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 fs-4">
                                    <i class="bi bi-award-fill"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold mb-0">1</h3>
                                    <span class="text-muted small">Completed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Grid Layout -->
                <div class="row g-4">
                    
                    <!-- Left Column: In Progress Modules -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0"><i class="bi bi-book me-2 text-primary"></i>In-Progress Modules</h5>
                                <a href="course.php" class="text-decoration-none fw-semibold small text-primary">View All <i class="bi bi-chevron-right"></i></a>
                            </div>
                            
                            <!-- Course Item 1: Software Engineering -->
                            <div class="card border rounded-4 p-3 mb-3 content-card-hover">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-3">
                                        <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=300&q=80" class="img-fluid rounded-3 w-100" style="height: 90px; object-fit: cover;" alt="Course">
                                    </div>
                                    <div class="col-md-6">
                                        <span class="badge bg-primary bg-opacity-10 text-primary mb-1">Computing Module</span>
                                        <h6 class="fw-bold text-dark mb-1">Software Engineering</h6>
                                        <p class="text-muted extra-small mb-2"><i class="bi bi-person me-1"></i> Prof. K. L. Perera • SE-2026</p>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: 65%;"></div>
                                            </div>
                                            <span class="small text-muted fw-semibold">65%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <a href="course-details-se.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 w-100 fw-semibold">
                                            Continue <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Item 2: Data Science & AI -->
                            <div class="card border rounded-4 p-3 mb-3 content-card-hover">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-3">
                                        <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=300&q=80" class="img-fluid rounded-3 w-100" style="height: 90px; object-fit: cover;" alt="Course">
                                    </div>
                                    <div class="col-md-6">
                                        <span class="badge bg-info bg-opacity-10 text-info mb-1">Data Science</span>
                                        <h6 class="fw-bold text-dark mb-1">Data Science & AI</h6>
                                        <p class="text-muted extra-small mb-2"><i class="bi bi-person me-1"></i> Dr. N. Wickramasinghe • DS-2026</p>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 40%;"></div>
                                            </div>
                                            <span class="small text-muted fw-semibold">40%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <a href="course-details-ds.php" class="btn btn-outline-info btn-sm rounded-pill px-3 w-100 fw-semibold">
                                            Continue <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Item 3: Cyber Security Fundamentals -->
                            <div class="card border rounded-4 p-3 content-card-hover">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-3">
                                        <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=300&q=80" class="img-fluid rounded-3 w-100" style="height: 90px; object-fit: cover;" alt="Course">
                                    </div>
                                    <div class="col-md-6">
                                        <span class="badge bg-purple bg-opacity-10 text-purple mb-1" style="color: #7c3aed; background-color: #f3e8ff;">Security</span>
                                        <h6 class="fw-bold text-dark mb-1">Cyber Security Fundamentals</h6>
                                        <p class="text-muted extra-small mb-2"><i class="bi bi-person me-1"></i> Eng. S. Samarawickrama • CS-2026</p>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-dark" role="progressbar" style="width: 25%;"></div>
                                            </div>
                                            <span class="small text-muted fw-semibold">25%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <a href="course-details-cyber.php" class="btn btn-outline-dark btn-sm rounded-pill px-3 w-100 fw-semibold">
                                            Continue <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Right Column: Deadlines & Recent Activities -->
                    <div class="col-lg-4">
                        
                        <!-- Pending Tasks / Assignments Card -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-alarm text-warning me-2"></i>Upcoming Deadlines</h5>
                            
                            <div class="d-flex flex-column gap-3">
                                <!-- Task 1 -->
                                <div class="p-3 bg-light rounded-3 border-start border-3 border-danger">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-dark mb-0 small">ER Diagram Design</h6>
                                        <span class="badge bg-danger extra-small">Aug 02</span>
                                    </div>
                                    <p class="text-muted extra-small mb-2">Software Engineering • Assignment 01</p>
                                    <a href="course-details-se.php" class="btn btn-sm btn-light border rounded-pill extra-small fw-semibold text-danger w-100">
                                        <i class="bi bi-upload me-1"></i> Submit Work
                                    </a>
                                </div>

                                <!-- Task 2 -->
                                <div class="p-3 bg-light rounded-3 border-start border-3 border-warning">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-dark mb-0 small">Housing Dataset Analysis</h6>
                                        <span class="badge bg-warning text-dark extra-small">Aug 10</span>
                                    </div>
                                    <p class="text-muted extra-small mb-2">Data Science & AI • Assignment 01</p>
                                    <a href="course-details-ds.php" class="btn btn-sm btn-light border rounded-pill extra-small fw-semibold text-warning-emphasis w-100">
                                        <i class="bi bi-upload me-1"></i> Submit Work
                                    </a>
                                </div>

                                <!-- Task 3 -->
                                <div class="p-3 bg-light rounded-3 border-start border-3 border-secondary">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-dark mb-0 small">Port Scanning & Threat Report</h6>
                                        <span class="badge bg-secondary extra-small">Aug 15</span>
                                    </div>
                                    <p class="text-muted extra-small mb-2">Cyber Security • Assignment 01</p>
                                    <a href="course-details-cyber.php" class="btn btn-sm btn-light border rounded-pill extra-small fw-semibold text-secondary w-100">
                                        <i class="bi bi-upload me-1"></i> Submit Work
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity Log Card -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <h5 class="fw-bold mb-3"><i class="bi bi-activity text-info me-2"></i>Recent Activity</h5>
                            
                            <ul class="list-unstyled mb-0 extra-small">
                                <li class="d-flex gap-3 mb-3">
                                    <div class="text-primary fs-6"><i class="bi bi-file-earmark-arrow-down-fill"></i></div>
                                    <div>
                                        <strong class="d-block text-dark">Downloaded Week 01 Lecture Notes</strong>
                                        <span class="text-muted">Software Engineering • Today at 09:15 AM</span>
                                    </div>
                                </li>
                                <li class="d-flex gap-3 mb-3">
                                    <div class="text-success fs-6"><i class="bi bi-check-circle-fill"></i></div>
                                    <div>
                                        <strong class="d-block text-dark">Watched Pandas & EDA Recording</strong>
                                        <span class="text-muted">Data Science & AI • Yesterday</span>
                                    </div>
                                </li>
                                <li class="d-flex gap-3">
                                    <div class="text-warning fs-6"><i class="bi bi-star-fill"></i></div>
                                    <div>
                                        <strong class="d-block text-dark">Enrolled in Cyber Security Fundamentals</strong>
                                        <span class="text-muted">Security Module • 2 days ago</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>