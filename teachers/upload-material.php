<?php
session_start();

// Redirect to login.php if the teacher is not logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../db.php';

// Get the logged-in lecturer's ID
$current_teacher_id = intval($_SESSION['teacher_id'] ?? 0);

// Fetch the module assigned to the instructor
$assigned_module_id = 0;
$assigned_module_name = 'No Module Assigned';

$modQuery = "SELECT c.id, c.course_code, c.course_name 
             FROM courses c 
             WHERE c.teacher_id = ? 
             LIMIT 1";

$stmtMod = $conn->prepare($modQuery);
$stmtMod->bind_param("i", $current_teacher_id);
$stmtMod->execute();
$modRes = $stmtMod->get_result();

if ($modRes && $rowMod = $modRes->fetch_assoc()) {
    $assigned_module_id = $rowMod['id'];
    $assigned_module_name = ($rowMod['course_code'] ? $rowMod['course_code'] . ' - ' : '') . $rowMod['course_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Lecture Resource - Teacher Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar -->
        <?php include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <!-- Topbar -->
            <?php include('topbar.php'); ?>

            <div class="p-4">
                
                <!-- Page Header -->
                <div class="mb-4">
                    <a href="modules.php" class="btn btn-light btn-sm rounded-pill px-3 text-muted fw-semibold mb-3">
                        <i class="bi bi-arrow-left me-1"></i> Back to Modules
                    </a>
                    <h4 class="fw-bold text-dark mb-1">Upload Lecture Resource</h4>
                    <p class="text-muted small mb-0">Add study materials, slides, or links for your assigned module.</p>
                </div>

                <!-- Main Form Card -->
                <div class="row">
                    <div class="col-lg-8 col-xl-7">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                            
                            <!-- Form with Auto-Filled Course/Module -->
                            <form id="uploadMaterialForm" enctype="multipart/form-data" novalidate>
                                
                                <!-- Auto-Filled Assigned Module (Readonly Input & Hidden Value for Backend) -->
                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Assigned Module / Subject</label>
                                    <input type="hidden" id="select_course" name="course_id" value="<?= $assigned_module_id ?>">
                                    <input type="text" class="form-control rounded-3 bg-light fw-bold text-primary" value="<?= htmlspecialchars($assigned_module_name) ?>" readonly>
                                </div>

                                <!-- Material Title -->
                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Lecture Title / Topic *</label>
                                    <input type="text" id="title" name="title" class="form-control rounded-3" placeholder="e.g. Lecture 01: Introduction to OOP" required>
                                </div>

                                <!-- Publish Date -->
                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Publish Date *</label>
                                    <input type="date" id="publish_date" name="publish_date" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                                </div>

                                <!-- Attachment File -->
                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Upload Lecture Note (PDF / PPT / Zip)</label>
                                    <input type="file" id="upload_note" name="material_file" class="form-control rounded-3">
                                </div>

                                <!-- External Link -->
                                <div class="mb-4">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Video / External Link (Optional)</label>
                                    <input type="url" id="external_link" name="external_link" class="form-control rounded-3" placeholder="https://youtube.com/... or Google Drive">
                                </div>

                                <!-- Form Actions -->
                                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                                    <a href="modules.php" class="btn btn-light rounded-pill px-4 btn-sm">Cancel</a>
                                    <button type="submit" id="btnSubmit" class="btn btn-primary rounded-pill px-4 btn-sm fw-semibold">
                                        <i class="bi bi-cloud-upload me-1"></i> Upload Resource
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="ajax/js/material.js"></script>
</body>
</html>