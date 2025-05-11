<?php
// Database connection parameters
$server = "localhost";
$username = "root";
$password = "";
$dbname = "hris";
$conn = new mysqli($server, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get a specific employee to check their image
$sql = "SELECT EmployeeID, `First Name`, `Last Name`, Image FROM employee LIMIT 5";
$result = $conn->query($sql);

echo "<h2>Image Debug Information</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>EmployeeID</th><th>Name</th><th>Image Type</th><th>Image Size</th><th>Image Preview</th></tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['EmployeeID'] . "</td>";
        echo "<td>" . $row['First Name'] . " " . $row['Last Name'] . "</td>";
        
        if (isset($row['Image']) && $row['Image'] !== null) {
            $image_size = strlen($row['Image']);
            $image_type = "Unknown";
            
            // Try to determine image type
            if ($image_size > 0) {
                $image_data = $row['Image'];
                $first_bytes = bin2hex(substr($image_data, 0, 4));
                
                if (strpos($first_bytes, 'ffd8') === 0) {
                    $image_type = "JPEG";
                } elseif (strpos($first_bytes, '89504e47') === 0) {
                    $image_type = "PNG";
                } elseif (strpos($first_bytes, '47494638') === 0) {
                    $image_type = "GIF";
                }
            }
            
            echo "<td>" . $image_type . "</td>";
            echo "<td>" . $image_size . " bytes</td>";
            
            // Only try to display if we have some data
            if ($image_size > 0) {
                echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['Image']) . "' alt='Employee Image' style='width:50px;height:50px;border-radius:50%;object-fit:cover;'></td>";
            } else {
                echo "<td>No image data</td>";
            }
        } else {
            echo "<td>NULL</td><td>0 bytes</td><td>No image</td>";
        }
        
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No employees found</td></tr>";
}

echo "</table>";

// Close connection
$conn->close();
?>
