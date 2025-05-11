<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the SessionManager class is available
if (file_exists('classes/SessionManager.php')) {
    require_once 'classes/SessionManager.php';
    // Use the SessionManager to destroy the session
    $sessionManager = SessionManager::getInstance();
    $sessionManager->destroy();
} else {
    // Fallback session destruction if SessionManager is not available
    // Clear all session variables
    $_SESSION = array();

    // If a session cookie is used, delete it
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();
}

// Redirect to homepage.php
header("Location: login.php");
exit();
?> 