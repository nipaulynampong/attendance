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

// Get employee ID from request
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $employeeID = $_GET['id'];
    
    // Prepare and execute query to get employee name
    $sql = "SELECT `First Name`, `Last Name` FROM employee WHERE EmployeeID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $employeeID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Return the full name (First Name + Last Name)
        echo $row['First Name'] . ' ' . $row['Last Name'];
    } else {
        echo "Employee";  // Fallback if employee not found
    }
} else {
    echo "Employee";  // Fallback if no ID provided
}

// Close connection
$conn->close();
?>
