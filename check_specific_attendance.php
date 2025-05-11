<?php
// Database connection
$server = "localhost";
$username = "root";
$password = "";
$dbname = "hris";
$conn = new mysqli($server, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the employee ID from the URL
$employee_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : '2021'; // Default to 2021 if not provided

echo "<h2>Attendance Records for Employee ID: $employee_id</h2>";

// Get total count for this employee
$count_query = "SELECT COUNT(*) as total FROM attendance WHERE EMPLOYEEID = '$employee_id'";
$count_result = $conn->query($count_query);
$count_data = $count_result->fetch_assoc();
echo "Total attendance records for this employee: " . $count_data['total'] . "<br>";

// Get the dates of attendance records for this employee
$dates_query = "SELECT DISTINCT DATE(LOGDATE) as attendance_date, 
                       MONTH(LOGDATE) as month, 
                       YEAR(LOGDATE) as year 
                FROM attendance 
                WHERE EMPLOYEEID = '$employee_id' 
                ORDER BY attendance_date DESC";
$dates_result = $conn->query($dates_query);

if ($dates_result && $dates_result->num_rows > 0) {
    echo "<h3>Dates with attendance:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Date</th><th>Month</th><th>Year</th></tr>";
    
    while ($date_row = $dates_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $date_row['attendance_date'] . "</td>";
        echo "<td>" . $date_row['month'] . "</td>";
        echo "<td>" . $date_row['year'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No attendance dates found for this employee.<br>";
}

// Get full attendance records for this employee
$records_query = "SELECT * FROM attendance WHERE EMPLOYEEID = '$employee_id' ORDER BY LOGDATE DESC";
$records_result = $conn->query($records_query);

if ($records_result && $records_result->num_rows > 0) {
    echo "<h3>All attendance records for this employee:</h3>";
    echo "<table border='1'>";
    
    // Table header
    $first_row = $records_result->fetch_assoc();
    $records_result->data_seek(0);
    
    echo "<tr>";
    foreach (array_keys($first_row) as $column) {
        echo "<th>" . $column . "</th>";
    }
    echo "</tr>";
    
    // Table data
    while ($row = $records_result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . ($value === null ? 'NULL' : $value) . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No records found.<br>";
}

// Print SQL query for debugging main view
$month = date('n'); // Current month
$year = date('Y');  // Current year
$sql = "SELECT a.*, e.`First Name`, e.`Last Name`, e.Department,
       TIMEDIFF(a.TIMEOUT, a.TIMEIN) as work_hours
       FROM attendance a 
       LEFT JOIN employee e ON a.EMPLOYEEID = e.EmployeeID 
       WHERE MONTH(a.LOGDATE) = $month 
       AND YEAR(a.LOGDATE) = $year
       AND a.EMPLOYEEID = '$employee_id'
       AND (a.TIMEOUT IS NOT NULL OR 
            (a.TIMEOUT IS NULL AND (a.STATUS LIKE '%No TimeOut%' OR TIME(NOW()) >= '19:00:00')))
       ORDER BY a.LOGDATE DESC, a.TIMEIN DESC";

echo "<h3>SQL Query that would be used in view_attendance.php:</h3>";
echo "<pre>" . htmlspecialchars($sql) . "</pre>";

$conn->close();
?> 