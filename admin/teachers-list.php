<?php
session_start();
include_once('../db.php');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Search and filter parameters
$search = $_GET['search'] ?? '';
$dept = $_GET['department'] ?? '';

$sql = "SELECT t.*, c.course_name, c.course_code 
        FROM teachers t 
        LEFT JOIN courses c ON t.assigned_module = c.id 
        WHERE 1=1";

if (!empty($search)) {
    $searchEsc = $conn->real_escape_string($search);
    $sql .= " AND (t.full_name LIKE '%$searchEsc%' OR t.email LIKE '%$searchEsc%' OR t.department LIKE '%$searchEsc%')";
}
if (!empty($dept)) {
    $deptEsc = $conn->real_escape_string($dept);
    $sql .= " AND t.department = '$deptEsc'";
}
$sql .= " ORDER BY t.id DESC";
$teachersResult = $conn->query($sql);

$totalCount = $teachersResult ? $teachersResult->num_rows : 0;

// Fetch all courses for drop down selection
$coursesQuery = $conn->query("SELECT id, course_code, course_name FROM courses ORDER BY course_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Instructors - EduMart Admin</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Admin Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="admin-layout-wrapper">
        <!-- Sidebar Include -->
        <?php include('admin-sidebar.php'); ?>

        <div class="main-wrapper">
            <!-- Topbar Include -->
            <?php include('topbar.php'); ?>

            <div class="content-area">
                
                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">Academic Instructors</h3>
                        <p class="text-secondary small mb-0">Manage all registered lecturers, module allocations, and active statuses.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="add-teacher.php" class="btn btn-outline-primary rounded-pill px-3 py-1.5 btn-sm fw-semibold extra-small">
                            <i class="bi bi-file-earmark-plus me-1"></i> Full Form Registration
                        </a>
                        <button class="btn btn-primary rounded-pill px-3 py-1.5 btn-sm fw-bold shadow-sm extra-small" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
                            <i class="bi bi-person-plus-fill me-1"></i> Quick Add Instructor
                        </button>
                    </div>
                </div>

                <!-- Search & Filter Card -->
                <div class="card card-custom p-3 mb-4 border-0 shadow-sm rounded-4 bg-white">
                    <form method="GET" action="teachers-list.php">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6 col-lg-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                                        <i class="bi bi-search text-muted small"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-pill extra-small py-2" placeholder="Search by name, email or department..." value="<?= htmlspecialchars($search) ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <select name="department" class="form-select bg-light rounded-pill extra-small py-2" onchange="this.form.submit()">
                                    <option value="">All Departments</option>
                                    <option value="Software Engineering" <?= ($dept == 'Software Engineering') ? 'selected' : '' ?>>Software Engineering</option>
                                    <option value="Data Science" <?= ($dept == 'Data Science') ? 'selected' : '' ?>>Data Science</option>
                                    <option value="Cyber Security" <?= ($dept == 'Cyber Security') ? 'selected' : '' ?>>Cyber Security</option>
                                    <option value="Information Technology" <?= ($dept == 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-4 text-md-end ms-auto">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 extra-small fw-bold">
                                    Total: <?= $totalCount ?> Instructors
                                </span>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Instructors Table Card -->
                <div class="card card-custom p-4 border-0 shadow-sm rounded-4 bg-white">
                    <div class="table-responsive w-100">
                        <table class="table table-hover align-middle mb-0 w-100">
                            <thead>
                                <tr class="text-muted extra-small text-uppercase">
                                    <th>Instructor Details</th>
                                    <th>Contact Info</th>
                                    <th>Assigned Module</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                
                                <?php if ($teachersResult && $teachersResult->num_rows > 0): ?>
                                    <?php while ($teacher = $teachersResult->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= htmlspecialchars(!empty($teacher['profile_photo']) ? $teacher['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($teacher['full_name']).'&background=0D6EFD&color=fff') ?>" alt="<?= htmlspecialchars($teacher['full_name']) ?>" width="40" height="40" class="rounded-circle border object-fit-cover shadow-sm">
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars(($teacher['title'] ?? '') . ' ' . $teacher['full_name']) ?></h6>
                                                        <span class="text-muted extra-small"><i class="bi bi-person-badge me-1"></i>ID: <?= htmlspecialchars(!empty($teacher['username']) ? $teacher['username'] : 'INS-' . str_pad($teacher['id'], 4, '0', STR_PAD_LEFT)) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="fw-semibold text-dark d-block extra-small"><i class="bi bi-envelope me-1 text-primary"></i><?= htmlspecialchars($teacher['email']) ?></span>
                                                    <span class="text-muted extra-small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars(!empty($teacher['phone']) ? $teacher['phone'] : 'N/A') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <?php if (!empty($teacher['course_code'])): ?>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 extra-small fw-semibold mb-1"><?= htmlspecialchars($teacher['course_code']) ?></span>
                                                    <?php endif; ?>
                                                    <span class="text-dark fw-bold extra-small d-block"><?= htmlspecialchars(!empty($teacher['course_name']) ? $teacher['course_name'] : (!empty($teacher['assigned_module']) ? $teacher['assigned_module'] : 'Not Assigned')) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border extra-small"><?= htmlspecialchars(!empty($teacher['department']) ? $teacher['department'] : 'General') ?></span>
                                            </td>
                                            <td>
                                                <?php if (strtolower($teacher['status']) === 'active'): ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5 extra-small fw-semibold">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3 py-1.5 extra-small fw-semibold">
                                                        Inactive
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="edit-teacher.php?id=<?= $teacher['id'] ?>" class="btn btn-sm btn-outline-primary rounded-3 px-2.5 py-1 me-1">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1 btn-delete-teacher" data-id="<?= $teacher['id'] ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-person-x fs-2 d-block mb-2 text-secondary"></i>
                                            No instructors found matching your selection.
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

    <!-- Quick Add Instructor Modal -->
    <div class="modal fade" id="addInstructorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom p-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Quick Add Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickAddTeacherForm">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Full Name *</label>
                            <input type="text" name="full_name" class="form-control rounded-3 py-2 fs-6" placeholder="e.g. Dr. Kasun Silva" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Email Address *</label>
                            <input type="email" name="email" class="form-control rounded-3 py-2 fs-6" placeholder="e.g. kasun@edumart.ac.lk" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Phone Number</label>
                                <input type="text" name="phone" class="form-control rounded-3 py-2 fs-6" placeholder="07x xxx xxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Assign Module</label>
                                <select name="assigned_module" class="form-select rounded-3 py-2 fs-6">
                                    <option value="">Choose Module...</option>
                                    <?php 
                                    if($coursesQuery && $coursesQuery->num_rows > 0):
                                        $coursesQuery->data_seek(0);
                                        while ($course = $coursesQuery->fetch_assoc()): 
                                    ?>
                                        <option value="<?= $course['id'] ?>"><?= htmlspecialchars(($course['course_code'] ? $course['course_code'] . ' - ' : '') . $course['course_name']) ?></option>
                                    <?php 
                                        endwhile; 
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3">
                        <button type="button" class="btn btn-light rounded-pill px-4 btn-sm fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">Save Instructor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Quick Add Form Submit
            $('#quickAddTeacherForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'ajax/instructor.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'Registered!', text: res.message, timer: 1500, showConfirmButton: false });
                            setTimeout(function() { location.reload(); }, 1500);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                        }
                    }
                });
            });

            // Delete Teacher Action
            $('.btn-delete-teacher').on('click', function() {
                let teacherId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This instructor will be removed permanently!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'ajax/instructor.php',
                            type: 'POST',
                            data: { action: 'delete', id: teacherId },
                            dataType: 'json',
                            success: function(res) {
                                if (res.status === 'success') {
                                    Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                                    setTimeout(function() { location.reload(); }, 1500);
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>