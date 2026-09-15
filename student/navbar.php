<?php
// දැනට Active වෙලා තියෙන Page එක Auto Detect කරගැනීම
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>

<!-- Sidebar Container -->
<div class="offcanvas-md offcanvas-start bg-dark text-white flex-shrink-0 p-3 d-flex flex-column" tabindex="-1" id="studentSidebar" style="width: 260px; min-height: 100vh;">
    
    <!-- Brand / Title (EduMart Web Matching Logo) -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-2 pt-1">
        <a href="home.php" class="d-flex align-items-center text-white text-decoration-none gap-2">
            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-mortarboard-fill fs-5"></i>
            </div>
            <span class="fs-4 fw-bold">Edu<span class="text-primary">Mart</span></span>
        </a>
        <button type="button" class="btn-close btn-close-white d-md-none" data-bs-dismiss="offcanvas" data-bs-target="#studentSidebar"></button>
    </div>

    <hr class="border-secondary opacity-25 mb-3">

    <!-- Navigation Menu (Dynamic Active Highlighting) -->
    <ul class="nav nav-pills flex-column mb-auto gap-1">
        <li class="nav-item">
            <a href="home.php" class="nav-link text-white fw-semibold <?= ($current_page == 'home.php') ? 'active bg-primary shadow-sm' : 'opacity-75 hover-link' ?>">
                <i class="bi bi-speedometer2 me-2 fs-5"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="course.php" class="nav-link text-white fw-semibold <?= ($current_page == 'course.php') ? 'active bg-primary shadow-sm' : 'opacity-75 hover-link' ?>">
                <i class="bi bi-journal-bookmark me-2 fs-5"></i> My Module
            </a>
        </li>
        <li class="nav-item">
            <a href="calender.php" class="nav-link text-white fw-semibold <?= ($current_page == 'calender.php') ? 'active bg-primary shadow-sm' : 'opacity-75 hover-link' ?>">
                <i class="bi bi-calendar-event me-2 fs-5"></i> Calendar
            </a>
        </li>
        <li class="nav-item">
            <a href="profile.php" class="nav-link text-white fw-semibold <?= ($current_page == 'profile.php') ? 'active bg-primary shadow-sm' : 'opacity-75 hover-link' ?>">
                <i class="bi bi-person-gear me-2 fs-5"></i> Profile
            </a>
        </li>
    </ul>

    <hr class="border-secondary opacity-25 mt-auto mb-3">
    
    <!-- User Quick Profile & Main Web Links -->
    <div class="px-1 d-flex flex-column gap-2">
        <a href="../web/home.php" class="btn btn-outline-light btn-sm w-100 rounded-pill py-2 fw-semibold">
            <i class="bi bi-globe me-1"></i> Main Website
        </a>

        <!-- Logout Button -->
        <!-- Logout Button -->
<a href="logout.php" class="btn btn-danger btn-sm bg-danger bg-opacity-10 text-danger border-0 w-100 rounded-pill py-2 fw-semibold">
    <i class="bi bi-box-arrow-right me-1"></i> Logout
</a>
    </div>

</div>

<!-- Hover Animation CSS -->
<style>
    .hover-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        opacity: 1 !important;
        transition: all 0.2s ease-in-out;
    }
</style>