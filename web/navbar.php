<?php
// Active page එක auto detect කරගැනීම
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>

<!-- Top Info Bar (Saegis Style) -->
<div class="top-bar text-white py-2 px-3 px-lg-5 d-none d-md-flex justify-content-between align-items-center" style="background-color: #0b1a51; font-size: 0.85rem;">
    <div class="top-info d-flex gap-4 align-items-center">
        <span><i class="bi bi-envelope-fill me-2 text-info"></i> info@edumart.ac.lk</span>
        <span><i class="bi bi-telephone-fill me-2 text-info"></i> +94 11 7430000 / +94 77 0430000</span>
    </div>
    <div class="top-actions d-flex align-items-center gap-3">
        <a href="../Student/login.php" class="text-white text-decoration-none d-flex align-items-center gap-1 hover-cyan">
            <i class="bi bi-person-circle text-info"></i> Student Portal
        </a>
        <div class="search-box position-relative">
            <input type="text" class="form-control form-control-sm rounded-pill px-3" placeholder="Search..." style="width: 180px; font-size: 0.8rem;">
        </div>
    </div>
</div>

<!-- Main Sticky Navbar -->
<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm py-3">
    <div class="container-fluid px-lg-5">
        
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-primary" href="index.php">
            <div class="brand-icon me-2 bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-mortarboard-fill fs-4"></i>
            </div>
            <span class="fs-3 fw-bold" style="color: #0b1a51;">Edu<span class="text-primary">Mart</span></span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Dynamic Nav Links -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1 me-3 my-3 my-lg-0">
                <li class="nav-item">
                    <a class="nav-link custom-nav-link <?php echo ($current_page == 'home.php' || $current_page == 'home.php') ? 'active' : ''; ?>" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link custom-nav-link <?php echo ($current_page == 'about_us.php') ? 'active' : ''; ?>" href="about_us.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link custom-nav-link <?php echo ($current_page == 'course.php') ? 'active' : ''; ?>" href="course.php">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link custom-nav-link <?php echo ($current_page == 'instructor.php') ? 'active' : ''; ?>" href="instructor.php">Instructors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link custom-nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact Us</a>
                </li>
            </ul>

            <!-- CTA Buttons -->
            <div class="d-flex gap-2">
                <a href="enroll.php" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">Enroll Now</a>
            </div>
        </div>
    </div>
</nav>