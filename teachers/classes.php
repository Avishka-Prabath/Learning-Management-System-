<?php
session_start();

// Redirect to login.php if the teacher is not logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../db.php';

// Get the logged-in teacher's ID and name
$current_teacher_id   = intval($_SESSION['teacher_id'] ?? 0);
$current_teacher_name = $_SESSION['teacher_name'] ?? 'Instructor';
$swal_script = "";

// 1. Logic for a teacher scheduling a new live class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_live_class') {
    $module_id    = intval($_POST['module_id'] ?? 0);
    $title        = trim($_POST['title'] ?? '');
    $event_date   = trim($_POST['event_date'] ?? '');
    $start_time   = trim($_POST['start_time'] ?? '');
    $end_time     = trim($_POST['end_time'] ?? '');
    $meeting_link = trim($_POST['meeting_link'] ?? '');

    if ($module_id > 0 && !empty($title) && !empty($event_date) && !empty($meeting_link)) {
        // Get details (name / code) for the module
        $modStmt = $conn->prepare("SELECT module_name, module_code FROM modules WHERE id = ?");
        $modStmt->bind_param("i", $module_id);
        $modStmt->execute();
        $modData = $modStmt->get_result()->fetch_assoc();
        $modStmt->close();

        $full_title = ($modData ? '[' . $modData['module_code'] . '] ' : '') . $title;

        // 1. Insert into schedules table (Cleaned: No course_batch column)
        $insStmt = $conn->prepare("INSERT INTO schedules (title, type, event_date, start_time, end_time, meeting_link, instructor_id) VALUES (?, 'Live Class', ?, ?, ?, ?, ?)");
        $insStmt->bind_param("sssssi", $full_title, $event_date, $start_time, $end_time, $meeting_link, $current_teacher_id);
        $insStmt->execute();
        $insStmt->close();

        // 2. Insert into live_classes table for Student View
        $insLive = $conn->prepare("INSERT INTO live_classes (module_id, class_title, class_date, start_time, end_time, meeting_link, instructor_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insLive->bind_param("issssss", $module_id, $title, $event_date, $start_time, $end_time, $meeting_link, $current_teacher_name);
        
        if ($insLive->execute()) {
            $swal_script = "Swal.fire({
                icon: 'success',
                title: 'Scheduled Successfully!',
                text: 'Live Class created and published to the student module section.',
                confirmButtonColor: '#0d6efd'
            });";
        } else {
            $swal_script = "Swal.fire({
                icon: 'error',
                title: 'Execution Error!',
                text: 'Database error: " . addslashes($conn->error) . "',
                confirmButtonColor: '#d33'
            });";
        }
        $insLive->close();

    } else {
        $swal_script = "Swal.fire({
            icon: 'warning',
            title: 'Incomplete Form!',
            text: 'Please fill in all required fields.',
            confirmButtonColor: '#ffc107'
        });";
    }
}

// 2. Backend logic that saves when the teacher updates the link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_meeting_link') {
    $schedule_id  = intval($_POST['schedule_id']);
    $meeting_link = trim($_POST['meeting_link']);

    $updateStmt = $conn->prepare("UPDATE schedules SET meeting_link = ? WHERE id = ? AND instructor_id = ?");
    $updateStmt->bind_param("sii", $meeting_link, $schedule_id, $current_teacher_id);
    $updateStmt->execute();
    $updateStmt->close();

    $swal_script = "Swal.fire({
        icon: 'success',
        title: 'Link Updated!',
        text: 'Meeting link updated successfully.',
        confirmButtonColor: '#0d6efd'
    });";
}

// 3. Backend logic for deleting a live class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_live_class') {
    $schedule_id = intval($_POST['schedule_id']);

    // Get details from the schedules table
    $fetchStmt = $conn->prepare("SELECT title, event_date FROM schedules WHERE id = ? AND (instructor_id = ? OR instructor_id = 0)");
    $fetchStmt->bind_param("ii", $schedule_id, $current_teacher_id);
    $fetchStmt->execute();
    $classInfo = $fetchStmt->get_result()->fetch_assoc();
    $fetchStmt->close();

    if ($classInfo) {
        // 1. Delete from schedules table
        $delSched = $conn->prepare("DELETE FROM schedules WHERE id = ? AND (instructor_id = ? OR instructor_id = 0)");
        $delSched->bind_param("ii", $schedule_id, $current_teacher_id);
        $delSched->execute();
        $delSched->close();

        // 2. Delete matching class from live_classes table
        $delLive = $conn->prepare("DELETE FROM live_classes WHERE class_date = ? AND meeting_link IN (SELECT meeting_link FROM schedules WHERE id = ?)");
        $delLive->bind_param("si", $classInfo['event_date'], $schedule_id);
        $delLive->execute();
        $delLive->close();

        $swal_script = "Swal.fire({
            icon: 'success',
            title: 'Deleted Successfully!',
            text: 'The scheduled live class has been removed.',
            confirmButtonColor: '#0d6efd'
        });";
    }
}

