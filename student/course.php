<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db.php';

// 1. Check Student Login Session
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$student_pk_id = intval($_SESSION['student_id'] ?? 0);

// 2. Logged-in Student Details Fetch කරගැනීම
$stmtStud = $conn->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmtStud->bind_param("i", $student_pk_id);
$stmtStud->execute();
$studentData = $stmtStud->get_result()->fetch_assoc();

$student_campus_id  = trim($studentData['student_id'] ?? ''); // e.g. SE-2026-0001
$student_course_ref = trim($studentData['course_id'] ?? '');

// Campus ID Prefix Extract කරගැනීම (SE, CS, BM, DS, ICT)
$prefix = 'SE'; // Default
if (!empty($student_campus_id)) {
    $parts = explode('-', $student_campus_id);
    $prefix = strtoupper($parts[0] ?? 'SE');
}

// 3. Prefix එකට අදාළ Degree Course එක Courses Table එකෙන් Fetch කිරීම
$stmtCourse = $conn->prepare("
    SELECT c.*, t.full_name AS instructor_name, t.title AS instructor_title 
    FROM courses c 
    LEFT JOIN teachers t ON c.teacher_id = t.id 
    WHERE c.id = ? 
       OR LOWER(c.course_code) = LOWER(?) 
       OR LOWER(c.course_code) LIKE CONCAT(LOWER(?), '%') 
       OR LOWER(c.course_name) LIKE CONCAT('%', LOWER(?), '%') 
    LIMIT 1
");
$stmtCourse->bind_param("ssss", $student_course_ref, $student_course_ref, $prefix, $student_course_ref);
$stmtCourse->execute();
$selectedCourse = $stmtCourse->get_result()->fetch_assoc();

// Dynamic Course Values Set කිරීම
$course_id           = intval($selectedCourse['id'] ?? 3); // SE Default Course ID = 3
$display_course_name = $selectedCourse['course_name'] ?? 'BSc (Hons) in Software Engineering';
$display_course_code = $selectedCourse['course_code'] ?? ($prefix . '-2026');

// 4. SEED / COMPLETE ALL SOFTWARE ENGINEERING MODULES (21 SUBJECTS TOTAL)
if ($prefix === 'SE') {
    $fullSeSubjects = [
        // Year 01
        ['SE 1101', 'Object Oriented Programming with Java', 'Semester 01', '#0d6efd'],
        ['SE 1102', 'Web Development Fundamentals', 'Semester 01', '#0284c7'],
        ['SE 1103', 'Fundamentals of Software Engineering', 'Semester 01', '#059669'],
        ['SE 1201', 'Database Management Systems', 'Semester 02', '#d97706'],
        ['SE 1202', 'Data Structures & Algorithms', 'Semester 02', '#7c3aed'],
        ['SE 1203', 'Mathematics for Computing', 'Semester 02', '#dc2626'],
        // Year 02
        ['SE 2101', 'Software Architecture & Design Patterns', 'Semester 03', '#0d6efd'],
        ['SE 2102', 'Advanced Web Application Development', 'Semester 03', '#0284c7'],
        ['SE 2103', 'Agile Software Development Methodologies', 'Semester 03', '#059669'],
        ['SE 2201', 'Software Testing & Quality Assurance', 'Semester 04', '#d97706'],
        ['SE 2202', 'Operating Systems & System Programming', 'Semester 04', '#7c3aed'],
        ['SE 2203', 'UI/UX Design & Human-Computer Interaction', 'Semester 04', '#dc2626'],
        // Year 03
        ['SE 3101', 'Cloud Computing & DevOps', 'Semester 05', '#0284c7'],
        ['SE 3102', 'Mobile Application Development', 'Semester 05', '#059669'],
        ['SE 3103', 'Software Metrics & Project Management', 'Semester 05', '#d97706'],
        ['SE 3201', 'Enterprise Application Architecture', 'Semester 06', '#7c3aed'],
        ['SE 3202', 'Microservices & Distributed Systems', 'Semester 06', '#dc2626'],
        ['SE 3203', 'Software Security & Secure Coding', 'Semester 06', '#0d6efd'],
        // Year 04
        ['SE 4101', 'Artificial Intelligence in Software Engineering', 'Semester 07', '#059669'],
        ['SE 4102', 'Big Data Engineering & Analytics', 'Semester 07', '#d97706'],
        ['SE 4201', 'Final Year Software Engineering Research & Capstone Project', 'Semester 08', '#0d6efd']
    ];

    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM modules WHERE course_id = ? AND module_code = ?");
    $insStmt = $conn->prepare("INSERT INTO modules (course_id, module_code, module_name, semester, teacher_id, completion_pct, banner_color) VALUES (?, ?, ?, ?, NULL, 0, ?)");

    foreach ($fullSeSubjects as $sub) {
        $checkStmt->bind_param("is", $course_id, $sub[0]);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->fetch_row()[0];

        if ($exists == 0) {
            $insStmt->bind_param("issss", $course_id, $sub[0], $sub[1], $sub[2], $sub[3]);
            $insStmt->execute();
        }
    }
    $checkStmt->close();
    $insStmt->close();
}

// 5. Fetch Student's Course Modules
$stmtMod = $conn->prepare("
    SELECT m.*, t.full_name AS instructor_name, t.title AS instructor_title 
    FROM modules m 
    LEFT JOIN teachers t ON m.teacher_id = t.id 
    WHERE m.course_id = ? OR LOWER(m.course_id) = LOWER(?) OR LOWER(m.course_id) LIKE CONCAT(LOWER(?), '%') 
    ORDER BY m.semester ASC, m.id ASC
");
$stmtMod->bind_param("iss", $course_id, $display_course_code, $prefix);
$stmtMod->execute();
$modulesRes = $stmtMod->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($display_course_name) ?> - Student Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        .module-card {
            border: none !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04) !important;
        }
        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
        }
        .module-banner {
            height: 115px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            position: relative;
        }
        .extra-small { font-size: 0.75rem; }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar Navigation -->
        <?php include('navbar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <!-- Topbar Navigation -->
            <?php include('topbar.php'); ?>

            <div class="content-area p-4">
                
                <!-- Page Header -->
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <a href="home.php" class="btn btn-white btn-sm rounded-pill mb-2 px-3 border shadow-sm fw-semibold text-secondary extra-small">
                            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                        </a>
                        <h3 class="fw-bold text-dark mb-1"><?= htmlspecialchars($display_course_name) ?></h3>
                        <p class="text-secondary small mb-0">
                            Course Code: <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-2 ms-1 fw-bold"><?= htmlspecialchars($display_course_code) ?></span> 
                            <span class="mx-2">•</span> Total Curriculum Modules: <strong class="text-dark"><?= $modulesRes ? $modulesRes->num_rows : 0 ?> Subjects</strong>
                        </p>
                    </div>
                </div>

                <!-- Saegis LMS Style Grid Cards View -->
                <div class="row g-4">
                    <?php if ($modulesRes && $modulesRes->num_rows > 0): ?>
                        <?php while ($mod = $modulesRes->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card module-card h-100 overflow-hidden">
                                    
                                    <!-- LMS Banner Header -->
                                    <div class="module-banner p-3 text-center" style="background-color: <?= $mod['banner_color'] ?? '#0066ff' ?>;">
                                        <div class="text-center px-2">
                                            <span class="badge bg-white bg-opacity-25 text-white mb-1 extra-small"><?= htmlspecialchars($mod['module_code']) ?></span>
                                            <h6 class="fw-bold mb-0 text-white text-truncate" title="<?= htmlspecialchars($mod['module_name']) ?>">
                                                <?= htmlspecialchars($mod['module_name']) ?>
                                            </h6>
                                        </div>
                                    </div>

                                    <!-- Card Content Body -->
                                    <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="text-muted extra-small fw-bold text-uppercase"><?= htmlspecialchars($mod['semester'] ?? 'Semester 01') ?></span>
                                                <span class="text-muted"><i class="bi bi-gear-fill"></i></span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-3" style="min-height: 42px; font-size: 0.95rem;">
                                                <?= htmlspecialchars($mod['module_name']) ?>
                                            </h6>
                                            
                                            <p class="text-secondary extra-small mb-1">
                                                <i class="bi bi-person-badge text-primary me-1"></i> 
                                                Lecturer: <strong class="text-dark"><?= htmlspecialchars(($mod['instructor_title'] ?? '') . ' ' . ($mod['instructor_name'] ?? 'Unassigned')) ?></strong>
                                            </p>
                                        </div>

                                        <!-- Progress Bar & Actions -->
                                        <div class="pt-3 border-top mt-3">
                                            <div class="d-flex justify-content-between extra-small fw-semibold mb-1">
                                                <span class="text-muted">Completion</span>
                                                <span class="text-primary"><?= $mod['completion_pct'] ?? 0 ?>% complete</span>
                                            </div>
                                            <div class="progress mb-3" style="height: 5px;">
                                                <div class="progress-bar bg-primary" style="width: <?= $mod['completion_pct'] ?? 0 ?>%"></div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center pt-1">
                                                <span class="extra-small text-muted">Module ID: #<?= $mod['id'] ?></span>
                                                
                                                <!-- DIRECT LINK TO STUDENT MODULE DETAILS -->
                                                <a href="student-module-details.php?id=<?= $mod['id'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 extra-small fw-semibold shadow-sm">
                                                    <i class="bi bi-journal-text me-1"></i> View Content
                                                </a>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>