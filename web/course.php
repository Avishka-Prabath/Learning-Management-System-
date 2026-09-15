<?php
include_once('../db.php');

// Fetch courses dynamically from database
$coursesResult = $conn->query("SELECT c.*, t.title AS teacher_title, t.full_name AS teacher_name FROM courses c LEFT JOIN teachers t ON c.teacher_id = t.id ORDER BY c.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - EduMart</title>
    
    <!-- Bootstrap 5 CDN & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <?php include('navbar.php'); ?>

    <!-- HERO BANNER -->
    <section class="position-relative text-white py-5" style="background: linear-gradient(135deg, rgba(11, 26, 81, 0.95) 0%, rgba(0, 86, 179, 0.9) 100%), url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;">
        <div class="container py-4 text-center">
            <span class="badge bg-white text-primary fw-semibold px-3 py-2 rounded-pill mb-3">🎓 World-Class Online Catalog</span>
            <h1 class="display-4 fw-bold mb-3 text-white">Explore Our Degree Programs</h1>
            <p class="lead mb-0 opacity-90 mx-auto" style="max-width: 650px;">Discover top-rated, industry-ready courses and start building your future with EduMart today.</p>
        </div>
    </section>

    <!-- SEARCH & FILTER BAR -->
    <section class="py-4 bg-white border-bottom shadow-sm">
        <div class="container">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="courseSearchInput" class="form-control bg-light border-start-0 py-2" placeholder="Search for courses, skills, or instructors...">
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 d-flex gap-2 justify-content-md-end">
                    <select id="categorySelect" class="form-select bg-light py-2">
                        <option value="all" selected>All Categories</option>
                        <option value="computing">Computing & IT</option>
                        <option value="business">Business Management</option>
                        <option value="data">Data Science</option>
                        <option value="cyber">Cyber Security</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN COURSE GRID -->
    <div class="container py-5 flex-grow-1">
        
        <div id="noResults" class="text-center py-5 d-none">
            <i class="bi bi-search fs-1 text-muted"></i>
            <h4 class="mt-3 text-secondary">No courses found</h4>
            <p class="text-muted">Try searching with a different keyword or category.</p>
        </div>

        <div class="row g-4">
            <?php if ($coursesResult && $coursesResult->num_rows > 0): ?>
                <?php while ($course = $coursesResult->fetch_assoc()): 
                    // Default values for layout display
                    $course_img = !empty($course['image']) ? htmlspecialchars($course['image']) : 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=600&q=80';
                    $description = !empty($course['description']) ? htmlspecialchars($course['description']) : 'Learn modern programming paradigms, system architecture, database design, and cloud deployments.';
                    $duration = !empty($course['duration']) ? htmlspecialchars($course['duration']) : '3 Years';
                    $credits = !empty($course['credits']) ? htmlspecialchars($course['credits']) : '120 Credits';
                    $price = !empty($course['price']) ? htmlspecialchars($course['price']) : 'LKR 450,000';
                ?>
                    <!-- DYNAMIC COURSE CARD -->
                    <div class="col-md-6 col-lg-4 course-item" data-category="computing">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 course-card">
                            <div class="position-relative">
                                <img src="<?= $course_img ?>" class="card-img-top" alt="<?= htmlspecialchars($course['course_name']) ?>" style="height: 200px; object-fit: cover;">
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-light text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 small"><?= htmlspecialchars($course['course_code']) ?></span>
                                </div>
                                <h5 class="card-title fw-bold mb-2 text-dark"><?= htmlspecialchars($course['course_name']) ?></h5>
                                <p class="card-text text-muted small flex-grow-1"><?= $description ?></p>
                                
                                <?php if (!empty($course['teacher_name'])): ?>
                                    <p class="small text-muted mb-2"><i class="bi bi-person-circle text-primary me-1"></i> Lecturer: <strong><?= htmlspecialchars($course['teacher_title'] . ' ' . $course['teacher_name']) ?></strong></p>
                                <?php endif; ?>

                                <div class="d-flex align-items-center gap-3 text-muted small mb-3">
                                    <span><i class="bi bi-clock me-1 text-primary"></i> <?= $duration ?></span>
                                    <span><i class="bi bi-book me-1 text-primary"></i> <?= $credits ?></span>
                                </div>
                                <hr class="my-2 text-muted opacity-25">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-primary fs-5"><?= $price ?></span>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold btn-sm" data-bs-toggle="modal" data-bs-target="#courseModal<?= $course['id'] ?>">Read More</button>
                                        <!-- MAIN ENROLL BUTTON (PASSED COURSE ID) -->
                                        <a href="enroll.php?course_id=<?= $course['id'] ?>" class="btn btn-primary rounded-pill px-3 fw-semibold btn-sm">Enroll</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DYNAMIC MODAL -->
                    <div class="modal fade" id="courseModal<?= $course['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-primary"><?= htmlspecialchars($course['course_name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3 mb-4 bg-light p-3 rounded-3">
                                        <div class="col-6 col-md-3"><strong>Code:</strong> <br><span class="text-muted"><?= htmlspecialchars($course['course_code']) ?></span></div>
                                        <div class="col-6 col-md-3"><strong>Duration:</strong> <br><span class="text-muted"><?= $duration ?></span></div>
                                        <div class="col-6 col-md-3"><strong>Fee:</strong> <br><span class="text-primary fw-bold"><?= $price ?></span></div>
                                        <div class="col-6 col-md-3"><strong>Instructor:</strong> <br><span class="text-muted"><?= $course['teacher_name'] ? htmlspecialchars($course['teacher_name']) : 'TBA' ?></span></div>
                                    </div>
                                    <h6 class="fw-bold text-dark">Course Overview</h6>
                                    <p class="text-muted small mb-4"><?= $description ?></p>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                    <!-- MODAL ENROLL BUTTON (PASSED COURSE ID) -->
                                    <a href="enroll.php?course_id=<?= $course['id'] ?>" class="btn btn-primary rounded-pill px-4">Enroll Now</a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                    <h5 class="mt-3 text-secondary">No Courses Available</h5>
                    <p class="text-muted">Courses added from Admin Panel will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="course-filter.js"></script>
</body>
</html>