<?php

// 1. Include core OOP dependencies
include __DIR__ . '/../../class/include.php';
header('Content-Type: application/json; charset=UTF-8');

// 2. Action validation logic
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

// -------------------------------------------------------------
// 1. FETCH ALL MESSAGES
// -------------------------------------------------------------
if ($action === 'fetch_all' || isset($_POST['fetch_all'])) {

    $db = Database::getInstance();
    $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

    $query = "SELECT * FROM contact_messages ORDER BY id DESC";
    $result = $conn->query($query);

    $array_res = array();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            array_push($array_res, $row);
        }
    }

    echo json_encode([
        "status" => 'success',
        "data"   => $array_res
    ]);
    exit();

// -------------------------------------------------------------
// 2. SEND CONTACT MESSAGE
// -------------------------------------------------------------
} elseif ($action === 'send_message' || isset($_POST['send_message'])) {

    $name    = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email   = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Field Validations
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    $db = Database::getInstance();
    $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

    // Create Message Record
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Your message has been sent successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to send message. Database insertion error.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 3. DELETE MESSAGE
// -------------------------------------------------------------
} elseif ($action === 'delete' || isset($_POST['delete'])) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Message ID provided.'
        ]);
        exit();
    }

    $db = Database::getInstance();
    $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Message deleted successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to delete message.'
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