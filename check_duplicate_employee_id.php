<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

// Get the employee ID from the POST request
$employeeID = isset($_POST['employeeID']) ? $_POST['employeeID'] : '';

// Initialize response array
$response = array(
    'isDuplicate' => false,
    'message' => ''
);

if (!empty($employeeID)) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $response['message'] = "Connection failed: " . $conn->connect_error;
        echo json_encode($response);
        exit;
    }

    // Prepare a statement to check if the employee ID already exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM employee WHERE EmployeeID = ?");
    $stmt->bind_param("s", $employeeID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $response['isDuplicate'] = true;
        $response['message'] = "Employee ID already exists in the database.";
    }

    $stmt->close();
    $conn->close();
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
