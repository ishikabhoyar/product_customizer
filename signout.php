<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// If a session cookie is used, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear any "remember me" cookies if they exist
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Set a message for the login page
session_start(); // Start a new session to store the message
$_SESSION['message'] = "You have been successfully logged out.";

// Redirect to login page
header("Location: login.php");
exit();
?>