<?php
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
// UPDATE INSTRUCTOR DETAILS & ASSIGN MODULE
// -------------------------------------------------------------
if ($action === 'update' || isset($_POST['update'])) {

    $teacher_id      = isset($_POST['teacher_id']) ? intval($_POST['teacher_id']) : 0;
    $title           = isset($_POST['title']) ? trim($_POST['title']) : '';
    $full_name       = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email           = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone           = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $department      = isset($_POST['department']) ? trim($_POST['department']) : '';
    $assigned_module = isset($_POST['assigned_module']) ? trim($_POST['assigned_module']) : '';
    $username        = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password        = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($teacher_id <= 0 || empty($title) || empty($full_name) || empty($email) || empty($username)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    $EDIT_TEACHER = new EditTeacher($teacher_id);

    if (empty($EDIT_TEACHER->id)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Instructor record not found.'
        ]);
        exit();
    }

    if ($EDIT_TEACHER->emailExists($email, $teacher_id)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'This email address is already registered.'
        ]);
        exit();
    }

    if ($EDIT_TEACHER->usernameExists($username, $teacher_id)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'This username is already taken.'
        ]);
        exit();
    }

    $EDIT_TEACHER->title           = $title;
    $EDIT_TEACHER->full_name       = $full_name;
    $EDIT_TEACHER->email           = $email;
    $EDIT_TEACHER->phone           = $phone;
    $EDIT_TEACHER->department      = $department;
    $EDIT_TEACHER->assigned_module = $assigned_module;
    $EDIT_TEACHER->username        = $username;

    if (!empty($password)) {
        $EDIT_TEACHER->password = (strlen($password) >= 60 && str_starts_with($password, '$2y$')) ? $password : password_hash($password, PASSWORD_DEFAULT);
    }

    $res = $EDIT_TEACHER->update();

    if ($res) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Instructor updated and module assigned successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to update instructor. Database error.'
        ]);
    }
    exit();

} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request action.'
    ]);
    exit();
}