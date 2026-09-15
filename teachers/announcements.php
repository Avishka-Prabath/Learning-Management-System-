<?php
// 1. Include core config & classes (session handling happens automatically)
require_once 'class/include.php';

// 2. Create an Announcement object and fetch data
$announcementObj = new Announcement();
$result = method_exists($announcementObj, 'getTeacherAnnouncements') ? $announcementObj->getTeacherAnnouncements() : $announcementObj->all()

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Announcements - Teacher Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar -->
        <?php if (file_exists('instructor-sidebar.php')) include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <!-- Topbar -->
            <?php if (file_exists('topbar.php')) include('topbar.php'); ?>

            <div class="p-4">
                
                <!-- Page Header -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Admin Announcements</h4>
                        <p class="text-muted small mb-0">Official notices and updates published by System Administration.</p>
                    </div>
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 rounded-pill extra-small">
                            <i class="bi bi-shield-check me-1"></i> Admin Broadcast Feed
                        </span>
                    </div>
                </div>

                <!-- Announcements Feed List -->
                <div class="d-flex flex-column gap-3">

                    <?php if (!empty($result)): ?>
                    <?php foreach ($result as $row): ?>
                            
                            <?php 
                                // Target Audience Check (Support 'instructors', 'Instructors Only', 'all', 'All')
                                $isInstructorOnly = in_array(strtolower($row['target_audience']), ['instructors', 'instructors only']);
                                // Content Field Check (Support both 'content' and 'message' column names)
                                $announcementText = isset($row['content']) ? $row['content'] : (isset($row['message']) ? $row['message'] : '');
                                $postedBy = isset($row['posted_by']) ? $row['posted_by'] : 'System Administration';
                            ?>

                            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden border-start border-4 <?= $isInstructorOnly ? 'border-warning' : 'border-primary' ?>">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($isInstructorOnly): ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle rounded-pill extra-small text-dark">
                                                    <i class="bi bi-person-badge-fill me-1"></i> Instructors Only
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill extra-small">
                                                    <i class="bi bi-globe me-1"></i> General Notice
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-muted extra-small">
                                            <i class="bi bi-clock me-1"></i> <?= date('M d, Y - h:i A', strtotime($row['created_at'])) ?>
                                        </span>
                                    </div>

                                    <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($row['title']) ?></h5>
                                    <p class="text-secondary small mb-3">
                                        <?= nl2br(htmlspecialchars(mb_strimwidth($announcementText, 0, 180, "..."))) ?>
                                    </p>

                                    <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                        <span class="extra-small text-muted">
                                            <i class="bi bi-person-circle me-1"></i> Posted by: <strong><?= htmlspecialchars($postedBy) ?></strong>
                                        </span>
                                        <button class="btn btn-link text-primary text-decoration-none extra-small fw-semibold p-0" data-bs-toggle="modal" data-bs-target="#announcementModal<?= $row['id'] ?>">
                                            Read Full Announcement →
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal: Full Announcement View -->
                            <div class="modal fade" id="announcementModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <div>
                                                <?php if ($isInstructorOnly): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill extra-small mb-1">Instructors Only</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary rounded-pill extra-small mb-1">General Notice</span>
                                                <?php endif; ?>
                                                <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($row['title']) ?></h5>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body py-4">
                                            <p class="text-muted extra-small mb-3">
                                                <i class="bi bi-person me-1"></i> Posted by <?= htmlspecialchars($postedBy) ?> • <?= date('F d, Y \a\t h:i A', strtotime($row['created_at'])) ?>
                                            </p>
                                            <div class="text-dark small lh-base">
                                                <?= nl2br(htmlspecialchars($announcementText)) ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
<?php else: ?>
    <!-- Empty State -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
        <i class="bi bi-megaphone-fill fs-1 text-muted opacity-50 mb-2"></i>
        <h6 class="fw-bold text-dark">No Announcements Yet</h6>
        <p class="text-secondary small mb-0">There are currently no active notices published by Administration.</p>
    </div>
<?php endif; ?>

                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>