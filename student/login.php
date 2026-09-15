<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';

// Already logged in නම් කෙලින්ම home.php එකට යැවීම
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    header("Location: home.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Please enter both email/student ID and password.";
    } else {
        // Course Code එකත් එක්කම Fetch කරගන්නා JOIN Query එක
        $stmt = $conn->prepare("
            SELECT 
                s.id, 
                s.student_id, 
                s.first_name, 
                s.last_name, 
                s.email, 
                s.password, 
                s.course_id, 
                s.status,
                c.course_code 
            FROM students s 
            LEFT JOIN courses c ON (s.course_id = c.id OR s.course_id = c.course_code OR s.course_id = c.course_name) 
            WHERE s.email = ? OR s.student_id = ? 
            LIMIT 1
        ");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();

        if ($student && password_verify($password, $student['password'])) {
            // 1. Clear previous session data completely
            session_unset();
            session_regenerate_id(true);

            // 2. Set new user session values dynamically
            $_SESSION['student_logged_in'] = true;
            $_SESSION['student_id']    = $student['id'];
            $_SESSION['student_code']  = $student['student_id'];
            $_SESSION['student_name']  = trim($student['first_name'] . ' ' . $student['last_name']);
            $_SESSION['student_email'] = $student['email'];
            $_SESSION['course_id']     = $student['course_id'];
            $_SESSION['course_code']   = $student['course_code'] ?? ''; // Registered Course Code

            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid Email/Student ID or Password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduMart | Student Portal Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container-fluid h-100 p-0">
        <div class="row h-100 g-0">
            
            <!-- Left Side Banner -->
            <div class="col-lg-7 d-none d-lg-flex hero-section">
                <div>
                    <span class="hero-tag"><i class="bi bi-mortarboard-fill me-1"></i> Higher Education Campus</span>
                    <h1 class="hero-title">EduMart Campus</h1>
                    <p class="hero-text">
                        Discover world-class academic programs, modern labs, and expert mentorship designed to fast-track your global tech & business career.
                    </p>
                    <a href="../web/home.php" class="btn-visit-site">
                        <i class="bi bi-globe me-1"></i> Explore Campus Website
                    </a>
                </div>
            </div>

            <!-- Right Side Login Form -->
            <div class="col-lg-5 login-section">
                <div class="login-box">
                    
                    <div class="text-center text-lg-start mb-4">
                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-2">
                            <div class="brand-logo-box shadow-sm">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <span class="brand-name">Edu<span>Mart</span></span>
                        </div>
                        <h3 class="portal-title">Student Portal</h3>
                        <p class="text-muted small">Please use your credentials to login.</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 p-3 mb-3 small d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div><?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form action="login.php" method="POST">
                        
                        <!-- Email Input -->
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text input-group-text-modern">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="text" name="email" class="form-control form-control-modern" placeholder="Email / Student ID" required>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text input-group-text-modern">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control form-control-modern" placeholder="Password" required>
                            </div>
                        </div>

                        <!-- Remember / Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4 px-1">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label small text-muted fw-medium" for="remember">Remember me</label>
                            </div>
                            <a href="#" class="small text-decoration-none fw-semibold" style="color: #0d5be1;">Forgot password?</a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-login w-100 text-white">Login to Portal</button>
                    </form>

                    <!-- Register Link -->
                    <div class="text-center text-lg-start mt-4 pt-2">
                        <p class="small text-muted mb-0">Don't have an account? 
                            <a href="register.php" class="fw-bold text-decoration-none ms-1" style="color: #0d5be1;">Register here</a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>