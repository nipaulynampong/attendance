<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id_for_question'])) {
    echo json_encode(['error' => 'No user selected']);
    exit;
}

$user_id = $_SESSION['user_id_for_question'];
$stmt = $conn->prepare("SELECT security_question FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo json_encode(['question' => $user['security_question']]);
} else {
    echo json_encode(['error' => 'User not found']);
}
?> 