<?php
session_start();

// Database & Admin Class Dependencies Include කිරීම
require_once __DIR__ . '/../admin/class/include.php';

$schedules = [];

// 1. Admin Schedules Table එකෙන් Data ගැනීම
if (class_exists('Calendar')) {
    $calendarObj = new Calendar();
    $adminSchedules = $calendarObj->all();
    if (!empty($adminSchedules)) {
        foreach ($adminSchedules as $item) {
             $rawType = $item['type'] ?? 'Live Class';
             // Standardizing Categories
             $catType = 'Live Class';
             if (strpos($rawType, 'Assignment') !== false) {
                 $catType = 'Assignment';
             } elseif (strpos($rawType, 'Exam') !== false || strpos($rawType, 'Quiz') !== false) {
                 $catType = 'Quiz/Exam';
             }

             $schedules[] = [
                 'id'              => $item['id'],
                 'title'           => $item['title'],
                 'type'            => $catType,
                 'event_date'      => $item['event_date'],
                 'start_time'      => $item['start_time'] ?? '',
                 'end_time'        => $item['end_time'] ?? '',
                 'instructor_name' => $item['instructor_name'] ?? 'Admin/Lecturer',
                 'source'          => 'admin'
             ];
        }
    }
}

// Database Connection එක ලබාගැනීම
$db = Database::getInstance();
$conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

// 2. Fetch Assignments from Module Materials (Instructor Module Briefs)
$matAssignQuery = "
    SELECT mm.*, m.module_code, m.module_name 
    FROM module_materials mm
    INNER JOIN modules m ON mm.module_id = m.id
    WHERE mm.section_type LIKE 'assignment_%'
    ORDER BY mm.end_date ASC, mm.id DESC
";
$matAssignRes = $conn->query($matAssignQuery);

if ($matAssignRes && $matAssignRes->num_rows > 0) {
    while ($mRow = $matAssignRes->fetch_assoc()) {
        // If Deadline/End Date is set, show Deadline Event
        $eventDate = !empty($mRow['end_date']) ? $mRow['end_date'] : (!empty($mRow['start_date']) ? $mRow['start_date'] : date('Y-m-d', strtotime($mRow['created_at'])));
        
        $schedules[] = [
            'id'              => 'mod_mat_' . $mRow['id'],
            'title'           => $mRow['title'] . ' (' . $mRow['module_code'] . ')',
            'type'            => 'Assignment',
            'event_date'      => $eventDate,
            'start_time'      => $mRow['start_time'] ?? '',
            'end_time'        => $mRow['end_time'] ?? '',
            'instructor_name' => 'Module Assignment Brief',
            'module_id'       => $mRow['module_id'],
            'source'          => 'module_materials'
        ];
    }
}

// 3. Teacher Assignments Table එකෙන් Data ගැනීම (General Assignments)
$assignmentsQuery = "SELECT * FROM assignments ORDER BY deadline_date ASC";
$assignmentsRes = $conn->query($assignmentsQuery);

if ($assignmentsRes && $assignmentsRes->num_rows > 0) {
    while ($row = $assignmentsRes->fetch_assoc()) {
        $schedules[] = [
            'id'              => 'assign_' . $row['id'],
            'title'           => $row['title'] . ' (' . ($row['course_name'] ?? 'Assignment') . ')',
            'type'            => 'Assignment',
            'event_date'      => $row['deadline_date'],
            'start_time'      => $row['deadline_time'] ?? '',
            'end_time'        => '',
            'instructor_name' => 'Instructor Coursework',
            'file_path'       => $row['file_path'] ?? '',
            'source'          => 'assignment'
        ];
    }
}

// 4. Teacher/Admin Live Classes Table එකෙන් Data ගැනීම
$liveClassesQuery = "SELECT * FROM live_classes ORDER BY class_date ASC";
$liveClassesRes = $conn->query($liveClassesQuery);

if ($liveClassesRes && $liveClassesRes->num_rows > 0) {
    while ($row = $liveClassesRes->fetch_assoc()) {
        $schedules[] = [
            'id'              => 'live_' . $row['id'],
            'title'           => $row['class_title'] . ' - ' . ($row['module_name'] ?? ''),
            'type'            => 'Live Class',
            'event_date'      => $row['class_date'],
            'start_time'      => $row['start_time'] ?? '',
            'end_time'        => $row['end_time'] ?? '',
            'instructor_name' => $row['platform'] ?? 'Zoom Session',
            'meeting_link'    => $row['meeting_link'] ?? '',
            'source'          => 'live_class'
        ];
    }
}

