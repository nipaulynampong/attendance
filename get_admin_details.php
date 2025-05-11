<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getAdminDetails($admin_id) {
    if (!$admin_id) {
        return [
            'username' => 'Admin',
            'email' => '',
            'full_name' => 'Administrator'
        ];
    }

    // Database connection
    $conn = new mysqli("localhost", "root", "", "hris");

    if ($conn->connect_error) {
        return [
            'username' => 'Admin',
            'email' => '',
            'full_name' => 'Administrator'
        ];
    }

    $stmt = $conn->prepare("SELECT id, username, email, full_name FROM admin WHERE id = ?");
    if (!$stmt) {
        $conn->close();
        return [
            'username' => 'Admin',
            'email' => '',
            'full_name' => 'Administrator'
        ];
    }

    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        $conn->close();
        return [
            'id' => $row['id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'full_name' => $row['full_name']
        ];
    }

    $stmt->close();
    $conn->close();
    return [
        'id' => '',
        'username' => 'Admin',
        'email' => '',
        'full_name' => 'Administrator'
    ];
}

// Only process API requests if directly accessed
if (basename($_SERVER['PHP_SELF']) == 'get_admin_details.php') {
    header('Content-Type: application/json');

    // Check if user is logged in as admin
    if (!isset($_SESSION['admin_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized access']);
        exit();
    }

    if (isset($_GET['id'])) {
        $admin_id = $_GET['id'];
        $details = getAdminDetails($admin_id);
        
        if (isset($details['error'])) {
            http_response_code(404);
        }
        
        echo json_encode($details);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No ID provided']);
    }
}
?>