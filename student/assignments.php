<?php
// Root folder එකේ ඇති db.php include කිරීම
require_once '../db.php'; 

// Fetch Active Assignments from Database
$query = "SELECT * FROM assignments WHERE status = 'Active' OR status IS NULL ORDER BY deadline_date ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student - Assignments & Quizzes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <div class="d-flex">
        <?php if (file_exists('navbar.php')) include('navbar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <?php if (file_exists('topbar.php')) include('topbar.php'); ?>

            <div class="p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="fw-bold mb-1 text-danger">
                            <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Assignments & Quizzes
                        </h4>
                        <p class="text-muted small mb-0">View your deadlines, submit coursework, and access online quizzes.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex flex-column gap-3">

                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): 
                                $month = strtoupper(date('M', strtotime($row['deadline_date'])));
                                $day = date('d', strtotime($row['deadline_date']));
                                $is_exam = ($row['type'] == 'Exam');
                            ?>
                                <div class="p-3 border rounded-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <!-- Date Badge -->
                                        <div class="text-center p-2 rounded-3 <?= $is_exam ? 'bg-warning bg-opacity-20' : 'bg-danger bg-opacity-10' ?>" style="min-width: 65px;">
                                            <span class="fw-bold <?= $is_exam ? 'text-dark' : 'text-danger' ?> small d-block"><?= $month ?></span>
                                            <span class="fs-4 fw-bold <?= $is_exam ? 'text-dark' : 'text-danger' ?> lh-1"><?= $day ?></span>
                                        </div>

                                        <!-- Details -->
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <?php if ($is_exam): ?>
                                                    <span class="badge bg-warning text-dark px-2.5 py-1 rounded-2"><i class="bi bi-clock-history me-1"></i> Online Exam</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger text-white px-2.5 py-1 rounded-2"><i class="bi bi-file-earmark-check me-1"></i> Assignment Due</span>
                                                <?php endif; ?>
                                            </div>
                                            <h6 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($row['title']) ?></h6>
                                            <div class="text-muted small d-flex flex-wrap gap-3 align-items-center">
                                                <span><i class="bi bi-clock me-1 text-danger"></i> Deadline: <?= date('h:i A', strtotime($row['deadline_time'])) ?></span>
                                                <span><i class="bi bi-journal-text me-1 text-danger"></i> <?= htmlspecialchars($row['course_name']) ?></span>
                                                <?php if (!empty($row['file_path'])): ?>
                                                    <a href="../uploads/<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="text-decoration-none text-primary fw-semibold">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i> Download Brief
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Submit/Action Button -->
                                    <div class="text-end">
                                        <?php if ($is_exam): ?>
                                            <button class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2" disabled>
                                                <i class="bi bi-lock-fill"></i> Locked (Scheduled)
                                            </button>
                                        <?php else: ?>
                                            <a href="course.php?id=<?= $row['id'] ?>" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold text-white d-inline-flex align-items-center gap-2 shadow-sm">
                                                <i class="bi bi-upload"></i> Submit Assignment
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-journal-x text-muted fs-1 d-block mb-2"></i>
                                <p class="text-muted mb-0">No assignments or quizzes published yet.</p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>