<?php
// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Get today's attendance summary
$today = date('Y-m-d');
$summary_sql = "SELECT a.EMPLOYEEID, a.TIMEIN, a.TIMEOUT, a.STATUS, a.REASON_TYPE, a.REASON_DETAILS, 
                       e.`First Name` as FIRST_NAME, e.`Last Name` as LAST_NAME, e.Department as DEPARTMENT 
                FROM attendance a 
                JOIN employee e ON a.EMPLOYEEID = e.EmployeeID 
                WHERE a.LOGDATE = ? 
                ORDER BY a.TIMEIN DESC";
$summary_stmt = $conn->prepare($summary_sql);
$summary_stmt->bind_param("s", $today);
$summary_stmt->execute();
$result = $summary_stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    // Format times to 12-hour format
    if ($row['TIMEIN']) {
        // Check if already in 12-hour format
        if (strpos($row['TIMEIN'], 'AM') === false && strpos($row['TIMEIN'], 'PM') === false) {
            $row['TIMEIN'] = date('h:i A', strtotime($row['TIMEIN']));
        }
    }
    
    if ($row['TIMEOUT']) {
        // Check if already in 12-hour format
        if (strpos($row['TIMEOUT'], 'AM') === false && strpos($row['TIMEOUT'], 'PM') === false) {
            $row['TIMEOUT'] = date('h:i A', strtotime($row['TIMEOUT']));
        }
        
        // Log successful formatting for debugging
        error_log("Formatting TIMEOUT: Original=" . $row['TIMEOUT'] . ", EmployeeID=" . $row['EMPLOYEEID']);
    }
    
    $data[] = $row;
}

// Output JSON data
header('Content-Type: application/json');
echo json_encode($data);

// Close connection
$conn->close();
?> 