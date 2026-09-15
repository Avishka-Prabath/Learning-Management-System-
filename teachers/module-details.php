<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db.php';

// Teacher Login Check
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$module_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$swal_script = ""; 

// Ensure Database Columns Exist for Start/End Date & Time
$conn->query("ALTER TABLE module_materials ADD COLUMN IF NOT EXISTS start_date DATE NULL");
$conn->query("ALTER TABLE module_materials ADD COLUMN IF NOT EXISTS start_time TIME NULL");
$conn->query("ALTER TABLE module_materials ADD COLUMN IF NOT EXISTS end_date DATE NULL");
$conn->query("ALTER TABLE module_materials ADD COLUMN IF NOT EXISTS end_time TIME NULL");

// 1. Delete Material Handler (PDFs / Assignments / Results)
if (isset($_GET['delete_material'])) {
    $mat_id = intval($_GET['delete_material']);
    
    // Fetch file path to delete actual file from storage
    $stmtFile = $conn->prepare("SELECT file_path FROM module_materials WHERE id = ? AND module_id = ?");
    $stmtFile->bind_param("ii", $mat_id, $module_id);
    $stmtFile->execute();
    $fileData = $stmtFile->get_result()->fetch_assoc();

    if ($fileData) {
        $file_to_delete = "../uploads/materials/" . $fileData['file_path'];
        if (file_exists($file_to_delete)) {
            @unlink($file_to_delete);
        }
        
        $stmtDel = $conn->prepare("DELETE FROM module_materials WHERE id = ?");
        $stmtDel->bind_param("i", $mat_id);
        $stmtDel->execute();

        $swal_script = "Swal.fire({
            icon: 'success',
            title: 'Deleted Successfully!',
            text: 'The material has been removed from the module.',
            confirmButtonColor: '#0d6efd'
        });";
    }
}

// 2. Delete Quiz Question Handler
if (isset($_GET['delete_quiz'])) {
    $quiz_id = intval($_GET['delete_quiz']);
    $stmtDelQ = $conn->prepare("DELETE FROM mid_exam_quizzes WHERE id = ? AND module_id = ?");
    $stmtDelQ->bind_param("ii", $quiz_id, $module_id);
    $stmtDelQ->execute();

    $swal_script = "Swal.fire({
        icon: 'success',
        title: 'Question Deleted!',
        text: 'Quiz question has been removed.',
        confirmButtonColor: '#0d6efd'
    });";
}

// 3. Generic Material / Assignment PDF Upload Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_material_pdf'])) {
    $sec_type   = trim($_POST['sec_type']);
    $title      = trim($_POST['title']);
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : NULL;
    $start_time = !empty($_POST['start_time']) ? $_POST['start_time'] : NULL;
    $end_date   = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;
    $end_time   = !empty($_POST['end_time']) ? $_POST['end_time'] : NULL;
    
    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
        $upload_dir = "../uploads/materials/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . $sec_type . '_' . basename($_FILES['material_file']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['material_file']['tmp_name'], $target_file)) {
            $stmtIns = $conn->prepare("INSERT INTO module_materials (module_id, title, section_type, file_path, start_date, start_time, end_date, end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtIns->bind_param("isssssss", $module_id, $title, $sec_type, $file_name, $start_date, $start_time, $end_date, $end_time);
            $stmtIns->execute();
            
            $swal_script = "Swal.fire({
                icon: 'success',
                title: 'Uploaded Successfully!',
                text: 'Resource file has been saved to the module.',
                confirmButtonColor: '#0d6efd'
            });";
        } else {
            $swal_script = "Swal.fire({
                icon: 'error',
                title: 'Upload Failed!',
                text: 'Failed to upload the requested file to the server.',
                confirmButtonColor: '#d33'
            });";
        }
    }
}

// 4. Single Inline Quiz Question Add Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_quiz_question'])) {
    $question = trim($_POST['question']);
    $opt_a = trim($_POST['option_a']);
    $opt_b = trim($_POST['option_b']);
    $opt_c = trim($_POST['option_c']);
    $opt_d = trim($_POST['option_d']);
    $correct_opt = trim($_POST['correct_option']);

    if (!empty($question) && !empty($opt_a) && !empty($opt_b)) {
        $stmtQuiz = $conn->prepare("INSERT INTO mid_exam_quizzes (module_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtQuiz->bind_param("issssss", $module_id, $question, $opt_a, $opt_b, $opt_c, $opt_d, $correct_opt);
        $stmtQuiz->execute();
        
        $swal_script = "Swal.fire({
            icon: 'success',
            title: 'MCQ Added!',
            text: 'Quiz question has been added successfully.',
            confirmButtonColor: '#0d6efd'
        });";
    }
}

