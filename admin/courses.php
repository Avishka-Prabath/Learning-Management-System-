<?php
session_start();
include_once('../db.php');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = "";

// Course Delete Logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM courses WHERE id = $id");
    header("Location: courses.php");
    exit();
}

// Course Update Logic (Form Submit වූ විට)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_course') {
    $course_id   = intval($_POST['edit_course_id']);
    $course_code = trim($_POST['edit_course_code']);
    $course_name = trim($_POST['edit_course_name']);
    $category    = trim($_POST['edit_category']);
    $teacher_id  = !empty($_POST['edit_teacher_id']) ? intval($_POST['edit_teacher_id']) : NULL;
    $duration    = trim($_POST['edit_duration']);
    $price       = trim($_POST['edit_price']);
    $image       = trim($_POST['edit_image']);
    $description = trim($_POST['edit_description']);

    if ($teacher_id) {
        $stmt = $conn->prepare("UPDATE courses SET course_code=?, course_name=?, category=?, teacher_id=?, duration=?, price=?, image=?, description=? WHERE id=?");
        $stmt->bind_param("sssissssi", $course_code, $course_name, $category, $teacher_id, $duration, $price, $image, $description, $course_id);
    } else {
        $stmt = $conn->prepare("UPDATE courses SET course_code=?, course_name=?, category=?, teacher_id=NULL, duration=?, price=?, image=?, description=? WHERE id=?");
        $stmt->bind_param("sssssssi", $course_code, $course_name, $category, $duration, $price, $image, $description, $course_id);
    }

    if ($stmt->execute()) {
        header("Location: courses.php?status=updated");
        exit();
    } else {
        $message = "Error updating course: " . $conn->error;
    }
}

// Fetch Analytics & Data
$totalCourses = ($res = $conn->query("SELECT COUNT(*) as total FROM courses")) ? $res->fetch_assoc()['total'] : 0;
$totalTeachers = ($res = $conn->query("SELECT COUNT(*) as total FROM teachers WHERE status='Active'")) ? $res->fetch_assoc()['total'] : 0;

$coursesResult = $conn->query("SELECT c.*, t.title AS teacher_title, t.full_name AS teacher_name FROM courses c LEFT JOIN teachers t ON c.teacher_id = t.id ORDER BY c.id DESC");
$teachersQuery = $conn->query("SELECT id, title, full_name FROM teachers WHERE status='Active'");

