<?php
// Database connection
$server = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to add the missing columns
$sql = "ALTER TABLE attendance 
        ADD COLUMN REASON_TYPE varchar(50) DEFAULT NULL,
        ADD COLUMN REASON_DETAILS varchar(255) DEFAULT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Columns added successfully";
} else {
    echo "Error adding columns: " . $conn->error;
}

$conn->close();
?> 