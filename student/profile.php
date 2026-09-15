<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db.php';

// Check whether the student is logged in
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$student_pk_id = intval($_SESSION['student_id'] ?? 0);
$swal_script = "";

// Database Table Auto-Migration
$checkCol = $conn->query("SHOW COLUMNS FROM students LIKE 'profile_photo'");
if ($checkCol && $checkCol->num_rows == 0) {
    $conn->query("ALTER TABLE students ADD COLUMN profile_photo VARCHAR(255) NULL");
}

$conn->query("CREATE TABLE IF NOT EXISTS student_payment_slips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    slip_title VARCHAR(150) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending Review',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// 1. Personal Info Update Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name  = trim($_POST['first_name'] ?? '');
    $last_name   = trim($_POST['last_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $full_name   = trim($first_name . ' ' . $last_name);

    $upStmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ?, full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $upStmt->bind_param("ssssssi", $first_name, $last_name, $full_name, $email, $phone, $address, $student_pk_id);

    if ($upStmt->execute()) {
        $_SESSION['student_name'] = $full_name;
        $swal_script = "Swal.fire({
            icon: 'success',
            title: 'Profile Updated!',
            text: 'Your personal information has been saved successfully.',
            confirmButtonColor: '#0d6efd'
        });";
    } else {
        $swal_script = "Swal.fire({
            icon: 'error',
            title: 'Update Failed!',
            text: 'Database Error: " . addslashes($conn->error) . "',
            confirmButtonColor: '#d33'
        });";
    }
    $upStmt->close();
}

// 2. Profile Photo Upload Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/../uploads/profile_photos/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp  = $_FILES['profile_photo']['tmp_name'];
        $file_name = $_FILES['profile_photo']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed)) {
            $new_filename = "STUDENT_" . $student_pk_id . "_" . time() . "." . $file_ext;
            $target_file  = $upload_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $target_file)) {
                $photoStmt = $conn->prepare("UPDATE students SET profile_photo = ? WHERE id = ?");
                $photoStmt->bind_param("si", $new_filename, $student_pk_id);
                
                if ($photoStmt->execute()) {
                    $swal_script = "Swal.fire({
                        icon: 'success',
                        title: 'Photo Uploaded!',
                        text: 'Your profile picture has been updated.',
                        confirmButtonColor: '#0d6efd'
                    });";
                } else {
                    $swal_script = "Swal.fire({
                        icon: 'error',
                        title: 'Database Error!',
                        text: '" . addslashes($conn->error) . "',
                        confirmButtonColor: '#d33'
                    });";
                }
                $photoStmt->close();
            } else {
                $swal_script = "Swal.fire({
                    icon: 'error',
                    title: 'Upload Error!',
                    text: 'Unable to save the uploaded image file.',
                    confirmButtonColor: '#d33'
                });";
            }
        } else {
            $swal_script = "Swal.fire({
                icon: 'warning',
                title: 'Invalid Image Type!',
                text: 'Only JPG, PNG, and WEBP files are allowed.',
                confirmButtonColor: '#ffc107'
            });";
        }
    } else {
        $swal_script = "Swal.fire({
            icon: 'error',
            title: 'No File Selected!',
            text: 'Please choose an image file to upload.',
            confirmButtonColor: '#d33'
        });";
    }
}

// 3. Payment Slip Upload Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_payment_slip'])) {
    $slip_title = trim($_POST['slip_title'] ?? 'Bank Payment Slip');

    if (isset($_FILES['slip_file']) && $_FILES['slip_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/../uploads/payment_slips/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_ext = strtolower(pathinfo($_FILES['slip_file']['name'], PATHINFO_EXTENSION));
        $allowed  = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($file_ext, $allowed)) {
            $new_filename = "SLIP_" . $student_pk_id . "_" . time() . "." . $file_ext;
            $target_path  = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['slip_file']['tmp_name'], $target_path)) {
                $slipStmt = $conn->prepare("INSERT INTO student_payment_slips (student_id, slip_title, file_path) VALUES (?, ?, ?)");
                $slipStmt->bind_param("iss", $student_pk_id, $slip_title, $new_filename);
                $slipStmt->execute();
                $slipStmt->close();

                $swal_script = "Swal.fire({
                    icon: 'success',
                    title: 'Payment Slip Submitted!',
                    text: 'Your bank payment slip has been sent for admin review.',
                    confirmButtonColor: '#0d6efd'
                });";
            } else {
                $swal_script = "Swal.fire({
                    icon: 'error',
                    title: 'Upload Error!',
                    text: 'Unable to save the payment slip document.',
                    confirmButtonColor: '#d33'
                });";
            }
        } else {
            $swal_script = "Swal.fire({
                icon: 'warning',
                title: 'Unsupported File!',
                text: 'Please upload PDF, JPG, or PNG files only.',
                confirmButtonColor: '#ffc107'
            });";
        }
    }
}

