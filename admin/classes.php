<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedules - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../admin/style.css">
</head>
<body class="bg-light">

    <div class="admin-layout-wrapper">
        <?php include('admin-sidebar.php'); ?>

        <div class="main-wrapper">
            <?php include('topbar.php'); ?>

            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Class Schedules & Timetable</h4>
                        <p class="text-muted small mb-0">Schedule lecture sessions and meeting links for classes.</p>
                    </div>
                    <button class="btn btn-primary rounded-pill px-3 py-2 fw-semibold btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleClassModal">
                        <i class="bi bi-calendar-plus me-1"></i> Schedule New Class
                    </button>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Class Subject / Module</th>
                                    <th>Instructor</th>
                                    <th>Date & Time</th>
                                    <th>Meeting Link</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-dark">Advanced Software Architecture</td>
                                    <td>Dr. Saman Perera</td>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary">Today - 02:00 PM</span></td>
                                    <td><a href="#" class="btn btn-light btn-sm rounded-3 extra-small border"><i class="bi bi-camera-video me-1 text-success"></i> Join Zoom Link</a></td>
                                    <td class="text-end">
                                        <button class="btn btn-light btn-sm text-primary rounded-3"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-light btn-sm text-danger rounded-3"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
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