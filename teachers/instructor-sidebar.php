<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

// Check the current PHP file name
$currentPage = basename($_SERVER['PHP_SELF']);

// Get the logged-in lecturer's name
$sidebar_teacher_name = $_SESSION['teacher_name'] ?? 'Instructor';
$sidebar_avatar_url   = "https://ui-avatars.com/api/?name=" . urlencode($sidebar_teacher_name) . "&background=0D6EFD&color=fff";
?>

<!-- Embedded CSS for Sidebar Styling -->
<style>
.teacher-sidebar {
    width: 260px;
    min-width: 260px;
    background-color: #1a233a !important; /* Dark theme sidebar background */
    color: #ffffff;
    min-height: 100vh;
}

.teacher-sidebar .brand-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #ffffff;
}

.teacher-sidebar .nav-link-custom {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    color: #94a3b8;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.teacher-sidebar .nav-link-custom:hover {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.05);
}

/* Active Highlight Pill Button */
.teacher-sidebar .nav-link-custom.active {
    background-color: #0d6efd !important;
    color: #ffffff !important;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.teacher-sidebar .extra-small {
    font-size: 0.75rem;
}

.teacher-sidebar .hover-bg:hover {
    background-color: rgba(255, 255, 255, 0.08);
}
</style>

<div class="d-flex flex-column flex-shrink-0 p-3 teacher-sidebar shadow">
    
    <!-- Brand Logo -->
    <a href="dashboard.php" class="d-flex align-items-center mb-4 text-decoration-none px-2 mt-1">
        <div class="p-2 bg-primary text-white rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-mortarboard-fill fs-5"></i>
        </div>
        <span class="brand-title">EduMart</span>
    </a>

    <!-- Navigation -->
    <div class="flex-grow-1">
        <ul class="nav nav-pills flex-column gap-1">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link-custom <?= ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 fs-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="modules.php" class="nav-link-custom <?= ($currentPage == 'modules.php' || $currentPage == 'course-view.php') ? 'active' : ''; ?>">
                    <i class="bi bi-journal-bookmark fs-5"></i>
                    <span>My Modules</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="classes.php" class="nav-link-custom <?= ($currentPage == 'classes.php') ? 'active' : ''; ?>">
                    <i class="bi bi-calendar-event fs-5"></i>
                    <span>Live Classes</span>
                </a>
            </li>
            <!-- Assignments Link Fix -->
            <li class="nav-item">
                <a href="assignments.php" class="nav-link-custom <?= (strpos($currentPage, 'assignments') !== false) ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-text fs-5"></i>
                    <span>Assignments</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="announcements.php" class="nav-link-custom <?= ($currentPage == 'announcements.php') ? 'active' : ''; ?>">
                    <i class="bi bi-megaphone fs-5"></i>
                    <span>Announcements</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="student.php" class="nav-link-custom <?= ($currentPage == 'student.php' || $currentPage == 'students.php') ? 'active' : ''; ?>">
                    <i class="bi bi-people fs-5"></i>
                    <span>Students</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Dynamic Quick Profile Footer -->
    <div class="pt-3 border-top border-secondary border-opacity-25">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle p-2 rounded-3 hover-bg" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?= $sidebar_avatar_url ?>" alt="User" width="35" height="35" class="rounded-circle me-2 border border-secondary">
                <div class="lh-1 overflow-hidden">
                    <strong class="d-block small text-truncate"><?= htmlspecialchars($sidebar_teacher_name) ?></strong>
                    <span class="text-secondary extra-small text-truncate d-block">Lecturer</span>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark shadow rounded-3 border-0">
                <li><a class="dropdown-item extra-small text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign out</a></li>
            </ul>
        </div>
    </div>

</div>