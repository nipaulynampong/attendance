<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

/**
 * Get database connection
 * @return mysqli Database connection
 */
function getConnection() {
    global $servername, $username, $password, $dbname;
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

/**
 * Create a new notification
 * 
 * @param int|null $userId User ID (admin ID) - null for system notifications
 * @param string $type Type of event (login, attendance, etc.)
 * @param string $message Notification message
 * @param int|null $referenceId Reference ID (optional) - ID of the related record
 * @return bool True if successful, false otherwise
 */
function createNotification($userId, $type, $message, $referenceId = null) {
    $conn = getConnection();
    
    // Check if the notifications table has user_id column or not
    $tableInfo = $conn->query("SHOW COLUMNS FROM notifications LIKE 'user_id'");
    
    if ($tableInfo && $tableInfo->num_rows > 0) {
        // Table has user_id column
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, message, reference_id, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
        $stmt->bind_param("issi", $userId, $type, $message, $referenceId);
    } else {
        // Table doesn't have user_id column - use the simpler structure
        $stmt = $conn->prepare("INSERT INTO notifications (message, type, reference_id, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        $stmt->bind_param("ssi", $message, $type, $referenceId);
    }
    
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

/**
 * Get unread notifications count
 * 
 * @return int Number of unread notifications
 */
function getUnreadNotificationsCount() {
    $conn = getConnection();
    
    $sql = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";
    $result = $conn->query($sql);
    
    $count = 0;
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $count = $row['count'];
    }
    
    $conn->close();
    
    return $count;
}

/**
 * Get recent notifications
 * 
 * @param int $limit Number of notifications to retrieve
 * @return array Array of notifications
 */
function getRecentNotifications($limit = 10) {
    $conn = getConnection();
    
    // Check if the notifications table has user_id column
    $tableInfo = $conn->query("SHOW COLUMNS FROM notifications LIKE 'user_id'");
    
    if ($tableInfo && $tableInfo->num_rows > 0) {
        // Table has user_id column - use join with admin table
        $sql = "SELECT n.*, a.username, a.full_name 
                FROM notifications n 
                LEFT JOIN admin a ON n.user_id = a.id 
                ORDER BY n.created_at DESC 
                LIMIT ?";
    } else {
        // Table doesn't have user_id column - use simpler query
        $sql = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT ?";
    }
    
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

/**
 * Mark notification as read
 * 
 * @param int $notificationId Notification ID
 * @return bool True if successful, false otherwise
 */
function markNotificationAsRead($notificationId) {
    $conn = getConnection();
    
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $notificationId);
    
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

/**
 * Mark all notifications as read
 * 
 * @return bool True if successful, false otherwise
 */
function markAllNotificationsAsRead() {
    $conn = getConnection();
    
    $sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
    $result = $conn->query($sql);
    
    $conn->close();
    
    return $result;
}

/**
 * Register admin login notification
 * 
 * @param int $adminId Admin ID
 * @param string $adminName Admin name
 * @return bool True if successful, false otherwise
 */
function registerLoginNotification($adminId, $adminName) {
    // Set timezone to Manila/Philippines time
    date_default_timezone_set('Asia/Manila');
    $login_time = date('F d, Y h:i A');
    
    $system_name = "Computerized QR Code Attendance Management System";
    $message = "{$adminName} just logged in to {$system_name} at {$login_time}";
    
    // Create a login notification - for simpler table structure, we'll just create one notification
    return createNotification(null, 'login', $message, $adminId);
}

/**
 * Register attendance scan notification
 * 
 * @param int $employeeId Employee ID
 * @param string $employeeName Employee name
 * @param string $type Type of scan (in/out)
 * @return bool True if successful, false otherwise
 */
function registerAttendanceScanNotification($employeeId, $employeeName, $type = 'in') {
    $message = "Employee $employeeName scanned $type";
    return createNotification(null, 'attendance_scan', $message, $employeeId);
}

/**
 * Register manual attendance modification notification
 * 
 * @param int $adminId Admin ID who made the change
 * @param string $adminName Admin name
 * @param int $employeeId Employee ID
 * @param string $employeeName Employee name
 * @return bool True if successful, false otherwise
 */
function registerAttendanceModificationNotification($adminId, $adminName, $employeeId, $employeeName) {
    $message = "Admin $adminName modified attendance for $employeeName";
    return createNotification($adminId, 'attendance_modification', $message, $employeeId);
}

/**
 * Register new employee addition notification
 * 
 * @param int $adminId Admin ID who added the employee
 * @param string $adminName Admin name
 * @param int $employeeId New employee ID
 * @param string $employeeName New employee name
 * @return bool True if successful, false otherwise
 */
function registerNewEmployeeNotification($adminId, $adminName, $employeeId, $employeeName) {
    $message = "Admin $adminName added new employee: $employeeName";
    return createNotification($adminId, 'new_employee', $message, $employeeId);
}

/**
 * Register new department addition notification
 * 
 * @param int $adminId Admin ID who added the department
 * @param string $adminName Admin name
 * @param string $departmentName New department name
 * @return bool True if successful, false otherwise
 */
function registerNewDepartmentNotification($adminId, $adminName, $departmentName) {
    $message = "Admin $adminName added new department: $departmentName";
    return createNotification($adminId, 'new_department', $message);
}

/**
 * Register new holiday/event addition notification
 * 
 * @param int $adminId Admin ID who added the holiday/event
 * @param string $adminName Admin name
 * @param string $eventName New holiday/event name
 * @param string $type Type (holiday/event)
 * @return bool True if successful, false otherwise
 */
function registerNewEventNotification($adminId, $adminName, $eventName, $type = 'holiday') {
    $message = "Admin $adminName added new $type: $eventName";
    return createNotification($adminId, 'new_event', $message);
}

/**
 * Register new admin creation notification
 * 
 * @param int $creatorId Admin ID who created the new admin
 * @param string $creatorName Admin name who created
 * @param int $newAdminId New admin ID
 * @param string $newAdminName New admin name
 * @return bool True if successful, false otherwise
 */
function registerNewAdminNotification($creatorId, $creatorName, $newAdminId, $newAdminName) {
    $message = "Admin $creatorName created new admin account: $newAdminName";
    return createNotification($creatorId, 'new_admin', $message, $newAdminId);
}
?>
