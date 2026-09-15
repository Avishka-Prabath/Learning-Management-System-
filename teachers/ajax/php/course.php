<?php

// 1. Include core OOP dependencies
include __DIR__ . '/../../class/include.php';
header('Content-Type: application/json; charset=UTF-8');

// 2. Action validation logic
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

// -------------------------------------------------------------
// 1. FETCH ALL MATERIALS / LECTURES
// -------------------------------------------------------------
if ($action === 'fetch_all' || isset($_POST['fetch_all'])) {

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $result = $conn->query("SELECT * FROM course_materials ORDER BY id DESC");
    $records = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
    }

    echo json_encode($records);
    exit();

// -------------------------------------------------------------
// 2. UPLOAD MATERIAL
// -------------------------------------------------------------
} elseif ($action === 'upload_material' || isset($_POST['upload_material'])) {

    $lecture_id    = isset($_POST['lecture']) ? trim($_POST['lecture']) : '';
    $title         = isset($_POST['title']) ? trim($_POST['title']) : '';
    $external_link = isset($_POST['external_link']) ? trim($_POST['external_link']) : '';
    $status        = 'Active';

    if (empty($lecture_id) || empty($title)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    $file_path = NULL;
    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
        $target_dir = __DIR__ . "/../../uploads/materials/";
        if (!file_exists($target_dir)) { 
            mkdir($target_dir, 0777, true); 
        }
        $file_name = time() . '_' . basename($_FILES["material_file"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["material_file"]["tmp_name"], $target_file)) {
            $file_path = $file_name;
        }
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO course_materials (lecture_id, title, file_path, external_link, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $lecture_id, $title, $file_path, $external_link, $status);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Material uploaded successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to upload material.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 3. DELETE MATERIAL
// -------------------------------------------------------------
} elseif ($action === 'delete' || isset($_POST['delete'])) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Material ID provided.'
        ]);
        exit();
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("DELETE FROM course_materials WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Material deleted successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to delete Material.'
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