<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the db.php file from the root
if (file_exists(__DIR__ . '/../db.php')) {
    require_once __DIR__ . '/../db.php';
} elseif (file_exists(__DIR__ . '/db.php')) {
    require_once __DIR__ . '/db.php';
}

// Redirect straight to dashboard if the instructor is already logged in
if (isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error_message = "";

// Backend logic that runs when the login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = trim($_POST['email'] ?? '');
    $password    = trim($_POST['password'] ?? '');

    if (!empty($login_input) && !empty($password)) {
        
        // Look up the instructor by email or username in the database
        $stmt = $conn->prepare("SELECT id, title, full_name, email, username, password FROM teachers WHERE (email = ? OR username = ?) LIMIT 1");
        $stmt->bind_param("ss", $login_input, $login_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $teacher = $result->fetch_assoc();

            // Password Verification Logic (Plain text, password_hash, OR Default Fallback)
            $db_password = $teacher['password'] ?? '';
            $is_valid_pass = false;

            if ($password === $db_password || password_verify($password, $db_password) || $password === 'lecturer123') {
                $is_valid_pass = true;
            }

            if ($is_valid_pass) {
                // Set session variables
                $_SESSION['teacher_logged_in'] = true;
                $_SESSION['teacher_id']        = $teacher['id'];
                $_SESSION['teacher_name']      = (!empty($teacher['title']) ? $teacher['title'] . ' ' : '') . $teacher['full_name'];
                $_SESSION['teacher_email']     = $teacher['email'];
                $_SESSION['teacher_username']  = $teacher['username'];

                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid password. Default password is 'lecturer123'.";
            }
        } else {
            $error_message = "No instructor account found with username/email: " . htmlspecialchars($login_input);
        }
        $stmt->close();
    } else {
        $error_message = "Please enter both Email/Username and Password.";
    }
}

// Active Instructors List (For Easy 1-Click Quick Login Selection)
$instructorsQuery = "SELECT id, title, full_name, email, username FROM teachers ORDER BY id DESC LIMIT 5";
$instructorsRes = $conn->query($instructorsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Portal Login - EduMart Campus</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            max-width: 450px;
            width: 100%;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #ffffff;
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin: 0 auto 15px auto;
            font-size: 1.8rem;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        .btn-login {
            background-color: #0d6efd;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
        }
        .extra-small { font-size: 0.75rem; }
    </style>
</head>
<body>

    <div class="card login-card p-4 p-md-5">
        
        <!-- Header Section -->
        <div class="text-center mb-4">
            <div class="brand-icon">
                <i class="bi bi-person-workspace"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Lecturer Portal</h4>
            <p class="text-muted small mb-0">Sign in to manage classes, coursework, and students.</p>
        </div>

        <!-- Dynamic Quick Login Buttons from Database -->
        <?php if ($instructorsRes && $instructorsRes->num_rows > 0): ?>
            <div class="bg-light p-3 rounded-4 mb-4 border">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-secondary extra-small fw-bold"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Select Instructor to Log In:</span>
                </div>
                <div class="d-flex flex-wrap gap-1.5">
                    <?php while ($ins = $instructorsRes->fetch_assoc()): ?>
                        <?php 
                            $login_val = !empty($ins['email']) ? $ins['email'] : $ins['username'];
                            $disp_name = trim(($ins['title'] ?? '') . ' ' . $ins['full_name']);
                        ?>
                        <button type="button" class="btn btn-sm btn-white border rounded-pill extra-small fw-bold py-1 px-2.5 text-primary shadow-sm" onclick="quickLogin('<?= htmlspecialchars($login_val) ?>', 'lecturer123')">
                            <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($disp_name) ?>
                        </button>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Error Alert Message -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger rounded-3 py-2 px-3 small border-0 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error_message ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form id="loginForm" action="login.php" method="POST">
            
            <!-- Email / Username Input -->
            <div class="mb-3">
                <label class="form-label text-uppercase text-muted extra-small fw-bold">Official Email / Username *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="text" id="emailInput" name="email" class="form-control border-start-0 ps-0 rounded-end py-2" placeholder="Email or Username (e.g., saman_p)" value="saman.p@edumart.ac.lk" required>
                </div>
            </div>

            <!-- Password Input -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label text-uppercase text-muted extra-small fw-bold mb-1">Portal Password *</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" id="passInput" name="password" class="form-control border-start-0 border-end-0 ps-0 py-2" placeholder="••••••••" value="lecturer123" required>
                    <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePass()">
                        <i class="bi bi-eye" id="passToggleIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-login w-100 text-white mb-3 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Log In to Dashboard
            </button>
        </form>

        <div class="text-center mt-3 pt-2 border-top">
            <span class="text-muted extra-small">EduMart Campus Management System © 2026</span>
        </div>

    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Quick Auto-Fill & Instant Login Trigger
        function quickLogin(emailOrUser, pass) {
            document.getElementById('emailInput').value = emailOrUser;
            document.getElementById('passInput').value = pass;
            document.getElementById('loginForm').submit();
        }

        // Show/Hide Password Toggle
        function togglePass() {
            const passInput = document.getElementById('passInput');
            const toggleIcon = document.getElementById('passToggleIcon');
            
            if (passInput.type === 'password') {
                passInput.type = 'text';
                toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passInput.type = 'password';
                toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</body>
</html>