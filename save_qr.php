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
if (!$data || !isset($data['employeeID'])) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Invalid data format"));
    exit;
}

$employeeID = $data['employeeID'];
$qrCodeURL = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($employeeID);
$qrCodeImage = file_get_contents($qrCodeURL);

// Save QR code to local directory
$qrCodeDir = 'qrcodes/';
if (!file_exists($qrCodeDir)) {
    mkdir($qrCodeDir, 0777, true);
}
$qrCodeFileName = $qrCodeDir . $employeeID . '.png';
file_put_contents($qrCodeFileName, $qrCodeImage);

// Return success response
echo json_encode(array(
    "success" => true,
    "status" => "success",
    "qr_url" => $qrCodeURL,
    "saved_path" => $qrCodeFileName
));
?> 