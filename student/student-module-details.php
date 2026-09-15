<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db.php';

// Student Login Check
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$module_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$student_pk_id = intval($_SESSION['student_id'] ?? 0);
$swal_script = "";

if ($module_id <= 0) {
    header("Location: course.php");
    exit();
}

// 1. STUDENT ASSIGNMENT ANSWER SUBMISSION HANDLER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_assignment_answer'])) {
    $assignment_key = trim($_POST['assignment_key'] ?? '');
    
    if (isset($_FILES['answer_file']) && $_FILES['answer_file']['error'] === 0) {
        $upload_dir = "../uploads/submissions/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_ext = pathinfo($_FILES['answer_file']['name'], PATHINFO_EXTENSION);
        $new_filename = "SUB_M" . $module_id . "_S" . $student_pk_id . "_" . time() . "." . $file_ext;
        $target_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['answer_file']['tmp_name'], $target_path)) {
            // Check if student submission table exists or save directly into DB
            $conn->query("CREATE TABLE IF NOT EXISTS assignment_submissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                module_id INT NOT NULL,
                student_id INT NOT NULL,
                assignment_key VARCHAR(50) NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            // Delete old submission if re-uploading
            $stmtDel = $conn->prepare("DELETE FROM assignment_submissions WHERE module_id = ? AND student_id = ? AND assignment_key = ?");
            $stmtDel->bind_param("iis", $module_id, $student_pk_id, $assignment_key);
            $stmtDel->execute();

            // Insert New Submission
            $stmtIns = $conn->prepare("INSERT INTO assignment_submissions (module_id, student_id, assignment_key, file_path) VALUES (?, ?, ?, ?)");
            $stmtIns->bind_param("iiss", $module_id, $student_pk_id, $assignment_key, $new_filename);
            $stmtIns->execute();

            $swal_script = "Swal.fire({
                icon: 'success',
                title: 'Submitted Successfully!',
                text: 'Your assignment answer sheet has been uploaded to the instructor.',
                confirmButtonColor: '#0d6efd'
            });";
        } else {
            $swal_script = "Swal.fire({ icon: 'error', title: 'Upload Failed!', text: 'Unable to save the file to server.' });";
        }
    }
}