// Events ටික Date එක අනුව Mapping කර ගැනීම (Calendar Grid එකේ පෙන්වීමට)
$eventsByDay = [];
if (!empty($schedules)) {
    foreach ($schedules as $item) {
        if (!empty($item['event_date'])) {
            $dayNum = date('j', strtotime($item['event_date']));
            $eventsByDay[$dayNum][] = $item;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Calendar - EduMart</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        .calendar-table {
            table-layout: fixed;
            width: 100%;
        }
        .calendar-table th {
            text-align: center;
            background-color: #f8f9fa;
            padding: 12px;
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #6c757d;
        }
        .calendar-table td {
            height: 105px;
            vertical-align: top;
            padding: 6px;
            border: 1px solid #e9ecef;
            background-color: #fff;
            transition: all 0.2s ease;
        }
        .calendar-table td:hover {
            background-color: #f8f9fa;
        }
        .calendar-table .date-num {
            font-weight: 700;
            font-size: 0.85rem;
            color: #495057;
            margin-bottom: 4px;
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            border-radius: 50%;
        }
        .calendar-table .today .date-num {
            background-color: #0d6efd;
            color: #fff;
        }
        .calendar-event-badge {
            font-size: 0.7rem;
            padding: 2px 5px;
            border-radius: 4px;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .other-month {
            background-color: #fafafa !important;
            opacity: 0.4;
        }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar -->
        <?php include('navbar.php'); ?>

        <div class="flex-grow-1 min-vh-100">
            <!-- Topbar -->
            <?php include('topbar.php'); ?>

            <div class="p-4">
                <!-- Page Title -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="fw-bold mb-1"><i class="bi bi-calendar-check me-2 text-primary"></i>Academic Schedule & Events</h4>
                        <p class="text-muted small mb-0">Interactive view for Teacher Assignments, Live Classes & Exam schedules.</p>
                    </div>
                </div>

                <!-- Schedule Card Container -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    
                    <!-- Header Tabs & Calendar Nav -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i><?= date('F Y') ?></h5>
                        </div>
                        
                        <div class="nav nav-pills gap-2" id="calendarTabs">
                            <button type="button" class="nav-link active filter-btn rounded-pill px-3 py-1.5 extra-small fw-bold" data-filter="all">
                                <i class="bi bi-grid-fill me-1"></i> All Items
                            </button>
                            <button type="button" class="nav-link filter-btn rounded-pill px-3 py-1.5 extra-small fw-bold btn-outline-primary" data-filter="Live Class">
                                <i class="bi bi-camera-video-fill me-1"></i> Live Lectures
                            </button>
                            <button type="button" class="nav-link filter-btn rounded-pill px-3 py-1.5 extra-small fw-bold btn-outline-danger" data-filter="Assignment">
                                <i class="bi bi-file-earmark-arrow-up-fill me-1"></i> Assignments
                            </button>
                            <button type="button" class="nav-link filter-btn rounded-pill px-3 py-1.5 extra-small fw-bold btn-outline-warning text-dark" data-filter="Quiz/Exam">
                                <i class="bi bi-clock-history me-1"></i> Exams & Quizzes
                            </button>
                        </div>
                    </div>

                    <!-- 1. DYNAMIC GRID CALENDAR VIEW -->
                    <div class="table-responsive rounded-3 overflow-hidden border mb-4">
                        <table class="table calendar-table mb-0">
                            <thead>
                                <tr>
                                    <th>Sun</th>
                                    <th>Mon</th>
                                    <th>Tue</th>
                                    <th>Wed</th>
                                    <th>Thu</th>
                                    <th>Fri</th>
                                    <th>Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $todayDay = date('j');
                                $currentDay = 1;
                                $maxDays = date('t');

                                for ($row = 0; $row < 5; $row++): ?>
                                    <tr>
                                        <?php for ($col = 0; $col < 7; $col++): ?>
                                            <?php if ($currentDay <= $maxDays): ?>
                                                <td class="<?= ($currentDay == $todayDay) ? 'today' : '' ?>">
                                                    <span class="date-num"><?= $currentDay ?></span>
                                                    
                                                    <!-- අදාළ දවසට තිබෙන Events Display කිරීම -->
                                                    <?php if (isset($eventsByDay[$currentDay])): ?>
                                                        <?php foreach ($eventsByDay[$currentDay] as $ev): ?>
                                                            <?php 
                                                                $evType = $ev['type'] ?? 'Live Class';
                                                                $bClass = 'bg-primary text-white';
                                                                if ($evType === 'Assignment') $bClass = 'bg-danger text-white';
                                                                if ($evType === 'Quiz/Exam') $bClass = 'bg-warning text-dark';
                                                            ?>
                                                            <a href="#event-<?= $ev['id'] ?>" 
                                                               class="calendar-event-badge grid-event-item <?= $bClass ?>" 
                                                               data-category="<?= htmlspecialchars($evType) ?>" 
                                                               title="<?= htmlspecialchars($ev['title']) ?>">
                                                                <?= htmlspecialchars($ev['title']) ?>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>

                                                </td>
                                                <?php $currentDay++; ?>
                                            <?php else: ?>
                                                <td class="other-month"></td>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>

                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-stars me-1 text-primary"></i> Detailed Schedule Feed</h6>

                    <!-- 2. DETAILED EVENTS LIST VIEW -->
                    <div class="d-flex flex-column gap-3" id="scheduleList">

                        <?php if (!empty($schedules)): ?>
                            <?php foreach ($schedules as $item): ?>
                                <?php 
                                    $type = $item['type'] ?? 'Live Class';
                                    $date = !empty($item['event_date']) ? strtotime($item['event_date']) : time();
                                    $month = date('M', $date);
                                    $day = date('d', $date);

                                    $badgeClass = 'bg-primary text-white';
                                    $borderClass = 'border-primary-subtle';
                                    $bgBoxClass = 'bg-primary bg-opacity-10 text-primary';
                                    $iconClass = 'bi-camera-video';
                                    $btnClass = 'btn-primary';
                                    $btnText = 'Join Class';

                                    if ($type === 'Assignment') {
                                        $badgeClass = 'bg-danger text-white';
                                        $borderClass = 'border-danger-subtle';
                                        $bgBoxClass = 'bg-danger bg-opacity-10 text-danger';
                                        $iconClass = 'bi-file-earmark-check';
                                        $btnClass = 'btn-danger';
                                        $btnText = 'View Assignment';
                                    } elseif ($type === 'Quiz/Exam') {
                                        $badgeClass = 'bg-warning text-dark';
                                        $borderClass = 'border-warning-subtle';
                                        $bgBoxClass = 'bg-warning bg-opacity-20 text-dark';
                                        $iconClass = 'bi-clock-history';
                                        $btnClass = 'btn-warning text-dark';
                                        $btnText = 'Start Quiz';
                                    }
                                ?>

                                <div id="event-<?= $item['id'] ?>" class="p-3 border rounded-4 event-card schedule-item d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" data-category="<?= htmlspecialchars($type) ?>">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="date-box <?= $bgBoxClass ?> border <?= $borderClass ?> text-center p-2 rounded-3" style="min-width: 60px;">
                                            <span class="fw-bold extra-small text-uppercase d-block"><?= $month ?></span>
                                            <span class="fs-4 fw-bold lh-1"><?= $day ?></span>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge <?= $badgeClass ?> px-2.5 py-1 rounded-2">
                                                    <i class="bi <?= $iconClass ?> me-1"></i> <?= htmlspecialchars($type) ?>
                                                </span>
                                            </div>
                                            <h6 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($item['title']) ?></h6>
                                            <div class="text-muted small d-flex flex-wrap gap-3 align-items-center">
                                                <?php if (!empty($item['start_time'])): ?>
                                                    <span><i class="bi bi-clock me-1 text-primary"></i> <?= date('h:i A', strtotime($item['start_time'])) ?> <?= !empty($item['end_time']) ? '- ' . date('h:i A', strtotime($item['end_time'])) : '' ?></span>
                                                <?php endif; ?>

                                                <?php if (!empty($item['instructor_name'])): ?>
                                                    <span><i class="bi bi-person-badge me-1 text-primary"></i> <?= htmlspecialchars($item['instructor_name']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <?php if (!empty($item['meeting_link'])): ?>
                                            <a href="<?= htmlspecialchars($item['meeting_link']) ?>" target="_blank" class="btn <?= $btnClass ?> rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> <?= $btnText ?>
                                            </a>
                                        <?php elseif ($type === 'Assignment' && isset($item['module_id'])): ?>
                                            <a href="student-module-details.php?id=<?= $item['module_id'] ?>" class="btn <?= $btnClass ?> rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                                                <i class="bi bi-upload me-1"></i> <?= $btnText ?>
                                            </a>
                                        <?php elseif ($type === 'Assignment'): ?>
                                            <a href="assignments.php" class="btn <?= $btnClass ?> rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                                                <i class="bi bi-upload me-1"></i> <?= $btnText ?>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn <?= $btnClass ?> rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                                                <?= $btnText ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x display-4 d-block mb-2 opacity-50"></i>
                                <p class="mb-0 fw-medium">No upcoming academic events scheduled yet.</p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Filter Buttons JS -->
    <script>
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                let filter = this.getAttribute('data-filter');
                
                // 1. Filter Detailed List Feed
                let listItems = document.querySelectorAll('.schedule-item');
                listItems.forEach(item => {
                    let cat = item.getAttribute('data-category');
                    if (filter === 'all' || cat === filter) {
                        item.style.setProperty('display', 'flex', 'important');
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });

                // 2. Filter Grid Calendar Badges
                let gridItems = document.querySelectorAll('.grid-event-item');
                gridItems.forEach(item => {
                    let cat = item.getAttribute('data-category');
                    if (filter === 'all' || cat === filter) {
                        item.style.setProperty('display', 'block', 'important');
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });
            });
        });
    </script>
</body>
</html>