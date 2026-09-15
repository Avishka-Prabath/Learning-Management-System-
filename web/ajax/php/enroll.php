<?php

// 1. Include core OOP dependencies
include __DIR__ . '/../../class/include.php';
header('Content-Type: application/json; charset=UTF-8');

// 2. Action validation logic
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

// -------------------------------------------------------------
// 1. FETCH ALL CATEGORIES
// -------------------------------------------------------------
if ($action === 'fetch_all' || isset($_POST['fetch_all'])) {

    $CATEGORY = new Category();
    $records = $CATEGORY->all();

    echo json_encode($records);
    exit();

// -------------------------------------------------------------
// 2. CREATE CATEGORY
// -------------------------------------------------------------
} elseif ($action === 'create' || isset($_POST['create'])) {

    $category_name = isset($_POST['category_name']) ? trim($_POST['category_name']) : '';
    $category_description = isset($_POST['category_description']) ? trim($_POST['category_description']) : '';

    if (empty($category_name)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please enter a category name.'
        ]);
        exit();
    }

    $CATEGORY = new Category(NULL);
    $CATEGORY->name        = $category_name;
    $CATEGORY->description = $category_description;

    $res = $CATEGORY->create();

    if ($res) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Category added successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to save Category data.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 3. UPDATE CATEGORY
// -------------------------------------------------------------
} elseif ($action === 'update' || isset($_POST['update'])) {

    $id                   = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';
    $category_name        = isset($_POST['category_name']) ? trim($_POST['category_name']) : '';
    $category_description = isset($_POST['category_description']) ? trim($_POST['category_description']) : '';

    if (empty($id) || empty($category_name)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid category parameters.'
        ]);
        exit();
    }

    $CATEGORY = new Category($id);
    $CATEGORY->name        = $category_name;
    $CATEGORY->description = $category_description;

    $res = $CATEGORY->update();

    if ($res) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Category updated successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to update Category data.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 4. DELETE CATEGORY
// -------------------------------------------------------------
} elseif ($action === 'delete' || isset($_POST['delete'])) {

    $id = isset($_POST['id']) ? trim($_POST['id']) : (isset($_POST['category_id']) ? trim($_POST['category_id']) : '');

    if (empty($id)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Category ID provided.'
        ]);
        exit();
    }

    $CATEGORY = new Category($id);
    $res = $CATEGORY->delete();

    if ($res) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Category deleted successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to delete Category.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 5. ENROLL STUDENT (UPDATED WITH ALL CAMPUS FORM FIELDS)
// -------------------------------------------------------------
} elseif ($action === 'enroll_student') {
    
    // Form Inputs Catching
    $full_name         = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $nic               = isset($_POST['nic']) ? trim($_POST['nic']) : '';
    $dob               = isset($_POST['dob']) ? trim($_POST['dob']) : '';
    $gender            = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $phone             = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email             = isset($_POST['email']) ? trim($_POST['email']) : '';
    $address           = isset($_POST['address']) ? trim($_POST['address']) : '';
    $course_val        = isset($_POST['course']) ? trim($_POST['course']) : '';
    $study_mode        = isset($_POST['study_mode']) ? trim($_POST['study_mode']) : '';
    $intake            = isset($_POST['intake']) ? trim($_POST['intake']) : '';
    $qualification     = isset($_POST['qualification']) ? trim($_POST['qualification']) : '';
    $school            = isset($_POST['school']) ? trim($_POST['school']) : '';
    $guardian_name     = isset($_POST['guardian_name']) ? trim($_POST['guardian_name']) : '';
    $guardian_phone    = isset($_POST['guardian_phone']) ? trim($_POST['guardian_phone']) : '';
    $guardian_relation = isset($_POST['guardian_relation']) ? trim($_POST['guardian_relation']) : '';
    $notes             = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    // Field Validations
    if (empty($full_name) || empty($nic) || empty($dob) || empty($gender) || empty($phone) || empty($email) || empty($address) || empty($course_val) || empty($study_mode) || empty($intake) || empty($qualification) || empty($guardian_name) || empty($guardian_phone) || empty($guardian_relation)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    $db = Database::getInstance();
    $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

    // 1. Get or Create Student User Account
    $checkStudent = $conn->prepare("SELECT id FROM students WHERE email = ? OR phone = ?");
    $checkStudent->bind_param("ss", $email, $phone);
    $checkStudent->execute();
    $studentResult = $checkStudent->get_result();

    if ($studentResult && $studentResult->num_rows > 0) {
        $student_id = $studentResult->fetch_assoc()['id'];
    } else {
        $parts = explode(' ', $full_name, 2);
        $first_name = $parts[0];
        $last_name = isset($parts[1]) ? $parts[1] : '';
        
        $generated_code = "STU-" . rand(1000, 9999);
        $pass_hash = password_hash('student123', PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name, email, phone, password, status) VALUES (?, ?, ?, ?, ?, ?, 'Active')");
        $stmt->bind_param("ssssss", $generated_code, $first_name, $last_name, $email, $phone, $pass_hash);
        
        if ($stmt->execute()) {
            $student_id = $stmt->insert_id;
        } else {
            echo json_encode([
                "status"  => 'error',
                "message" => 'Failed to create student user account.'
            ]);
            exit();
        }
    }

    // 2. Resolve Course ID
    $course_id = 0;
    if (is_numeric($course_val)) {
        $course_id = intval($course_val);
    } else {
        $checkCourse = $conn->prepare("SELECT id FROM courses WHERE course_name = ? OR course_code = ? LIMIT 1");
        $checkCourse->bind_param("ss", $course_val, $course_val);
        $checkCourse->execute();
        $cResult = $checkCourse->get_result();

        if ($cResult && $cResult->num_rows > 0) {
            $course_id = $cResult->fetch_assoc()['id'];
        }
    }

    if ($course_id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Selected course was not found in the system.'
        ]);
        exit();
    }

    // 3. Check existing enrollment for the selected course
    $checkEnroll = $conn->prepare("SELECT id FROM enrollments WHERE email = ? AND course_id = ? LIMIT 1");
    $checkEnroll->bind_param("si", $email, $course_id);
    $checkEnroll->execute();
    $enrollResult = $checkEnroll->get_result();

    if ($enrollResult && $enrollResult->num_rows > 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'You have already applied for this course.'
        ]);
        exit();
    }

    // 4. Create Full Enrollment Application Record
    $stmtEnroll = $conn->prepare("INSERT INTO enrollments (full_name, nic, dob, gender, phone, email, address, course_id, study_mode, intake, qualification, school, guardian_name, guardian_phone, guardian_relation, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmtEnroll->bind_param("sssssssissssssss", 
        $full_name, 
        $nic, 
        $dob, 
        $gender, 
        $phone, 
        $email, 
        $address, 
        $course_id, 
        $study_mode, 
        $intake, 
        $qualification, 
        $school, 
        $guardian_name, 
        $guardian_phone, 
        $guardian_relation, 
        $notes
    );

    if ($stmtEnroll->execute()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Application submitted successfully! Use password "student123" to log in to student portal.'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to process campus application.'
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