// 2. Fetch Selected Module Details with Teacher Info
$stmtMod = $conn->prepare("
    SELECT m.*, c.course_name, c.course_code, t.full_name AS instructor_name, t.title AS instructor_title, t.email AS instructor_email 
    FROM modules m 
    LEFT JOIN courses c ON m.course_id = c.id 
    LEFT JOIN teachers t ON m.teacher_id = t.id 
    WHERE m.id = ? 
    LIMIT 1
");
$stmtMod->bind_param("i", $module_id);
$stmtMod->execute();
$module = $stmtMod->get_result()->fetch_assoc();

if (!$module) {
    echo "<div style='padding:50px; text-align:center;'><h3>Module Not Found!</h3><a href='course.php'>Back to Courses</a></div>";
    exit();
}

$module_code = $module['module_code'] ?? 'SE 1101';
$module_name = $module['module_name'] ?? 'Subject Module';
$lecturer    = trim(($module['instructor_title'] ?? '') . ' ' . ($module['instructor_name'] ?? 'Unassigned Lecturer'));

// 3. Fetch Instructor Uploaded Materials & Results Sheet
$stmtMat = $conn->prepare("SELECT * FROM module_materials WHERE module_id = ? ORDER BY id ASC");
$stmtMat->bind_param("i", $module_id);
$stmtMat->execute();
$materialsRes = $stmtMat->get_result();

$uploadedMaterials = [];
if ($materialsRes) {
    while ($row = $materialsRes->fetch_assoc()) {
        $uploadedMaterials[$row['section_type']][] = $row;
    }
}

// 4. Fetch Student's Own Submissions
$mySubmissions = [];
$checkTable = $conn->query("SHOW TABLES LIKE 'assignment_submissions'");
if ($checkTable && $checkTable->num_rows > 0) {
    $stmtSub = $conn->prepare("SELECT * FROM assignment_submissions WHERE module_id = ? AND student_id = ?");
    $stmtSub->bind_param("ii", $module_id, $student_pk_id);
    $stmtSub->execute();
    $subRes = $stmtSub->get_result();
    while ($sRow = $subRes->fetch_assoc()) {
        $mySubmissions[$sRow['assignment_key']] = $sRow;
    }
}

// 5. Fetch Mid Exam Online MCQ Quizzes
$stmtQ = $conn->prepare("SELECT * FROM mid_exam_quizzes WHERE module_id = ? ORDER BY id ASC");
$stmtQ->bind_param("i", $module_id);
$stmtQ->execute();
$quizQuestionsRes = $stmtQ->get_result();

// 6. Fetch Latest Assigned Live Class for this Module
$stmtLiveClass = $conn->prepare("
    SELECT * FROM live_classes 
    WHERE module_id = ? OR class_title LIKE CONCAT('%', ?, '%')
    ORDER BY class_date DESC, start_time ASC 
    LIMIT 1
");
$stmtLiveClass->bind_param("is", $module_id, $module_code);
$stmtLiveClass->execute();
$liveClass = $stmtLiveClass->get_result()->fetch_assoc();
$stmtLiveClass->close();

// Pre-defined Lectures
$lectures = [
    1 => "1. Introduction & Fundamental Overview",
    2 => "2. System Architecture & Core Concepts",
    3 => "3. Operational Frameworks & Methodologies",
    4 => "4. Process Management & Implementation",
    5 => "5. Analytical Decision Making Frameworks",
    6 => "6. Practical Case Studies & Exercises",
    7 => "7. Enterprise Solutions & Industry Standards",
    8 => "8. System Integration & Optimization",
    9 => "9. Quality Assurance & Risk Management",
    10 => "10. Emerging Trends & Final Review"
];

// Pre-defined Assignments
$assignments = [
    1 => "Assignment 01 - Practical Case Study",
    2 => "Assignment 02 - Technical Documentation",
    3 => "Assignment 03 - System Implementation Task",
    4 => "Assignment 04 - Advanced Design Task",
    5 => "Assignment 05 - Final Capstone Project Brief"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($module_code) ?> - <?= htmlspecialchars($module_name) ?></title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        .hero-banner {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border-radius: 16px;
            color: white;
            padding: 30px;
        }
        .content-card {
            border: none;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }
        .pdf-icon-badge {
            width: 36px; height: 36px; 
            background-color: #ffebee; color: #d32f2f;
            border-radius: 8px; 
            display: inline-flex; align-items: center; justify-content: center;
        }
        .assign-icon-badge {
            width: 36px; height: 36px; 
            background-color: #e3f2fd; color: #0d6efd;
            border-radius: 8px; 
            display: inline-flex; align-items: center; justify-content: center;
        }
        .quiz-card {
            background-color: #f8fafc;
            border-left: 4px solid #0d6efd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
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
                
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="course.php" class="btn btn-sm btn-white border rounded-pill px-3 shadow-sm text-secondary fw-semibold extra-small">
                        <i class="bi bi-arrow-left me-1"></i> Back to Enrolled Modules
                    </a>
                </div>

                <!-- Hero Banner for Module -->
                <div class="hero-banner mb-4 shadow-sm">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <span class="badge bg-white bg-opacity-25 text-white mb-2 extra-small fw-bold px-3 py-1.5 rounded-pill">
                                <?= htmlspecialchars($module_code) ?> &bull; <?= htmlspecialchars($module['semester'] ?? 'Semester 01') ?>
                            </span>
                            <h2 class="fw-bold text-white mb-2"><?= htmlspecialchars($module_name) ?></h2>
                            <p class="text-white-50 mb-0 small">
                                Course: <strong><?= htmlspecialchars($module['course_name'] ?? 'Degree Program') ?></strong> 
                                (Code: <?= htmlspecialchars($module['course_code'] ?? 'SE-2026') ?>)
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-25 text-start">
                            <span class="text-white-50 d-block extra-small">Module Instructor:</span>
                            <strong class="text-white small d-block"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($lecturer) ?></strong>
                            <?php if(!empty($module['instructor_email'])): ?>
                                <span class="text-white-50 extra-small"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($module['instructor_email']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Main Grid Layout -->
                <div class="row g-4">
                    
                    <!-- Left Side Column -->
                    <div class="col-lg-8">
                        
                        <!-- 1. LECTURE THEORY NOTES -->
                        <div class="card content-card p-4 mb-4">
                            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>Lecture Notes & Learning Slides</h5>
                            
                            <?php foreach ($lectures as $num => $lecture_title): ?>
                                <?php $sec_key = "lecture_" . $num; ?>
                                <div class="py-2.5 border-bottom">
                                    <h6 class="fw-bold text-dark small mb-2"><?= htmlspecialchars($lecture_title) ?></h6>

                                    <?php if (isset($uploadedMaterials[$sec_key]) && count($uploadedMaterials[$sec_key]) > 0): ?>
                                        <div class="ps-2">
                                            <?php foreach ($uploadedMaterials[$sec_key] as $pdf): ?>
                                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border mb-1.5">
                                                    <div class="d-flex align-items-center gap-2.5">
                                                        <div class="pdf-icon-badge">
                                                            <i class="bi bi-file-earmark-pdf-fill fs-6"></i>
                                                        </div>
                                                        <div>
                                                            <a href="../uploads/materials/<?= htmlspecialchars($pdf['file_path']) ?>" target="_blank" class="text-primary text-decoration-none extra-small fw-bold d-block">
                                                                <?= htmlspecialchars($pdf['title']) ?>
                                                            </a>
                                                            <span class="text-muted extra-small" style="font-size: 0.7rem;">Uploaded: <?= date('M d, Y', strtotime($pdf['created_at'])) ?></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <a href="../uploads/materials/<?= htmlspecialchars($pdf['file_path']) ?>" download class="btn btn-sm btn-white border rounded-pill extra-small text-secondary py-1 px-3">
                                                        <i class="bi bi-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="ps-2">
                                            <span class="text-muted extra-small fst-italic">No materials uploaded yet for this lecture session.</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 2. ASSIGNMENTS & ANSWER SUBMISSIONS SECTION -->
                        <div class="card content-card p-4 mb-4">
                            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-check me-2 text-danger"></i>Assignments & Answer Submissions</h5>
                            
                            <?php foreach ($assignments as $num => $assign_title): ?>
                                <?php $sec_key = "assignment_" . $num; ?>
                                <div class="py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark small mb-0"><?= htmlspecialchars($assign_title) ?></h6>
                                        
                                        <!-- UPLOAD / SUBMIT ANSWER SHEET BUTTON -->
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 extra-small fw-bold" onclick="openSubmitModal('<?= $sec_key ?>', '<?= addslashes($assign_title) ?>')">
                                            <i class="bi bi-cloud-upload-fill me-1"></i> 
                                            <?= isset($mySubmissions[$sec_key]) ? 'Re-upload Answer Sheet' : 'Upload Answer Sheet' ?>
                                        </button>
                                    </div>

                                    <!-- Instructor Brief File -->
                                    <?php if (isset($uploadedMaterials[$sec_key]) && count($uploadedMaterials[$sec_key]) > 0): ?>
                                        <div class="ps-2 mb-2">
                                            <?php foreach ($uploadedMaterials[$sec_key] as $item): ?>
                                                <div class="p-2 rounded-3 bg-light border mb-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="assign-icon-badge">
                                                                <i class="bi bi-file-earmark-check-fill fs-6"></i>
                                                            </div>
                                                            <div>
                                                                <a href="../uploads/materials/<?= htmlspecialchars($item['file_path']) ?>" target="_blank" class="text-primary text-decoration-none extra-small fw-bold d-block">
                                                                    <?= htmlspecialchars($item['title']) ?> (Brief)
                                                                </a>
                                                                <span class="text-muted extra-small" style="font-size: 0.7rem;">Instructor Brief</span>
                                                            </div>
                                                        </div>
                                                        <a href="../uploads/materials/<?= htmlspecialchars($item['file_path']) ?>" download class="btn btn-sm btn-white border rounded-pill extra-small text-secondary py-1 px-2.5">
                                                            <i class="bi bi-download me-1"></i> Brief PDF
                                                        </a>
                                                    </div>

                                                    <!-- Display Start Date, Start Time, End Date, & End Time -->
                                                    <?php if(!empty($item['start_date']) || !empty($item['end_date'])): ?>
                                                        <div class="d-flex flex-wrap gap-2 mt-2 pt-2 border-top">
                                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 extra-small">
                                                                <i class="bi bi-calendar-event me-1"></i>Start: 
                                                                <?= !empty($item['start_date']) ? date('M d, Y', strtotime($item['start_date'])) : 'N/A' ?>
                                                                <?= !empty($item['start_time']) ? ' at ' . date('h:i A', strtotime($item['start_time'])) : '' ?>
                                                            </span>
                                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 extra-small">
                                                                <i class="bi bi-clock-history me-1"></i>Deadline: 
                                                                <?= !empty($item['end_date']) ? date('M d, Y', strtotime($item['end_date'])) : 'N/A' ?>
                                                                <?= !empty($item['end_time']) ? ' at ' . date('h:i A', strtotime($item['end_time'])) : '' ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Student Submitted Answer Display -->
                                    <?php if (isset($mySubmissions[$sec_key])): ?>
                                        <?php $sub = $mySubmissions[$sec_key]; ?>
                                        <div class="ps-2 mt-2">
                                            <div class="p-2.5 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                                    <div>
                                                        <span class="fw-bold text-success extra-small d-block">Submitted Answer Sheet</span>
                                                        <span class="text-muted extra-small" style="font-size: 0.68rem;">Uploaded on: <?= date('M d, Y h:i A', strtotime($sub['submitted_at'])) ?></span>
                                                    </div>
                                                </div>
                                                <a href="../uploads/submissions/<?= htmlspecialchars($sub['file_path']) ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 extra-small fw-bold">
                                                    <i class="bi bi-file-earmark-arrow-down me-1"></i> View My Solution
                                                </a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="ps-2 mt-1">
                                            <span class="text-muted extra-small fst-italic"><i class="bi bi-info-circle me-1"></i> You haven't submitted an answer sheet for this assignment yet.</span>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 3. ONLINE MID EXAMINATION MCQ QUIZ -->
                        <div class="card content-card p-4">
                            <h5 class="fw-bold text-danger mb-3"><i class="bi bi-question-circle-fill me-2"></i>Mid Examination Online Quiz</h5>
                            
                            <?php if ($quizQuestionsRes && $quizQuestionsRes->num_rows > 0): ?>
                                <?php $qNum = 1; while($q = $quizQuestionsRes->fetch_assoc()): ?>
                                    <div class="quiz-card">
                                        <div class="fw-bold text-dark small mb-2">Q<?= $qNum++ ?>: <?= htmlspecialchars($q['question']) ?></div>
                                        <div class="row g-2 extra-small text-secondary">
                                            <div class="col-6">A) <?= htmlspecialchars($q['option_a']) ?></div>
                                            <div class="col-6">B) <?= htmlspecialchars($q['option_b']) ?></div>
                                            <div class="col-6">C) <?= htmlspecialchars($q['option_c']) ?></div>
                                            <div class="col-6">D) <?= htmlspecialchars($q['option_d']) ?></div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-muted extra-small fst-italic mb-0">No online exam questions published for this module yet.</p>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Right Side Column -->
                    <div class="col-lg-4">
                        
                        <!-- MID EXAMINATION RESULTS SHEET -->
                        <div class="card content-card p-4 mb-4">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-bar-graph text-danger me-2"></i>Mid Exam Published Results</h6>
                            <p class="text-muted extra-small mb-3">Download the officially published results sheet PDF.</p>
                            
                            <?php if (isset($uploadedMaterials['mid_exam_results']) && count($uploadedMaterials['mid_exam_results']) > 0): ?>
                                <?php foreach ($uploadedMaterials['mid_exam_results'] as $resPdf): ?>
                                    <div class="p-2.5 rounded-3 bg-light border mb-2">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="pdf-icon-badge">
                                                <i class="bi bi-file-earmark-pdf-fill fs-6"></i>
                                            </div>
                                            <div>
                                                <strong class="extra-small text-dark d-block"><?= htmlspecialchars($resPdf['title']) ?></strong>
                                                <span class="text-muted extra-small" style="font-size: 0.68rem;">Published: <?= date('M d, Y', strtotime($resPdf['created_at'])) ?></span>
                                            </div>
                                        </div>
                                        <a href="../uploads/materials/<?= htmlspecialchars($resPdf['file_path']) ?>" target="_blank" class="btn btn-sm btn-danger w-100 rounded-pill extra-small fw-bold">
                                            <i class="bi bi-download me-1"></i> View / Download Grades
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-light border text-center p-3 mb-0 rounded-3">
                                    <i class="bi bi-clock-history fs-4 text-muted d-block mb-1"></i>
                                    <span class="extra-small text-muted d-block">Results not published yet.</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- LIVE VIRTUAL CLASS DYNAMIC CARD -->
                        <div class="card content-card p-4 text-center shadow-sm rounded-4 border-0">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 text-primary" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-camera-video-fill fs-3"></i>
                            </div>
                            
                            <h6 class="fw-bold text-dark mb-1">Live Virtual Class</h6>

                            <?php if (!empty($liveClass)): ?>
                                <!-- Live Class Title -->
                                <p class="fw-bold text-primary extra-small mb-2">
                                    <?= htmlspecialchars($liveClass['class_title']) ?>
                                </p>

                                <!-- Date & Time Info Badges -->
                                <div class="bg-light p-2.5 rounded-3 mb-3 text-start border">
                                    <div class="extra-small text-dark fw-semibold mb-1">
                                        <i class="bi bi-calendar-check-fill text-primary me-1.5"></i>
                                        <?= date('l, M d, Y', strtotime($liveClass['class_date'])) ?>
                                    </div>
                                    <div class="extra-small text-muted">
                                        <i class="bi bi-clock-fill text-warning me-1.5"></i>
                                        <?= date('h:i A', strtotime($liveClass['start_time'])) ?> 
                                        <?= !empty($liveClass['end_time']) ? ' - ' . date('h:i A', strtotime($liveClass['end_time'])) : '' ?>
                                    </div>
                                    <?php if (!empty($liveClass['instructor_name'])): ?>
                                        <div class="extra-small text-secondary mt-1 pt-1 border-top">
                                            <i class="bi bi-person-badge-fill text-secondary me-1.5"></i>
                                            Instructor: <?= htmlspecialchars($liveClass['instructor_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Direct Join Meeting Button -->
                                <a href="<?= htmlspecialchars($liveClass['meeting_link']) ?>" target="_blank" class="btn btn-success w-100 rounded-pill fw-bold btn-sm shadow-sm py-2">
                                    <i class="bi bi-box-arrow-up-right me-1.5"></i> Join Live Class
                                </a>

                            <?php else: ?>
                                <!-- Class එකක් Schedule කර නැති විට -->
                                <p class="text-muted extra-small mb-3">No live sessions scheduled for this module at the moment.</p>
                                <button class="btn btn-light text-muted border w-100 rounded-pill btn-sm extra-small py-2" disabled>
                                    <i class="bi bi-lock-fill me-1"></i> Pending Schedule
                                </button>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- STUDENT ASSIGNMENT UPLOAD MODAL -->
    <div class="modal fade" id="submitAnswerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-danger" id="modalAssignTitle">Upload Assignment Answer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="submit_assignment_answer" value="1">
                        <input type="hidden" name="assignment_key" id="modalAssignKey" value="">

                        <div class="alert alert-info border-0 rounded-3 extra-small mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i> Allowed file formats: <strong>PDF, ZIP, DOCX</strong> (Max size: 20MB)
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Select Answer File *</label>
                            <input type="file" name="answer_file" class="form-control rounded-3" accept=".pdf,.zip,.rar,.docx" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold">Submit Answer Sheet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function openSubmitModal(assignKey, title) {
            document.getElementById('modalAssignKey').value = assignKey;
            document.getElementById('modalAssignTitle').innerText = 'Upload: ' + title;
            var modal = new bootstrap.Modal(document.getElementById('submitAnswerModal'));
            modal.show();
        }

        <?php if (!empty($swal_script)): ?>
            <?= $swal_script ?>
        <?php endif; ?>
    </script>
</body>
</html>