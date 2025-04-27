<?php
// Check if employee ID is provided
if(isset($_GET['id'])) {
    // Retrieve employee ID from the GET parameters
    $employeeID = $_GET['id'];
    
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

    // Prepare SQL statement to delete employee by ID
    $sql = "DELETE FROM employee WHERE EmployeeID = $employeeID";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        // Deletion successful
        echo "Employee deleted successfully!";
    } else {
        // Error occurred during deletion
        echo "Error deleting record: " . $conn->error;
    }

    // Close database connection
    $conn->close();
} else {
    // Error message if employee ID is not provided
    echo "Employee ID not provided!";
}
?>
