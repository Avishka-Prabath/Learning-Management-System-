<?php
session_start();

// 1. All session variables clear කිරීම
$_SESSION = array();

// 2. Session cookie එක Delete කිරීම (If exists)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Session එක සම්පූර්ණයෙන්ම Destroy කිරීම
session_destroy();

// 4. Student Login Page එකට Redirect කිරීම
header("Location: login.php");
exit();
?>