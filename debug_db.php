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

// Get employee ID 2 (the one in the screenshot)
$employeeID = 2;

// Get table structure
echo "<h2>Employee Table Structure</h2>";
$result = $conn->query("DESCRIBE employee");
echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
}
echo "</table>";

// Get employee data
echo "<h2>Employee Data (ID: $employeeID)</h2>";
$stmt = $conn->prepare("SELECT * FROM employee WHERE EmployeeID = ?");
$stmt->bind_param("s", $employeeID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<table border='1'>";
    foreach ($row as $key => $value) {
        echo "<tr><td>$key</td><td>" . (is_null($value) ? "NULL" : htmlspecialchars($value)) . "</td></tr>";
    }
    echo "</table>";
    
    // Check if image file exists
    if (isset($row['image_path'])) {
        $imagePath = $row['image_path'];
        echo "<h3>Image Path Check</h3>";
        echo "Image path in DB: " . htmlspecialchars($imagePath) . "<br>";
        
        $fullPath = __DIR__ . '/employee_images/' . $imagePath;
        echo "Full path constructed: " . htmlspecialchars($fullPath) . "<br>";
        echo "File exists: " . (file_exists($fullPath) ? "Yes" : "No") . "<br>";
        
        // Also check for the files we found in the directory
        echo "<h3>Available Images in Directory</h3>";
        $files = scandir(__DIR__ . '/employee_images/');
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>" . htmlspecialchars($file) . " - Exists: " . 
                     (file_exists(__DIR__ . '/employee_images/' . $file) ? "Yes" : "No") . "</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>No image_path field found in the result.</p>";
    }
} else {
    echo "<p>No employee found with ID $employeeID</p>";
}

$conn->close();
?>
