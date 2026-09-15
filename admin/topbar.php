<!-- Admin Topbar Header -->
<nav class="navbar navbar-expand-lg navbar-light border-bottom px-4 py-2 sticky-top shadow-sm bg-white">
    <div class="container-fluid p-0">
        
        <!-- Search Bar -->
        <div class="d-none d-md-flex align-items-center bg-light rounded-pill px-3 py-1 border" style="width: 300px;">
            <i class="bi bi-search text-muted me-2"></i>
            <input type="text" class="form-control border-0 bg-transparent p-0 extra-small" placeholder="Search students, courses or system logs...">
        </div>

        <!-- Right Side Actions & Profile -->
        <div class="d-flex align-items-center gap-3 ms-auto">
            
            <!-- Quick System Badge -->
            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 extra-small fw-bold">
                <i class="bi bi-shield-check me-1"></i> Admin Access Mode
            </span>

            <div class="vr mx-1 my-auto" style="height: 24px;"></div>

            <!-- Admin Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle p-1 rounded-pill pe-2 hover-bg" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=0D6EFD&color=fff" alt="Admin" width="36" height="36" class="rounded-circle border">
                    <div class="d-none d-sm-flex flex-column text-start ms-2">
                        <span class="fw-bold text-dark extra-small lh-1">System Administrator</span>
                        <span class="text-muted extra-small" style="font-size: 0.7rem;">admin@edumart.ac.lk</span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 rounded-4" style="min-width: 200px;">
                    <li><a class="dropdown-item rounded-3 py-2 text-danger fw-semibold" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
                </ul>
            </div>

        </div>
    </div>
</nav>