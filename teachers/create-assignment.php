<?php
// Include db.php from the root folder
require_once '../db.php'; 

$error = '';

// Form Submission Logic (PHP Fallback)
if (isset($_POST['create_assignment'])) {
    $title = trim($_POST['title']);
    $course = trim($_POST['course_name']);
    $deadline_date = $_POST['deadline_date'];
    $deadline_time = $_POST['deadline_time'];
    $type = $_POST['type'];
    $status = 'Active';

    $file_path = NULL;
    if (isset($_FILES['brief_doc']) && $_FILES['brief_doc']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) { 
            mkdir($target_dir, 0777, true); 
        }
        $file_path = time() . '_' . basename($_FILES["brief_doc"]["name"]);
        move_uploaded_file($_FILES["brief_doc"]["tmp_name"], $target_dir . $file_path);
    }

    if (!empty($title) && !empty($course) && !empty($deadline_date) && !empty($deadline_time)) {
        $stmt = $conn->prepare("INSERT INTO assignments (title, course_name, deadline_date, deadline_time, file_path, type, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $course, $deadline_date, $deadline_time, $file_path, $type, $status);
        
        if ($stmt->execute()) {
            header("Location: assignments.php?msg=created");
            exit();
        } else {
            $error = "Something went wrong! Please try again.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Assignment - Teacher Portal</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
</head>
<body>

    <div class="d-flex">
        <!-- Sidebar Component -->
        <?php if (file_exists('instructor-sidebar.php')) include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100 d-flex flex-column">
            <!-- Topbar Component -->
            <?php if (file_exists('topbar.php')) include('topbar.php'); ?>

            <!-- Main Content Area -->
            <div class="p-4 flex-grow-1">
                
                <!-- Back Button & Header -->
                <div class="mb-4">
                    <a href="assignments.php" class="btn btn-light btn-sm rounded-pill px-3 text-muted fw-semibold mb-3">
                        <i class="bi bi-arrow-left me-1"></i> Back to Assignments
                    </a>
                    <h3 class="fw-bold text-dark mb-1">Publish New Assignment</h3>
                    <p class="text-secondary small mb-0">Fill in the details below to create and publish a new coursework or exam.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 max-w-75" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Form Card -->
                <div class="card border-0 rounded-4 shadow-sm bg-white p-4" style="max-width: 800px;">
                    <form action="" method="POST" enctype="multipart/form-data" id="create-assignment-form" novalidate>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label small fw-bold text-dark">Course Name <span class="text-danger">*</span></label>
            <input type="text" name="course_name" class="form-control rounded-3 p-2.5" placeholder="e.g. Software Engineering">
        </div>

        <div class="col-md-6">
            <label class="form-label small fw-bold text-dark">Type <span class="text-danger">*</span></label>
            <select name="type" class="form-select rounded-3 p-2.5">
                <option value="Assignment">Assignment</option>
                <option value="Exam">Exam / Quiz</option>
                <option value="Project">Project</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label small fw-bold text-dark">Assignment Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control rounded-3 p-2.5" placeholder="Enter assignment title">
        </div>

        <div class="col-md-6">
            <label class="form-label small fw-bold text-dark">Deadline Date <span class="text-danger">*</span></label>
            <input type="date" name="deadline_date" class="form-control rounded-3 p-2.5">
        </div>

        <div class="col-md-6">
            <label class="form-label small fw-bold text-dark">Deadline Time <span class="text-danger">*</span></label>
            <input type="time" name="deadline_time" class="form-control rounded-3 p-2.5">
        </div>

        <div class="col-12">
            <label class="form-label small fw-bold text-dark">Brief Document (PDF/Doc)</label>
            <input type="file" name="brief_doc" class="form-control rounded-3 p-2.5">
            <span class="text-muted extra-small">Optional: Attach brief instructions or guidelines file.</span>
        </div>

        <div class="col-12 pt-3 d-flex gap-2">
            <button type="submit" name="create_assignment" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Publish Assignment
            </button>
            <a href="assignments.php" class="btn btn-light rounded-pill px-4 py-2 text-muted fw-semibold">
                Cancel
            </a>
        </div>
    </div>
</form>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS Link -->
    <script src="ajax/js/assignment_validation.js"></script>
</body>
</html>