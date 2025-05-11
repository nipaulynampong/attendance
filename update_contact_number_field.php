<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to alter the Contact Number field from INT to VARCHAR
$sql = "ALTER TABLE employee MODIFY `Contact Number` VARCHAR(15) NOT NULL";

if ($conn->query($sql) === TRUE) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>";
    echo "<strong>Success!</strong> The Contact Number field has been changed from INT to VARCHAR(15).";
    echo "</div>";
    
    echo "<p>You can now update employee records with proper phone numbers starting with '09'.</p>";
    echo "<p><a href='employee.php' style='background-color: #4F6F52; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Go to Employee List</a></p>";
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>";
    echo "<strong>Error:</strong> " . $conn->error;
    echo "</div>";
}

// Close connection
$conn->close();
?>
