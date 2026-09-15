<?php
// Output buffering ආරම්භ කර ඕනෑම නොපෙනෙන warnings/notices නිසා JSON කඩාවැටීම වැළැක්වීම
ob_start();

// 1. Include core OOP dependencies
$includePath = __DIR__ . '/../../class/include.php';
if (file_exists($includePath)) {
    require_once $includePath;
} else {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        "status"  => 'error',
        "message" => 'Core class files missing.'
    ]);
    exit();
}

// Output Buffer එක සුද්ධ කර JSON Header එක සැකසීම
ob_clean();
header('Content-Type: application/json; charset=UTF-8');

// 2. Request Method Check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request method.'
    ]);
    exit();
}

// 3. Action validation logic
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

// -------------------------------------------------------------
// 1. CREATE / REGISTER INSTRUCTOR
// -------------------------------------------------------------
if ($action === 'create' || isset($_POST['create'])) {

    // Input Data ලබා ගැනීම
    $title           = isset($_POST['title']) ? trim($_POST['title']) : '';
    $full_name       = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email           = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone           = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $department      = isset($_POST['department']) ? trim($_POST['department']) : '';
    $assigned_module = isset($_POST['assigned_module']) ? trim($_POST['assigned_module']) : '';
    $username        = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password        = isset($_POST['password']) ? trim($_POST['password']) : '';

    // අනිවාර්ය Fields හිස්දැයි පරීක්ෂා කිරීම
    if (empty($title) || empty($full_name) || empty($email) || empty($username) || empty($password)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    // Instructor Object එකක් සෑදීම
    $INSTRUCTOR = new Instructor(NULL);

    // Email එක කලින් භාවිතා කර ඇත්දැයි පරීක්ෂා කිරීම
    if ($INSTRUCTOR->emailExists($email)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'This Email address is already registered.'
        ]);
        exit();
    }

    // Data Assign කිරීම
    $INSTRUCTOR->title           = $title;
    $INSTRUCTOR->full_name       = $full_name;
    $INSTRUCTOR->email           = $email;
    $INSTRUCTOR->phone           = $phone;
    $INSTRUCTOR->department      = $department;
    $INSTRUCTOR->assigned_module = $assigned_module;
    $INSTRUCTOR->username        = $username;
    $INSTRUCTOR->password        = password_hash($password, PASSWORD_DEFAULT); // Secure Password Hashing
    $INSTRUCTOR->status          = 'Active';

    // Profile Photo එක Upload කිරීමේ Logic එක
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../uploads/instructors/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        $allowed_exts   = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_extension, $allowed_exts)) {
            $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
            $target_file  = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
                $INSTRUCTOR->profile_photo = $new_filename;
            } else {
                $INSTRUCTOR->profile_photo = NULL;
            }
        } else {
            $INSTRUCTOR->profile_photo = NULL;
        }
    } else {
        $INSTRUCTOR->profile_photo = NULL;
    }

    // Database එකට ඇතුළත් කිරීම
    $res = $INSTRUCTOR->create();

    if ($res) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Instructor registered successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to register instructor. Database insertion error.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 2. FETCH ALL INSTRUCTORS
// -------------------------------------------------------------
} elseif ($action === 'fetch_all' || isset($_POST['fetch_all'])) {

    $INSTRUCTOR = new Instructor(NULL);
    $instructors = $INSTRUCTOR->all();

    echo json_encode([
        "status" => 'success',
        "data"   => $instructors
    ]);
    exit();

// -------------------------------------------------------------
// 3. DELETE INSTRUCTOR
// -------------------------------------------------------------
} elseif ($action === 'delete' || isset($_POST['delete'])) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Instructor ID provided.'
        ]);
        exit();
    }

    $INSTRUCTOR = new Instructor($id);
    if ($INSTRUCTOR->delete()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Instructor deleted successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to delete instructor.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// INVALID ACTION
// -------------------------------------------------------------
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request action.'
    ]);
    exit();
}