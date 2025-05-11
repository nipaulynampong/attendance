<?php
session_start();
header('Content-Type: application/json');

// Include notifications functions
require_once 'notifications_functions.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

// Helper function to verify password with multiple methods
function verify_password($input_password, $stored_password) {
    if (password_verify($input_password, $stored_password)) return true;
    if ($input_password === $stored_password) return true;
    if (md5($input_password) === $stored_password) return true;
    if (sha1($input_password) === $stored_password) return true;
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Check if the account exists and its status
    $check_sql = "SELECT * FROM admin WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($user = $check_result->fetch_assoc()) {
        // Check if account is locked
        $now = time();
        $locked_until = strtotime($user['locked_until'] ?? '2000-01-01 00:00:00');
        $cooldown_until = strtotime($user['cooldown_until'] ?? '2000-01-01 00:00:00');

        // Auto-reset login attempts if 24 hours have passed since lockout
        if ($locked_until > 0 && $now > $locked_until) {
            // Reset the account after lockout period
            $reset_sql = "UPDATE admin SET login_attempts = 0, locked_until = NULL WHERE id = ?";
            $reset_stmt = $conn->prepare($reset_sql);
            $reset_stmt->bind_param("i", $user['id']);
            $reset_stmt->execute();
            $user['login_attempts'] = 0;
            $locked_until = 0;
        }

        // Check if account is still locked
        if ($locked_until > $now) {
            $time_remaining = $locked_until - $now;
            $hours = floor($time_remaining / 3600);
            $minutes = floor(($time_remaining % 3600) / 60);
            echo json_encode([
                'success' => false, 
                'message' => "Account is locked. Please try again after {$hours} hours and {$minutes} minutes or reset your password."
            ]);
            exit;
        }

        // Check if account is in cooldown
        if ($cooldown_until > $now) {
            $time_remaining = $cooldown_until - $now;
            echo json_encode([
                'success' => false, 
                'message' => "Please wait {$time_remaining} seconds before trying again.",
                'cooldown' => $time_remaining
            ]);
            exit;
        }

        // Check if password is correct
        if (verify_password($password, $user['password'])) {
            // Successful login - reset login attempts
            $update_sql = "UPDATE admin SET last_login = NOW(), login_attempts = 0, last_attempt = NULL, cooldown_until = NULL WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();

            // Set session variables
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_full_name'] = $user['full_name'];
            
            // Create login notification using the improved registerLoginNotification function
            // This function handles timezone, formatting, and creates both system-wide and user-specific notifications
            registerLoginNotification($user['id'], $user['full_name']);

            // Include a redirect URL in the response
            echo json_encode(['success' => true, 'redirect' => 'AdminPanel.php']);
        } else {
            // Wrong password - increment attempts
            $new_attempts = $user['login_attempts'] + 1;
            $cooldown_until = null;
            $locked_until = null;
            $message = 'Invalid credentials';
            
            // Apply the same cooldown logic as in login.php
            if ($new_attempts === 4) {
                $cooldown_time = 60;
                $cooldown_until = date('Y-m-d H:i:s', strtotime('+60 seconds'));
                $message = 'Cooldown: Please wait 60 seconds before trying again.';
            } else if ($new_attempts === 7) {
                $cooldown_time = 300;
                $cooldown_until = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                $message = 'Cooldown: Please wait 5 minutes before trying again.';
            } else if ($new_attempts >= 10) {
                $locked_until = date('Y-m-d H:i:s', strtotime('+24 hours'));
                $message = 'Account locked for 24 hours due to too many failed attempts.';
            }
            
            // Update the database
            $update_sql = "UPDATE admin SET login_attempts = ?, last_attempt = NOW(), cooldown_until = ?, locked_until = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("issi", $new_attempts, $cooldown_until, $locked_until, $user['id']);
            $update_stmt->execute();
            
            // Return appropriate response
            if ($cooldown_until) {
                $cooldown_time = strtotime($cooldown_until) - time();
                echo json_encode([
                    'success' => false, 
                    'message' => $message,
                    'cooldown' => $cooldown_time
                ]);
            } else if ($locked_until) {
                echo json_encode([
                    'success' => false, 
                    'message' => $message
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => $message
                ]);
            }
        }
    } else {
        // Username not found
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
