<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Register - EduMax</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 p-4 bg-white">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Create Student Account</h4>
                        <p class="text-muted small">Enter your details to register as a student.</p>
                    </div>

                    <form action="login.php" method="POST">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">First Name</label>
                                <input type="text" class="form-control bg-light" placeholder="Avishka" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Last Name</label>
                                <input type="text" class="form-control bg-light" placeholder="Prabath" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" class="form-control bg-light" placeholder="name@example.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password</label>
                            <input type="password" class="form-control bg-light" placeholder="••••••••" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Confirm Password</label>
                            <input type="password" class="form-control bg-light" placeholder="••••••••" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3">Register Now</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="small text-muted mb-0">Already have an account? <a href="login.php" class="fw-bold text-primary text-decoration-none">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>