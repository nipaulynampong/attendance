<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id_for_question'])) {
    echo json_encode(['success' => false, 'message' => 'No user selected']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$answer = $data['answer'] ?? '';

$user_id = $_SESSION['user_id_for_question'];
$stmt = $conn->prepare("SELECT security_answer FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (strtolower($answer) === strtolower($user['security_answer'])) {
        // Reset login attempts and clear cooldown
        $stmt = $conn->prepare("UPDATE users SET login_attempts = 0, cooldown_until = NULL, locked_until = NULL WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $_SESSION['user_id'] = $user_id;
        echo json_encode(['success' => true]);
    } else {
        // Lock account for 24 hours
        $locked_until = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $stmt = $conn->prepare("UPDATE users SET locked_until = ? WHERE id = ?");
        $stmt->bind_param("si", $locked_until, $user_id);
        $stmt->execute();
        
        echo json_encode(['success' => false, 'message' => 'Incorrect answer. Account locked for 24 hours.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?> 