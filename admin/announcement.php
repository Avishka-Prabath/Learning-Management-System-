<?php
session_start();
include_once('../db.php');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Handle Delete Announcement
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM announcements WHERE id = $delete_id");
    header("Location: announcement.php");
    exit();
}

// Fetch Announcements from Database
$noticesResult = $conn->query("SELECT * FROM announcements ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Announcements - EduMart Admin</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
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
                        <h3 class="fw-bold text-dark mb-1">System Announcements</h3>
                        <p class="text-secondary small mb-0">Broadcast notices specifically to instructors, students, or all users.</p>
                    </div>
                </div>

                <div class="row g-4">
                    
                    <!-- LEFT COLUMN: POST NEW NOTICE FORM -->
                    <div class="col-lg-5">
                        <div class="card card-custom p-4">
                            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-megaphone-fill text-primary"></i> Post New Notice
                            </h5>

                            <!-- Form ID: addAnnouncementForm -->
                            <form id="addAnnouncementForm" novalidate>
                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Target Audience *</label>
                                    <!-- Select ID: target_audience -->
                                    <select name="target_audience" id="target_audience" class="form-select rounded-3 py-2 fs-6">
                                        <option value="all" selected>All Users (Students & Instructors)</option>
                                        <option value="students">Students Only</option>
                                        <option value="instructors">Instructors Only</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Notice Title *</label>
                                    <!-- Input ID: title -->
                                    <input type="text" name="title" id="title" class="form-control rounded-3 py-2 fs-6" placeholder="e.g. Semester Exam Schedule Released">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Message Content *</label>
                                    <!-- Textarea ID: message -->
                                    <textarea name="message" id="message" class="form-control rounded-3 py-2 fs-6" rows="5" placeholder="Type notice description here..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold w-100 shadow-sm d-flex align-items-center justify-content-center gap-2 extra-small">
                                    <i class="bi bi-send-fill"></i> Publish Announcement
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: PUBLISHED NOTICES LIST -->
                    <div class="col-lg-7">
                        <div class="card card-custom p-4">
                            <h5 class="fw-bold text-dark mb-3">Published Notices</h5>

                            <div class="notice-list-container">
                                <?php if ($noticesResult && $noticesResult->num_rows > 0): ?>
                                    <?php while ($notice = $noticesResult->fetch_assoc()): ?>
                                        <div class="p-3.5 bg-light rounded-4 mb-3 border position-relative">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                
                                                <!-- Target Audience Badge -->
                                                <?php if ($notice['target_audience'] === 'students'): ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-semibold">
                                                        <i class="bi bi-people-fill me-1"></i> Students Only
                                                    </span>
                                                <?php elseif ($notice['target_audience'] === 'instructors'): ?>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 extra-small fw-semibold text-dark">
                                                        <i class="bi bi-person-badge-fill me-1"></i> Instructors Only
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 extra-small fw-semibold">
                                                        <i class="bi bi-globe me-1"></i> All Users
                                                    </span>
                                                <?php endif; ?>

                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted extra-small">
                                                        <?= date('M d, Y', strtotime($notice['created_at'])) ?>
                                                    </span>
                                                    <button type="button" class="btn btn-light btn-sm rounded-circle p-1 text-danger extra-small shadow-sm delete-announcement" data-id="<?= $notice['id'] ?>" title="Delete Notice">
                                                            <i class="bi bi-trash pe-none"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($notice['title']) ?></h6>
                                            <p class="text-secondary small mb-0 leading-relaxed"><?= nl2br(htmlspecialchars($notice['message'] ?? $notice['content'] ?? '')) ?></p>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center text-muted py-5">
                                        <i class="bi bi-megaphone-fill fs-2 text-secondary opacity-50 d-block mb-2"></i>
                                        <p class="mb-0">No announcements published yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Path එක ajax/js/ Folder එකට සකස් කරන ලදී -->
    <script src="ajax/js/announcements.js"></script>
</body>
</html>