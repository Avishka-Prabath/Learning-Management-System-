<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Lectures & Notes - Teacher Portal</title>
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
                
                <!-- Back Button & Course Header -->
                <div class="mb-4">
                    <a href="courses.php" class="btn btn-light btn-sm rounded-pill px-3 text-muted fw-semibold mb-3">
                        <i class="bi bi-arrow-left me-1"></i> Back to Courses
                    </a>
                    
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-white p-4 rounded-4 shadow-sm border-start border-4 border-primary">
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill mb-2">SE-2026 Batch</span>
                            <h3 class="fw-bold text-dark mb-1">BSc in Software Engineering</h3>
                            <p class="text-muted small mb-0"><i class="bi bi-people me-1"></i> 120 Enrolled Students • Assigned Lecturer: Dr. Saman Perera</p>
                        </div>
                        <!-- Link to the Upload Material page -->
                        <a href="upload-material.php" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold btn-sm shadow-sm">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Lecture Note
                        </a>
                    </div>
                </div>

                <!-- Lecture List Accordion / Card View -->
                <div class="d-flex flex-column gap-3">
                    
                    <!-- Lecture 01 -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 fw-bold">
                                    01
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Lecture 01: Introduction to Software Architecture</h5>
                                    <span class="text-muted extra-small"><i class="bi bi-calendar-event me-1"></i> Published on: July 15, 2026</span>
                                </div>
                            </div>
                            <a href="upload-material.php?lecture=1" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-plus-lg me-1"></i> Add Note
                            </a>
                        </div>

                        <!-- Uploaded Materials for Lecture 01 -->
                        <div class="card-body px-4 pb-4 pt-0">
                            <div class="p-3 bg-light rounded-3 d-flex flex-column gap-2">
                                <div class="d-flex align-items-center justify-content-between bg-white p-2 px-3 rounded-3 border">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                        <div>
                                            <span class="fw-semibold text-dark extra-small d-block">Software_Architecture_Overview.pdf</span>
                                            <span class="text-muted extra-small">2.4 MB • Uploaded PDF</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="#" class="btn btn-light btn-sm rounded-circle"><i class="bi bi-download"></i></a>
                                        <button class="btn btn-light btn-sm rounded-circle text-danger"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between bg-white p-2 px-3 rounded-3 border">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-link-45deg text-primary fs-5"></i>
                                        <div>
                                            <span class="fw-semibold text-dark extra-small d-block">Lecture 01 Recorded Session (Zoom Link)</span>
                                            <span class="text-muted extra-small">External Resource</span>
                                        </div>
                                    </div>
                                    <a href="#" target="_blank" class="btn btn-light btn-sm rounded-pill extra-small">View Link →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lecture 02 -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 fw-bold">
                                    02
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Lecture 02: Monolithic vs Microservices Architecture</h5>
                                    <span class="text-muted extra-small"><i class="bi bi-calendar-event me-1"></i> Published on: July 22, 2026</span>
                                </div>
                            </div>
                            <a href="upload-material.php?lecture=2" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-plus-lg me-1"></i> Add Note
                            </a>
                        </div>

                        <!-- Uploaded Materials for Lecture 02 -->
                        <div class="card-body px-4 pb-4 pt-0">
                            <div class="p-3 bg-light rounded-3 d-flex flex-column gap-2">
                                <div class="d-flex align-items-center justify-content-between bg-white p-2 px-3 rounded-3 border">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-ppt-fill text-warning fs-5"></i>
                                        <div>
                                            <span class="fw-semibold text-dark extra-small d-block">Microservices_Design_Slides.pptx</span>
                                            <span class="text-muted extra-small">5.1 MB • Presentation Slides</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="#" class="btn btn-light btn-sm rounded-circle"><i class="bi bi-download"></i></a>
                                        <button class="btn btn-light btn-sm rounded-circle text-danger"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lecture 03 (Empty Note State) -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 bg-secondary bg-opacity-10 text-secondary rounded-4 fw-bold">
                                    03
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Lecture 03: Design Patterns & SOLID Principles</h5>
                                    <span class="text-muted extra-small"><i class="bi bi-clock me-1"></i> Scheduled for Today</span>
                                </div>
                            </div>
                            <a href="upload-material.php?lecture=3" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="bi bi-cloud-upload me-1"></i> Upload Note
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>