<?php
// Database connection parameters
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "hris"; // Replace with your database name

// Get the employee ID from the request
$employeeId = $_GET['EmployeeID'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to check if the employee ID exists in the database
$sql = "SELECT * FROM employee WHERE EmployeeID = '$employeeId'";
$result = $conn->query($sql);

// Check if any rows are returned
if ($result->num_rows > 0) {
    // Employee exists
    $response = array('exists' => true);
} else {
    // Employee does not exist
    $response = array('exists' => false);
}

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
