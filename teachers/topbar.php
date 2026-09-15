<?php
// Session එක දැනටමත් Start වී නැත්නම් පමණක් Silent එකේ Start කිරීම
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

// Log වී සිටින Lecturer ගේ Details ලබා ගැනීම
$logged_teacher_name  = $_SESSION['teacher_name'] ?? 'Instructor';
$logged_teacher_email = $_SESSION['teacher_email'] ?? 'instructor@edumart.ac.lk';

// Dynamic Profile Photo (UI-Avatars API)
$avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($logged_teacher_name) . "&background=0D6EFD&color=fff";
?>

<!-- Instructor Smart Topbar -->
<nav class="navbar navbar-expand-lg navbar-light border-bottom px-4 py-2 sticky-top shadow-sm smart-topbar bg-white">
    <div class="container-fluid p-0">
        
        <!-- Mobile Toggle Button for Sidebar -->
        <button class="btn btn-light d-md-none me-2 border rounded-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#instructorSidebar">
            <i class="bi bi-list fs-5"></i>
        </button>

        <!-- Portal Indicator -->
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill p-2">
                <i class="bi bi-person-workspace fs-6"></i>
            </span>
            <span class="fw-bold text-dark fs-6 mb-0 d-none d-sm-inline">Instructor Control Panel</span>
        </div>

        <!-- Right Action Items -->
        <div class="d-flex align-items-center gap-3 ms-auto">

            <!-- Active Status Indicator -->
            <div class="d-none d-md-flex align-items-center bg-light border rounded-pill px-3 py-1">
                <span class="badge bg-success rounded-circle p-1 me-2" style="width: 8px; height: 8px;"></span>
                <span class="extra-small fw-semibold text-secondary">Semester 02 - Active</span>
            </div>

            <!-- Notifications Dropdown -->
            <div class="dropdown">
                <a href="#" class="btn btn-light border-0 rounded-circle position-relative p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell text-secondary fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle pulse-badge">
                        <span class="visually-hidden">Pending tasks</span>
                    </span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 notification-dropdown">
                    <li class="d-flex justify-content-between align-items-center px-2 py-1 mb-2">
                        <h6 class="fw-bold mb-0 text-dark">Teacher Alerts</h6>
                        <span class="badge bg-danger rounded-pill extra-small">2 Pending</span>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    
                    <div class="d-flex flex-column gap-1 my-1 custom-scrollbar" style="max-height: 280px; overflow-y: auto;">
                        <a href="classes.php" class="dropdown-item p-2 notification-item text-wrap rounded-3">
                            <div class="d-flex gap-2 align-items-start">
                                <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                                    <i class="bi bi-camera-video"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="small text-dark fw-semibold mb-0">Upcoming Class Today</p>
                                    <p class="extra-small text-muted mb-1">Advanced Software Architecture at 02:00 PM.</p>
                                    <span class="extra-small text-primary fw-medium">In 1 hour</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item text-center small text-primary fw-semibold py-1 rounded-3" href="classes.php">
                            View Schedule →
                        </a>
                    </li>
                </ul>
            </div>

            <div class="vr mx-1 my-auto" style="height: 24px;"></div>

            <!-- Profile Dropdown (Dynamic Data) -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle p-1 rounded-pill pe-2 hover-bg" id="instructorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="position-relative">
                        <img src="<?= $avatar_url ?>" alt="User" width="36" height="36" class="rounded-circle border">
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle"></span>
                    </div>
                    <div class="d-none d-sm-flex flex-column text-start ms-2">
                        <span class="fw-bold text-dark extra-small lh-1"><?= htmlspecialchars($logged_teacher_name) ?></span>
                        <span class="text-muted extra-small" style="font-size: 0.7rem;">Lecturer</span>
                    </div>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 rounded-4" style="min-width: 210px;">
                    <li class="px-3 py-2 bg-light rounded-3 mb-2">
                        <p class="fw-bold text-dark mb-0 small"><?= htmlspecialchars($logged_teacher_name) ?></p>
                        <p class="text-muted extra-small mb-0"><?= htmlspecialchars($logged_teacher_email) ?></p>
                    </li>
                    <!-- Profile Link -->
                    <li>
                        <a class="dropdown-item rounded-3 py-2 fw-semibold" href="profile.php">
                            <i class="bi bi-person-circle me-2 text-primary"></i> My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2" href="courses.php">
                            <i class="bi bi-journal-bookmark me-2 text-success"></i> My Modules
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2 text-danger fw-semibold" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</nav>