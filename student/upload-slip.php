<?php
// Database connection details (adjust to match your own database details)
$host = "127.0.0.1:8889";
$user = "root";
$pass = "";
$db   = "edumart_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'] ?? '';
    $amount    = $_POST['amount'] ?? '';
    $student_id = "EM-2026-884"; // Student ID from session (e.g. $_SESSION['student_id'])

    // Handle the slip file
    if (isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['payment_slip']['tmp_name'];
        $fileName    = $_FILES['payment_slip']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Allowed file extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Create an upload directory
            $uploadFileDir = './uploads/slips/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Insert into database (adjust to match your table structure)
                /*
                $sql = "INSERT INTO payments (student_id, course_id, amount, slip_path, status) VALUES (?, ?, ?, ?, 'Pending')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sids", $student_id, $course_id, $amount, $dest_path);
                $stmt->execute();
                */

                echo "<script>
                        alert('Payment Slip Uploaded Successfully! Pending Verification.');
                        window.location.href = 'profile.php';
                      </script>";
                exit();
            } else {
                echo "<script>alert('Error moving uploaded file.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Invalid file format. Only JPG, PNG, and PDF files are allowed.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Please select a file to upload.'); window.history.back();</script>";
    }
}
?>