<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Enroll.php';
require_once __DIR__ . '/Contact.php';



// Session Start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}