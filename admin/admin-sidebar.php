<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Query counts if db connection is initialized
$sidebar_teachers = 0;
$sidebar_students = 0;
$sidebar_courses  = 0;
$sidebar_modules  = 0;

if (isset($conn)) {
    $resT = $conn->query("SELECT COUNT(*) AS count FROM teachers");
    if ($resT) $sidebar_teachers = $resT->fetch_assoc()['count'];
    
    $resS = $conn->query("SELECT COUNT(*) AS count FROM students");
    if ($resS) $sidebar_students = $resS->fetch_assoc()['count'];

    $resC = $conn->query("SELECT COUNT(*) AS count FROM courses");
    if ($resC) $sidebar_courses = $resC->fetch_assoc()['count'];
}
?>
<!-- Admin Navigation Sidebar -->
<aside class="text-white border-end border-dark vh-100 position-sticky top-0 d-flex flex-column justify-content-between p-3 custom-sidebar" style="width: 260px; min-width: 260px; background-color: #171e2e !important;">
    
    <div>
        <!-- Brand Header -->
        <div class="d-flex align-items-center gap-3 px-2 py-3 mb-2">
            <div class="p-2 text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; background-color: #0066ff;">
                <i class="bi bi-shield-lock-fill fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold text-white mb-0 lh-1" style="font-size: 1.1rem;">EduMart</h5>
                <span class="text-white-50 extra-small" style="font-size: 0.7rem;">System Admin Panel</span>
            </div>
        </div>

        <!-- Navigation Menu Links -->
        <div class="nav nav-pills flex-column gap-1 custom-scrollbar overflow-auto pe-1" style="max-height: calc(100vh - 180px);">
            
            <!-- Dashboard Main Link -->
            <a href="dashboard.php" class="nav-link fw-semibold px-3 py-2.5 d-flex align-items-center gap-3 transition-all <?= ($currentPage == 'dashboard.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="active-icon-box d-flex align-items-center justify-content-center">
                    <i class="bi bi-grid-1x2-fill fs-6"></i>
                </div>
                <span>Dashboard</span>
            </a>

            <!-- SECTION 1: TEACHER / INSTRUCTOR MANAGEMENT -->
            <div class="mt-3 mb-1 px-2">
                <span class="text-uppercase text-white-50 extra-small fw-bold tracking-wider" style="font-size: 0.62rem; letter-spacing: 0.8px; opacity: 0.5;">Teacher Management</span>
            </div>

            <a href="teachers-list.php" class="nav-link fw-medium px-3 py-2.5 d-flex align-items-center justify-content-between transition-all <?= ($currentPage == 'teachers-list.php' || $currentPage == 'edit-teacher.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="active-icon-box d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-workspace fs-6"></i>
                    </div>
                    <span>All Instructors</span>
                </div>
                <span class="badge rounded-pill bg-danger bg-opacity-75 extra-small" style="font-size: 0.65rem;"><?= $sidebar_teachers ?></span>
            </a>

            <a href="add-teacher.php" class="nav-link fw-medium px-3 py-2.5 d-flex align-items-center gap-3 transition-all <?= ($currentPage == 'add-teacher.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="active-icon-box d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-plus-fill fs-6"></i>
                </div>
                <span>Add New Instructor</span>
            </a>

            <!-- SECTION 2: STUDENT MANAGEMENT -->
            <div class="mt-3 mb-1 px-2">
                <span class="text-uppercase text-white-50 extra-small fw-bold tracking-wider" style="font-size: 0.62rem; letter-spacing: 0.8px; opacity: 0.5;">Student Management</span>
            </div>

            <a href="students-list.php" class="nav-link fw-medium px-3 py-2.5 d-flex align-items-center justify-content-between transition-all <?= ($currentPage == 'students-list.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="active-icon-box d-flex align-items-center justify-content-center">
                        <i class="bi bi-people-fill fs-6"></i>
                    </div>
                    <span>All Students</span>
                </div>
                <span class="badge rounded-pill bg-success bg-opacity-75 extra-small" style="font-size: 0.65rem;"><?= $sidebar_students ?></span>
            </a>

            <a href="add-student.php" class="nav-link fw-medium px-3 py-2.5 d-flex align-items-center gap-3 transition-all <?= ($currentPage == 'add-student.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="active-icon-box d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-badge-fill fs-6"></i>
                </div>
                <span>Register Student</span>
            </a>

            <a href="student-enrollments.php" class="nav-link fw-medium px-3 py-2.5 d-flex align-items-center gap-3 transition-all <?= ($currentPage == 'student-enrollments.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="active-icon-box d-flex align-items-center justify-content-center">
                    <i class="bi bi-card-checklist fs-6"></i>
                </div>
                <span>Course Enrollments</span>
            </a>

            <!-- SECTION 3: SYSTEM & ACADEMICS -->
            <div class="mt-3 mb-1 px-2">
                <span class="text-uppercase text-white-50 extra-small fw-bold tracking-wider" style="font-size: 0.62rem; letter-spacing: 0.8px; opacity: 0.5;">Academic Settings</span>
            </div>

            <!-- Manage Courses Link -->
            <a href="courses.php" class="nav-link fw-medium px-3 py-2.5 d-flex align-items-center justify-content-between transition-all <?= ($currentPage == 'courses.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="active-icon-box d-flex align-items-center justify-content-center">
                        <i class="bi bi-book-half fs-6"></i>
                    </div>
                    <span>Manage Courses</span>
                </div>
                <span class="badge rounded-pill bg-primary bg-opacity-75 extra-small" style="font-size: 0.65rem;"><?= $sidebar_courses ?></span>
            </a>

            <!-- NEW: Course Modules Link -->
            <a href="modules-list.php" class="nav-link fw-medium px-3 py-2.5 d-flex align-items-center gap-3 transition-all <?= ($currentPage == 'modules-list.php' || $currentPage == 'course-modules.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="active-icon-box d-flex align-items-center justify-content-center">
                    <i class="bi bi-journal-bookmark-fill fs-6"></i>
                </div>
                <span>Course Modules</span>
            </a>

            <a href="announcement.php" class="nav-link fw-medium px-3 py-2.5 d-flex align-items-center gap-3 transition-all <?= ($currentPage == 'announcement.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="active-icon-box d-flex align-items-center justify-content-center">
                    <i class="bi bi-megaphone-fill fs-6"></i>
                </div>
                <span>Announcements</span>
            </a>

            <a href="admin-calendar.php" class="nav-link fw-medium px-3 py-2.5 d-flex align-items-center gap-3 transition-all <?= ($currentPage == 'admin-calendar.php' || $currentPage == 'calendar.php') ? 'active-sidebar-pill' : 'text-white-50 hover-dark' ?>" style="border-radius: 12px;">
                <div class="active-icon-box d-flex align-items-center justify-content-center">
                    <i class="bi bi-calendar-fill fs-6"></i>
                </div>
                <span>Calendar</span>
            </a>

        </div>
    </div>

    <!-- Admin Profile Footer Info -->
    <div class="border-top border-secondary border-opacity-10 pt-3">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle p-2 rounded-3 hover-dark" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; background-color: #0066ff;">
                    A
                </div>
                <div class="lh-1 ms-2 overflow-hidden">
                    <p class="fw-bold text-white extra-small mb-1 text-truncate">System Admin</p>
                    <span class="text-white-50 extra-small d-block text-truncate" style="font-size: 0.65rem;">admin@edumart.ac.lk</span>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark shadow rounded-3 border-0">
                <li><a class="dropdown-item extra-small text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign out</a></li>
            </ul>
        </div>
    </div>

</aside>

<style>
.active-sidebar-pill {
    background-color: #0066ff !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(0, 102, 255, 0.35);
}
.active-sidebar-pill .active-icon-box {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    width: 28px;
    height: 28px;
}
.hover-dark:hover {
    background-color: rgba(255, 255, 255, 0.05);
    color: #ffffff !important;
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
</style>