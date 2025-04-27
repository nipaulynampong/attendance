<?php
// Script to get employee image from the database
header('Content-Type: image/jpeg');

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

// Validate input
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Return a placeholder image or error image
    readfile('placeholder.jpg');
    exit;
}

$employeeID = $_GET['id'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Return a placeholder image or error image
    readfile('placeholder.jpg');
    exit;
}

// Prepare and execute query
$stmt = $conn->prepare("SELECT Image FROM employee WHERE EmployeeID = ?");
$stmt->bind_param("s", $employeeID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($image);
    $stmt->fetch();
    
    if ($image) {
        // Output the image data
        echo $image;
    } else {
        // No image found, return placeholder
        readfile('placeholder.jpg');
    }
} else {
    // Employee not found, return placeholder
    readfile('placeholder.jpg');
}

// Close connection
$stmt->close();
$conn->close();
?>