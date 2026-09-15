<?php 



require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Course.php';
require_once __DIR__ . '/Materials.php';
require_once __DIR__ . '/Assignment.php';
require_once __DIR__ . '/Announcement.php'; // <-- Added this line






if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>