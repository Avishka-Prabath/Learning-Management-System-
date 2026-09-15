<?php
session_start();
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../db.php';

$module_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Fetch Module Details
$stmt = $conn->prepare("SELECT * FROM modules WHERE id = ?");
$stmt->bind_param("i", $module_id);
$stmt->execute();
$module = $stmt->get_result()->fetch_assoc();

// Fetch Materials Uploaded for this Module
$matQuery = "SELECT * FROM materials WHERE module_id = ? ORDER BY id DESC";
$stmtMat = $conn->prepare($matQuery);
$stmtMat->bind_param("i", $module_id);
$stmtMat->execute();
$materials = $stmtMat->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($module['module_name'] ?? 'Module View') ?> - Teacher Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <div class="d-flex">
        <?php include('instructor-sidebar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <?php include('topbar.php'); ?>

            <div class="p-4">
                
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="modules.php" class="btn btn-light btn-sm rounded-pill mb-2 px-3 text-muted fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i> Back to My Modules
                        </a>
                        <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($module['module_code'] ?? 'CS') ?> - <?= htmlspecialchars($module['module_name'] ?? 'Subject') ?></h4>
                        <p class="text-muted small mb-0">Upload lecture slides, tutorial notes, and reference links for students.</p>
                    </div>

                    <a href="upload-material.php?module_id=<?= $module_id ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold btn-sm shadow-sm">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Lecture Note
                    </a>
                </div>

                <!-- Lecture Resources List Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-file-earmark-pdf-fill me-2 text-primary"></i>Uploaded Lecture Notes & Resources
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light extra-small text-uppercase text-muted">
                                <tr>
                                    <th>Title / Topic</th>
                                    <th>Published Date</th>
                                    <th>Attachment File</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($materials && $materials->num_rows > 0): ?>
                                    <?php while ($mat = $materials->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><?= htmlspecialchars($mat['title']) ?></td>
                                            <td class="small text-muted"><?= htmlspecialchars($mat['publish_date']) ?></td>
                                            <td>
                                                <a href="../uploads/<?= htmlspecialchars($mat['file_path']) ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 extra-small">
                                                    <i class="bi bi-download me-1"></i> Download Note
                                                </a>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-light btn-sm text-danger rounded-circle"><i class="bi bi-trash-fill"></i></button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No lecture notes uploaded yet for this subject.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>