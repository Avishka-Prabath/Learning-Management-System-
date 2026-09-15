<?php
session_start();
include_once('../db.php');

// URL එකෙන් instructor ID එක ලබාගැනීම
$instructor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Database එකෙන් අදාළ Instructor ගේ දත්ත Fetch කිරීම
$stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ? AND status = 'Active' LIMIT 1");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

// Instructor කෙනෙක් හමු නොවූයේ නම් Instructors ලැයිස්තුවට Redirect කිරීම
if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    header("Location: instructors.php");
    exit();
}

// Full Name සකසා ගැනීම
$full_title_name = trim(($data['title'] ?? '') . ' ' . ($data['full_name'] ?? ''));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($full_title_name); ?> - EduMart</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Main Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-white d-flex flex-column min-vh-100">

    <!-- Navbar Include -->
    <?php include('navbar.php'); ?>

    <!-- PROFILE HEADER BANNER -->
    <section class="profile-header-bg py-5">
        <div class="container">
            <div class="row align-items-center g-4">
                
                <!-- Profile Image -->
                <div class="col-md-3 text-center">
                    <img src="<?= htmlspecialchars(!empty($data['profile_photo']) ? $data['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($full_title_name).'&background=0D6EFD&color=fff'); ?>" 
                         alt="<?= htmlspecialchars($full_title_name); ?>" 
                         class="rounded-circle shadow border border-4 border-white profile-img-lg">
                </div>

                <!-- Basic Info -->
                <div class="col-md-5 text-center text-md-start">
                    <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($full_title_name); ?></h2>
                    <p class="text-primary fw-semibold mb-3"><?= htmlspecialchars(!empty($data['assigned_module']) ? $data['assigned_module'] : 'Academic Instructor'); ?></p>
                    <a href="instructor.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="bi bi-arrow-left me-1"></i> Back to Instructors</a>
                </div>

                <!-- Contact Box -->
                <div class="col-md-4">
                    <div class="contact-box p-4 shadow-sm">
                        <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3 text-dark">Contact Information</h6>
                        <p class="small text-muted mb-2"><i class="bi bi-envelope-fill text-primary me-2"></i> <?= htmlspecialchars($data['email']); ?></p>
                        <p class="small text-muted mb-2"><i class="bi bi-telephone-fill text-primary me-2"></i> <?= htmlspecialchars(!empty($data['phone']) ? $data['phone'] : 'N/A'); ?></p>
                        <p class="small text-muted mb-2"><i class="bi bi-building text-primary me-2"></i> Department: <?= htmlspecialchars(!empty($data['department']) ? $data['department'] : 'Faculty Member'); ?></p>
                        <p class="small text-muted mb-0"><i class="bi bi-person-badge text-primary me-2"></i> Username: <?= htmlspecialchars(!empty($data['username']) ? $data['username'] : 'N/A'); ?></p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- DETAILS CONTENT SECTION -->
    <div class="container py-5 flex-grow-1">
        
        <!-- Profile / Biography -->
        <div class="mb-5">
            <h4 class="section-title">Profile Overview</h4>
            <p class="text-secondary leading-relaxed fs-6">
                <?= !empty($data['bio']) ? nl2br(htmlspecialchars($data['bio'])) : 'Academic instructor committed to delivering quality education, practical learning, and software development excellence at EduMart Campus.'; ?>
            </p>
        </div>

        <!-- Assigned Course Modules -->
        <div class="mb-5">
            <h4 class="section-title">Assigned Academic Module</h4>
            <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-journal-check text-primary fs-4"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars(!empty($data['assigned_module']) ? $data['assigned_module'] : 'General Academic Lecturing'); ?></h6>
                        <small class="text-muted">Primary teaching allocation for this academic year.</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <?php include('footer.php'); ?>

    <!-- JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>