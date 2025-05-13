<?php
// Test page to display all employees with their images
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Employees Image Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background-color: #f5f5f5;
        }
        h1 { 
            color: #4F6F52; 
            text-align: center;
        }
        .employee-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .employee-card {
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .employee-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 10px;
            display: block;
            border: 3px solid #4F6F52;
        }
        .employee-name {
            font-weight: bold;
            font-size: 16px;
            margin: 5px 0;
            color: #333;
        }
        .employee-details {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>All Employees Image Test</h1>
    
    <div class="employee-grid">
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
        
        // Get all employees
        $sql = "SELECT EmployeeID, `First Name`, `Last Name`, Department, Image FROM employee ORDER BY EmployeeID";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $employeeId = $row['EmployeeID'];
                $firstName = $row['First Name'];
                $lastName = $row['Last Name'];
                $department = $row['Department'];
                $imagePath = $row['Image'];
                
                echo '<div class="employee-card">';
                
                // Image from QRCodeAttendance system
                echo '<img src="QRCodeAttendance/get_employee_image.php?id=' . $employeeId . '" class="employee-image" alt="Employee">';
                
                echo '<div class="employee-name">' . $firstName . ' ' . $lastName . '</div>';
                echo '<div class="employee-details">ID: ' . $employeeId . '</div>';
                echo '<div class="employee-details">Department: ' . $department . '</div>';
                
                // Show image path for debugging
                echo '<div class="employee-details" style="font-size: 11px; color: #999; overflow: hidden; text-overflow: ellipsis;">' . 
                     (empty($imagePath) ? 'No image path' : $imagePath) . '</div>';
                
                echo '</div>';
            }
        } else {
            echo "<p>No employees found</p>";
        }
        
        $conn->close();
        ?>
    </div>
    
    <div style="margin-top: 30px; text-align: center;">
        <p><a href="QRCodeAttendance/index.php" style="color: #4F6F52; text-decoration: none;">Go to QR Code Attendance System</a></p>
    </div>
</body>
</html>
