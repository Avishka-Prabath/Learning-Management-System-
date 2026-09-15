<?php
session_start();

// Include class/include.php with the correct path
$includePath = __DIR__ . '/class/include.php';

if (file_exists($includePath)) {
    require_once $includePath;
} else {
    die("Error: include.php file is missing at " . $includePath);
}

// Session Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get the database connection
$db = Database::getInstance();
$conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

// Get the list of courses
$coursesQuery = $conn->query("SELECT id, course_code, course_name FROM courses ORDER BY course_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Instructor - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Admin Main Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="admin-layout-wrapper">
        <!-- Sidebar Include -->
        <?php include('admin-sidebar.php'); ?>

        <!-- Main Area -->
        <div class="main-wrapper">
            <!-- Topbar Include -->
            <?php include('topbar.php'); ?>

            <div class="content-area">
                
                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">Add New Instructor</h3>
                        <p class="text-secondary small mb-0">Register a new academic lecturer or instructor to the portal system.</p>
                    </div>
                    <a href="teachers-list.php" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm fw-semibold extra-small">
                        <i class="bi bi-arrow-left me-1"></i> Back to All Instructors
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card card-custom p-4 p-md-5">
                    <form id="createInstructorForm" method="POST" enctype="multipart/form-data" novalidate>

                        <!-- SECTION 1: PERSONAL DETAILS -->
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="bi bi-person-badge text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Personal & Contact Information</h6>
                        </div>

                        <div class="row g-3 mb-4 align-items-center">
                            <!-- Photo Preview Box -->
                            <div class="col-md-3 text-center border-end pe-md-4 mb-3 mb-md-0">
                                <label class="form-label d-block extra-small fw-bold text-uppercase text-muted mb-2">Profile Photo</label>
                                <div class="position-relative d-inline-block">
                                    <img src="https://ui-avatars.com/api/?name=New+User&background=f1f5f9&color=64748b" alt="Preview" class="rounded-circle border border-2 shadow-sm object-fit-cover" width="110" height="110" id="profilePreview">
                                    <label for="photoUpload" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px; cursor: pointer;">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                    <input type="file" id="photoUpload" name="profile_photo" class="d-none" accept="image/*" onchange="previewImage(event)">
                                </div>
                                <div class="extra-small text-muted mt-2">Allowed JPG, PNG or WEBP</div>
                            </div>

                            <div class="col-md-9 ps-md-4">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Title <span class="text-danger">*</span></label>
                                        <select class="form-select rounded-3 py-2 fs-6" id="select_title" name="title" required>
                                            <option value="">-- Select --</option>
                                            <option value="Dr.">Dr.</option>
                                            <option value="Prof.">Prof.</option>
                                            <option value="Mr." selected>Mr.</option>
                                            <option value="Mrs.">Mrs.</option>
                                            <option value="Ms.">Ms.</option>
                                        </select>
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control rounded-3 py-2 fs-6" id="full_name" name="full_name" placeholder="e.g. Saman Perera" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control rounded-3 py-2 fs-6" id="email" name="email" placeholder="e.g. saman.p@edumart.ac.lk" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Phone Number</label>
                                        <input type="tel" class="form-control rounded-3 py-2 fs-6" id="phone" name="phone" placeholder="e.g. 0771234567">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: ACADEMIC & ALLOCATION -->
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="bi bi-journal-bookmark text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Academic & Department Allocation</h6>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Department</label>
                                <select class="form-select rounded-3 py-2 fs-6" id="department" name="department">
                                    <option value="" disabled selected>-- Select Department --</option>
                                    <option value="Software Engineering">Software Engineering</option>
                                    <option value="Data Science">Data Science</option>
                                    <option value="Cyber Security">Cyber Security</option>
                                    <option value="Information Technology">Information Technology</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Assign Initial Course Module</label>
                                <select class="form-select rounded-3 py-2 fs-6" id="assigned_module" name="assigned_module">
                                    <option value="">-- Select Course (Optional) --</option>
                                    <?php if($coursesQuery && $coursesQuery->num_rows > 0): ?>
                                        <?php while ($course = $coursesQuery->fetch_assoc()): ?>
                                            <option value="<?= $course['id'] ?>"><?= htmlspecialchars(($course['course_code'] ? $course['course_code'] . ' - ' : '') . $course['course_name']) ?></option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- SECTION 3: CREDENTIALS -->
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="bi bi-shield-lock text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Portal Login Credentials</h6>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3 py-2 fs-6 bg-light" id="username" name="username" placeholder="e.g. saman_p" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Default Password <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3 py-2 fs-6" id="password" name="password" value="lecturer123" required>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                            <a href="teachers-list.php" class="btn btn-light rounded-pill px-4 py-2 btn-sm fw-semibold">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 btn-sm fw-bold shadow-sm">
                                <i class="bi bi-person-check-fill me-1"></i> Register Instructor
                            </button>
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
    
    <!-- Image Live Preview & Dynamic Auto-fill Logic -->
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profilePreview');
                output.src = reader.result;
            };
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        // Auto-fill email & username while typing the full name
        $(document).ready(function() {
            $('#full_name').on('input', function() {
                let nameVal = $(this).val().trim().toLowerCase();

                if (nameVal.length > 0) {
                    let parts = nameVal.split(/\s+/);
                    let firstName = parts[0] || '';
                    let lastName = parts.length > 1 ? parts[parts.length - 1] : '';

                    // Username Generation (e.g., saman_perera or saman_p)
                    let generatedUsername = firstName;
                    if (lastName.length > 0) {
                        generatedUsername += '_' + lastName.charAt(0);
                    }

                    // Email Generation (e.g., saman.p@edumart.ac.lk)
                    let generatedEmail = firstName;
                    if (lastName.length > 0) {
                        generatedEmail += '.' + lastName.charAt(0);
                    }
                    generatedEmail += '@edumart.ac.lk';

                    // Auto-fill form field values
                    $('#username').val(generatedUsername);
                    $('#email').val(generatedEmail);

                    // Update avatar
                    let avatarName = encodeURIComponent($('#full_name').val().trim());
                    $('#profilePreview').attr('src', 'https://ui-avatars.com/api/?name=' + avatarName + '&background=0d6efd&color=fff');
                } else {
                    $('#username').val('');
                    $('#email').val('');
                    $('#profilePreview').attr('src', 'https://ui-avatars.com/api/?name=New+User&background=f1f5f9&color=64748b');
                }
            });
        });
    </script>
    <script src="ajax/js/instructor.js"></script>
</body>
</html>