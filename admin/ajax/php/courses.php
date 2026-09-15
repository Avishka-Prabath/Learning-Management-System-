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
// 1. CREATE COURSE MODULE
// -------------------------------------------------------------
if ($action === 'create' || isset($_POST['create'])) {

    $course_code = isset($_POST['course_code']) ? trim($_POST['course_code']) : '';
    $course_name = isset($_POST['course_name']) ? trim($_POST['course_name']) : '';
    $category    = isset($_POST['category']) ? trim($_POST['category']) : '';
    $teacher_id  = isset($_POST['teacher_id']) ? intval($_POST['teacher_id']) : 0;
    $duration    = isset($_POST['duration']) ? trim($_POST['duration']) : '';
    $price       = isset($_POST['price']) ? trim($_POST['price']) : '';
    $image       = isset($_POST['image']) ? trim($_POST['image']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Required inputs validation
    if (empty($course_code) || empty($course_name)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    $COURSE = new Course(NULL);
    $COURSE->course_code = $course_code;
    $COURSE->course_name = $course_name;
    $COURSE->category    = $category;
    $COURSE->teacher_id  = $teacher_id;
    $COURSE->duration    = $duration;
    $COURSE->price       = $price;
    $COURSE->image       = $image;
    $COURSE->description = $description;

    $res = $COURSE->create();

    if ($res) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Course created successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to create course. Database insertion error.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 2. FETCH ALL COURSES
// -------------------------------------------------------------
} elseif ($action === 'fetch_all' || isset($_POST['fetch_all'])) {

    $COURSE = new Course(NULL);
    $courses = $COURSE->all();

    echo json_encode([
        "status" => 'success',
        "data"   => $courses
    ]);
    exit();

// -------------------------------------------------------------
// 3. DELETE COURSE
// -------------------------------------------------------------
} elseif ($action === 'delete' || isset($_POST['delete'])) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Course ID provided.'
        ]);
        exit();
    }

    $COURSE = new Course($id);
    if ($COURSE->delete()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Course deleted successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to delete course.'
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