<?php
session_start();
include_once('../db.php');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$teacher_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$teacher = null;

if ($teacher_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $teacher = $res->fetch_assoc();
    }
}

if (!$teacher) {
    header("Location: teachers-list.php");
    exit();
}

// Available Modules Query
$modulesQuery = $conn->query("SELECT id, course_code, course_name FROM courses ORDER BY course_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Instructor & Assign Module - Admin Panel</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="admin-layout-wrapper">
        <?php include('admin-sidebar.php'); ?>

        <div class="main-wrapper">
            <?php include('topbar.php'); ?>

            <div class="content-area">
                
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">Assign Subject Module to Instructor</h3>
                        <p class="text-secondary small mb-0">Manage instructor profile and allocate academic subject modules.</p>
                    </div>
                    <a href="teachers-list.php" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm fw-semibold extra-small">
                        <i class="bi bi-arrow-left me-1"></i> Back to All Instructors
                    </a>
                </div>

                <div class="card card-custom p-4 p-md-5 border-0 shadow-sm rounded-4 bg-white">
                    <form id="editInstructorForm" method="POST" novalidate>

                        <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                        <input type="hidden" name="action" value="update">

                        <!-- SECTION 1: PERSONAL DETAILS -->
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="bi bi-person-badge text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Instructor Personal Information</h6>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Title *</label>
                                <select class="form-select rounded-3 py-2" id="title" name="title" required>
                                    <option value="Dr." <?= ($teacher['title'] == 'Dr.') ? 'selected' : '' ?>>Dr.</option>
                                    <option value="Prof." <?= ($teacher['title'] == 'Prof.') ? 'selected' : '' ?>>Prof.</option>
                                    <option value="Mr." <?= ($teacher['title'] == 'Mr.') ? 'selected' : '' ?>>Mr.</option>
                                    <option value="Mrs." <?= ($teacher['title'] == 'Mrs.') ? 'selected' : '' ?>>Mrs.</option>
                                    <option value="Ms." <?= ($teacher['title'] == 'Ms.') ? 'selected' : '' ?>>Ms.</option>
                                </select>
                            </div>
                            <div class="col-md-10">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Full Name *</label>
                                <input type="text" class="form-control rounded-3 py-2" id="full_name" name="full_name" value="<?= htmlspecialchars($teacher['full_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Official Email Address *</label>
                                <input type="email" class="form-control rounded-3 py-2" id="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Phone Number</label>
                                <input type="tel" class="form-control rounded-3 py-2" id="phone" name="phone" value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- SECTION 2: MODULE ASSIGNMENT -->
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="bi bi-journal-check text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Module Allocation & Department</h6>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Department</label>
                                <select class="form-select rounded-3 py-2" id="department" name="department">
                                    <option value="Software Engineering" <?= ($teacher['department'] == 'Software Engineering') ? 'selected' : '' ?>>Software Engineering</option>
                                    <option value="Data Science" <?= ($teacher['department'] == 'Data Science') ? 'selected' : '' ?>>Data Science</option>
                                    <option value="Cyber Security" <?= ($teacher['department'] == 'Cyber Security') ? 'selected' : '' ?>>Cyber Security</option>
                                    <option value="Information Technology" <?= ($teacher['department'] == 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Assign Primary Subject Module *</label>
                                <select class="form-select rounded-3 py-2 border-primary" id="assigned_module" name="assigned_module" required>
                                    <option value="">-- Choose Module to Assign --</option>
                                    <?php if ($modulesQuery && $modulesQuery->num_rows > 0): ?>
                                        <?php while ($mod = $modulesQuery->fetch_assoc()): ?>
                                            <option value="<?= $mod['id'] ?>" <?= ($teacher['assigned_module'] == $mod['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars(($mod['course_code'] ? $mod['course_code'] . ' - ' : '') . $mod['course_name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- SECTION 3: LOGIN CREDENTIALS -->
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="bi bi-shield-lock text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Portal Access Credentials</h6>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Username *</label>
                                <input type="text" class="form-control rounded-3 py-2 bg-light" id="username" name="username" value="<?= htmlspecialchars($teacher['username'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">New Password (Optional)</label>
                                <input type="text" class="form-control rounded-3 py-2" id="password" name="password" placeholder="Leave blank to keep unchanged">
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                            <a href="teachers-list.php" class="btn btn-light rounded-pill px-4 py-2 btn-sm fw-semibold">Cancel</a>
                            <button type="submit" id="btnSubmit" class="btn btn-primary rounded-pill px-4 py-2 btn-sm fw-bold shadow-sm">
                                <i class="bi bi-save-fill me-1"></i> Save & Assign Module
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Link to JS File using Dash (-) -->
    <script src="ajax/js/edit-teacher.js"></script>
</body>
</html>