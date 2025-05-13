<?php
// Comprehensive image debug script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Image Path Debug Tool</h1>";

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

// Get employee data
echo "<h2>Employee Data (ID: $employeeID)</h2>";
$stmt = $conn->prepare("SELECT EmployeeID, `First Name`, `Last Name`, Image FROM employee WHERE EmployeeID = ?");
$stmt->bind_param("s", $employeeID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<p>Name: " . htmlspecialchars($row['First Name'] . ' ' . $row['Last Name']) . "</p>";
    
    $imagePath = $row['Image'];
    echo "<p>Image path in database: <code>" . htmlspecialchars($imagePath) . "</code></p>";
    
    // Check if the image exists
    if (!empty($imagePath)) {
        // Test different path combinations
        $paths = [
            'Direct path' => $imagePath,
            'From root' => '/' . $imagePath,
            'From current directory' => './' . $imagePath,
            'From parent directory' => '../' . $imagePath,
            'From QRCodeAttendance' => 'QRCodeAttendance/' . $imagePath,
            'Absolute path' => $_SERVER['DOCUMENT_ROOT'] . '/' . $imagePath,
            'Absolute from capstone' => $_SERVER['DOCUMENT_ROOT'] . '/capstone/' . $imagePath,
        ];
        
        echo "<h3>Path Tests</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Path Type</th><th>Full Path</th><th>File Exists?</th><th>Image Preview</th></tr>";
        
        foreach ($paths as $type => $path) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($type) . "</td>";
            echo "<td><code>" . htmlspecialchars($path) . "</code></td>";
            echo "<td>" . (file_exists($path) ? "Yes" : "No") . "</td>";
            echo "<td><img src='" . htmlspecialchars($path) . "' style='max-height: 50px;' onerror=\"this.src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAABTSURBVDhPY/hPJTBqIBkAZODx48dJxmfOnAEbePLkSZLxqVOnwAZS7EKQ5gEw8NChQyTjgwcPgg0cNZAMQEz4JGOi8h8+BpKKBzSUKQJGDSQdMPwHABdoOKV6rQwPAAAAAElFTkSuQmCC'; this.style.opacity=0.3;\" /></td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Check QRCodeAttendance script
        echo "<h3>QRCodeAttendance Script Test</h3>";
        echo "<p>Using: <code>QRCodeAttendance/get_employee_image.php?id=$employeeID</code></p>";
        echo "<img src='QRCodeAttendance/get_employee_image.php?id=$employeeID' style='max-width: 200px; border: 1px solid #333;' />";
        
        // List all files in employee_images directory
        echo "<h3>Files in employee_images directory:</h3>";
        $files = scandir('employee_images');
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>" . htmlspecialchars($file) . " - Size: " . filesize("employee_images/$file") . " bytes";
                
                // Check if this file matches the employee ID
                if (strpos($file, $employeeID . '_') === 0) {
                    echo " <strong>(Matches employee ID)</strong>";
                }
                
                echo "</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>No image path found for this employee.</p>";
    }
} else {
    echo "<p>No employee found with ID $employeeID</p>";
}

// Check placeholder image
echo "<h3>Placeholder Image Check</h3>";
$placeholderPath = "QRCodeAttendance/placeholder.jpg";
echo "<p>Placeholder path: <code>$placeholderPath</code></p>";
echo "<p>File exists: " . (file_exists($placeholderPath) ? "Yes" : "No") . "</p>";

$conn->close();
?>
