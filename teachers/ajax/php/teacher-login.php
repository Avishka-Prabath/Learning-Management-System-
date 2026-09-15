<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

// Include db.php from the root
require_once __DIR__ . '/../../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($username) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in both fields.']);
    exit();
}

// Check the teacher by username or email in the database
$stmt = $conn->prepare("SELECT id, title, full_name, username, email, password FROM teachers WHERE (username = ? OR email = ?) LIMIT 1");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $teacher = $result->fetch_assoc();

    // Support both plain text and hashed passwords
    if ($password === $teacher['password'] || password_verify($password, $teacher['password'])) {
        
        // Set session variables
        $_SESSION['teacher_logged_in'] = true;
        $_SESSION['teacher_id']        = $teacher['id'];
        $_SESSION['teacher_name']      = (!empty($teacher['title']) ? $teacher['title'] . ' ' : '') . $teacher['full_name'];
        $_SESSION['teacher_email']     = $teacher['email'];

        echo json_encode([
            'status'  => 'success',
            'message' => 'Login successful! Redirecting...'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Incorrect password provided.'
        ]);
    }
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Instructor account not found.'
    ]);
}
$stmt->close();