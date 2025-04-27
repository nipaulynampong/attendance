<?php
// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("success" => false, "message" => "Invalid request method"));
    exit;
}

// Get the JSON data sent from JavaScript
$jsonInput = file_get_contents('php://input');
if (empty($jsonInput)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "No data received"));
    exit;
}

$data = json_decode($jsonInput, true);
if (!$data || !isset($data['qr_data'])) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Invalid data format"));
    exit;
}

// Process the QR code data
$qrData = $data['qr_data'];

// Success response should match what the JavaScript expects
echo json_encode(array("success" => true, "status" => "success", "qr_data" => $qrData));
?>
