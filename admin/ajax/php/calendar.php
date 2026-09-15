<?php
// exact error එක බලාගැනීමට temporary errors enable කරමු
ini_set('display_errors', 1);
error_reporting(E_ALL);
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
// 1. CREATE CALENDAR SCHEDULE
// -------------------------------------------------------------
if ($action === 'create' || isset($_POST['create'])) {

    $title         = isset($_POST['title']) ? trim($_POST['title']) : '';
    $type          = isset($_POST['type']) ? trim($_POST['type']) : '';
    $event_date    = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $start_time    = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
    $end_time      = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';
    $instructor_id = isset($_POST['instructor_id']) ? intval($_POST['instructor_id']) : 0;

    // Required inputs validation
    if (empty($title) || empty($type) || empty($event_date) || $instructor_id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Please fill in all required fields.'
        ]);
        exit();
    }

    try {
        $CALENDAR = new Calendar(NULL);
        $CALENDAR->title         = $title;
        $CALENDAR->type          = $type;
        $CALENDAR->event_date    = $event_date;
        $CALENDAR->start_time    = $start_time;
        $CALENDAR->end_time      = $end_time;
        $CALENDAR->instructor_id = $instructor_id;

        $res = $CALENDAR->create();

        if ($res) {
            echo json_encode([
                "status"  => 'success',
                "message" => 'Schedule created successfully!'
            ]);
        } else {
            echo json_encode([
                "status"  => 'error',
                "message" => 'Failed to create schedule. Database insertion error.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Database Error: ' . $e->getMessage()
        ]);
    }
    exit();

// -------------------------------------------------------------
// 2. FETCH ALL SCHEDULES
// -------------------------------------------------------------
} elseif ($action === 'fetch_all' || isset($_POST['fetch_all'])) {

    $CALENDAR = new Calendar(NULL);
    $events = $CALENDAR->all();

    echo json_encode([
        "status" => 'success',
        "data"   => $events
    ]);
    exit();

// -------------------------------------------------------------
// 3. DELETE SCHEDULE
// -------------------------------------------------------------
} elseif ($action === 'delete' || isset($_POST['delete'])) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Invalid Schedule ID provided.'
        ]);
        exit();
    }

    $EVENT = new Calendar($id);
    if ($EVENT->delete()) {
        echo json_encode([
            "status"  => 'success',
            "message" => 'Schedule deleted successfully!'
        ]);
    } else {
        echo json_encode([
            "status"  => 'error',
            "message" => 'Failed to delete schedule.'
        ]);
    }
    exit();

} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request action.'
    ]);
    exit();
}