// 4. Password Change Backend Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_pass = trim($_POST['current_password'] ?? '');
    $new_pass     = trim($_POST['new_password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        $swal_script = "Swal.fire({
            icon: 'warning',
            title: 'Incomplete Fields!',
            text: 'Please fill in all password fields.',
            confirmButtonColor: '#ffc107'
        });";
    } elseif ($new_pass !== $confirm_pass) {
        $swal_script = "Swal.fire({
            icon: 'warning',
            title: 'Password Mismatch!',
            text: 'New password and confirm password do not match.',
            confirmButtonColor: '#ffc107'
        });";
    } elseif (strlen($new_pass) < 6) {
        $swal_script = "Swal.fire({
            icon: 'warning',
            title: 'Weak Password!',
            text: 'New password must be at least 6 characters long.',
            confirmButtonColor: '#ffc107'
        });";
    } else {
        $passStmt = $conn->prepare("SELECT password FROM students WHERE id = ? LIMIT 1");
        $passStmt->bind_param("i", $student_pk_id);
        $passStmt->execute();
        $db_pass = $passStmt->get_result()->fetch_assoc()['password'] ?? '';
        $passStmt->close();

        $is_valid = password_verify($current_pass, $db_pass) || ($current_pass === $db_pass);

        if ($is_valid) {
            $new_hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            $updatePassStmt = $conn->prepare("UPDATE students SET password = ? WHERE id = ?");
            $updatePassStmt->bind_param("si", $new_hashed_password, $student_pk_id);

            if ($updatePassStmt->execute()) {
                $swal_script = "Swal.fire({
                    icon: 'success',
                    title: 'Password Changed Successfully!',
                    text: 'Your account password has been updated.',
                    confirmButtonColor: '#0d6efd'
                });";
            } else {
                $swal_script = "Swal.fire({
                    icon: 'error',
                    title: 'Update Error!',
                    text: 'Failed to update password in database.',
                    confirmButtonColor: '#d33'
                });";
            }
            $updatePassStmt->close();
        } else {
            $swal_script = "Swal.fire({
                icon: 'error',
                title: 'Incorrect Current Password!',
                text: 'The current password you entered is incorrect.',
                confirmButtonColor: '#d33'
            });";
        }
    }
}

// Student Data Fetch
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $student_pk_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Field fallback values
$first_name  = $student['first_name'] ?? 'Avishka';
$last_name   = $student['last_name'] ?? 'Prabath';
$full_name   = $student['full_name'] ?? ($first_name . ' ' . $last_name);
$email       = $student['email'] ?? 'avishka@edumart.ac.lk';
$phone       = $student['phone'] ?? '+94 77 123 4567';
$address     = $student['address'] ?? 'No. 123, Main Street, Colombo, Sri Lanka';
$student_id  = $student['student_id'] ?? 'SE-2026-0001';
$degree      = $student['course_name'] ?? 'BSc (Hons) in Software Engineering';
$acad_year   = $student['academic_year'] ?? 'Year 02 / Batch 01';
$campus      = $student['campus'] ?? 'EduMart Main Campus';
$profile_photo = $student['profile_photo'] ?? '';

// Short Initials
$initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
if (empty($initials)) { $initials = 'AP'; }

