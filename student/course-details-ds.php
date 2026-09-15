<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Science & AI - EduMart LMS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        .course-header-bg {
            background: linear-gradient(135deg, #0f172a 0%, #0284c7 100%);
            border-radius: 20px;
        }
        .nav-pills-custom .nav-link {
            color: #475569;
            font-weight: 600;
            border-radius: 12px;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }
        .nav-pills-custom .nav-link.active {
            background-color: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }
        .content-card {
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .content-card:hover {
            transform: translateY(-2px);
            border-color: #0284c7 !important;
        }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar Navigation -->
        <?php include('navbar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <!-- Topbar Navigation -->
            <?php include('topbar.php'); ?>

            <div class="p-4">
                
                <!-- Back Link & Navigation -->
                <div class="mb-3">
                    <a href="course.php" class="text-decoration-none text-muted fw-semibold small">
                        <i class="bi bi-arrow-left me-1"></i> Back to Enrolled Courses
                    </a>
                </div>

                <!-- Course Header Hero Card -->
                <div class="course-header-bg p-4 p-md-5 text-white mb-4 shadow-sm position-relative overflow-hidden">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <span class="badge bg-white text-info rounded-pill px-3 py-2 fw-bold mb-3">Data Science Module</span>
                            <h2 class="fw-bold mb-2">Data Science & AI</h2>
                            <p class="text-white-50 mb-4">Master Python, Machine Learning models, Neural Networks, Deep Learning architectures, and data visualization.</p>
                            
                            <div class="d-flex flex-wrap gap-4 text-white-50 small">
                                <div><i class="bi bi-person me-1 text-white"></i> Lecturer: <strong>Dr. N. Wickramasinghe</strong></div>
                                <div><i class="bi bi-journal-code me-1 text-white"></i> Code: <strong>DS-2026</strong></div>
                                <div><i class="bi bi-clock me-1 text-white"></i> Duration: <strong>10 Weeks</strong></div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                            <!-- Direct Class Join Button if active -->
                            <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur border border-white border-opacity-25 text-start text-lg-end">
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 mb-2"><i class="bi bi-calendar-event me-1"></i> Scheduled Class</span>
                                <p class="small text-white mb-2">Python & Pandas Workshop</p>
                                <a href="https://zoom.us" target="_blank" class="btn btn-light rounded-pill px-4 fw-bold text-info w-100">
                                    <i class="bi bi-camera-video-fill me-1"></i> Join Tomorrow 02:00 PM
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Search Filter Input -->
                <div class="mb-3">
                    <div class="input-group rounded-4 shadow-sm">
                        <span class="input-group-text bg-white border-0 ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="materialSearchInput" class="form-control border-0 py-2" placeholder="Search lecture notes, slides, or topics...">
                    </div>
                </div>

                <!-- Navigation Categories Tabs -->
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
                    <ul class="nav nav-pills nav-pills-custom gap-2" id="courseCategoryTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="notes-tab" data-bs-toggle="pill" data-bs-target="#lecture-notes" type="button" role="tab">
                                <i class="bi bi-file-earmark-text me-2"></i> Lecture Notes & Slides
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="videos-tab" data-bs-toggle="pill" data-bs-target="#video-lectures" type="button" role="tab">
                                <i class="bi bi-play-btn me-2"></i> Video Recordings
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="assignments-tab" data-bs-toggle="pill" data-bs-target="#assignments" type="button" role="tab">
                                <i class="bi bi-journal-check me-2"></i> Assignments & Quizzes
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="pill" data-bs-target="#ref-documents" type="button" role="tab">
                                <i class="bi bi-folder2-open me-2"></i> Reference Documents
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content Sections -->
                <div class="tab-content" id="courseCategoryTabsContent">

                    <!-- CATEGORY 1: LECTURE NOTES & SLIDES -->
                    <div class="tab-pane fade show active" id="lecture-notes" role="tabpanel">
                        <div class="d-flex flex-column gap-3">

                            <!-- Item 1 -->
                            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white content-card border">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger">
                                            <i class="bi bi-file-earmark-pdf-fill fs-3"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Week 01 - Python for Data Science & NumPy Foundations</h6>
                                            <p class="text-muted small mb-0">PDF Document • 3.8 MB • Uploaded on Jul 12, 2026</p>
                                        </div>
                                    </div>
                                    <a href="#" class="btn btn-outline-info rounded-pill px-3 btn-sm fw-semibold">
                                        <i class="bi bi-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>

                            <!-- Item 2 -->
                            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white content-card border">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                                            <i class="bi bi-file-earmark-ppt-fill fs-3"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Week 02 - Data Cleaning & Wrangling with Pandas</h6>
                                            <p class="text-muted small mb-0">PowerPoint Presentation • 8.5 MB • Uploaded on Jul 19, 2026</p>
                                        </div>
                                    </div>
                                    <a href="#" class="btn btn-outline-info rounded-pill px-3 btn-sm fw-semibold">
                                        <i class="bi bi-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- CATEGORY 2: VIDEO RECORDINGS -->
                    <div class="tab-pane fade" id="video-lectures" role="tabpanel">
                        <div class="row g-4">

                            <!-- Video Card 1 -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden content-card h-100">
                                    <div class="position-relative">
                                        <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=500&q=80" class="card-img-top" style="height: 160px; object-fit: cover;">
                                        <span class="badge bg-dark bg-opacity-75 position-absolute bottom-0 end-0 m-2 px-2 py-1 small">55:10 Mins</span>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark mb-1">Lecture 01: Pandas & Exploratory Data Analysis</h6>
                                        <p class="text-muted small mb-3">Hands-on walkthrough on dataset preprocessing.</p>
                                        <button onclick="playVideoModal('Lecture 01: Pandas & EDA', 'https://www.youtube.com/embed/dQw4w9WgXcQ')" class="btn btn-info text-white rounded-pill w-100 btn-sm fw-semibold">
                                            <i class="bi bi-play-circle me-1"></i> Watch Recording
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Video Card 2 -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden content-card h-100">
                                    <div class="position-relative">
                                        <img src="https://images.unsplash.com/photo-1509228468518-180dd4864904?auto=format&fit=crop&w=500&q=80" class="card-img-top" style="height: 160px; object-fit: cover;">
                                        <span class="badge bg-dark bg-opacity-75 position-absolute bottom-0 end-0 m-2 px-2 py-1 small">1 hr 05 Mins</span>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark mb-1">Lecture 02: Introduction to Supervised Machine Learning</h6>
                                        <p class="text-muted small mb-3">Understanding Linear Regression & Classification.</p>
                                        <button onclick="playVideoModal('Lecture 02: Machine Learning', 'https://www.youtube.com/embed/dQw4w9WgXcQ')" class="btn btn-info text-white rounded-pill w-100 btn-sm fw-semibold">
                                            <i class="bi bi-play-circle me-1"></i> Watch Recording
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- CATEGORY 3: ASSIGNMENTS & QUIZZES -->
                    <div class="tab-pane fade" id="assignments" role="tabpanel">
                        <div class="d-flex flex-column gap-3">

                            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning-emphasis">
                                            <i class="bi bi-journal-text fs-3"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Assignment 01: Exploratory Data Analysis on Housing Dataset</h6>
                                            <span class="text-danger small fw-semibold"><i class="bi bi-clock me-1"></i> Deadline: Aug 10, 2026 at 11:59 PM</span>
                                        </div>
                                    </div>
                                    <div>
                                        <button onclick="openAssignmentModal('Assignment 01: Housing Dataset Analysis')" class="btn btn-danger rounded-pill px-4 btn-sm fw-semibold">
                                            <i class="bi bi-upload me-1"></i> Submit Work
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- CATEGORY 4: REFERENCE DOCUMENTS -->
                    <div class="tab-pane fade" id="ref-documents" role="tabpanel">
                        <div class="d-flex flex-column gap-3">

                            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                                            <i class="bi bi-book-fill fs-3"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Python Data Science Handbook (PDF)</h6>
                                            <p class="text-muted small mb-0">Essential Reference Book by Jake VanderPlas</p>
                                        </div>
                                    </div>
                                    <a href="#" class="btn btn-outline-secondary rounded-pill px-3 btn-sm fw-semibold">
                                        <i class="bi bi-download me-1"></i> Download eBook
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- 1. VIDEO PLAYER MODAL -->
    <div class="modal fade" id="videoPlayerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 bg-dark text-white rounded-top-4">
                    <h6 class="modal-title fw-bold" id="modalVideoTitle">Lecture Recording</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-dark">
                    <div class="ratio ratio-16x9">
                        <iframe id="modalVideoIframe" src="" allowfullscreen allow="autoplay"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. ASSIGNMENT UPLOAD MODAL -->
    <div class="modal fade" id="assignmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold" id="modalAssignmentTitle">Submit Assignment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Choose Document (PDF or ZIP)</label>
                        <input type="file" id="assignmentFileInput" class="form-control rounded-3" accept=".pdf,.zip,.docx">
                    </div>

                    <!-- Preview Area -->
                    <div id="filePreviewArea" class="p-3 bg-light rounded-3 d-none mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-check fs-3 text-success"></i>
                            <div>
                                <strong id="fileNameDisplay" class="d-block small text-truncate" style="max-width: 250px;">filename.pdf</strong>
                                <span id="fileSizeDisplay" class="text-muted extra-small">0 MB</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmSubmitBtn" class="btn btn-primary rounded-pill px-4" disabled onclick="alert('Assignment Submitted Successfully!')">Upload & Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- External Custom JS Link -->
    <script src="js/course-details.js"></script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>