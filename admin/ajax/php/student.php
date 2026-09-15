<?php
// Start output buffering to stop hidden warnings/notices from breaking the JSON response
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

// Initialize the global database connection ($conn)
$db = Database::getInstance();
$conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

// Clear the output buffer and set the JSON header
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
// 1. CREATE / REGISTER STUDENT
// -------------------------------------------------------------
if ($action === 'create' || isset($_POST['create'])) {

    // Get input data (all fields matched to the JavaScript file)
    $full_name         = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $nic               = isset($_POST['nic']) ? trim($_POST['nic']) : '';
    $first_name        = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name         = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $dob               = !empty(trim($_POST['dob'] ?? '')) ? trim($_POST['dob']) : NULL;
    $gender            = !empty(trim($_POST['gender'] ?? '')) ? trim($_POST['gender']) : NULL;
    $phone             = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email             = isset($_POST['email']) ? trim($_POST['email']) : '';
    $address           = !empty(trim($_POST['address'] ?? '')) ? trim($_POST['address']) : NULL;
    $course_id         = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $study_mode        = !empty(trim($_POST['study_mode'] ?? '')) ? trim($_POST['study_mode']) : 'Full-Time';
    $intake            = !empty(trim($_POST['intake'] ?? '')) ? trim($_POST['intake']) : '2026-Batch-01';
    $qualification     = !empty(trim($_POST['qualification'] ?? '')) ? trim($_POST['qualification']) : NULL;
    $school            = !empty(trim($_POST['school'] ?? '')) ? trim($_POST['school']) : NULL;
    $guardian_name     = !empty(trim($_POST['guardian_name'] ?? '')) ? trim($_POST['guardian_name']) : NULL;
    $guardian_phone    = !empty(trim($_POST['guardian_phone'] ?? '')) ? trim($_POST['guardian_phone']) : NULL;
    $guardian_relation = !empty(trim($_POST['guardian_relation'] ?? '')) ? trim($_POST['guardian_relation']) : NULL;
    
    // Campus Specific Credentials
    $campus_id         = !empty(trim($_POST['campus_id'] ?? '')) ? trim($_POST['campus_id']) : 'EM-2026-' . rand(1000, 9999);
    $campus_email      = !empty(trim($_POST['campus_email'] ?? '')) ? trim($_POST['campus_email']) : strtolower($first_name . '.' . rand(100,999) . '@edumart.ac.lk');
    $password          = isset($_POST['password']) ? trim($_POST['password']) : 'student123';
    
    // Hidden Enrollment ID if registered from application
    $enrollment_id     = isset($_POST['enrollment_id']) ? intval($_POST['enrollment_id']) : 0;

    // Check whether required fields are empty
    if (empty($full_name) || empty($nic) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($password) || $course_id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    // Create a Student object
    $STUDENT = new Student(NULL);

    // Check whether the email is already in use
    if (method_exists($STUDENT, 'emailExists') && $STUDENT->emailExists($email)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'This Email address is already registered.'
        ]);
        exit();
    }

    // Assign data (new properties added without breaking the old structure)
    $STUDENT->student_id        = $campus_id; // Mapping campus_id to student_id column
    $STUDENT->campus_id         = $campus_id;
    $STUDENT->full_name         = $full_name;
    $STUDENT->nic               = $nic;
    $STUDENT->first_name        = $first_name;
    $STUDENT->last_name         = $last_name;
    $STUDENT->dob               = $dob;
    $STUDENT->gender            = $gender;
    $STUDENT->phone             = $phone;
    $STUDENT->email             = $email;
    $STUDENT->campus_email      = $campus_email;
    $STUDENT->address           = $address;
    $STUDENT->course_id         = $course_id;
    $STUDENT->study_mode        = $study_mode;
    $STUDENT->intake            = $intake;
    $STUDENT->qualification     = $qualification;
    $STUDENT->school            = $school;
    $STUDENT->guardian_name     = $guardian_name;
    $STUDENT->guardian_phone    = $guardian_phone;
    $STUDENT->guardian_relation = $guardian_relation;
    $STUDENT->emergency_contact = $guardian_phone;
    $STUDENT->password          = password_hash($password, PASSWORD_DEFAULT);
    $STUDENT->status            = 'Active';

    if ($enrollment_id > 0) {
        $STUDENT->enrollment_id = $enrollment_id;
    }

    // Default Avatar
    if (empty($STUDENT->profile_photo)) {
        $STUDENT->profile_photo = "https://ui-avatars.com/api/?name=" . urlencode($first_name . '+' . $last_name) . "&background=0d6efd&color=fff";
    }

    // Insert into database
    $res = $STUDENT->create();

    if ($res) {
        // If registration succeeds, update the enrollment status to 'Registered'
        if ($enrollment_id > 0) {
            $updateStmt = $conn->prepare("UPDATE enrollments SET status = 'Registered' WHERE id = ?");
            $updateStmt->bind_param("i", $enrollment_id);
            $updateStmt->execute();
            $updateStmt->close();
        }

        echo json_encode([
            "status"  => 'success',
            "message" => 'Student registered successfully! Campus ID: ' . $campus_id
        ]);
    
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to register student. Database insertion error.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 2. UPDATE STUDENT
// -------------------------------------------------------------
} elseif ($action === 'update' || (isset($_POST['form_action']) && $_POST['form_action'] === 'update')) {

    $student_db_id = isset($_POST['student_db_id']) ? intval($_POST['student_db_id']) : 0;

    if ($student_db_id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Student ID provided.'
        ]);
        exit();
    }

    $campus_id         = !empty(trim($_POST['campus_id'] ?? '')) ? trim($_POST['campus_id']) : '';
    $campus_email      = !empty(trim($_POST['campus_email'] ?? '')) ? trim($_POST['campus_email']) : '';
    $password          = isset($_POST['password']) ? trim($_POST['password']) : '';
    $full_name         = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $nic               = isset($_POST['nic']) ? trim($_POST['nic']) : '';
    $first_name        = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name         = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $dob               = !empty(trim($_POST['dob'] ?? '')) ? trim($_POST['dob']) : NULL;
    $gender            = !empty(trim($_POST['gender'] ?? '')) ? trim($_POST['gender']) : NULL;
    $phone             = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email             = isset($_POST['email']) ? trim($_POST['email']) : '';
    $address           = !empty(trim($_POST['address'] ?? '')) ? trim($_POST['address']) : NULL;
    $course_id         = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $study_mode        = !empty(trim($_POST['study_mode'] ?? '')) ? trim($_POST['study_mode']) : 'Full-Time';
    $intake            = !empty(trim($_POST['intake'] ?? '')) ? trim($_POST['intake']) : '2026-Batch-01';
    $qualification     = !empty(trim($_POST['qualification'] ?? '')) ? trim($_POST['qualification']) : NULL;
    $school            = !empty(trim($_POST['school'] ?? '')) ? trim($_POST['school']) : NULL;
    $guardian_name     = !empty(trim($_POST['guardian_name'] ?? '')) ? trim($_POST['guardian_name']) : NULL;
    $guardian_phone    = !empty(trim($_POST['guardian_phone'] ?? '')) ? trim($_POST['guardian_phone']) : NULL;
    $guardian_relation = !empty(trim($_POST['guardian_relation'] ?? '')) ? trim($_POST['guardian_relation']) : NULL;

    if (empty($full_name) || empty($nic) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($campus_id) || empty($campus_email) || $course_id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    $STUDENT = new Student($student_db_id);

    if (empty($STUDENT->id)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Student record not found.'
        ]);
        exit();
    }

    if ($STUDENT->emailExists($email, $student_db_id)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'This email address is already registered to another student.'
        ]);
        exit();
    }

    if ($STUDENT->campusEmailExists($campus_email, $student_db_id)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'This campus email is already registered to another student.'
        ]);
        exit();
    }

    // Keep existing hash if password field left blank on update
    if (!empty($password) && !(strlen($password) >= 60 && str_starts_with($password, '$2y$'))) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $passwordHash = !empty($STUDENT->password) ? $STUDENT->password : password_hash('student123', PASSWORD_DEFAULT);
    }

    $STUDENT->student_id        = $campus_id;
    $STUDENT->campus_id         = $campus_id;
    $STUDENT->campus_email      = $campus_email;
    $STUDENT->full_name         = $full_name;
    $STUDENT->nic               = $nic;
    $STUDENT->first_name        = $first_name;
    $STUDENT->last_name         = $last_name;
    $STUDENT->dob               = $dob;
    $STUDENT->gender            = $gender;
    $STUDENT->phone             = $phone;
    $STUDENT->email             = $email;
    $STUDENT->address           = $address;
    $STUDENT->course_id         = $course_id;
    $STUDENT->study_mode        = $study_mode;
    $STUDENT->intake            = $intake;
    $STUDENT->qualification     = $qualification;
    $STUDENT->school            = $school;
    $STUDENT->guardian_name     = $guardian_name;
    $STUDENT->guardian_phone    = $guardian_phone;
    $STUDENT->guardian_relation = $guardian_relation;
    $STUDENT->password          = $passwordHash;
    $STUDENT->status            = $STUDENT->status ?? 'Active';

    if ($STUDENT->update()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Student details updated successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to update student. Database error.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 3. FETCH ALL STUDENTS
// -------------------------------------------------------------
} elseif ($action === 'fetch_all' || isset($_POST['fetch_all'])) {

    $STUDENT = new Student(NULL);
    $students = $STUDENT->all();

    echo json_encode([
        "status" => 'success',
        "data"   => $students
    ]);
    exit();

// -------------------------------------------------------------
// 3. DELETE STUDENT
// -------------------------------------------------------------
} elseif ($action === 'delete' || isset($_POST['delete'])) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Student ID provided.'
        ]);
        exit();
    }

    $STUDENT = new Student($id);
    if ($STUDENT->delete()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Student deleted successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to delete student.'
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