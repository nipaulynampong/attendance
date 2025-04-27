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

if(isset($_GET['id'])) {
    $employeeID = $_GET['id'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Move employee to archived_employees table
        $sql = "INSERT INTO archived_employees SELECT * FROM employee WHERE EmployeeID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $employeeID);
        $stmt->execute();
        
        // Delete from active employees
        $sql = "DELETE FROM employee WHERE EmployeeID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $employeeID);
        $stmt->execute();
        
        $conn->commit();
        echo "Employee archived successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error archiving employee: " . $e->getMessage();
    }
} else {
    echo "Employee ID not provided!";
}
?>