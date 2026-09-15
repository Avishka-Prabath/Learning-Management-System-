<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Profile - EduMart</title>
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
                
                <!-- Profile Cover & Header -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="bg-primary bg-gradient p-5 text-white position-relative" style="min-height: 140px;">
                        <span class="badge bg-white text-primary rounded-pill position-absolute top-0 end-0 m-3 px-3 py-2 extra-small fw-bold">
                            <i class="bi bi-patch-check-fill text-primary me-1"></i> Verified Academic Staff
                        </span>
                    </div>
                    <div class="card-body p-4 pt-0 position-relative">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3" style="margin-top: -50px;">
                            <div class="d-flex align-items-end gap-3">
                                <div class="position-relative">
                                    <img src="https://ui-avatars.com/api/?name=Dr+Saman+Perera&background=0D6EFD&color=fff&size=128" alt="Profile" class="rounded-circle border border-4 border-white shadow" width="110" height="110">
                                    <button class="btn btn-sm btn-light rounded-circle shadow position-absolute bottom-0 end-0 p-1 border" title="Change Avatar">
                                        <i class="bi bi-camera-fill text-muted"></i>
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <h4 class="fw-bold text-dark mb-0">Dr. Saman Perera</h4>
                                    <p class="text-muted small mb-1"><i class="bi bi-briefcase me-1"></i> Senior Lecturer in Computer Science</p>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle extra-small rounded-pill">Active Faculty</span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Profile Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Quick Stats Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
                            <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 fs-4">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">03</h4>
                                <span class="text-muted extra-small">Assigned Modules</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
                            <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 fs-4">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">248</h4>
                                <span class="text-muted extra-small">Total Enrolled Students</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
                            <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 fs-4">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">8+ Years</h4>
                                <span class="text-muted extra-small">Teaching Experience</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Information Details & Security Tabs -->
                <div class="row g-4">
                    
                    <!-- Left Column: Personal Info -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-vcard text-primary me-2"></i>Academic & Contact Information</h5>
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <span class="text-muted extra-small d-block text-uppercase fw-semibold">Staff ID</span>
                                    <span class="fw-semibold text-dark small">EMP-2026-889</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted extra-small d-block text-uppercase fw-semibold">Department</span>
                                    <span class="fw-semibold text-dark small">Software Engineering</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted extra-small d-block text-uppercase fw-semibold">Official Email</span>
                                    <span class="fw-semibold text-dark small">saman.p@edumart.ac.lk</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted extra-small d-block text-uppercase fw-semibold">Contact Mobile</span>
                                    <span class="fw-semibold text-dark small">+94 77 123 4567</span>
                                </div>
                                <div class="col-12"><hr class="my-2"></div>
                                <div class="col-12">
                                    <span class="text-muted extra-small d-block text-uppercase fw-semibold mb-1">Biography / Overview</span>
                                    <p class="text-secondary small mb-0 lh-base">
                                        Doctorate in Software Engineering from the University of Colombo. Specializing in Cloud Computing, Software Architecture, and Distributed Systems with over 8 years of higher education teaching experience.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Password & Account Security -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock text-danger me-2"></i>Account Security</h5>
                            
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Current Password</label>
                                    <input type="password" class="form-control rounded-3" placeholder="••••••••" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">New Password</label>
                                    <input type="password" class="form-control rounded-3" placeholder="Enter new password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Confirm New Password</label>
                                    <input type="password" class="form-control rounded-3" placeholder="Repeat new password" required>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-semibold w-100">
                                    <i class="bi bi-key me-1"></i> Update Password
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Edit Profile Details Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Update Profile Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Full Name</label>
                            <input type="text" class="form-control rounded-3" value="Dr. Saman Perera" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Designation</label>
                            <input type="text" class="form-control rounded-3" value="Senior Lecturer in Computer Science" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Contact Phone Number</label>
                            <input type="text" class="form-control rounded-3" value="+94 77 123 4567" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Biography</label>
                            <textarea class="form-control rounded-3" rows="3">Doctorate in Software Engineering from the University of Colombo...</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-semibold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>