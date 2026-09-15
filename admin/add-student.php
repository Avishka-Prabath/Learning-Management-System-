<?php
session_start();
include_once('../db.php');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$enrollment_id = isset($_GET['enrollment_id']) ? intval($_GET['enrollment_id']) : 0;
$edit_student_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$is_direct_add = isset($_GET['action']) && $_GET['action'] === 'new';
$studentData = [];
$is_edit_mode = false;

// Helper Function: Generate Dynamic Prefix based on Course Name or Code
function getCoursePrefix($course_code, $course_name) {
    $code = strtoupper(trim($course_code ?? ''));
    $name = strtolower(trim($course_name ?? ''));

    if (!empty($code)) {
        if (strpos($code, 'SE') !== false) return 'SE';
        if (strpos($code, 'CS') !== false) return 'CS';
        if (strpos($code, 'BM') !== false) return 'BM';
        if (strpos($code, 'DS') !== false) return 'DS';
        if (strpos($code, 'IT') !== false || strpos($code, 'ICT') !== false) return 'IT';
    }

    if (strpos($name, 'software') !== false) return 'SE';
    if (strpos($name, 'cyber') !== false) return 'CS';
    if (strpos($name, 'business') !== false) return 'BM';
    if (strpos($name, 'data science') !== false) return 'DS';
    if (strpos($name, 'information') !== false || strpos($name, 'ict') !== false) return 'IT';

    return 'EM'; // Default EduMart Prefix
}

// 1. If EDIT Mode
if ($edit_student_id > 0) {
    $is_edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $edit_student_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $studentData = $res->fetch_assoc();
        if (empty($studentData['campus_id']) && !empty($studentData['student_id'])) {
            $studentData['campus_id'] = $studentData['student_id'];
        }
    }
}
// 2. If Approval Request Auto-fill
elseif ($enrollment_id > 0) {
    $stmt = $conn->prepare("
        SELECT e.*, c.course_code, c.course_name 
        FROM enrollments e 
        LEFT JOIN courses c ON e.course_id = c.id 
        WHERE e.id = ?
    ");
    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $enrollData = $res->fetch_assoc();
        
        $first_name = '';
        $last_name = '';
        if (!empty($enrollData['full_name'])) {
            $parts = explode(' ', trim($enrollData['full_name']), 2);
            $first_name = $parts[0] ?? '';
            $last_name = $parts[1] ?? '';
        }

        $prefix = getCoursePrefix($enrollData['course_code'] ?? '', $enrollData['course_name'] ?? '');
        $generated_campus_id = $prefix . '-2026-' . sprintf("%04d", $enrollData['id']);

        $studentData = [
            'campus_id'         => $generated_campus_id,
            'full_name'         => $enrollData['full_name'],
            'first_name'        => $first_name,
            'last_name'         => $last_name,
            'nic'               => $enrollData['nic'],
            'dob'               => $enrollData['dob'],
            'gender'            => $enrollData['gender'],
            'email'             => $enrollData['email'],
            'campus_email'      => strtolower($first_name . '.' . $enrollData['id'] . '@edumart.ac.lk'),
            'phone'             => $enrollData['phone'],
            'address'           => $enrollData['address'],
            'course_id'         => $enrollData['course_id'],
            'study_mode'        => $enrollData['study_mode'],
            'intake'            => $enrollData['intake'],
            'qualification'     => $enrollData['qualification'],
            'school'            => $enrollData['school'],
            'guardian_name'     => $enrollData['guardian_name'],
            'guardian_phone'    => $enrollData['guardian_phone'],
            'guardian_relation' => $enrollData['guardian_relation'],
            'password'          => 'student123',
            'status'            => 'Active'
        ];
    }
} 
// 3. Direct Add Mode
elseif ($is_direct_add) {
    $lastRes = $conn->query("SELECT id FROM students ORDER BY id DESC LIMIT 1");
    $nextId = ($lastRes && $lastRes->num_rows > 0) ? ($lastRes->fetch_assoc()['id'] + 1) : 1;

    $studentData = [
        'campus_id'         => 'SE-2026-' . sprintf("%04d", $nextId),
        'full_name'         => '',
        'first_name'        => '',
        'last_name'         => '',
        'nic'               => '',
        'dob'               => '',
        'gender'            => '',
        'email'             => '',
        'campus_email'      => '',
        'phone'             => '',
        'address'           => '',
        'course_id'         => '',
        'study_mode'        => 'Full-Time',
        'intake'            => '2026-Batch-01',
        'qualification'     => '',
        'school'            => '',
        'guardian_name'     => '',
        'guardian_phone'    => '',
        'guardian_relation' => 'Father',
        'password'          => 'student123',
        'status'            => 'Active'
    ];
}

