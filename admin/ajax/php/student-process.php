<?php
session_start();
include_once('../../db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $enrollment_id = intval($_POST['enrollment_id'] ?? 0);
    $student_db_id = intval($_POST['student_db_id'] ?? 0);
    $campus_id     = trim($_POST['campus_id'] ?? '');
    $campus_email  = trim($_POST['campus_email'] ?? '');
    $password      = trim($_POST['password'] ?? '');

    // Basic Validation
    if (empty($campus_id) || empty($campus_email) || empty($password)) {
        echo json_encode([
            'status'  => 'error', 
            'message' => 'Campus Student ID, Email, and Password are required!'
        ]);
        exit();
    }

    // -------------------------------------------------------------
    // EMAIL DUPLICATE CHECK
    // -------------------------------------------------------------
    if ($student_db_id > 0) {
        // UPDATE MODE: තමන්ගේ ID එක හැර වෙනත් කෙනෙකුට මේ Email එක ඇත්දැයි බැලීම
        $checkEmail = $conn->prepare("SELECT id FROM students WHERE campus_email = ? AND id != ?");
        $checkEmail->bind_param("si", $campus_email, $student_db_id);
    } else {
        // INSERT MODE: ඩේටාබේස් එකේ වෙනත් කෙනෙකුට මේ Email එක තිබේදැයි බැලීම
        $checkEmail = $conn->prepare("SELECT id FROM students WHERE campus_email = ?");
        $checkEmail->bind_param("s", $campus_email);
    }

    $checkEmail->execute();
    $emailRes = $checkEmail->get_result();

    if ($emailRes && $emailRes->num_rows > 0) {
        echo json_encode([
            'status'  => 'error', 
            'message' => 'This Campus Email address is already assigned to another student.'
        ]);
        exit();
    }
    $checkEmail->close();

    // -------------------------------------------------------------
    // EXECUTE UPDATE OR INSERT
    // -------------------------------------------------------------
    if ($student_db_id > 0) {
        // UPDATE Existing Student Credentials
        $stmt = $conn->prepare("UPDATE students SET campus_id = ?, campus_email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $campus_id, $campus_email, $password, $student_db_id);

        if ($stmt->execute()) {
            echo json_encode([
                'status'  => 'success', 
                'message' => 'Campus Credentials updated successfully!'
            ]);
        } else {
            echo json_encode([
                'status'  => 'error', 
                'message' => 'Failed to update credentials: ' . $conn->error
            ]);
        }
        $stmt->close();

    } else {
        // FETCH ENROLLMENT DATA TO LINK PERSONAL DETAILS
        $full_name = '';
        $email     = '';
        $phone     = '';
        $nic       = '';
        $course_id = 0;

        if ($enrollment_id > 0) {
            $enrStmt = $conn->prepare("SELECT full_name, email, phone, nic, course_id FROM enrollments WHERE id = ?");
            $enrStmt->bind_param("i", $enrollment_id);
            $enrStmt->execute();
            $enrRes = $enrStmt->get_result();
            if ($enrRow = $enrRes->fetch_assoc()) {
                $full_name = $enrRow['full_name'];
                $email     = $enrRow['email'];
                $phone     = $enrRow['phone'];
                $nic       = $enrRow['nic'];
                $course_id = $enrRow['course_id'];
            }
            $enrStmt->close();
        }

        // INSERT Credentials into Students Table
        $stmt = $conn->prepare("INSERT INTO students (enrollment_id, campus_id, campus_email, password, full_name, email, phone, nic, course_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
        $stmt->bind_param("isssssssi", $enrollment_id, $campus_id, $campus_email, $password, $full_name, $email, $phone, $nic, $course_id);

        if ($stmt->execute()) {
            // Mark enrollment as Registered
            if ($enrollment_id > 0) {
                $conn->query("UPDATE enrollments SET status = 'Registered' WHERE id = $enrollment_id");
            }
            echo json_encode([
                'status'  => 'success', 
                'message' => 'Student Campus Credentials created and registered successfully!'
            ]);
        } else {
            echo json_encode([
                'status'  => 'error', 
                'message' => 'Failed to save credentials: ' . $conn->error
            ]);
        }
        $stmt->close();
    }
}