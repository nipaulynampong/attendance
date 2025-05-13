<?php
// Debug script for QRCodeAttendance image display

// Get employee ID from URL parameter
$employeeID = isset($_GET['id']) ? $_GET['id'] : 2; // Default to ID 2 if not specified

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

echo "<h1>Image Debug for Employee ID: $employeeID</h1>";

// Get employee data
$stmt = $conn->prepare("SELECT EmployeeID, `First Name`, `Last Name`, Image FROM employee WHERE EmployeeID = ?");
$stmt->bind_param("s", $employeeID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<h2>Employee: " . htmlspecialchars($row['First Name'] . ' ' . $row['Last Name']) . "</h2>";
    
    // Display image path information
    $imagePath = $row['Image'];
    echo "<p>Image path in database: <strong>" . htmlspecialchars($imagePath) . "</strong></p>";
    
    // Check if it's a file path
    if (strpos($imagePath, 'employee_images/') === 0) {
        $fullPath = '../' . $imagePath;
        echo "<p>Full path constructed: <strong>" . htmlspecialchars($fullPath) . "</strong></p>";
        echo "<p>File exists: <strong>" . (file_exists($fullPath) ? "Yes" : "No") . "</strong></p>";
        
        if (file_exists($fullPath)) {
            echo "<p>File size: <strong>" . filesize($fullPath) . " bytes</strong></p>";
            
            // Get file extension
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            echo "<p>File extension: <strong>" . $extension . "</strong></p>";
            
            // Display the image using the get_employee_image.php script
            echo "<h3>Image display test using get_employee_image.php:</h3>";
            echo "<img src='get_employee_image.php?id=$employeeID' style='max-width: 300px; border: 2px solid #333;'>";
            
            // Display the image directly
            echo "<h3>Direct image display test:</h3>";
            echo "<img src='$fullPath' style='max-width: 300px; border: 2px solid #333;'>";
        }
    } else {
        echo "<p>The Image field does not contain a valid file path.</p>";
    }
} else {
    echo "<p>No employee found with ID $employeeID</p>";
}

// List all images in the employee_images directory
echo "<h3>Available Images in Directory</h3>";
$files = scandir('../employee_images/');
echo "<ul>";
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "<li>" . htmlspecialchars($file) . " - Size: " . 
             filesize('../employee_images/' . $file) . " bytes</li>";
    }
}
echo "</ul>";

$conn->close();
?>
