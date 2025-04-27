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

// Handle restore action
if(isset($_GET['restore'])) {
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
        $employee = $result->fetch_assoc();
        
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
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-archive"></i> Archived Employees</h1>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
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
                        echo "<td>" . $row['EmployeeID'] . "</td>";
                        echo "<td>" . $row['Last Name'] . ", " . $row['First Name'] . " " . $row['Middle Name'] . "</td>";
                        echo "<td>" . $row['Department'] . "</td>";
                        echo "<td>
                                <button class='btn-restore' onclick='restoreEmployee(" . $row['EmployeeID'] . ")'><i class='fas fa-undo'></i> Restore</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='no-records'>No archived employees found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function restoreEmployee(employeeID) {
            if(confirm('Are you sure you want to restore this employee?')) {
                window.location.href = 'archive.php?restore=' + employeeID;
            }
        }
    </script>
</body>
</html>