<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="QRCodeAttendance/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="QRCodeAttendance/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: white;
            font-family: 'Poppins';
        }

        .container {
            padding: 20px;
            background-color:#f5efe6;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 1300px; /* Adjust the maximum width as needed */
            margin-top: 10px;
            max-height: 85vh;
            overflow-y: scroll;
        }
        
        /* Scrollbar styling for container */
        .container::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb {
            background: #4F6F52;
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb:hover {
            background: #3A4D39;
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins';
        }

        .table th, .table td {
            border-top: none;
            border-bottom: 1px solid #dee2e6;
            background-color: #F8F4EC;
        }

        .table th {
            background-color: #d6dac9;
            color: black;
            font-weight: bold;
        }

        .table td {
            vertical-align: middle;
        }

        .table img {
            border-radius: 50%;
        }

        .table a {
            color: #007bff;
            text-decoration: none;
        }

        .table a:hover {
            text-decoration: underline;
        }

        .no-records {
            text-align: center;
            font-style: italic;
            color: #868e96;
        }
    </style>
</head>
<body>


    <div class="container">
        <h3 style="font-weight: bold;">Employee Attendance Report</h3><br>   
        
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-control" id="departmentFilter">
                    <option value="">All Departments</option>
                    <?php
                    // Get unique departments
                    $server = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "hris";
                    $conn = new mysqli($server, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $dept_sql = "SELECT DISTINCT Department FROM employee ORDER BY Department";
                    $dept_result = $conn->query($dept_sql);

                    while($dept_row = $dept_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($dept_row['Department']) . "'>" . 
                             htmlspecialchars($dept_row['Department']) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <table class="table table-striped" id="employeeTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>EmployeeID</th>
                    <th>Full Name</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
              
                // Modified query to handle image data more carefully
                $sql = "SELECT EmployeeID, `Last Name`, `First Name`, Department, 
                       CASE WHEN Image IS NULL OR LENGTH(Image) < 100 THEN 0 ELSE 1 END AS has_valid_image 
                       FROM employee ORDER BY Department, `Last Name`, `First Name`";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        
                        // Check if image exists using our database-level check
                        if ($row['has_valid_image'] == 1) {
                            // Get the image directly with a separate query
                            $img_sql = "SELECT Image FROM employee WHERE EmployeeID = " . $row['EmployeeID'];
                            $img_result = $conn->query($img_sql);
                            $img_row = $img_result->fetch_assoc();
                            
                            // Display the image
                            echo "<td><img src='data:image/jpeg;base64," . base64_encode($img_row['Image']) . "' alt='Employee Image' style='width:50px;height:50px;border-radius:50%;object-fit:cover;'></td>";
                        } else {
                            // Display default image with Font Awesome icon
                            echo "<td>
                                <div style='width:50px;height:50px;border-radius:50%;background-color:#4F6F52;display:flex;align-items:center;justify-content:center;'>
                                    <i class='fas fa-user' style='color:white;font-size:28px;'></i>
                                </div>
                            </td>";
                        }
                        echo "<td>" . $row['EmployeeID'] . "</td>";
                        echo "<td>" . $row['Last Name'] . ", " . $row['First Name'] . "</td>";
                        echo "<td>" . $row['Department'] . "</td>";
                        echo "<td><a href='view_attendance.php?employee_id=" . $row['EmployeeID'] . "' class='btn btn-success btn-sm text-white'>View Attendance</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No records found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- DataTables JS -->
    <script src="QRCodeAttendance/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="QRCodeAttendance/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#employeeTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 10,
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                }
            });
            
            // Add department filter functionality
            $('#departmentFilter').on('change', function() {
                var department = $(this).val();
                table.column(3).search(department).draw();
            });
        });
    </script>
</body>
</html>
