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

echo "<h2>Attendance Database Check</h2>";

// Check if attendance table exists
$result = $conn->query("SHOW TABLES LIKE 'attendance'");
echo "Attendance table exists: " . ($result->num_rows > 0 ? "Yes" : "No") . "<br>";

// Get total attendance count
$result = $conn->query("SELECT COUNT(*) as count FROM attendance");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total attendance records: " . $row['count'] . "<br>";
} else {
    echo "Error counting attendance: " . $conn->error . "<br>";
}

// Get employees with attendance
$result = $conn->query("SELECT EMPLOYEEID, COUNT(*) as count FROM attendance GROUP BY EMPLOYEEID");
if ($result) {
    echo "<h3>Employees with attendance:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>Employee ID: " . $row['EMPLOYEEID'] . " - Records: " . $row['count'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error getting employee attendance: " . $conn->error . "<br>";
}

// Check structure of attendance table
$result = $conn->query("DESCRIBE attendance");
if ($result) {
    echo "<h3>Attendance table structure:</h3>";
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error getting table structure: " . $conn->error . "<br>";
}

// Get sample of records
echo "<h3>Sample attendance records:</h3>";
$result = $conn->query("SELECT * FROM attendance LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    
    // Table header
    $first_row = $result->fetch_assoc();
    $result->data_seek(0);
    
    echo "<tr>";
    foreach (array_keys($first_row) as $column) {
        echo "<th>" . $column . "</th>";
    }
    echo "</tr>";
    
    // Table data
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . ($value === null ? 'NULL' : $value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No records found or error: " . $conn->error . "<br>";
}

$conn->close();
?> 