// 4. Fetch UNIQUE Software Engineering Modules
$modulesQuery = "
    SELECT MIN(m.id) AS id, m.module_code, m.module_name, m.semester, c.course_name 
    FROM modules m
    LEFT JOIN courses c ON m.course_id = c.id
    WHERE m.course_id = 3 OR m.module_code LIKE 'SE%'
    GROUP BY m.module_code
    ORDER BY m.semester ASC, m.module_code ASC
";
$mStmt = $conn->prepare($modulesQuery);
$mStmt->execute();
$teacherModules = $mStmt->get_result();

// 5. Fetch Scheduled Live Classes
$query = "SELECT * FROM schedules 
          WHERE (instructor_id = ? OR instructor_id = 0)
            AND (type = 'Live Class' OR type LIKE '%Live%')
          ORDER BY event_date DESC, start_time ASC";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $current_teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Live Classes - Teacher Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .extra-small { font-size: 0.75rem; }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex">
        <?php include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <?php include('topbar.php'); ?>

            <div class="p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Scheduled Live Classes</h4>
                        <p class="text-muted small mb-0">Create new live lectures for your Software Engineering modules.</p>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <!-- Create New Live Class Trigger Button -->
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm extra-small" data-bs-toggle="modal" data-bs-target="#createClassModal">
                            <i class="bi bi-plus-circle-fill me-1"></i> Schedule New Live Class
                        </button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Class Title & Subject</th>
                                    <th>Event Type</th>
                                    <th>Date & Time</th>
                                    <th>Meeting Link</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): 
                                        $class_title  = $row['title'] ?? $row['class_title'] ?? 'Live Session';
                                        $event_date   = $row['event_date'] ?? $row['class_date'] ?? date('Y-m-d');
                                        $start_time   = !empty($row['start_time']) ? date('h:i A', strtotime($row['start_time'])) : 'TBD';
                                        $end_time     = !empty($row['end_time']) ? date('h:i A', strtotime($row['end_time'])) : '';
                                        $meeting_link = $row['meeting_link'] ?? '';
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($class_title) ?></div>
                                                <span class="text-muted extra-small"><i class="bi bi-person-badge me-1"></i>Instructor Live Class</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">
                                                    <i class="bi bi-camera-video me-1"></i><?= htmlspecialchars($row['type'] ?? 'Live Class') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-dark"><?= date('M d, Y', strtotime($event_date)) ?></div>
                                                <span class="text-muted extra-small"><i class="bi bi-clock me-1"></i><?= $start_time ?> <?= $end_time ? ' - ' . $end_time : '' ?></span>
                                            </td>
                                            <td>
                                                <?php if (!empty($meeting_link)): ?>
                                                    <a href="<?= htmlspecialchars($meeting_link) ?>" target="_blank" class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill text-decoration-none px-3 py-1.5 extra-small">
                                                        <i class="bi bi-link-45deg me-1"></i> Link Attached
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle rounded-pill px-3 py-1.5 extra-small">
                                                        <i class="bi bi-exclamation-circle me-1"></i> Pending Link
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm rounded-pill px-3 me-1 btn-add-link" 
                                                        data-id="<?= $row['id'] ?>"
                                                        data-title="<?= htmlspecialchars($class_title) ?>"
                                                        data-link="<?= htmlspecialchars($meeting_link) ?>">
                                                    <i class="bi bi-link-45deg me-1"></i> <?= !empty($meeting_link) ? 'Edit Link' : 'Add Link' ?>
                                                </button>

                                                <?php if (!empty($meeting_link)): ?>
                                                    <a href="<?= htmlspecialchars($meeting_link) ?>" target="_blank" class="btn btn-success btn-sm rounded-pill fw-semibold px-3 shadow-sm me-1">
                                                        <i class="bi bi-play-circle-fill me-1"></i> Start Class
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-light btn-sm rounded-pill text-muted px-3 border me-1" disabled>
                                                        <i class="bi bi-lock-fill me-1"></i> Start Class
                                                    </button>
                                                <?php endif; ?>

                                                <!-- Delete Class Button -->
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 btn-delete-class" data-id="<?= $row['id'] ?>">
                                                    <i class="bi bi-trash-fill me-1"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-calendar-x fs-1 opacity-50 d-block mb-2"></i>
                                            <p class="mb-0 fw-medium">No live class schedules found for your account.</p>
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

    <!-- Hidden Form for Delete Operation -->
    <form id="deleteClassForm" action="classes.php" method="POST" style="display:none;">
        <input type="hidden" name="action" value="delete_live_class">
        <input type="hidden" name="schedule_id" id="delete_schedule_id">
    </form>

    <!-- 1. CREATE NEW LIVE CLASS MODAL -->
    <div class="modal fade" id="createClassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-camera-video-fill text-primary"></i> Schedule New Live Session
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createClassForm" action="classes.php" method="POST">
                    <input type="hidden" name="action" value="create_live_class">

                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Select Software Engineering Module *</label>
                            <select name="module_id" id="module_id" class="form-select rounded-3 py-2">
                                <option value="" selected disabled>-- Select Module Subject --</option>
                                <?php if ($teacherModules && $teacherModules->num_rows > 0): ?>
                                    <?php while ($m = $teacherModules->fetch_assoc()): ?>
                                        <option value="<?= $m['id'] ?>">
                                            <?= htmlspecialchars($m['module_code']) ?> - <?= htmlspecialchars($m['module_name']) ?> (<?= htmlspecialchars($m['semester'] ?? 'SE') ?>)
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Session Title *</label>
                            <input type="text" name="title" id="class_title" class="form-control rounded-3 py-2" placeholder="e.g. Lecture 05 - Software Systems Overview">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Class Date *</label>
                                <input type="date" name="event_date" id="event_date" class="form-control rounded-3 py-2" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">Start Time *</label>
                                <input type="time" name="start_time" id="start_time" class="form-control rounded-3 py-2" value="09:00">
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted">End Time *</label>
                                <input type="time" name="end_time" id="end_time" class="form-control rounded-3 py-2" value="11:00">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Meeting URL (Zoom / Teams / Meet) *</label>
                            <input type="url" name="meeting_link" id="meeting_link" class="form-control rounded-3 py-2" placeholder="https://zoom.us/j/1234567890">
                        </div>
                    </div>

                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold extra-small" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm extra-small">
                            <i class="bi bi-calendar-check me-1"></i> Schedule Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. EDIT MEETING LINK MODAL -->
    <div class="modal fade" id="meetingLinkModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-camera-video text-primary"></i> Add / Edit Live Session Link
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editLinkForm" action="classes.php" method="POST">
                    <input type="hidden" name="action" value="update_meeting_link">
                    <input type="hidden" name="schedule_id" id="modal_schedule_id">

                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Class Title</label>
                            <input type="text" id="modal_class_title" class="form-control bg-light rounded-3 py-2" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Zoom / Microsoft Teams / Google Meet Link *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-link-45deg text-muted"></i></span>
                                <input type="url" name="meeting_link" id="modal_meeting_link" class="form-control border-start-0 ps-0 rounded-end py-2" placeholder="https://zoom.us/j/1234567890">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold extra-small" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm extra-small">
                            <i class="bi bi-check-lg me-1"></i> Save Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Edit Link Trigger
            $('.btn-add-link').on('click', function() {
                let id    = $(this).data('id');
                let title = $(this).data('title');
                let link  = $(this).data('link');

                $('#modal_schedule_id').val(id);
                $('#modal_class_title').val(title);
                $('#modal_meeting_link').val(link);

                $('#meetingLinkModal').modal('show');
            });

            // Delete Class Trigger
            $('.btn-delete-class').on('click', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This live class schedule will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete_schedule_id').val(id);
                        $('#deleteClassForm').submit();
                    }
                });
            });

            // Form Validation
            $('#createClassForm').on('submit', function(e) {
                let moduleId  = $('#module_id').val();
                let title     = $.trim($('#class_title').val());
                let date      = $('#event_date').val();
                let startTime = $('#start_time').val();
                let endTime   = $('#end_time').val();
                let link      = $.trim($('#meeting_link').val());

                if (!moduleId) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Select a Module',
                        text: 'Please select a Subject/Module for this live class.',
                        confirmButtonColor: '#0d6efd'
                    });
                    return false;
                }

                if (title === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Title',
                        text: 'Please enter a session title for the class.',
                        confirmButtonColor: '#0d6efd'
                    });
                    return false;
                }

                let today = new Date().toISOString().split('T')[0];
                if (date < today) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date',
                        text: 'You cannot schedule a live class for a past date.',
                        confirmButtonColor: '#0d6efd'
                    });
                    return false;
                }

                if (startTime >= endTime) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Time Range',
                        text: 'End time must be later than the start time.',
                        confirmButtonColor: '#0d6efd'
                    });
                    return false;
                }

                if (link === '' || !link.startsWith('http')) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Meeting URL',
                        text: 'Please enter a valid URL starting with http:// or https://',
                        confirmButtonColor: '#0d6efd'
                    });
                    return false;
                }
            });
        });

        <?php if (!empty($swal_script)): ?>
            <?= $swal_script ?>
        <?php endif; ?>
    </script>
</body>
</html>