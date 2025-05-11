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

// Create archived_employees table if it doesn't exist
$sql_create_table = "CREATE TABLE IF NOT EXISTS archived_employees LIKE employee";
$conn->query($sql_create_table);

// Handle archive action (via AJAX from employee.php)
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $employeeID = $_GET['id'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // First, check if employee exists
        $check_sql = "SELECT * FROM employee WHERE EmployeeID = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $employeeID);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if($result->num_rows == 0) {
            echo "Employee not found!";
            exit;
        }
        
        // Move employee to archived_employees table
        $sql = "INSERT INTO archived_employees SELECT * FROM employee WHERE EmployeeID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $employeeID);
        $stmt->execute();
        
        // Delete from active employees
        $sql = "DELETE FROM employee WHERE EmployeeID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $employeeID);
        $stmt->execute();
        
        $conn->commit();
        echo "Employee archived successfully!";
        exit; // Exit after AJAX response
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error archiving employee: " . $e->getMessage();
        exit; // Exit after AJAX response
    }
}

// Handle restore action
if(isset($_GET['restore']) && !empty($_GET['restore'])) {
    $employeeID = $_GET['restore'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Get employee data from archived_employees
        $sql_select = "SELECT * FROM archived_employees WHERE EmployeeID = ?";
        $stmt = $conn->prepare($sql_select);
        $stmt->bind_param("s", $employeeID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 0) {
            echo "<script>alert('Error: Employee not found in archive!');</script>";
        } else {
            // Insert into employee table
            $sql_insert = "INSERT INTO employee SELECT * FROM archived_employees WHERE EmployeeID = ?";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bind_param("s", $employeeID);
            $stmt->execute();
            
            // Delete from archived_employees
            $sql_delete = "DELETE FROM archived_employees WHERE EmployeeID = ?";
            $stmt = $conn->prepare($sql_delete);
            $stmt->bind_param("s", $employeeID);
            $stmt->execute();
            
            $conn->commit();
            echo "<script>alert('Employee restored successfully!');</script>";
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error restoring employee: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Employees</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Import Poppins font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        /* Apply Poppins to all elements */
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            margin: 20px;
            background-color: #f5efe6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #f5efe6;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem; /* Reduced from default size */
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4F6F52;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn-restore {
            background-color: #4F6F52;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-restore:hover {
            background-color: #739072;
        }
        .no-records {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .employee-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-archive"></i> Archived Employees</h1>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Employee ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch archived employees
                $sql = "SELECT * FROM archived_employees";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['Image']) . "' alt='Employee Image' class='employee-img'></td>";
                        echo "<td>" . $row['EmployeeID'] . "</td>";
                        echo "<td>" . $row['Last Name'] . "</td>";
                        echo "<td>" . $row['First Name'] . "</td>";
                        echo "<td>" . $row['Middle Name'] . "</td>";
                        echo "<td>" . $row['Department'] . "</td>";
                        echo "<td>
                                <button class='btn-restore' onclick='restoreEmployee(" . $row['EmployeeID'] . ")'><i class='fas fa-undo'></i> Restore</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='no-records'>No archived employees found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function restoreEmployee(employeeID) {
            // First fetch the employee name
            var nameXhttp = new XMLHttpRequest();
            nameXhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var employeeName = this.responseText;
                    // Show confirmation with employee name
                    if(confirm('Are you sure you want to restore ' + employeeName + '?')) {
                        window.location.href = 'archive_employee.php?restore=' + employeeID;
                    }
                }
            };
            nameXhttp.open("GET", "get_archived_employee_name.php?id=" + employeeID, true);
            nameXhttp.send();
        }
    </script>
</body>
</html>