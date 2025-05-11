<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
function getConnection() {
    $server = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hris";

    $conn = new mysqli($server, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Get unread notifications count
function getUnreadNotificationsCount() {
    $conn = getConnection();
    $sql = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    return $row['count'];
}

// Get recent notifications (limit to 10)
function getRecentNotifications($limit = 10) {
    $conn = getConnection();
    $sql = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    return $notifications;
}

// Mark notification as read
function markNotificationAsRead($id) {
    $conn = getConnection();
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Mark all notifications as read
function markAllNotificationsAsRead() {
    $conn = getConnection();
    $sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
    $success = $conn->query($sql);
    $conn->close();
    return $success;
}

// Add a new notification
function addNotification($message, $type, $referenceId = null) {
    $conn = getConnection();
    $sql = "INSERT INTO notifications (message, type, reference_id, is_read, created_at) VALUES (?, ?, ?, 0, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $message, $type, $referenceId);
    $success = $stmt->execute();
    $insertId = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return $insertId;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'get_count':
                echo json_encode(['count' => getUnreadNotificationsCount()]);
                break;
                
            case 'get_notifications':
                echo json_encode(['notifications' => getRecentNotifications()]);
                break;
                
            case 'mark_read':
                if (isset($_POST['id'])) {
                    echo json_encode(['success' => markNotificationAsRead($_POST['id'])]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'No notification ID provided']);
                }
                break;
                
            case 'mark_all_read':
                echo json_encode(['success' => markAllNotificationsAsRead()]);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No action specified']);
    }
    
    exit;
}
?> 