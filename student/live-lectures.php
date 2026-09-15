<?php
// Database Connection
require_once('../db.php'); // ඔබේ DB Connection file එකේ නම මෙතැනට යොදන්න

// Fetch classes for students
$query = "SELECT * FROM live_classes WHERE status != 'Completed' ORDER BY class_date ASC, start_time ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Online Lectures - EduMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <div class="d-flex">
        <?php include('navbar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <?php include('topbar.php'); ?>

            <div class="p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-camera-video-fill me-2"></i>Live Online Lectures</h4>
                        <p class="text-muted small mb-0">Join your upcoming live interactive sessions and virtual classes.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex flex-column gap-3">

                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): 
                                $month = strtoupper(date('M', strtotime($row['class_date'])));
                                $day = date('d', strtotime($row['class_date']));
                            ?>
                                <div class="p-3 border rounded-4 event-card event-lecture d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="date-box bg-primary bg-opacity-10 border border-primary-subtle text-center">
                                            <span class="fw-bold text-primary extra-small text-uppercase"><?php echo $month; ?></span>
                                            <span class="fs-4 fw-bold text-primary lh-1"><?php echo $day; ?></span>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-primary text-white px-2.5 py-1 rounded-2"><i class="bi bi-camera-video me-1"></i> Live Class</span>
                                                <?php if ($row['status'] == 'Ready'): ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill extra-small"><i class="bi bi-record-fill me-1"></i> Starting Soon</span>
                                                <?php endif; ?>
                                            </div>
                                            <h6 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($row['class_title']); ?></h6>
                                            <div class="text-muted small d-flex flex-wrap gap-3 align-items-center">
                                                <span><i class="bi bi-clock me-1 text-primary"></i> <?php echo date('h:i A', strtotime($row['start_time'])) . ' - ' . date('h:i A', strtotime($row['end_time'])); ?></span>
                                                <span><i class="bi bi-person-badge me-1 text-primary"></i> <?php echo htmlspecialchars($row['instructor_name']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <?php if ($row['status'] == 'Ready'): ?>
                                            <a href="<?php echo htmlspecialchars($row['meeting_link']); ?>" target="_blank" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold text-white d-inline-flex align-items-center gap-2 shadow-sm">
                                                <i class="bi bi-box-arrow-up-right"></i> Join Online Class
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo htmlspecialchars($row['meeting_link']); ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                                                <i class="bi bi-camera-video"></i> Lecture Link
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center text-muted py-4 mb-0">No live classes scheduled at the moment.</p>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>