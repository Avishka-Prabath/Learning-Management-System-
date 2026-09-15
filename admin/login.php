<?php
session_start();
require_once __DIR__ . '/../db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {
        // 1. DEFAULT HARDCODED ADMIN CHECK (Fallback)
        if ($email === 'admin@edumart.ac.lk' && $password === 'admin123') {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_username'] = 'Super Admin';
            $_SESSION['admin_email'] = 'admin@edumart.ac.lk';

            header("Location: dashboard.php");
            exit();
        } 
        
        // 2. DATABASE ADMIN CHECK
        if ($conn) {
            $stmt = $conn->prepare("SELECT id, username, email, password FROM admins WHERE email = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $admin = $result->fetch_assoc();
                $stmt->close();

                if ($admin && password_verify($password, $admin['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_email'] = $admin['email'];

                    header("Location: dashboard.php");
                    exit();
                }
            }
        }

        $error = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login - EduMart</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS Link -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-logo shadow-sm">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h4 class="fw-bold text-white mb-1">EduMart System Admin</h4>
            <p class="text-muted small">Access dashboard using admin credentials</p>
        </div>

        <!-- Default Credentials Alert Hint -->
        <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info rounded-3 p-3 mb-3 small">
            <i class="bi bi-info-circle-fill me-1"></i> <strong>Default Admin Login:</strong><br>
            Email: <code>admin@edumart.ac.lk</code><br>
            Password: <code>admin123</code>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 p-3 mb-3 small d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div><?= $error ?></div>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 border-opacity-10 text-muted" style="border-color: rgba(255,255,255,0.1); border-radius: 12px 0 0 12px;"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control form-control-modern border-start-0" value="admin@edumart.ac.lk" placeholder="admin@edumart.ac.lk" required style="border-radius: 0 12px 12px 0;">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 border-opacity-10 text-muted" style="border-color: rgba(255,255,255,0.1); border-radius: 12px 0 0 12px;"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control form-control-modern border-start-0" value="admin123" placeholder="••••••••" required style="border-radius: 0 12px 12px 0;">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100 text-white shadow-sm">Sign In to Admin Panel</button>
        </form>

        <div class="text-center mt-4">
            <a href="../web/home.php" class="text-muted text-decoration-none small hover-link"><i class="bi bi-arrow-left me-1"></i> Back to Home</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>