// Fetch Active Courses for Form Dropdown
$coursesQuery = $conn->query("SELECT id, course_code, course_name FROM courses ORDER BY course_name ASC");
$courses_data = [];
if ($coursesQuery && $coursesQuery->num_rows > 0) {
    while ($c = $coursesQuery->fetch_assoc()) {
        $courses_data[] = $c;
    }
}

// Approved / Registered Enrollments List
$approvedEnrollments = $conn->query("
    SELECT 
        e.*, 
        c.course_name, 
        c.course_code 
    FROM enrollments e 
    LEFT JOIN courses c ON e.course_id = c.id 
    WHERE e.status IN ('Approved', 'Registered') 
    ORDER BY e.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Admin Panel</title>
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

                <?php if ($enrollment_id == 0 && !$is_direct_add && $edit_student_id == 0): ?>
                    <!-- VIEW 1: APPROVED STUDENTS LIST + DIRECT ADD BUTTON -->
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1">Approved Student Applications</h3>
                            <p class="text-secondary small mb-0">Select an approved application below or register a new student directly.</p>
                        </div>
                        <!-- Direct Register Button -->
                        <a href="add-student.php?action=new" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm btn-sm">
                            <i class="bi bi-person-plus-fill me-1"></i> Register New Student
                        </a>
                    </div>

                    <div class="card card-custom p-4 border-0 shadow-sm rounded-4 bg-white">
                        <div class="table-responsive w-100">
                            <table class="table table-hover align-middle mb-0 w-100">
                               <thead>
                                    <tr class="text-muted extra-small text-uppercase">
                                        <th>Applicant Details</th>
                                        <th>NIC / Contact</th>
                                        <th>Course & Intake</th>
                                        <th>Guardian Contact</th>
                                        <th class="text-end">Status / Action</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    <?php if ($approvedEnrollments && $approvedEnrollments->num_rows > 0): ?>
                                        <?php while ($row = $approvedEnrollments->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['full_name']) ?></h6>
                                                        <span class="badge bg-light text-dark border extra-small me-1"><?= htmlspecialchars($row['gender'] ?? 'N/A') ?></span>
                                                        <span class="text-muted extra-small"><i class="bi bi-mortarboard me-1"></i><?= htmlspecialchars($row['qualification'] ?? 'N/A') ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <span class="fw-semibold text-dark d-block extra-small"><i class="bi bi-card-heading me-1 text-primary"></i><?= htmlspecialchars($row['nic']) ?></span>
                                                        <span class="text-muted extra-small d-block"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($row['phone']) ?></span>
                                                        <span class="text-muted extra-small"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($row['email']) ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <?php if(!empty($row['course_code'])): ?>
                                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 extra-small fw-semibold"><?= htmlspecialchars($row['course_code']) ?></span>
                                                        <?php endif; ?>
                                                        <span class="text-dark fw-bold extra-small d-block mt-1"><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></span>
                                                        <span class="text-secondary extra-small"><i class="bi bi-clock-history me-1"></i><?= htmlspecialchars(($row['study_mode'] ?? 'Full-Time') . ' | ' . ($row['intake'] ?? '')) ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <span class="fw-semibold text-dark d-block extra-small"><?= htmlspecialchars($row['guardian_name'] ?? 'N/A') ?></span>
                                                        <span class="text-muted extra-small"><i class="bi bi-telephone-fill me-1"></i><?= htmlspecialchars($row['guardian_phone'] ?? 'N/A') ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <?php if ($row['status'] === 'Registered'): ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Registered
                                                        </span>
                                                    <?php else: ?>
                                                        <a href="add-student.php?enrollment_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold shadow-sm">
                                                            <i class="bi bi-person-check-fill me-1"></i> Register Student
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                <i class="bi bi-check2-all fs-2 opacity-50 d-block mb-2"></i>
                                                No applications found. You can use "Register New Student" above to add students manually.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- VIEW 2: REGISTRATION / EDIT FORM -->
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1"><?= $is_edit_mode ? 'Edit Student Details' : ($is_direct_add ? 'Direct Student Registration Form' : 'Official Student Registration Form') ?></h3>
                            <p class="text-secondary small mb-0">Fill out student credentials, program assignment, and portal details.</p>
                        </div>
                        <a href="add-student.php" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm fw-semibold extra-small">
                            <i class="bi bi-arrow-left me-1"></i> Back to Approved List
                        </a>
                    </div>

                    <div class="card custom-card p-4 p-md-5 shadow-sm rounded-4 border-0 bg-white mb-5">
                        <form id="createStudentForm" novalidate>
                            
                            <!-- Hidden Fields for Tracking Action & Student ID -->
                            <input type="hidden" name="form_action" value="<?= $is_edit_mode ? 'update' : 'create' ?>">
                            <input type="hidden" name="student_db_id" value="<?= $edit_student_id ?>">
                            <input type="hidden" name="enrollment_id" value="<?= $enrollment_id ?>">

                            <!-- 1. CAMPUS ISSUED CREDENTIALS -->
                            <div class="d-flex align-items-center mb-4 border-bottom pb-2">
                                <i class="bi bi-shield-lock-fill fs-4 text-primary me-2"></i>
                                <h5 class="fw-bold section-title mb-0">1. Official Campus Credentials</h5>
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary small">Campus Student ID *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                                        <input type="text" class="form-control fw-bold text-primary" name="campus_id" id="campus_id" value="<?= htmlspecialchars($studentData['campus_id'] ?? '') ?>" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary small">Official Campus Email *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-envelope-at"></i></span>
                                        <input type="email" class="form-control" name="campus_email" id="campus_email" placeholder="student@edumart.ac.lk" value="<?= htmlspecialchars($studentData['campus_email'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary small">Portal Login Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                        <input type="text" class="form-control" name="password" id="password" value="<?= $is_edit_mode ? '' : htmlspecialchars($studentData['password'] ?? 'student123') ?>" placeholder="<?= $is_edit_mode ? 'Leave blank to keep current password' : 'student123' ?>" <?= $is_edit_mode ? '' : 'required' ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. PERSONAL INFORMATION -->
                            <div class="d-flex align-items-center mb-4 border-bottom pb-2">
                                <i class="bi bi-person-badge-fill fs-4 text-primary me-2"></i>
                                <h5 class="fw-bold section-title mb-0">2. Personal Details</h5>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold text-secondary small">Full Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?= htmlspecialchars($studentData['full_name'] ?? '') ?>" placeholder="e.g. Kaluaratchige Nimal Perera" required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold text-secondary small">NIC / Passport Number *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-card-heading"></i></span>
                                        <input type="text" id="nic" name="nic" class="form-control" value="<?= htmlspecialchars($studentData['nic'] ?? '') ?>" placeholder="e.g. 1998XXXXXXXX" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">First Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" id="first_name" name="first_name" class="form-control" value="<?= htmlspecialchars($studentData['first_name'] ?? '') ?>" placeholder="Kasun" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Last Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person-lines-fill"></i></span>
                                        <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($studentData['last_name'] ?? '') ?>" placeholder="Perera" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary small">Date of Birth *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar-date"></i></span>
                                        <input type="date" id="dob" name="dob" class="form-control" value="<?= htmlspecialchars($studentData['dob'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary small">Gender *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-gender-ambiguous"></i></span>
                                        <select id="gender" name="gender" class="form-select">
                                            <option value="" disabled <?= empty($studentData['gender']) ? 'selected' : '' ?>>-- Select Gender --</option>
                                            <option value="Male" <?= (isset($studentData['gender']) && $studentData['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= (isset($studentData['gender']) && $studentData['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                                            <option value="Other" <?= (isset($studentData['gender']) && $studentData['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary small">Phone Number *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                        <input type="tel" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($studentData['phone'] ?? '') ?>" placeholder="07X XXXXXXX" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Personal Email Address *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($studentData['email'] ?? '') ?>" placeholder="name@example.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Permanent Address *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($studentData['address'] ?? '') ?>" placeholder="No, Street, City" required>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. COURSE & ADMISSION DETAILS -->
                            <div class="d-flex align-items-center my-4 border-bottom pb-2">
                                <i class="bi bi-journal-bookmark-fill fs-4 text-primary me-2"></i>
                                <h5 class="fw-bold section-title mb-0">3. Academic Program & Intake Selection</h5>
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Select Course / Degree Program *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-book"></i></span>
                                        <select id="course_id" name="course_id" class="form-select" required>
                                            <option value="" disabled <?= empty($studentData['course_id']) ? 'selected' : '' ?>>-- Choose a Course --</option>
                                            <?php if (!empty($courses_data)): ?>
                                                <?php foreach ($courses_data as $course): ?>
                                                    <?php $isSelected = (isset($studentData['course_id']) && $studentData['course_id'] == $course['id']) ? 'selected' : ''; ?>
                                                    <option value="<?= $course['id'] ?>" 
                                                            data-code="<?= htmlspecialchars($course['course_code'] ?? '') ?>" 
                                                            data-name="<?= htmlspecialchars($course['course_name'] ?? '') ?>" 
                                                            <?= $isSelected ?>>
                                                        <?= htmlspecialchars(($course['course_code'] ? $course['course_code'] . ' - ' : '') . $course['course_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-secondary small">Study Mode *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-clock-history"></i></span>
                                        <select id="study_mode" name="study_mode" class="form-select">
                                            <option value="Full-Time" <?= (isset($studentData['study_mode']) && $studentData['study_mode'] == 'Full-Time') ? 'selected' : '' ?>>Full-Time</option>
                                            <option value="Part-Time" <?= (isset($studentData['study_mode']) && $studentData['study_mode'] == 'Part-Time') ? 'selected' : '' ?>>Part-Time (Weekend)</option>
                                            <option value="Online" <?= (isset($studentData['study_mode']) && $studentData['study_mode'] == 'Online') ? 'selected' : '' ?>>Online / Distance</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-secondary small">Preferred Intake *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar-event"></i></span>
                                        <input type="text" id="intake" name="intake" class="form-control" value="<?= htmlspecialchars($studentData['intake'] ?? '2026-Batch-01') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- 4. EDUCATIONAL QUALIFICATIONS -->
                            <div class="d-flex align-items-center my-4 border-bottom pb-2">
                                <i class="bi bi-mortarboard-fill fs-4 text-primary me-2"></i>
                                <h5 class="fw-bold section-title mb-0">4. Educational Background</h5>
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Highest Academic Qualification *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-award"></i></span>
                                        <select id="qualification" name="qualification" class="form-select">
                                            <option value="" disabled <?= empty($studentData['qualification']) ? 'selected' : '' ?>>-- Select Qualification --</option>
                                            <option value="GCE A/L" <?= (isset($studentData['qualification']) && $studentData['qualification'] == 'GCE A/L') ? 'selected' : '' ?>>G.C.E. A/L Completed</option>
                                            <option value="GCE O/L" <?= (isset($studentData['qualification']) && $studentData['qualification'] == 'GCE O/L') ? 'selected' : '' ?>>G.C.E. O/L Completed</option>
                                            <option value="Diploma" <?= (isset($studentData['qualification']) && $studentData['qualification'] == 'Diploma') ? 'selected' : '' ?>>Diploma / Higher Diploma</option>
                                            <option value="Bachelor Degree" <?= (isset($studentData['qualification']) && $studentData['qualification'] == 'Bachelor Degree') ? 'selected' : '' ?>>Undergraduate Degree</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">School / Institute Attended</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-building"></i></span>
                                        <input type="text" id="school" name="school" class="form-control" value="<?= htmlspecialchars($studentData['school'] ?? '') ?>" placeholder="e.g. Royal College Colombo">
                                    </div>
                                </div>
                            </div>

                            <!-- 5. PARENT / GUARDIAN CONTACT -->
                            <div class="d-flex align-items-center my-4 border-bottom pb-2">
                                <i class="bi bi-shield-person fs-4 text-primary me-2"></i>
                                <h5 class="fw-bold section-title mb-0">5. Parent / Guardian Details</h5>
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold text-secondary small">Guardian Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" id="guardian_name" name="guardian_name" class="form-control" value="<?= htmlspecialchars($studentData['guardian_name'] ?? '') ?>" placeholder="Guardian Full Name" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary small">Guardian Contact No *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-telephone-fill"></i></span>
                                        <input type="tel" id="guardian_phone" name="guardian_phone" class="form-control" value="<?= htmlspecialchars($studentData['guardian_phone'] ?? '') ?>" placeholder="07X XXXXXXX" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-secondary small">Relationship *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-people"></i></span>
                                        <select id="guardian_relation" name="guardian_relation" class="form-select">
                                            <option value="Father" <?= (isset($studentData['guardian_relation']) && $studentData['guardian_relation'] == 'Father') ? 'selected' : '' ?>>Father</option>
                                            <option value="Mother" <?= (isset($studentData['guardian_relation']) && $studentData['guardian_relation'] == 'Mother') ? 'selected' : '' ?>>Mother</option>
                                            <option value="Guardian" <?= (isset($studentData['guardian_relation']) && $studentData['guardian_relation'] == 'Guardian') ? 'selected' : '' ?>>Guardian</option>
                                            <option value="Spouse" <?= (isset($studentData['guardian_relation']) && $studentData['guardian_relation'] == 'Spouse') ? 'selected' : '' ?>>Spouse</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Submit Button -->
                            <div class="mt-4">
                                <button type="submit" id="btnSubmit" class="btn btn-primary btn-custom-submit w-100 fw-bold text-white shadow-sm py-2.5">
                                    <i class="bi <?= $is_edit_mode ? 'bi-save-fill' : 'bi-person-check-fill' ?> me-2"></i> 
                                    <?= $is_edit_mode ? 'Update Student Details' : 'Confirm & Complete Registration' ?>
                                </button>
                            </div>

                        </form>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="ajax/js/student.js"></script>

    <!-- Dynamic Course ID Generator Script -->
    <script>
        $(document).ready(function() {
            function updateDynamicStudentId() {
                var selectedOption = $('#course_id option:selected');
                var code = selectedOption.data('code') || '';
                var name = selectedOption.data('name') || '';

                var prefix = 'EM'; // Default
                code = code.toUpperCase();
                name = name.toLowerCase();

                if (code.includes('SE') || name.includes('software')) {
                    prefix = 'SE';
                } else if (code.includes('CS') || name.includes('cyber')) {
                    prefix = 'CS';
                } else if (code.includes('BM') || name.includes('business')) {
                    prefix = 'BM';
                } else if (code.includes('DS') || name.includes('data science')) {
                    prefix = 'DS';
                } else if (code.includes('IT') || code.includes('ICT') || name.includes('information')) {
                    prefix = 'IT';
                }

                var currentIdVal = $('#campus_id').val();
                if (currentIdVal) {
                    var parts = currentIdVal.split('-');
                    if (parts.length === 3) {
                        var newCampusId = prefix + '-' + parts[1] + '-' + parts[2];
                        $('#campus_id').val(newCampusId);
                    }
                }
            }

            // Trigger ID change when Course dropdown changes
            $('#course_id').on('change', function() {
                updateDynamicStudentId();
            });

            // Initial check on page load if course is pre-selected
            if ($('#course_id').val()) {
                updateDynamicStudentId();
            }
        });
    </script>
</body>
</html>