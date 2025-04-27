<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Summary</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <!-- Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            font-family: 'Poppins';
            background-color: white;
        }
        
        .container-fluid {
            padding: 20px;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #f5efe6;
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #4F6F52;
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        table thead th {
            background-color: #d6dac9;
            color: black;
        }
        
        .btn-export {
            background-color: #4F6F52;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            margin-top: 10px;
        }
        
        .btn-export:hover {
            background-color: #3a5740;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 50px;
            font-weight: normal;
            font-size: 12px;
        }
        
        .badge-success {
            background-color: #A6E3A1;
            color: #2D5E2D;
        }
        
        .badge-warning {
            background-color: #FFECD6;
            color: #996600;
        }
        
        .badge-info {
            background-color: #ADE8F4;
            color: #1A5F7A;
        }
        
        .badge-danger {
            background-color: #FFB5B5;
            color: #9A0000;
        }
        
        .badge-holiday {
            background-color: #E9D8FD;
            color: #6B46C1;
        }

        .badge-event {
            background-color: #D8BFD8;
            color: #800080;
        }

        .info-text {
            font-size: 12px;
            margin-top: 4px;
            padding: 4px 8px;
            border-radius: 4px;
            display: block;
        }

        .holiday-info {
            background-color: rgba(233, 216, 253, 0.2);
            color: #6B46C1;
        }

        .event-info {
            background-color: rgba(216, 191, 216, 0.2);
            color: #800080;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-clipboard-check"></i> Attendance Summary Report</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>EMPLOYEE ID</th>
                                <th>TIME IN</th>
                                <th>TIME OUT</th>
                                <th>DATE</th>
                                <th>STATUS</th>
                                <th>ADDITIONAL INFO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Database connection
                            $server = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "hris";

                            $conn = new mysqli($server, $username, $password, $dbname);
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            // Query to get attendance data joined with employee data
                            $sql = "SELECT a.*, e.`First Name`, e.`Last Name` 
                                    FROM attendance a 
                                    LEFT JOIN employee e ON a.EMPLOYEEID = e.EmployeeID 
                                    WHERE (a.TIMEOUT IS NOT NULL OR 
                                          (a.TIMEOUT IS NULL AND (a.STATUS LIKE '%No TimeOut%' OR TIME(NOW()) >= '19:00:00')))
                                    ORDER BY a.LOGDATE DESC, a.TIMEIN DESC";
                            
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Determine badge class based on status
                                    $badgeClass = 'badge-success';
                                    if (strpos($row['STATUS'], 'Late') !== false) {
                                        $badgeClass = 'badge-warning';
                                    } else if (strpos($row['STATUS'], 'Early Out') !== false) {
                                        $badgeClass = 'badge-danger';
                                    } else if ($row['STATUS'] == 'Rest Day') {
                                        $badgeClass = 'badge-info';
                                    } else if ($row['STATUS'] == 'Holiday') {
                                        $badgeClass = 'badge-holiday';
                                    } else if ($row['STATUS'] == 'Event') {
                                        $badgeClass = 'badge-event';
                                    } else if (strpos($row['STATUS'], 'No TimeOut') !== false) {
                                        $badgeClass = 'badge-warning';
                                    }
                                    
                                    // Format additional info
                                    $additionalInfo = '';
                                    if (!empty($row['HolidayInfo'])) {
                                        $additionalInfo .= '<span class="info-text holiday-info"><i class="fas fa-calendar-day"></i> ' 
                                            . htmlspecialchars($row['HolidayInfo']) . '</span>';
                                    }
                                    if (!empty($row['EventInfo'])) {
                                        $additionalInfo .= '<span class="info-text event-info"><i class="fas fa-calendar-check"></i> ' 
                                            . htmlspecialchars($row['EventInfo']) . '</span>';
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['Last Name']) . ", " . htmlspecialchars($row['First Name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['EMPLOYEEID']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['TIMEIN']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['TIMEOUT']) . "</td>";
                                    echo "<td>" . date('M d, Y', strtotime($row['LOGDATE'])) . "</td>";
                                    echo "<td><span class='badge {$badgeClass}'>" . htmlspecialchars($row['STATUS']) . "</span></td>";
                                    echo "<td>" . $additionalInfo . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No attendance records found</td></tr>";
                            }

                            // Close connection
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-export" onclick="exportAttendance()">
                    <i class="fas fa-file-export"></i> Export to Excel
                </button>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#attendanceTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "order": [[4, 'desc'], [2, 'desc']], // Sort by date desc, then time in desc
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });
        });
        
        function exportAttendance() {
            if (confirm("Do you want to export the attendance records to Excel?")) {
                window.location.href = "export_attendance.php";
            }
        }
    </script>
</body>
</html>