<?php
include_once('../db.php');
// Fetch teachers
$teachersResult = $conn->query("SELECT * FROM teachers WHERE status = 'Active' ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructors - EduMart</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Navbar Include -->
    <?php include('navbar.php'); ?>

    <!-- HERO BANNER -->
    <section class="position-relative text-white py-5" style="background: linear-gradient(135deg, rgba(11, 26, 81, 0.95) 0%, rgba(0, 86, 179, 0.9) 100%), url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;">
        <div class="container py-4 text-center">
            <span class="badge bg-white text-primary fw-semibold px-3 py-2 rounded-pill mb-3">👨‍🏫 Industry Mentors</span>
            <h1 class="display-4 fw-bold mb-3 text-white">Meet Our Instructors</h1>
            <p class="lead mb-0 opacity-90 mx-auto" style="max-width: 650px;">Learn directly from experienced engineers, data scientists, and senior product designers.</p>
        </div>
    </section>

    <!-- INSTRUCTORS GRID -->
    <div class="container py-5 flex-grow-1">
        <div class="row g-4">
            
            <?php if ($teachersResult && $teachersResult->num_rows > 0): ?>
                <?php while ($teacher = $teachersResult->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 course-card position-relative">
                            <div class="position-relative d-inline-block mx-auto mb-3">
                                <img src="<?= htmlspecialchars($teacher['profile_photo']) ?>" class="rounded-circle shadow-sm border border-3 border-primary" style="width: 120px; height: 120px; object-fit: cover;" alt="<?= htmlspecialchars($teacher['full_name']) ?>">
                            </div>
                            <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($teacher['title'] . ' ' . $teacher['full_name']) ?></h5>
                            <p class="text-primary fw-semibold small mb-3"><?= htmlspecialchars($teacher['assigned_module'] ? $teacher['assigned_module'] : 'Faculty Mentor') ?></p>
                            <p class="text-muted small mb-4"><?= htmlspecialchars($teacher['bio'] ? $teacher['bio'] : 'Experienced academic instructor specializing in industry standards and software development.') ?></p>
                            
                            <!-- View Profile Link Button -->
                            <a href="instructor_details.php?id=<?= $teacher['id'] ?>" class="btn btn-outline-primary rounded-pill btn-sm fw-bold mb-3 px-4">View Profile</a>

                            <hr class="my-2 text-muted opacity-25">
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="small text-muted"><i class="bi bi-envelope me-1 text-primary"></i><?= htmlspecialchars($teacher['email']) ?></span>
                                <div class="d-flex gap-2 fs-5 text-muted z-2">
                                    <a href="tel:<?= htmlspecialchars($teacher['phone']) ?>" class="text-secondary"><i class="bi bi-telephone"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    No instructors registered yet.
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- FOOTER -->
    <?php include('footer.php'); ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>