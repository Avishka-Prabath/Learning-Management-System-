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
// 1. CREATE ANNOUNCEMENT
// -------------------------------------------------------------
if ($action === 'create' || isset($_POST['create'])) {

    $target_audience = isset($_POST['target_audience']) ? trim($_POST['target_audience']) : 'all';
    $title           = isset($_POST['title']) ? trim($_POST['title']) : '';
    $message         = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($title) || empty($message)) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    $ANNOUNCEMENT = new Announcements(NULL);
    $ANNOUNCEMENT->target_audience = $target_audience;
    $ANNOUNCEMENT->title           = $title;
    $ANNOUNCEMENT->message         = $message;

    $res = $ANNOUNCEMENT->create();

    if ($res) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Announcement published successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to publish announcement. Database insertion error.'
        ]);
    }
    exit();

// -------------------------------------------------------------
// 2. FETCH ALL ANNOUNCEMENTS
// -------------------------------------------------------------
} elseif ($action === 'fetch_all' || isset($_POST['fetch_all'])) {

    $ANNOUNCEMENT = new Announcements(NULL);
    $announcements = $ANNOUNCEMENT->all();

    echo json_encode([
        "status" => 'success',
        "data"   => $announcements
    ]);
    exit();

// -------------------------------------------------------------
// 3. DELETE ANNOUNCEMENT
// -------------------------------------------------------------
} elseif ($action === 'delete' || isset($_POST['delete'])) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Announcement ID provided.'
        ]);
        exit();
    }

    $ANNOUNCEMENT = new Announcements($id);
    if ($ANNOUNCEMENT->delete()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Announcement deleted successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to delete announcement.'
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