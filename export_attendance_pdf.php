<?php
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="attendance_report.pdf"');

// Get filter parameters
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$dateRange = isset($_GET['daterange']) ? $_GET['daterange'] : '';

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

// Initialize query parameters
$conditions = [];
$params = [];
$types = "";

// Add month and year filter if selected
if (!empty($month) && !empty($year)) {
    $conditions[] = "MONTH(a.LOGDATE) = ?";
    $params[] = $month;
    $types .= "s";
    
    $conditions[] = "YEAR(a.LOGDATE) = ?";
    $params[] = $year;
    $types .= "s";
}

// Add date range filter if selected
if (!empty($dateRange)) {
    $dateRange = explode(' - ', $dateRange);
    if (count($dateRange) == 2) {
        $startDate = date('Y-m-d', strtotime($dateRange[0]));
        $endDate = date('Y-m-d', strtotime($dateRange[1]));
        
        $conditions[] = "a.LOGDATE BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= "ss";
    }
}

// Build the WHERE clause
$whereClause = "";
if (!empty($conditions)) {
    $whereClause = "WHERE " . implode(" AND ", $conditions);
}

// Get all attendance records with filtering
$sql = "SELECT a.*, e.`First Name` as emp_first_name, e.`Last Name` as emp_last_name 
        FROM attendance a 
        LEFT JOIN employee e ON a.EMPLOYEEID = e.EmployeeID
        $whereClause
        ORDER BY a.LOGDATE DESC, a.TIMEIN DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Create PDF content
$title = "Attendance Summary Report";
$subtitle = "";

if (!empty($month) && !empty($year)) {
    $monthName = date("F", mktime(0, 0, 0, $month, 10));
    $subtitle = "For $monthName $year";
} elseif (!empty($dateRange)) {
    $subtitle = "From $startDate to $endDate";
}

// Start building the PDF content
ob_start();

// Create a simple PDF using PHP's output buffering
echo "<html><head>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #4F6F52; text-align: center; }
    h2 { color: #4F6F52; text-align: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { background-color: #4F6F52; color: white; padding: 8px; text-align: left; }
    td { padding: 8px; border-bottom: 1px solid #ddd; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .footer { text-align: center; font-size: 12px; margin-top: 30px; }
    .status-ontime { color: green; }
    .status-late { color: orange; }
    .status-absent { color: red; }
</style>";
echo "</head><body>";
echo "<h1>$title</h1>";
if (!empty($subtitle)) {
    echo "<h2>$subtitle</h2>";
}

echo "<table>";
echo "<tr>
        <th>Employee Name</th>
        <th>Employee ID</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Date</th>
        <th>Status</th>
      </tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Determine status class
        $statusClass = '';
        if ($row['STATUS'] == 'ONTIME') {
            $statusClass = 'status-ontime';
        } elseif ($row['STATUS'] == 'LATE') {
            $statusClass = 'status-late';
        } elseif ($row['STATUS'] == 'ABSENT') {
            $statusClass = 'status-absent';
        }
        
        // Use employee names from employee table, fallback to attendance table if null
        $lastName = !empty($row['emp_last_name']) ? $row['emp_last_name'] : $row['Last Name'];
        $firstName = !empty($row['emp_first_name']) ? $row['emp_first_name'] : $row['First Name'];
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($lastName) . ", " . htmlspecialchars($firstName) . "</td>";
        echo "<td>" . htmlspecialchars($row['EMPLOYEEID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['TIMEIN']) . "</td>";
        echo "<td>" . htmlspecialchars($row['TIMEOUT']) . "</td>";
        echo "<td>" . date('M d, Y', strtotime($row['LOGDATE'])) . "</td>";
        echo "<td class='$statusClass'>" . htmlspecialchars($row['STATUS']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6' style='text-align: center;'>No attendance records found for the selected criteria.</td></tr>";
}

echo "</table>";

echo "<div class='footer'>Generated on " . date('Y-m-d H:i:s') . " by Nipaulyn Attendance System</div>";
echo "</body></html>";

// Get the HTML content
$html = ob_get_clean();

// Use mPDF or other PDF libraries if available
// For this example, we'll use a simple HTML to PDF conversion
// Note: This is a simplified approach and might not work in all environments
// For a production environment, consider using a proper PDF library like mPDF or TCPDF

// Convert HTML to PDF using browser's print capabilities
echo $html;

// Close database connection
$conn->close();
?>
