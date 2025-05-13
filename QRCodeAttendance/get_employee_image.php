<?php
// Script to get employee image from the file system

// Validate input
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Return a default user icon
    header('Content-Type: image/svg+xml');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
        <circle cx="100" cy="100" r="100" fill="#4F6F52"/>
        <path d="M100,50 a30,30 0 1,0 0,60 a30,30 0 1,0 0,-60 M60,150 C60,120 140,120 140,150" stroke="white" stroke-width="10" fill="white"/>
    </svg>';
    exit;
}

$employeeID = $_GET['id'];

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Return a default user icon
    header('Content-Type: image/svg+xml');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
        <circle cx="100" cy="100" r="100" fill="#4F6F52"/>
        <path d="M100,50 a30,30 0 1,0 0,60 a30,30 0 1,0 0,-60 M60,150 C60,120 140,120 140,150" stroke="white" stroke-width="10" fill="white"/>
    </svg>';
    exit;
}

// Prepare and execute query to get the image path
$stmt = $conn->prepare("SELECT Image FROM employee WHERE EmployeeID = ?");
$stmt->bind_param("s", $employeeID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imagePath = $row['Image'];
    
    // Check if image exists and is not empty
    if (!empty($imagePath) && $imagePath != null) {
        // For paths stored as employee_images/filename.jpg
        if (strpos($imagePath, 'employee_images/') === 0) {
            $fullPath = '../' . $imagePath;
        } else {
            $fullPath = $imagePath;
        }
        
        // Check if the file exists
        if (file_exists($fullPath)) {
            // Determine content type based on file extension
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $contentType = 'image/jpeg'; // Default
            
            if ($extension == 'png') {
                $contentType = 'image/png';
            } elseif ($extension == 'gif') {
                $contentType = 'image/gif';
            }
            
            // Output the image
            header('Content-Type: ' . $contentType);
            readfile($fullPath);
            exit;
        }
    }
}

// If we get here, either the employee wasn't found, the image path wasn't valid,
// or the file doesn't exist - return a default user icon
header('Content-Type: image/svg+xml');
echo '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
    <circle cx="100" cy="100" r="100" fill="#4F6F52"/>
    <path d="M100,50 a30,30 0 1,0 0,60 a30,30 0 1,0 0,-60 M60,150 C60,120 140,120 140,150" stroke="white" stroke-width="10" fill="white"/>
</svg>';

// Close connection
$stmt->close();
$conn->close();
?>