<?php
// Output buffering ආරම්භ කර නොපෙනෙන warnings/notices නිසා JSON කඩාවැටීම වැළැක්වීම[cite: 14]
ob_start();

// 1. Include core OOP dependencies[cite: 14]
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

// Global Database Connection ($conn) එක initialize කර ගැනීම
$db = Database::getInstance();
$conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

// Output Buffer එක සුද්ධ කර JSON Header එක සැකසීම[cite: 14]
ob_clean();
header('Content-Type: application/json; charset=UTF-8');

// 2. Request Method Check[cite: 14]
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request method.'
    ]);
    exit();
}

// 3. Action validation logic[cite: 14]
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

// -------------------------------------------------------------
// 1. CREATE / REGISTER STUDENT
// -------------------------------------------------------------
if ($action === 'create' || isset($_POST['create'])) {

    // Input Data ලබා ගැනීම (JavaScript ෆයිල් එකට අනුකූලව සියලුම Fields එකතු කර ඇත)[cite: 14]
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
    
    // Campus Specific Credentials[cite: 14]
    $campus_id         = !empty(trim($_POST['campus_id'] ?? '')) ? trim($_POST['campus_id']) : 'EM-2026-' . rand(1000, 9999);
    $campus_email      = !empty(trim($_POST['campus_email'] ?? '')) ? trim($_POST['campus_email']) : strtolower($first_name . '.' . rand(100,999) . '@edumart.ac.lk');
    $password          = isset($_POST['password']) ? trim($_POST['password']) : 'student123';
    
    // Hidden Enrollment ID if registered from application[cite: 14]
    $enrollment_id     = isset($_POST['enrollment_id']) ? intval($_POST['enrollment_id']) : 0;

    // අනිවාර්ය Fields හිස්දැයි පරීක්ෂා කිරීම[cite: 14]
    if (empty($full_name) || empty($nic) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($password) || $course_id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    // Student Object එකක් සෑදීම[cite: 14]
    $STUDENT = new Student(NULL);

    // Email එක කලින් භාවිතා කර ඇත්දැයි පරීක්ෂා කිරීම[cite: 14]
    if (method_exists($STUDENT, 'emailExists') && $STUDENT->emailExists($email)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'This Email address is already registered.'
        ]);
        exit();
    }

    // Data Assign කිරීම (පැරණි Structure එක නොවස්වා අවශ්‍ය නව properties එකතු කර ඇත)[cite: 14]
    $STUDENT->student_id        = $campus_id; // Mapping campus_id to student_id column[cite: 14]
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

    // Default Avatar[cite: 14]
    if (empty($STUDENT->profile_photo)) {
        $STUDENT->profile_photo = "https://ui-avatars.com/api/?name=" . urlencode($first_name . '+' . $last_name) . "&background=0d6efd&color=fff";
    }

    // Database එකට ඇතුළත් කිරීම[cite: 14]
    $res = $STUDENT->create();

    if ($res) {
        // Registration එක සාර්ථක නම් Enrollment එකේ status එක 'Registered' බවට update කරන්න[cite: 14]
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

    // Keep existing hash if password field left blank on update[cite: 14]
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
// 3. FETCH ALL STUDENTS[cite: 14]
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
// 3. DELETE STUDENT[cite: 14]
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
// INVALID ACTION[cite: 14]
// -------------------------------------------------------------
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request action.'
    ]);
    exit();
}