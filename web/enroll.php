<?php
include_once 'class/include.php';
$database = Database::getInstance();
$coursesQuery = $database->readQuery("SELECT id, course_code, course_name FROM courses ORDER BY course_name ASC");

// Catch Selected Course ID/Name from URL
$selectedCourseId = isset($_GET['course_id']) ? $_GET['course_id'] : (isset($_GET['course']) ? $_GET['course'] : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Student Application Form - EduMart</title>

    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Hero Header Section -->
    <section class="hero-header text-white text-center py-4 position-relative">
        <div class="container py-2">
            <!-- BACK BUTTON -->
            <div class="text-start mb-3">
                <a href="javascript:history.back()" class="btn btn-outline-light rounded-pill px-3 py-1 text-white text-decoration-none shadow-sm small">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <span class="badge badge-custom rounded-pill px-3 py-2 text-uppercase mb-2">Campus Application Portal</span>
            <h1 class="fw-bold display-6 mb-2">Student Enrollment & Admission Form</h1>
            <p class="small text-white-50 mb-0 mx-auto" style="max-width: 600px;">Complete all required sections below to submit your formal application for academic programs.</p>
        </div>
    </section>

    <!-- Main Content Form Section -->
    <div class="container flex-grow-1 mb-5 form-card-wrapper">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">

                <div class="card custom-card p-4 p-md-5 shadow-sm rounded-4 border-0">
                    <form id="enrollForm" novalidate>
                        
                        <!-- 1. PERSONAL INFORMATION -->
                        <div class="d-flex align-items-center mb-4 border-bottom pb-2">
                            <i class="bi bi-person-badge-fill fs-4 text-primary me-2"></i>
                            <h5 class="fw-bold section-title mb-0">1. Personal Details</h5>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold text-secondary small">Full Name (as in NIC/Passport) *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" id="full_name" name="full_name" class="form-control" placeholder="e.g. Kaluaratchige Nimal Perera" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-secondary small">NIC / Passport Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" id="nic" name="nic" class="form-control" placeholder="e.g. 1998XXXXXXXX / 2000XXXX" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Date of Birth *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                                    <input type="date" id="dob" name="dob" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Gender *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                    <select id="gender" name="gender" class="form-select" required>
                                        <option value="" disabled selected>-- Select Gender --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Phone Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="07X XXXXXXX" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Email Address *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Permanent Address *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" id="address" name="address" class="form-control" placeholder="No, Street, City" required>
                                </div>
                            </div>
                        </div>

                        <!-- 2. COURSE & ADMISSION DETAILS -->
                        <div class="d-flex align-items-center my-4 border-bottom pb-2">
                            <i class="bi bi-journal-bookmark-fill fs-4 text-primary me-2"></i>
                            <h5 class="fw-bold section-title mb-0">2. Academic Program & Intake Selection</h5>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Select Course / Degree Program *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-book"></i></span>
                                    <select id="course" name="course" class="form-select" required>
                                        <option value="" disabled <?= empty($selectedCourseId) ? 'selected' : '' ?>>-- Choose a Course --</option>
                                        <?php if ($coursesQuery && mysqli_num_rows($coursesQuery) > 0): ?>
                                            <?php while ($course = mysqli_fetch_assoc($coursesQuery)): ?>
                                                <?php 
                                                    $isSelected = ($selectedCourseId == $course['id'] || $selectedCourseId == $course['course_name']) ? 'selected' : '';
                                                ?>
                                                <option value="<?= $course['id'] ?>" <?= $isSelected ?>>
                                                    <?= htmlspecialchars($course['course_name'] . ' (' . $course['course_code'] . ')') ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary small">Study Mode *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
                                    <select id="study_mode" name="study_mode" class="form-select" required>
                                        <option value="Full-Time" selected>Full-Time</option>
                                        <option value="Part-Time">Part-Time (Weekend)</option>
                                        <option value="Online">Online / Distance</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary small">Preferred Intake *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <select id="intake" name="intake" class="form-select" required>
                                        <option value="2026-Batch-01">2026 Intake 01 (March)</option>
                                        <option value="2026-Batch-02">2026 Intake 02 (July)</option>
                                        <option value="2026-Batch-03">2026 Intake 03 (October)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 3. EDUCATIONAL QUALIFICATIONS -->
                        <div class="d-flex align-items-center my-4 border-bottom pb-2">
                            <i class="bi bi-mortarboard-fill fs-4 text-primary me-2"></i>
                            <h5 class="fw-bold section-title mb-0">3. Educational Background</h5>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Highest Academic Qualification *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-award"></i></span>
                                    <select id="qualification" name="qualification" class="form-select" required>
                                        <option value="" disabled selected>-- Select Qualification --</option>
                                        <option value="GCE A/L">G.C.E. A/L Completed</option>
                                        <option value="GCE O/L">G.C.E. O/L Completed</option>
                                        <option value="Diploma">Diploma / Higher Diploma</option>
                                        <option value="Bachelor Degree">Undergraduate Degree</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">School / Institute Attended</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" id="school" name="school" class="form-control" placeholder="e.g. Royal College Colombo">
                                </div>
                            </div>
                        </div>

                        <!-- 4. PARENT / GUARDIAN CONTACT -->
                        <div class="d-flex align-items-center my-4 border-bottom pb-2">
                            <i class="bi bi-shield-person fs-4 text-primary me-2"></i>
                            <h5 class="fw-bold section-title mb-0">4. Parent / Guardian Details</h5>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-secondary small">Guardian Name *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" id="guardian_name" name="guardian_name" class="form-control" placeholder="Guardian Full Name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Guardian Contact No *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="tel" id="guardian_phone" name="guardian_phone" class="form-control" placeholder="07X XXXXXXX" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary small">Relationship *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-people"></i></span>
                                    <select id="guardian_relation" name="guardian_relation" class="form-select" required>
                                        <option value="Father">Father</option>
                                        <option value="Mother">Mother</option>
                                        <option value="Guardian">Guardian</option>
                                        <option value="Spouse">Spouse</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 5. ADDITIONAL NOTES & DECLARATION -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small">Additional Remarks / Inquiries (Optional)</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Any scholarships, credit transfers or special inquiries..."></textarea>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="declaration" name="declaration" required>
                            <label class="form-check-label small text-secondary" for="declaration">
                                I hereby declare that all information provided in this application form is true and accurate.
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btnSubmit" class="btn btn-primary btn-custom-submit w-100 fw-bold text-white shadow-sm py-2.5">
                            <i class="bi bi-send-fill me-2"></i> Submit Campus Application
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="ajax/js/enroll_validation.js"></script>
</body>
</html>