// Fetch Uploaded Slips
$slipsRes = $conn->query("SELECT * FROM student_payment_slips WHERE student_id = {$student_pk_id} ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - EduMart</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        .extra-small { font-size: 0.75rem; }
        .profile-avatar-circle {
            width: 105px;
            height: 105px;
            background-color: #ffffff;
            color: #0d6efd;
            font-size: 2.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            overflow: hidden;
            border: 3px solid #ffffff;
        }
        .profile-avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0d5dd7 100%);
            border-radius: 16px 16px 0 0;
            padding: 30px 20px 20px 20px;
        }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar Navigation -->
        <?php include('navbar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <!-- Topbar Navigation -->
            <?php include('topbar.php'); ?>

            <div class="content-area p-4">

                <!-- Page Header -->
                <div class="mb-4">
                    <h4 class="fw-bold text-dark mb-1"><i class="bi bi-person-gear text-primary me-2"></i>Profile & Account Settings</h4>
                    <p class="text-muted small mb-0">Manage your personal information, security, payments, and slips.</p>
                </div>

                <div class="row g-4">
                    <!-- Left Column: Summary Card -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white text-center">
                            <div class="profile-card-header text-white position-relative">
                                <span class="badge bg-success bg-opacity-75 text-white rounded-pill extra-small position-absolute top-0 end-0 m-3 px-3 py-1">
                                    Active Student
                                </span>
                                <div class="profile-avatar-circle mb-2">
                                    <?php if (!empty($profile_photo) && file_exists(__DIR__ . "/../uploads/profile_photos/" . $profile_photo)): ?>
                                        <img src="../uploads/profile_photos/<?= htmlspecialchars($profile_photo) ?>?v=<?= time() ?>" alt="Profile Photo">
                                    <?php else: ?>
                                        <?= htmlspecialchars($initials) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="p-4">
                                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($full_name) ?></h5>
                                <p class="text-primary fw-semibold extra-small mb-3"><?= htmlspecialchars($degree) ?></p>

                                <div class="text-start bg-light p-3 rounded-3 extra-small mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Student ID:</span>
                                        <strong class="text-dark"><?= htmlspecialchars($student_id) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Academic Year:</span>
                                        <strong class="text-dark"><?= htmlspecialchars($acad_year) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Campus:</span>
                                        <strong class="text-dark"><?= htmlspecialchars($campus) ?></strong>
                                    </div>
                                </div>

                                <button class="btn btn-outline-primary btn-sm rounded-pill w-100 extra-small fw-bold" data-bs-toggle="modal" data-bs-target="#photoModal">
                                    <i class="bi bi-camera me-1"></i> Change Profile Photo
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Settings Form & Tabs -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                            
                            <!-- Tab Navigation -->
                            <ul class="nav nav-pills mb-4 gap-2" id="profileTabs">
                                <li class="nav-item">
                                    <button class="nav-link active rounded-pill extra-small fw-bold px-3 py-2" data-bs-toggle="tab" data-bs-target="#personal-tab">
                                        <i class="bi bi-person me-1"></i> Personal Info
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link rounded-pill extra-small fw-bold px-3 py-2 text-secondary" data-bs-toggle="tab" data-bs-target="#payments-tab">
                                        <i class="bi bi-wallet2 me-1"></i> Payments & Slips
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link rounded-pill extra-small fw-bold px-3 py-2 text-secondary" data-bs-toggle="tab" data-bs-target="#security-tab">
                                        <i class="bi bi-shield-lock me-1"></i> Security
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- 1. Personal Info Tab -->
                                <div class="tab-pane fade show active" id="personal-tab">
                                    <form action="" method="POST">
                                        <input type="hidden" name="update_profile" value="1">

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label extra-small fw-bold text-muted">First Name</label>
                                                <input type="text" name="first_name" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($first_name) ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label extra-small fw-bold text-muted">Last Name</label>
                                                <input type="text" name="last_name" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($last_name) ?>" required>
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label extra-small fw-bold text-muted">Email Address</label>
                                                <input type="email" name="email" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($email) ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label extra-small fw-bold text-muted">Phone Number</label>
                                                <input type="text" name="phone" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($phone) ?>" required>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label extra-small fw-bold text-muted">Residential Address</label>
                                            <textarea name="address" class="form-control rounded-3" rows="3" required><?= htmlspecialchars($address) ?></textarea>
                                        </div>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 extra-small fw-bold shadow-sm">
                                                <i class="bi bi-check-circle-fill me-1"></i> Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- 2. Payments & Slips Tab -->
                                <div class="tab-pane fade" id="payments-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-receipt text-primary me-2"></i>Uploaded Bank Payment Slips</h6>
                                        <button class="btn btn-primary btn-sm rounded-pill px-3 extra-small fw-bold" data-bs-toggle="modal" data-bs-target="#slipModal">
                                            <i class="bi bi-cloud-upload me-1"></i> Upload New Slip
                                        </button>
                                    </div>

                                    <div class="table-responsive border rounded-3">
                                        <table class="table table-hover align-middle mb-0 extra-small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Slip Title / Description</th>
                                                    <th>Date Uploaded</th>
                                                    <th>Verification Status</th>
                                                    <th class="text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($slipsRes && $slipsRes->num_rows > 0): ?>
                                                    <?php while ($s = $slipsRes->fetch_assoc()): ?>
                                                        <tr>
                                                            <td class="fw-bold text-dark"><?= htmlspecialchars($s['slip_title']) ?></td>
                                                            <td class="text-muted"><?= date('M d, Y h:i A', strtotime($s['created_at'])) ?></td>
                                                            <td>
                                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle rounded-pill px-2.5 py-1">
                                                                    <i class="bi bi-clock me-1"></i><?= htmlspecialchars($s['status']) ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-end">
                                                                <a href="../uploads/payment_slips/<?= htmlspecialchars($s['file_path']) ?>" target="_blank" class="btn btn-light border btn-sm rounded-pill px-3 fw-semibold">
                                                                    <i class="bi bi-eye me-1"></i> View Slip
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-4">
                                                            <i class="bi bi-receipt fs-2 opacity-50 d-block mb-1"></i>
                                                            No payment slips uploaded yet. Click "Upload New Slip" above.
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 3. Security Tab -->
                                <div class="tab-pane fade" id="security-tab">
                                    <div class="mb-4 pb-3 border-bottom">
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Change Password</h6>
                                        <p class="text-muted extra-small">Update your password regularly to keep your student account secure.</p>
                                        
                                        <form action="" method="POST" class="mt-3">
                                            <input type="hidden" name="change_password" value="1">
                                            
                                            <div class="mb-3">
                                                <label class="form-label extra-small fw-bold text-muted">Current Password *</label>
                                                <input type="password" name="current_password" class="form-control rounded-3 py-2" placeholder="Enter current password" required>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label extra-small fw-bold text-muted">New Password *</label>
                                                    <input type="password" name="new_password" class="form-control rounded-3 py-2" placeholder="At least 6 characters" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label extra-small fw-bold text-muted">Confirm New Password *</label>
                                                    <input type="password" name="confirm_password" class="form-control rounded-3 py-2" placeholder="Re-type new password" required>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 extra-small fw-bold shadow-sm">
                                                    <i class="bi bi-key-fill me-1"></i> Update Password
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Active Login Session Information -->
                                    <div>
                                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-laptop text-success me-2"></i>Active Session Info</h6>
                                        <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                                                    <i class="bi bi-display fs-5"></i>
                                                </div>
                                                <div>
                                                    <strong class="extra-small text-dark d-block">Current Active Browser Session</strong>
                                                    <span class="text-muted extra-small">Logged in via Student Portal Session Management</span>
                                                </div>
                                            </div>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 extra-small">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Active Now
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL 1: CHANGE PROFILE PHOTO -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-camera text-primary me-2"></i>Upload Profile Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="upload_photo" value="1">
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted">Select Photo (JPG, PNG, WEBP)</label>
                            <input type="file" name="profile_photo" class="form-control rounded-3" accept="image/png, image/jpeg, image/jpg, image/webp" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Upload Photo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: UPLOAD PAYMENT SLIP -->
    <div class="modal fade" id="slipModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-cloud-upload text-primary me-2"></i>Upload Payment Slip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="upload_payment_slip" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted">Payment Title / Description *</label>
                            <input type="text" name="slip_title" class="form-control rounded-3" placeholder="e.g. Semester 02 Tuition Fee Slip" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted">Select Document File (PDF, JPG, PNG) *</label>
                            <input type="file" name="slip_file" class="form-control rounded-3" accept=".pdf,image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Upload Slip</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (!empty($swal_script)): ?>
            <?= $swal_script ?>
        <?php endif; ?>
    </script>
</body>
</html>