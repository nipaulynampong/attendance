<?php
// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="attendance_report.xls"');

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

$conn = new mysqli($server, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get attendance data joined with employee data
$sql = "SELECT a.*, e.`First Name`, e.`Last Name`, e.Department 
        FROM attendance a 
        LEFT JOIN employee e ON a.EMPLOYEEID = e.EmployeeID
        WHERE (a.TIMEOUT IS NOT NULL OR 
              (a.TIMEOUT IS NULL AND (a.STATUS LIKE '%No TimeOut%' OR TIME(NOW()) >= '19:00:00')))
        ORDER BY a.LOGDATE DESC, a.TIMEIN DESC";

$result = $conn->query($sql);
?>

<table border="1">
    <thead>
        <tr>
            <th bgcolor="#4F6F52" style="color: white;">Employee ID</th>
            <th bgcolor="#4F6F52" style="color: white;">Last Name</th>
            <th bgcolor="#4F6F52" style="color: white;">First Name</th>
            <th bgcolor="#4F6F52" style="color: white;">Department</th>
            <th bgcolor="#4F6F52" style="color: white;">Date</th>
            <th bgcolor="#4F6F52" style="color: white;">Time In</th>
            <th bgcolor="#4F6F52" style="color: white;">Time Out</th>
            <th bgcolor="#4F6F52" style="color: white;">Status</th>
            <th bgcolor="#4F6F52" style="color: white;">Holiday Info</th>
            <th bgcolor="#4F6F52" style="color: white;">Event Info</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Set background color based on status
                $bgColor = "#FFFFFF"; // Default white
                if ($row['STATUS'] == 'Late') {
                    $bgColor = "#FFECD6";
                } else if ($row['STATUS'] == 'Early Out') {
                    $bgColor = "#FFB5B5";
                } else if ($row['STATUS'] == 'Present') {
                    $bgColor = "#A6E3A1";
                } else if ($row['STATUS'] == 'Rest Day') {
                    $bgColor = "#ADE8F4";
                } else if ($row['STATUS'] == 'Holiday') {
                    $bgColor = "#E9D8FD";
                } else if ($row['STATUS'] == 'Event') {
                    $bgColor = "#D8BFD8";
                }
                
                echo "<tr bgcolor='" . $bgColor . "'>";
                echo "<td>" . $row['EMPLOYEEID'] . "</td>";
                echo "<td>" . $row['Last Name'] . "</td>";
                echo "<td>" . $row['First Name'] . "</td>";
                echo "<td>" . $row['Department'] . "</td>";
                echo "<td>" . date('M d, Y', strtotime($row['LOGDATE'])) . "</td>";
                echo "<td>" . $row['TIMEIN'] . "</td>";
                echo "<td>" . $row['TIMEOUT'] . "</td>";
                echo "<td>" . $row['STATUS'] . "</td>";
                echo "<td>" . ($row['HolidayInfo'] ? $row['HolidayInfo'] : '') . "</td>";
                echo "<td>" . ($row['EventInfo'] ? $row['EventInfo'] : '') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No attendance records found</td></tr>";
        }
        
        // Close connection
        $conn->close();
        ?>
    </tbody>
</table>