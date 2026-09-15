<?php

// 1. Include core OOP dependencies
include __DIR__ . '/../../class/include.php';
header('Content-Type: application/json; charset=UTF-8');

// 2. Action validation logic
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

// -------------------------------------------------------------
// 1. FETCH ALL CATEGORIES / ASSIGNMENTS
// -------------------------------------------------------------
if ($action === 'fetch_all' || isset($_POST['fetch_all'])) {

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $result = $conn->query("SELECT * FROM assignments ORDER BY deadline_date DESC");
    $records = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
    }

    echo json_encode($records);
    exit();

// -------------------------------------------------------------
// 2. CREATE ASSIGNMENT
// -------------------------------------------------------------
} elseif ($action === 'create' || $action === 'create_assignment' || isset($_POST['create'])) {

    $title         = isset($_POST['title']) ? trim($_POST['title']) : '';
    $course_name   = isset($_POST['course_name']) ? trim($_POST['course_name']) : '';
    $deadline_date = isset($_POST['deadline_date']) ? trim($_POST['deadline_date']) : '';
    $deadline_time = isset($_POST['deadline_time']) ? trim($_POST['deadline_time']) : '';
    $type          = isset($_POST['type']) ? trim($_POST['type']) : 'Assignment';
    $status        = 'Active';

    if (empty($title) || empty($course_name) || empty($deadline_date) || empty($deadline_time)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    $file_path = NULL;
    if (isset($_FILES['brief_doc']) && $_FILES['brief_doc']['error'] == 0) {
        $target_dir = __DIR__ . "/../../uploads/";
        if (!file_exists($target_dir)) { 
            mkdir($target_dir, 0777, true); 
        }
        $file_name = time() . '_' . basename($_FILES["brief_doc"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["brief_doc"]["tmp_name"], $target_file)) {
            $file_path = $file_name;
        }
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO assignments (title, course_name, deadline_date, deadline_time, file_path, type, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $title, $course_name, $deadline_date, $deadline_time, $file_path, $type, $status);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Assignment published successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to publish assignment.'
        ]);
    }
    $stmt->close();
    exit();

// -------------------------------------------------------------
// 3. UPDATE ASSIGNMENT
// -------------------------------------------------------------
} elseif ($action === 'update' || isset($_POST['update'])) {

    $id            = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title         = isset($_POST['title']) ? trim($_POST['title']) : '';
    $course_name   = isset($_POST['course_name']) ? trim($_POST['course_name']) : '';
    $deadline_date = isset($_POST['deadline_date']) ? trim($_POST['deadline_date']) : '';
    $deadline_time = isset($_POST['deadline_time']) ? trim($_POST['deadline_time']) : '';
    $type          = isset($_POST['type']) ? trim($_POST['type']) : 'Assignment';

    if ($id <= 0 || empty($title) || empty($course_name)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid parameters.'
        ]);
        exit();
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("UPDATE assignments SET title = ?, course_name = ?, deadline_date = ?, deadline_time = ?, type = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $title, $course_name, $deadline_date, $deadline_time, $type, $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Assignment updated successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to update Assignment.'
        ]);
    }
    $stmt->close();
    exit();

// -------------------------------------------------------------
// 4. DELETE ASSIGNMENT
// -------------------------------------------------------------
} elseif ($action === 'delete' || isset($_POST['delete'])) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Assignment ID provided.'
        ]);
        exit();
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("DELETE FROM assignments WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Assignment deleted successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to delete Assignment.'
        ]);
    }
    $stmt->close();
    exit();

// -------------------------------------------------------------
// 5. ENROLL STUDENT
// -------------------------------------------------------------
} elseif ($action === 'enroll_student') {
    $full_name  = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email      = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone      = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $course_val = isset($_POST['course']) ? trim($_POST['course']) : '';

    if (empty($full_name) || empty($email) || empty($phone) || empty($course_val)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    // 1. Get or Create Student Record
    $checkStudent = $conn->prepare("SELECT id FROM students WHERE email = ?");
    $checkStudent->bind_param("s", $email);
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
                "message" => 'Failed to create student record.'
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

    // 3. Check existing enrollment
    $checkEnroll = $conn->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ? LIMIT 1");
    $checkEnroll->bind_param("ii", $student_id, $course_id);
    $checkEnroll->execute();
    $enrollResult = $checkEnroll->get_result();

    if ($enrollResult && $enrollResult->num_rows > 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'You are already enrolled in this course.'
        ]);
        exit();
    }

    // 4. Create enrollment record
    $stmtEnroll = $conn->prepare("INSERT INTO enrollments (student_id, course_id, payment_status, status) VALUES (?, ?, 'Pending', 'Pending')");
    $stmtEnroll->bind_param("ii", $student_id, $course_id);

    if ($stmtEnroll->execute()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Enrollment submitted successfully! Use password "student123" to log in.'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to process course enrollment.'
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