$teachersList = [];
if ($teachersQuery && $teachersQuery->num_rows > 0) {
    while ($t = $teachersQuery->fetch_assoc()) {
        $teachersList[] = $t;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Academic Courses - EduMart Admin</title>
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="admin-layout-wrapper">
        <!-- SIDEBAR -->
        <?php include('admin-sidebar.php'); ?>

        <!-- MAIN CONTENT AREA -->
        <div class="main-wrapper">
            <!-- TOPBAR -->
            <?php include('topbar.php'); ?>

            <div class="content-area">
                
                <!-- PAGE TITLE & HEADER -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">Academic Courses</h3>
                        <p class="text-secondary small mb-0">Create new degree programs, assign lecturers, and monitor portfolio status.</p>
                    </div>
                </div>

                <?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Course module updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div id="alertContainer"></div>

                <!-- ADVANCE STATS OVERVIEW CARDS -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                </div>
                                <div>
                                    <span class="text-muted extra-small fw-bold text-uppercase">Total Modules</span>
                                    <h4 class="fw-bold text-dark mb-0"><?= $totalCourses ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-person-badge-fill"></i>
                                </div>
                                <div>
                                    <span class="text-muted extra-small fw-bold text-uppercase">Active Faculty</span>
                                    <h4 class="fw-bold text-dark mb-0"><?= $totalTeachers ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-tags-fill"></i>
                                </div>
                                <div>
                                    <span class="text-muted extra-small fw-bold text-uppercase">Categories</span>
                                    <h4 class="fw-bold text-dark mb-0">4 Active</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>
                                <div>
                                    <span class="text-muted extra-small fw-bold text-uppercase">Status</span>
                                    <h4 class="fw-bold text-dark mb-0">Published</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOP: FORM CARD (ADD COURSE) -->
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill text-primary"></i> Add New Academic Module
                    </h5>

                    <form id="addCourseForm" novalidate>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Course Code *</label>
                                <input type="text" name="course_code" id="course_code" class="form-control rounded-3 py-2 fs-6" placeholder="e.g. CS-2026">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Course Title *</label>
                                <input type="text" name="course_name" id="course_name" class="form-control rounded-3 py-2 fs-6" placeholder="e.g. BSc (Hons) in Cyber Security">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Category</label>
                                <select name="category" id="category" class="form-select rounded-3 py-2 fs-6">
                                    <option value="" selected>-- Select Category --</option>
                                    <option value="computing">Computing</option>
                                    <option value="business">Business</option>
                                    <option value="data">Data Science</option>
                                    <option value="cyber">Cyber Security</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Instructor</label>
                                <select name="teacher_id" id="teacher_id" class="form-select rounded-3 py-2 fs-6">
                                    <option value="" selected>-- Choose Teacher --</option>
                                    <?php foreach ($teachersList as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>">
                                            <?= htmlspecialchars($teacher['title'] . ' ' . $teacher['full_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Duration</label>
                                <input type="text" name="duration" id="duration" class="form-control rounded-3 py-2 fs-6" placeholder="e.g. 3 Years" value="3 Years">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Course Fee</label>
                                <input type="text" name="price" id="price" class="form-control rounded-3 py-2 fs-6" placeholder="LKR 450,000" value="LKR 450,000">
                            </div>

                            <div class="col-12">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Cover Image URL</label>
                                <input type="url" name="image" id="image" class="form-control rounded-3 py-2 fs-6" placeholder="https://images.unsplash.com/...">
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Description / Overview</label>
                                <textarea name="description" id="description" class="form-control rounded-3 py-2 fs-6" rows="3" placeholder="Briefly detail what students will learn..."></textarea>
                            </div>
                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2 extra-small">
                                <i class="bi bi-plus-lg"></i> Create Academic Course
                            </button>
                        </div>
                    </form>
                </div>

                <!-- BOTTOM: TABLE CARD -->
                <div class="card card-custom p-4">
                    
                    <!-- FILTER & SEARCH BAR -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                        <h5 class="fw-bold text-dark mb-0">Active Course Modules</h5>
                        <div class="input-group" style="max-width: 260px;">
                            <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                                <i class="bi bi-search text-muted extra-small"></i>
                            </span>
                            <input type="text" id="adminSearchInput" class="form-control bg-light border-start-0 rounded-end-pill extra-small py-2" placeholder="Search course module...">
                        </div>
                    </div>

                    <!-- COURSES TABLE -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-custom mb-0" id="coursesTable">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Fee</th>
                                    <th>Instructor</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if ($coursesResult && $coursesResult->num_rows > 0): ?>
                                    <?php while ($row = $coursesResult->fetch_assoc()): 
                                        $img = !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=100&q=80';
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2.5">
                                                    <img src="<?= $img ?>" class="course-thumb-lg shadow-sm border" alt="Cover">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 extra-small fw-bold"><?= htmlspecialchars($row['course_code']) ?></span>
                                                </div>
                                            </td>
                                            <td class="fw-bold text-dark">
                                                <?= htmlspecialchars($row['course_name']) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 extra-small"><?= ucfirst(htmlspecialchars($row['category'] ?? 'Computing')) ?></span>
                                            </td>
                                            <td class="text-primary fw-bold extra-small">
                                                <?= htmlspecialchars($row['price'] ?? 'LKR 450,000') ?>
                                            </td>
                                            <td class="extra-small text-secondary">
                                                <?= $row['teacher_name'] ? htmlspecialchars($row['teacher_title'] . ' ' . $row['teacher_name']) : '<span class="text-muted">Not Assigned</span>' ?>
                                            </td>
                                            <td class="text-end">
                                                <!-- Edit Button -->
                                                <button type="button" 
                                                        class="btn btn-light btn-sm rounded-circle p-2 text-primary shadow-sm me-1 btn-edit-course" 
                                                        data-id="<?= $row['id'] ?>"
                                                        data-code="<?= htmlspecialchars($row['course_code']) ?>"
                                                        data-name="<?= htmlspecialchars($row['course_name']) ?>"
                                                        data-category="<?= htmlspecialchars($row['category'] ?? '') ?>"
                                                        data-teacher="<?= $row['teacher_id'] ?? '' ?>"
                                                        data-duration="<?= htmlspecialchars($row['duration'] ?? '') ?>"
                                                        data-price="<?= htmlspecialchars($row['price'] ?? '') ?>"
                                                        data-image="<?= htmlspecialchars($row['image'] ?? '') ?>"
                                                        data-description="<?= htmlspecialchars($row['description'] ?? '') ?>"
                                                        title="Edit Course">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <a href="courses.php?delete=<?= $row['id'] ?>" class="btn btn-light btn-sm rounded-circle p-2 text-danger shadow-sm" onclick="return confirm('Are you sure you want to delete this course module?')" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-journal-x fs-2 d-block mb-2 text-secondary"></i>
                                            <p class="mb-0">No course modules found. Fill the form above to create one.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- EDIT COURSE MODAL -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="editCourseModalLabel">
                        <i class="bi bi-pencil-square text-primary"></i> Edit Academic Course Module
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="courses.php" method="POST">
                    <input type="hidden" name="action" value="update_course">
                    <input type="hidden" name="edit_course_id" id="modal_course_id">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Course Code *</label>
                                <input type="text" name="edit_course_code" id="modal_course_code" class="form-control rounded-3 py-2 fs-6" required>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Course Title *</label>
                                <input type="text" name="edit_course_name" id="modal_course_name" class="form-control rounded-3 py-2 fs-6" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Category</label>
                                <select name="edit_category" id="modal_category" class="form-select rounded-3 py-2 fs-6">
                                    <option value="">-- Select --</option>
                                    <option value="computing">Computing</option>
                                    <option value="business">Business</option>
                                    <option value="data">Data Science</option>
                                    <option value="cyber">Cyber Security</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Assign Lecturer</label>
                                <select name="edit_teacher_id" id="modal_teacher_id" class="form-select rounded-3 py-2 fs-6">
                                    <option value="">-- Choose Teacher --</option>
                                    <?php foreach ($teachersList as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>">
                                            <?= htmlspecialchars($teacher['title'] . ' ' . $teacher['full_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Duration</label>
                                <input type="text" name="edit_duration" id="modal_duration" class="form-control rounded-3 py-2 fs-6">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Course Fee</label>
                                <input type="text" name="edit_price" id="modal_price" class="form-control rounded-3 py-2 fs-6">
                            </div>

                            <div class="col-12">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Cover Image URL</label>
                                <input type="url" name="edit_image" id="modal_image" class="form-control rounded-3 py-2 fs-6">
                            </div>

                            <div class="col-12">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Description / Overview</label>
                                <textarea name="edit_description" id="modal_description" class="form-control rounded-3 py-2 fs-6" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold extra-small" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm extra-small">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- External JS Files -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Edit Modal Script -->
    <script>
        $(document).ready(function() {
            $('.btn-edit-course').on('click', function() {
                let id          = $(this).data('id');
                let code        = $(this).data('code');
                let name        = $(this).data('name');
                let category    = $(this).data('category');
                let teacher     = $(this).data('teacher');
                let duration    = $(this).data('duration');
                let price       = $(this).data('price');
                let image       = $(this).data('image');
                let description = $(this).data('description');

                // Modal Form Fields වලට Data Fill කිරීම
                $('#modal_course_id').val(id);
                $('#modal_course_code').val(code);
                $('#modal_course_name').val(name);
                $('#modal_category').val(category);
                $('#modal_teacher_id').val(teacher);
                $('#modal_duration').val(duration);
                $('#modal_price').val(price);
                $('#modal_image').val(image);
                $('#modal_description').val(description);

                // Modal එක Open කිරීම
                $('#editCourseModal').modal('show');
            });
        });
    </script>
    <script src="ajax/js/courses.js"></script>
</body>
</html>