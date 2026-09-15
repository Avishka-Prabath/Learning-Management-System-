<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection එක Check කරගැනීම
if (file_exists('../db.php')) {
    require_once '../db.php';
} elseif (file_exists('db.php')) {
    require_once 'db.php';
}

$s_id = $_SESSION['student_id'] ?? 0;

// Database එකෙන් Student ගේ Latest Dynamic ID, Name, Email සහ Course Code එක Fetch කිරීම
$s_name  = $_SESSION['student_name'] ?? 'Student Profile';
$s_email = $_SESSION['student_email'] ?? 'student@edumart.ac.lk';
$s_code  = $_SESSION['student_code'] ?? 'STUDENT';
$s_course_code = $_SESSION['course_code'] ?? '';

if (isset($conn) && $s_id > 0) {
    $stmtTop = $conn->prepare("
        SELECT s.student_id, s.first_name, s.last_name, s.email, c.course_code 
        FROM students s 
        LEFT JOIN courses c ON (s.course_id = c.id OR s.course_id = c.course_code OR s.course_id = c.course_name) 
        WHERE s.id = ? 
        LIMIT 1
    ");
    $stmtTop->bind_param("i", $s_id);
    $stmtTop->execute();
    $topData = $stmtTop->get_result()->fetch_assoc();
    $stmtTop->close();

    if ($topData) {
        $s_code  = !empty($topData['student_id']) ? $topData['student_id'] : $s_code;
        $s_name  = trim(($topData['first_name'] ?? '') . ' ' . ($topData['last_name'] ?? '')) ?: $s_name;
        $s_email = $topData['email'] ?? $s_email;
        $s_course_code = $topData['course_code'] ?? $s_course_code;

        // Session values auto update කිරීම
        $_SESSION['student_code'] = $s_code;
        $_SESSION['student_name'] = $s_name;
        $_SESSION['student_email'] = $s_email;
        $_SESSION['course_code']  = $s_course_code;
    }
}

// Dynamic Avatar URL Generator
$avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($s_name) . "&background=0D6EFD&color=fff";
?>

<nav class="navbar navbar-expand-lg navbar-light border-bottom px-4 py-2 sticky-top shadow-sm smart-topbar">
    <div class="container-fluid p-0">
        
        <!-- Mobile Toggle Button -->
        <button class="btn btn-light d-md-none me-2 border rounded-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#studentSidebar">
            <i class="bi bi-list fs-5"></i>
        </button>

        <!-- Brand Title & Active Course Code Badge -->
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill p-2">
                <i class="bi bi-person-badge fs-6"></i>
            </span>
            <span class="fw-bold text-dark fs-6 mb-0 d-none d-sm-inline">Student Portal</span>
            <?php if (!empty($s_course_code)): ?>
                <span class="badge bg-primary rounded-pill px-2.5 py-1 extra-small fw-bold ms-1">
                    <?= htmlspecialchars($s_course_code) ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Right Side Actions -->
        <div class="d-flex align-items-center gap-3 ms-auto">

            <!-- Dynamic Notifications Dropdown -->
            <div class="dropdown">
                <a href="#" class="btn btn-light border-0 rounded-circle position-relative p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell text-secondary fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle pulse-badge">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 notification-dropdown">
                    <li class="d-flex justify-content-between align-items-center px-2 py-1 mb-2">
                        <h6 class="fw-bold mb-0 text-dark">Notifications</h6>
                        <span class="badge bg-primary rounded-pill extra-small">2 New</span>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    
                    <div class="d-flex flex-column gap-1 my-1" style="max-height: 280px; overflow-y: auto;">
                        <a href="#" class="dropdown-item p-2 notification-item text-wrap rounded-3">
                            <div class="d-flex gap-2 align-items-start">
                                <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-circle">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="small text-dark fw-semibold mb-0">Assignment Deadline Warning</p>
                                    <p class="extra-small text-muted mb-1">Semester assignment submission due soon.</p>
                                    <span class="extra-small text-primary fw-medium">10 mins ago</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item text-center small text-primary fw-semibold py-1 rounded-3" href="#">
                            Mark all as read
                        </a>
                    </li>
                </ul>
            </div>

            <div class="vr mx-1 my-auto" style="height: 24px;"></div>

            <!-- Dynamic Student Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle p-1 rounded-pill pe-2 hover-bg" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="position-relative">
                        <img src="<?= htmlspecialchars($avatar_url) ?>" alt="User" width="36" height="36" class="rounded-circle border">
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle"></span>
                    </div>
                    <div class="d-none d-sm-flex flex-column text-start ms-2">
                        <span class="fw-bold text-dark extra-small lh-1"><?= htmlspecialchars($s_name) ?></span>
                        <span class="text-primary fw-bold extra-small mt-1" style="font-size: 0.72rem;"><?= htmlspecialchars($s_code) ?></span>
                    </div>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 rounded-4" style="min-width: 220px;">
                    <li class="px-3 py-2 bg-light rounded-3 mb-2">
                        <p class="fw-bold text-dark mb-0 small"><?= htmlspecialchars($s_name) ?></p>
                        <p class="text-muted extra-small mb-0 text-truncate"><?= htmlspecialchars($s_email) ?></p>
                        <span class="badge bg-primary bg-opacity-10 text-primary extra-small fw-bold mt-1"><?= htmlspecialchars($s_code) ?></span>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2" href="profile.php">
                            <i class="bi bi-person-gear me-2 text-primary"></i> Account Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2" href="profile.php#payments-info">
                            <i class="bi bi-credit-card me-2 text-success"></i> Payment Slips
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