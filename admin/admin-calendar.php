<?php
session_start();
require_once __DIR__ . '/class/include.php';

$calendarObj = new Calendar();
$schedules = $calendarObj->all();

// Get instructors from the database
$instructorObj = new Instructor();
$instructors = $instructorObj->all();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Calendar - EduMart Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Select2 Searchable Dropdown CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light m-0 p-0 overflow-x-hidden">
    <!-- Full Width Layout Wrapper -->
    <div class="d-flex w-100 min-vh-100">
        
        <!-- Sidebar -->
        <?php include 'admin-sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="main-content flex-grow-1 bg-light p-4 min-vh-100">
            
            <!-- Top Header Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm border border-light-subtle">
                <div class="position-relative w-50">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" class="form-control ps-5 rounded-pill border-0 bg-light" placeholder="Search students, courses or system logs...">
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill border border-danger-subtle fw-semibold">
                        <i class="bi bi-shield-check me-1"></i> Admin Access Mode
                    </span>
                    <div class="d-flex align-items-center gap-2 ms-2">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">AU</div>
                        <div class="text-start d-none d-md-block">
                            <div class="fw-bold small text-dark">System Administrator</div>
                            <div class="text-muted" style="font-size: 11px;">admin@edumart.ac.lk</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Title Header -->
            <div class="mb-4">
                <h2 class="fw-bold text-dark mb-1">Calendar Management</h2>
                <p class="text-secondary small mb-0">Create schedule slots and assign instructors for live lectures and exams.</p>
            </div>

            <!-- Content Grid -->
            <div class="row g-4">
                <!-- Left Column: Add Schedule Form -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <i class="bi bi-plus-circle text-primary fs-5"></i>
                            <h5 class="fw-bold mb-0 text-dark">Create Schedule Slot</h5>
                        </div>

                        <form id="schedule-form" novalidate>
                            <div class="mb-3">
                                <label class="form-label text-uppercase text-muted fw-bold style-label">EVENT TITLE *</label>
                                <input type="text" class="form-control rounded-3 py-2 border-light-subtle" id="title" name="title" placeholder="e.g. Web Dev Live Session" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase text-muted fw-bold style-label">EVENT TYPE *</label>
                                <select class="form-select rounded-3 py-2 border-light-subtle" id="type" name="type" required>
                                    <option value="Live Class">Live Class</option>
                                    <option value="Assignment">Assignment</option>
                                    <option value="Quiz/Exam">Quiz / Exam</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase text-muted fw-bold style-label">EVENT DATE *</label>
                                <input type="date" class="form-control rounded-3 py-2 border-light-subtle" id="event_date" name="event_date" required>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label text-uppercase text-muted fw-bold style-label">START TIME</label>
                                    <input type="time" class="form-control rounded-3 py-2 border-light-subtle" id="start_time" name="start_time">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-uppercase text-muted fw-bold style-label">END TIME</label>
                                    <input type="time" class="form-control rounded-3 py-2 border-light-subtle" id="end_time" name="end_time">
                                </div>
                            </div>

                            <!-- Searchable Instructor Selection -->
                            <div class="mb-4">
                                <label class="form-label text-uppercase text-muted fw-bold style-label">ASSIGN INSTRUCTOR *</label>
                                <select class="form-select select2-instructor" id="instructor_id" name="instructor_id" required>
                                    <option value="">Search & Select Instructor...</option>
                                    <?php if(!empty($instructors)): ?>
                                        <?php foreach ($instructors as $inst): ?>
                                            <?php 
                                                $inst_name = !empty($inst['full_name']) ? trim(($inst['title'] ?? '') . ' ' . $inst['full_name']) : ($inst['name'] ?? 'Instructor');
                                            ?>
                                            <option value="<?= $inst['id'] ?>">
                                                <?= htmlspecialchars($inst_name) ?> (<?= htmlspecialchars($inst['email'] ?? '') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm">
                                <i class="bi bi-calendar-check me-2"></i>Create Schedule
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Scheduled Events List -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="min-height: 520px;">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <i class="bi bi-list-task text-primary fs-5"></i>
                            <h5 class="fw-bold mb-0 text-dark">Scheduled Events</h5>
                        </div>

                        <div id="schedule-list">
                            <?php if (empty($schedules)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x display-5 d-block mb-3 opacity-50"></i>
                                    <p class="mb-0 fw-medium">No schedules available. Add items using the form.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($schedules as $item): ?>
                                    <div class="card border border-light-subtle rounded-3 p-3 mb-3 bg-light-subtle">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="badge bg-primary-subtle text-primary mb-2 px-2 py-1 rounded-2">
                                                    <?= htmlspecialchars($item['type']) ?>
                                                </span>
                                                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($item['title']) ?></h6>
                                                <p class="text-muted small mb-1">
                                                    <i class="bi bi-calendar3 me-1"></i> <?= $item['event_date'] ?>
                                                    <?php if(!empty($item['start_time'])): ?>
                                                        | <i class="bi bi-clock me-1"></i> <?= $item['start_time'] ?> - <?= $item['end_time'] ?>
                                                    <?php endif; ?>
                                                </p>
                                                <?php if(!empty($item['instructor_info']) || !empty($item['instructor_name'])): ?>
                                                    <p class="text-secondary small mb-0"><i class="bi bi-person me-1"></i> <?= htmlspecialchars($item['instructor_name'] ?? $item['instructor_info']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <button class="btn btn-outline-danger btn-sm border-0 delete-schedule" data-id="<?= $item['id'] ?>">
                                                <i class="bi bi-trash fs-6"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        body {
            background-color: #f8f9fa !important;
        }
        .style-label {
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            border-radius: 0.375rem;
            border-color: #dee2e6;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 Searchable Dropdown JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Turn the instructor dropdown into a searchable dropdown
            $('.select2-instructor').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Search & Select Instructor...'
            });
        });
    </script>
    <script src="ajax/js/calendar.js"></script>
</body>
</html>