// 5. BULK CSV MCQ UPLOAD HANDLER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_csv_mcq'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        fgetcsv($handle); // Skip Header
        
        $insertedCount = 0;
        $stmtQuiz = $conn->prepare("INSERT INTO mid_exam_quizzes (module_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (!empty($data[0])) {
                $q = trim($data[0]);
                $a = trim($data[1]);
                $b = trim($data[2]);
                $c = trim($data[3] ?? '');
                $d = trim($data[4] ?? '');
                $correct = strtoupper(trim($data[5] ?? 'A'));

                $stmtQuiz->bind_param("issssss", $module_id, $q, $a, $b, $c, $d, $correct);
                $stmtQuiz->execute();
                $insertedCount++;
            }
        }
        fclose($handle);
        
        $swal_script = "Swal.fire({
            icon: 'success',
            title: 'Bulk Import Successful!',
            text: 'Successfully imported {$insertedCount} MCQ questions into Mid-Exam.',
            confirmButtonColor: '#198754'
        });";
    }
}

// 6. Fetch Selected Module Info
$stmtMod = $conn->prepare("SELECT m.*, c.course_name, t.full_name AS instructor_name, t.title AS instructor_title 
                          FROM modules m 
                          LEFT JOIN courses c ON m.course_id = c.id 
                          LEFT JOIN teachers t ON m.teacher_id = t.id 
                          WHERE m.id = ?");
$stmtMod->bind_param("i", $module_id);
$stmtMod->execute();
$moduleData = $stmtMod->get_result()->fetch_assoc();

if (!$moduleData) {
    header("Location: modules.php");
    exit();
}

// 7. Fetch All Materials & Results
$stmtMat = $conn->prepare("SELECT * FROM module_materials WHERE module_id = ? ORDER BY id DESC");
$stmtMat->bind_param("i", $module_id);
$stmtMat->execute();
$materialsRes = $stmtMat->get_result();

$uploadedMaterials = [];
if ($materialsRes) {
    while ($row = $materialsRes->fetch_assoc()) {
        $uploadedMaterials[$row['section_type']][] = $row;
    }
}

// 8. Fetch Mid Exam Quiz Questions
$stmtQ = $conn->prepare("SELECT * FROM mid_exam_quizzes WHERE module_id = ? ORDER BY id ASC");
$stmtQ->bind_param("i", $module_id);
$stmtQ->execute();
$quizQuestionsRes = $stmtQ->get_result();

// Pre-defined 10 Lectures Outline
$lectures = [
    1 => "1. Introduction to " . ($moduleData['module_name'] ?? 'Subject Overview'),
    2 => "2. Fundamental Concepts & System Architecture",
    3 => "3. Core Principles & Operational Procedures",
    4 => "4. Intermediate Concepts & Process Management",
    5 => "5. Advanced Analytical Frameworks",
    6 => "6. Practical Case Studies & Implementation",
    7 => "7. Enterprise Solutions & Industry Standards",
    8 => "8. System Integration & Optimization",
    9 => "9. Quality Assurance & Risk Management",
    10 => "10. Emerging Trends & Capstone Summary"
];

// Pre-defined 5 Assignment Slots
$assignments = [
    1 => "Assignment 01 - Coursework Brief & Guidelines",
    2 => "Assignment 02 - Technical Documentation & Implementation",
    3 => "Assignment 03 - Practical Task & Case Study",
    4 => "Assignment 04 - Advanced System Design Task",
    5 => "Assignment 05 - Final Capstone Project Submission"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($moduleData['module_name']) ?> - Teacher Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        body { background-color: #ffffff !important; color: #1a202c; }
        .lms-breadcrumb { font-size: 0.85rem; color: #6c757d; }
        .lms-breadcrumb a { color: #6c757d; text-decoration: none; }
        .lesson-card { 
            border: none; 
            border-bottom: 1px solid #f1f3f5; 
            padding-bottom: 1.5rem; 
            margin-bottom: 1.5rem; 
        }
        .pdf-icon-badge {
            width: 32px; height: 32px; 
            background-color: #ffebee; color: #d32f2f;
            border-radius: 6px; 
            display: inline-flex; align-items: center; justify-content: center;
        }
        .assign-icon-badge {
            width: 32px; height: 32px; 
            background-color: #e3f2fd; color: #0d6efd;
            border-radius: 6px; 
            display: inline-flex; align-items: center; justify-content: center;
        }
        .quiz-card {
            background-color: #f8fafc;
            border-left: 4px solid #0d6efd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
        }
        .extra-small { font-size: 0.75rem; }
    </style>
</head>
<body>

    <div class="d-flex">
        <?php include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100 p-4 p-md-5">
            
            <!-- Breadcrumb Navigation & Actions -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="lms-breadcrumb mb-0">
                    <a href="dashboard.php">Dashboard</a> / 
                    <a href="modules.php">My courses</a> / 
                    <span class="text-dark fw-semibold"><?= htmlspecialchars($moduleData['module_code']) ?></span>
                </div>

                <a href="modules.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold extra-small">
                    <i class="bi bi-arrow-left me-1"></i> Back to Modules
                </a>
            </div>

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($moduleData['module_name']) ?></h2>
                    <p class="text-muted small mb-0">
                        Degree: <strong><?= htmlspecialchars($moduleData['course_name'] ?? 'Program') ?></strong> 
                        <span class="mx-2">•</span> Lecturer: <strong><?= htmlspecialchars($_SESSION['teacher_name'] ?? 'Lecturer') ?></strong>
                    </p>
                </div>
            </div>

            <!-- Section 1: Announcements -->
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-megaphone-fill text-warning fs-5"></i>
                    <a href="#" class="text-primary text-decoration-none fw-semibold">Announcements</a>
                </div>
            </div>

            <!-- Section 2: MID EXAMINATION (INLINE QUIZ & BULK CSV UPLOAD) -->
            <div class="mb-5 p-4 rounded-4 bg-light border">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="fw-bold text-danger mb-0">
                            <i class="bi bi-journal-text me-2"></i>Mid Examination & MCQ Quizzes
                        </h4>
                        <span class="text-muted extra-small">Manage inline MCQs or bulk import 30+ questions via Excel CSV</span>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <!-- Bulk CSV Upload Modal Trigger -->
                        <button class="btn btn-success btn-sm rounded-pill px-3 fw-semibold extra-small" data-bs-toggle="modal" data-bs-target="#bulkCsvModal">
                            <i class="bi bi-file-earmark-excel-fill me-1"></i> Bulk Import CSV (30 MCQs)
                        </button>

                        <!-- Single Question Modal Trigger -->
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold extra-small" data-bs-toggle="modal" data-bs-target="#addQuizModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Single MCQ
                        </button>
                    </div>
                </div>

                <!-- Display Quiz Questions -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-question-circle me-1 text-primary"></i> Active Quiz Questions (Total: <?= $quizQuestionsRes ? $quizQuestionsRes->num_rows : 0 ?>):
                    </h6>
                    
                    <?php if ($quizQuestionsRes && $quizQuestionsRes->num_rows > 0): ?>
                        <?php $qNum = 1; while($q = $quizQuestionsRes->fetch_assoc()): ?>
                            <div class="quiz-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="fw-bold text-dark">Q<?= $qNum++ ?>: <?= htmlspecialchars($q['question']) ?></div>
                                    
                                    <!-- DELETE QUIZ QUESTION BUTTON -->
                                    <button class="btn btn-outline-danger btn-xs py-0.5 px-2 rounded-pill extra-small" 
                                            onclick="confirmDeleteQuiz(<?= $q['id'] ?>, <?= $module_id ?>)">
                                        <i class="bi bi-trash3-fill me-1"></i> Delete
                                    </button>
                                </div>
                                <div class="row g-2 extra-small text-secondary">
                                    <div class="col-6">A) <?= htmlspecialchars($q['option_a']) ?> <?= $q['correct_option'] == 'A' ? ' <span class="badge bg-success">Correct</span>' : '' ?></div>
                                    <div class="col-6">B) <?= htmlspecialchars($q['option_b']) ?> <?= $q['correct_option'] == 'B' ? ' <span class="badge bg-success">Correct</span>' : '' ?></div>
                                    <div class="col-6">C) <?= htmlspecialchars($q['option_c']) ?> <?= $q['correct_option'] == 'C' ? ' <span class="badge bg-success">Correct</span>' : '' ?></div>
                                    <div class="col-6">D) <?= htmlspecialchars($q['option_d']) ?> <?= $q['correct_option'] == 'D' ? ' <span class="badge bg-success">Correct</span>' : '' ?></div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-muted extra-small fst-italic mb-3 ps-1">
                            No online quiz questions added yet. Use "Bulk Import CSV" or "Add Single MCQ" button above.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mid Exam Results Upload Section -->
                <div class="pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-bar-graph text-danger me-1"></i> Mid Examination Results Sheet</h6>
                            <span class="text-muted extra-small">Upload or View Published Grades PDF</span>
                        </div>
                        
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-3 extra-small fw-semibold" 
                                onclick="openUploadModal('mid_exam_results', 'Upload Mid Exam Results Sheet', 'Mid Examination Results Sheet', false)">
                            <i class="bi bi-upload me-1"></i> Upload Result Sheet (PDF)
                        </button>
                    </div>

                    <?php if (isset($uploadedMaterials['mid_exam_results']) && count($uploadedMaterials['mid_exam_results']) > 0): ?>
                        <div class="mt-3">
                            <?php foreach ($uploadedMaterials['mid_exam_results'] as $resPdf): ?>
                                <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-white border mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="pdf-icon-badge">
                                            <i class="bi bi-file-earmark-pdf-fill fs-6"></i>
                                        </div>
                                        <div>
                                            <a href="../uploads/materials/<?= htmlspecialchars($resPdf['file_path']) ?>" target="_blank" class="text-danger text-decoration-none extra-small fw-bold d-block">
                                                <?= htmlspecialchars($resPdf['title']) ?>
                                            </a>
                                            <span class="text-muted extra-small" style="font-size: 0.72rem;">Published on: <?= date('M d, Y', strtotime($resPdf['created_at'])) ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="../uploads/materials/<?= htmlspecialchars($resPdf['file_path']) ?>" download class="btn btn-sm btn-light border rounded-pill extra-small text-secondary py-1 px-2.5">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                        <!-- DELETE RESULT BUTTON -->
                                        <button class="btn btn-sm btn-outline-danger rounded-pill extra-small py-1 px-2.5" 
                                                onclick="confirmDeleteMaterial(<?= $resPdf['id'] ?>, <?= $module_id ?>)">
                                            <i class="bi bi-trash3-fill me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Section 3: Lecture Outline (Lectures 01 - 10) -->
            <div class="mb-5">
                <h4 class="fw-bold text-primary mb-4">Theory</h4>

                <?php foreach ($lectures as $num => $lecture_title): ?>
                    <?php $sec_key = "lecture_" . $num; ?>
                    <div class="lesson-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="fw-semibold text-dark mb-0"><?= htmlspecialchars($lecture_title) ?></h5>
                            
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 extra-small fw-semibold" 
                                    onclick="openUploadModal('<?= $sec_key ?>', 'Upload PDF for Lecture <?= sprintf('%02d', $num) ?>', '<?= addslashes($lecture_title) ?> Slides', false)">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload PDF Note
                            </button>
                        </div>

                        <?php if (isset($uploadedMaterials[$sec_key]) && count($uploadedMaterials[$sec_key]) > 0): ?>
                            <div class="mt-3 ps-2">
                                <?php foreach ($uploadedMaterials[$sec_key] as $pdf): ?>
                                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light border mb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="pdf-icon-badge">
                                                <i class="bi bi-file-earmark-pdf-fill fs-6"></i>
                                            </div>
                                            <div>
                                                <a href="../uploads/materials/<?= htmlspecialchars($pdf['file_path']) ?>" target="_blank" class="text-primary text-decoration-none extra-small fw-semibold d-block">
                                                    <?= htmlspecialchars($pdf['title']) ?>
                                                </a>
                                                <span class="text-muted extra-small" style="font-size: 0.72rem;">Uploaded on: <?= date('M d, Y', strtotime($pdf['created_at'])) ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="../uploads/materials/<?= htmlspecialchars($pdf['file_path']) ?>" download class="btn btn-sm btn-white border rounded-pill extra-small text-secondary py-1 px-2.5">
                                                <i class="bi bi-download me-1"></i> Download
                                            </a>
                                            <!-- DELETE LECTURE NOTE BUTTON -->
                                            <button class="btn btn-sm btn-outline-danger rounded-pill extra-small py-1 px-2.5" 
                                                    onclick="confirmDeleteMaterial(<?= $pdf['id'] ?>, <?= $module_id ?>)">
                                                <i class="bi bi-trash3-fill me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="ps-2 mt-2">
                                <span class="text-muted extra-small fst-italic">
                                    <i class="bi bi-info-circle me-1"></i> No PDF uploaded yet for Lecture <?= sprintf("%02d", $num) ?>.
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Section 4: 5 Assignment Slots -->
            <div class="mb-4">
                <h4 class="fw-bold text-primary mb-4">Assignments & Courseworks</h4>

                <?php foreach ($assignments as $num => $assign_title): ?>
                    <?php $sec_key = "assignment_" . $num; ?>
                    <div class="lesson-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="fw-semibold text-dark mb-0">
                                <i class="bi bi-journal-check text-primary me-2"></i>
                                <?= htmlspecialchars($assign_title) ?>
                            </h5>
                            
                            <button class="btn btn-outline-success btn-sm rounded-pill px-3 extra-small fw-semibold" 
                                    onclick="openUploadModal('<?= $sec_key ?>', 'Upload Brief for Assignment <?= sprintf('%02d', $num) ?>', '<?= addslashes($assign_title) ?> Brief', true)">
                                <i class="bi bi-upload me-1"></i> Upload Assignment Brief
                            </button>
                        </div>

                        <?php if (isset($uploadedMaterials[$sec_key]) && count($uploadedMaterials[$sec_key]) > 0): ?>
                            <div class="mt-3 ps-2">
                                <?php foreach ($uploadedMaterials[$sec_key] as $item): ?>
                                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light border mb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="assign-icon-badge">
                                                <i class="bi bi-file-earmark-check-fill fs-6"></i>
                                            </div>
                                            <div>
                                                <a href="../uploads/materials/<?= htmlspecialchars($item['file_path']) ?>" target="_blank" class="text-primary text-decoration-none extra-small fw-semibold d-block">
                                                    <?= htmlspecialchars($item['title']) ?>
                                                </a>
                                                
                                                <!-- Start Date, End Date, and Time Display -->
                                                <?php if(!empty($item['start_date']) || !empty($item['end_date'])): ?>
                                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                                        <span class="badge bg-primary bg-opacity-10 text-primary extra-small">
                                                            <i class="bi bi-calendar-event me-1"></i>Start: 
                                                            <?= !empty($item['start_date']) ? date('M d, Y', strtotime($item['start_date'])) : 'N/A' ?>
                                                            <?= !empty($item['start_time']) ? ' at ' . date('h:i A', strtotime($item['start_time'])) : '' ?>
                                                        </span>
                                                        <span class="badge bg-danger bg-opacity-10 text-danger extra-small">
                                                            <i class="bi bi-clock-history me-1"></i>Deadline: 
                                                            <?= !empty($item['end_date']) ? date('M d, Y', strtotime($item['end_date'])) : 'N/A' ?>
                                                            <?= !empty($item['end_time']) ? ' at ' . date('h:i A', strtotime($item['end_time'])) : '' ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted extra-small" style="font-size: 0.72rem;">Uploaded: <?= date('M d, Y', strtotime($item['created_at'])) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="../uploads/materials/<?= htmlspecialchars($item['file_path']) ?>" download class="btn btn-sm btn-white border rounded-pill extra-small text-secondary py-1 px-2.5">
                                                <i class="bi bi-download me-1"></i> Download
                                            </a>
                                            <!-- DELETE ASSIGNMENT BRIEF BUTTON -->
                                            <button class="btn btn-sm btn-outline-danger rounded-pill extra-small py-1 px-2.5" 
                                                    onclick="confirmDeleteMaterial(<?= $item['id'] ?>, <?= $module_id ?>)">
                                                <i class="bi bi-trash3-fill me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="ps-2 mt-2">
                                <span class="text-muted extra-small fst-italic">
                                    <i class="bi bi-info-circle me-1"></i> No brief uploaded yet for Assignment <?= sprintf("%02d", $num) ?>.
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <!-- 1. BULK CSV UPLOAD MODAL -->
    <div class="modal fade" id="bulkCsvModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-success">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Bulk Import MCQs via CSV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="upload_csv_mcq" value="1">
                        
                        <div class="alert alert-info border-0 rounded-3 extra-small mb-3">
                            <strong>CSV Columns Format:</strong><br>
                            <code>Question, Option A, Option B, Option C, Option D, Correct Option (A/B/C/D)</code>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Select CSV File *</label>
                            <input type="file" name="csv_file" class="form-control rounded-3" accept=".csv" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 fw-semibold">Import 30 MCQs</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. SINGLE QUIZ QUESTION MODAL -->
    <div class="modal fade" id="addQuizModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-question-circle me-1"></i> Add Single MCQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="add_quiz_question" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Question *</label>
                            <textarea name="question" class="form-control rounded-3" rows="2" placeholder="e.g. Which of the following is an OS process state?" required></textarea>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-secondary">Option A *</label>
                                <input type="text" name="option_a" class="form-control rounded-3" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-secondary">Option B *</label>
                                <input type="text" name="option_b" class="form-control rounded-3" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-secondary">Option C</label>
                                <input type="text" name="option_c" class="form-control rounded-3">
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-secondary">Option D</label>
                                <input type="text" name="option_d" class="form-control rounded-3">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Correct Answer Option *</label>
                            <select name="correct_option" class="form-select rounded-3" required>
                                <option value="A">Option A</option>
                                <option value="B">Option B</option>
                                <option value="C">Option C</option>
                                <option value="D">Option D</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-semibold">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. GENERIC PDF UPLOAD MODAL (WITH DATE & TIME INPUTS) -->
    <div class="modal fade" id="uploadMaterialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="modalSecTitle">Upload Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="upload_material_pdf" value="1">
                        <input type="hidden" name="sec_type" id="modalSecType" value="lecture_1">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Document Title *</label>
                            <input type="text" name="title" id="modalTitleInput" class="form-control rounded-3" required>
                        </div>

                        <!-- Date and Time Input Fields (Only Displayed for Assignments) -->
                        <div id="dateTimeFieldsSection" style="display: none;" class="mb-3">
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label extra-small fw-bold text-secondary">Start Date</label>
                                    <input type="date" name="start_date" class="form-control rounded-3 extra-small" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label extra-small fw-bold text-secondary">Start Time</label>
                                    <input type="time" name="start_time" class="form-control rounded-3 extra-small" value="08:00">
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label extra-small fw-bold text-secondary">Deadline (End Date)</label>
                                    <input type="date" name="end_date" class="form-control rounded-3 extra-small" value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label extra-small fw-bold text-secondary">Deadline Time</label>
                                    <input type="time" name="end_time" class="form-control rounded-3 extra-small" value="23:59">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Select PDF / Resource File *</label>
                            <input type="file" name="material_file" class="form-control rounded-3" accept=".pdf,.ppt,.pptx,.docx,.zip" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">Upload File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function openUploadModal(secType, modalTitle, defaultDocTitle, isAssignment = false) {
            document.getElementById('modalSecType').value = secType;
            document.getElementById('modalSecTitle').innerText = modalTitle;
            document.getElementById('modalTitleInput').value = defaultDocTitle;
            
            const dateTimeSec = document.getElementById('dateTimeFieldsSection');
            if (isAssignment) {
                dateTimeSec.style.display = 'block';
            } else {
                dateTimeSec.style.display = 'none';
            }
            
            var uploadModal = new bootstrap.Modal(document.getElementById('uploadMaterialModal'));
            uploadModal.show();
        }

        // Delete Material Confirmation
        function confirmDeleteMaterial(matId, modId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This uploaded file will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'module-details.php?id=' + modId + '&delete_material=' + matId;
                }
            });
        }

        // Delete Quiz Question Confirmation
        function confirmDeleteQuiz(quizId, modId) {
            Swal.fire({
                title: 'Delete Question?',
                text: "This MCQ question will be removed from the quiz!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'module-details.php?id=' + modId + '&delete_quiz=' + quizId;
                }
            });
        }

        // Trigger SweetAlert popup after server actions
        <?php if (!empty($swal_script)): ?>
            <?= $swal_script ?>
        <?php endif; ?>
    </script>
</body>
</html>