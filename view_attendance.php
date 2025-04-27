<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        .container {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
            margin-bottom: 30px;
        }

        h2 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 28px;
            text-align: center;
        }

        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        label {
            font-weight: 500;
            color: #2c3e50;
            margin-right: 15px;
        }

        select {
            padding: 8px 15px;
            border: 1px solid #dce4ec;
            border-radius: 5px;
            margin-right: 15px;
            font-family: 'Poppins', sans-serif;
            background-color: white;
        }

        .btn-primary {
            background-color: #4F6F52;
            border: none;
            padding: 10px 25px;
            border-radius: 7px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3d5641;
            transform: translateY(-2px);
        }

        .employee-info {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .employee-info img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .employee-details {
            flex-grow: 1;
        }

        .employee-name {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .employee-department {
            color: #7f8c8d;
            font-size: 16px;
        }

        table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        th {
            background-color: #4F6F52 !important;
            color: white !important;
            font-weight: 500;
            padding: 15px !important;
            border: none !important;
        }

        td {
            padding: 12px 15px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #eef0f3 !important;
            color: #2c3e50;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .no-records {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-size: 16px;
        }

        .print-btn {
            background-color: #4F6F52;
            color: white;
            padding: 10px 25px;
            border-radius: 7px;
            border: none;
            font-weight: 500;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            background-color: #3d5641;
            transform: translateY(-2px);
        }

        @media print {
            body {
                background-color: white;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .container {
                box-shadow: none;
                margin: 0;
                padding: 15px;
                width: 100%;
                max-width: 100%;
            }
            .form-section, .print-btn {
                display: none;
            }
            .employee-info {
                background-color: white;
                padding: 15px 0;
                margin-bottom: 20px;
            }
            table {
                box-shadow: none;
                width: 100%;
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            th {
                background-color: #4F6F52 !important;
                color: white !important;
            }
            td, th {
                font-size: 12pt;
            }
            .alert-info {
                border: 1px solid #ccc;
                background-color: #f9f9f9 !important;
                color: #333 !important;
            }
            h2, h3 {
                margin: 10px 0;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            .employee-info {
                flex-direction: column;
                text-align: center;
            }
            .employee-details {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-calendar-check mr-2"></i>Attendance Record</h2>
        
        <div class="form-section">
            <form method="POST" class="d-flex align-items-center justify-content-center flex-wrap gap-3">
                <label for="month">Select Month:</label>
                <select name="month" id="month" class="form-control-sm">
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <label for="year">Select Year:</label>
                <select name="year" id="year" class="form-control-sm">
                    <?php
                    $currentYear = date('Y');
                    $startYear = 2020; // You can adjust the start year as needed
                    for($year = $currentYear; $year >= $startYear; $year--) {
                        echo "<option value='$year'>$year</option>";
                    }
                    ?>
                </select>

                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-search mr-2"></i>Show Attendance
                </button>
            </form>
        </div>

        <?php
        if (isset($_POST['submit'])) {
            // Get the selected month and year
            $selected_month = $_POST['month'];
            $selected_year = $_POST['year'];

            // Connect to the database
            $server = "localhost";
            $username = "root";
            $password = "";
            $dbname = "hris";
            $conn = new mysqli($server, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch attendance for the selected month and year for the specific employee
            $employee_id = $_GET['employee_id']; // Assuming you're passing employee_id via GET parameter

            // Calculate the number of days in the selected month
            $days_in_month = cal_days_in_month(CAL_GREGORIAN, $selected_month, $selected_year);
            
            // Calculate working days and rest days more accurately
            $total_days = cal_days_in_month(CAL_GREGORIAN, $selected_month, $selected_year);
            $first_day = "$selected_year-$selected_month-01";
            $last_day = "$selected_year-$selected_month-$total_days";
            
            // Calculate number of complete weeks and remaining days
            $first_day_timestamp = strtotime($first_day);
            $last_day_timestamp = strtotime($last_day);
            
            // Get employee's configured rest days
            $sql_rest_days = "SELECT Monday_Rest, Tuesday_Rest, Wednesday_Rest, Thursday_Rest, 
                                    Friday_Rest, Saturday_Rest, Sunday_Rest 
                             FROM employee 
                             WHERE EmployeeID = $employee_id";
            $rest_days_result = $conn->query($sql_rest_days);
            $rest_days_config = $rest_days_result->fetch_assoc();
            
            // Calculate rest days and working days based on employee's schedule
            $rest_days = 0;
            $total_working_days = $total_days; // Start with total days
            
            // Loop through each day of the month
            for ($day = 1; $day <= $total_days; $day++) {
                $date = date('Y-m-d', strtotime("$selected_year-$selected_month-$day"));
                $day_of_week = date('N', strtotime($date)); // 1 (Monday) through 7 (Sunday)
                
                // Check if this day is configured as a rest day for the employee
                $is_rest_day = false;
                switch ($day_of_week) {
                    case 1: $is_rest_day = $rest_days_config['Monday_Rest'] == 1; break;
                    case 2: $is_rest_day = $rest_days_config['Tuesday_Rest'] == 1; break;
                    case 3: $is_rest_day = $rest_days_config['Wednesday_Rest'] == 1; break;
                    case 4: $is_rest_day = $rest_days_config['Thursday_Rest'] == 1; break;
                    case 5: $is_rest_day = $rest_days_config['Friday_Rest'] == 1; break;
                    case 6: $is_rest_day = $rest_days_config['Saturday_Rest'] == 1; break;
                    case 7: $is_rest_day = $rest_days_config['Sunday_Rest'] == 1; break;
                }
                
                if ($is_rest_day) {
                    $rest_days++;
                    $total_working_days--; // Subtract rest days from total days
                }
            }

            // Fetch attendance records
            $sql = "SELECT a.*, e.`First Name`, e.`Last Name`, e.Department,
                   TIMEDIFF(a.TIMEOUT, a.TIMEIN) as work_hours
                   FROM attendance a 
                   LEFT JOIN employee e ON a.EMPLOYEEID = e.EmployeeID 
                   WHERE MONTH(a.LOGDATE) = $selected_month 
                   AND YEAR(a.LOGDATE) = $selected_year
                   AND a.EMPLOYEEID = $employee_id
                   AND (a.TIMEOUT IS NOT NULL OR 
                        (a.TIMEOUT IS NULL AND (a.STATUS LIKE '%No TimeOut%' OR TIME(NOW()) >= '19:00:00')))
                   ORDER BY a.LOGDATE DESC, a.TIMEIN DESC";
            $result = $conn->query($sql);

            // Calculate total absences and work hours
            $attendance_dates = array();
            $total_hours = 0;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $attendance_dates[] = $row['LOGDATE'];
                    if ($row['TIMEOUT'] !== null) {
                        $work_hours = $row['work_hours'];
                        $total_hours += strtotime($work_hours) - strtotime('TODAY');
                    }
                }
            }

            // Calculate absences (only counting non-rest days)
            $absences = 0;
            for ($day = 1; $day <= $total_days; $day++) {
                $date = date('Y-m-d', strtotime("$selected_year-$selected_month-$day"));
                $day_of_week = date('N', strtotime($date));
                
                // Check if this is a rest day for the employee
                $is_rest_day = false;
                switch ($day_of_week) {
                    case 1: $is_rest_day = $rest_days_config['Monday_Rest'] == 1; break;
                    case 2: $is_rest_day = $rest_days_config['Tuesday_Rest'] == 1; break;
                    case 3: $is_rest_day = $rest_days_config['Wednesday_Rest'] == 1; break;
                    case 4: $is_rest_day = $rest_days_config['Thursday_Rest'] == 1; break;
                    case 5: $is_rest_day = $rest_days_config['Friday_Rest'] == 1; break;
                    case 6: $is_rest_day = $rest_days_config['Saturday_Rest'] == 1; break;
                    case 7: $is_rest_day = $rest_days_config['Sunday_Rest'] == 1; break;
                }
                
                // Only count absences on non-rest days
                if (!$is_rest_day && !in_array($date, $attendance_dates)) {
                    $absences++;
                }
            }

            // Format total hours
            $total_hours_formatted = sprintf(
                '%02d:%02d',
                floor($total_hours / 3600),
                floor(($total_hours / 60) % 60)
            );

            // Display employee information
            $sql_employee = "SELECT * FROM employee WHERE EmployeeID = $employee_id";
            $result_employee = $conn->query($sql_employee);

            // Display employee information
            if ($result_employee->num_rows > 0) {
                $employee_row = $result_employee->fetch_assoc();
                $employee_image = $employee_row['Image'];
                $employee_name = $employee_row['First Name'] . " " . $employee_row['Last Name'];
                $employee_department = $employee_row['Department'];
            
                echo '<div class="employee-info">';
                echo '<img src="data:image/jpeg;base64,' . base64_encode($employee_image) . '" alt="Employee Image">';
                echo '<div class="employee-details">';
                echo '<div class="employee-name">' . $employee_name . '</div>';
                echo '<div class="employee-department"><i class="fas fa-building mr-2"></i>' . $employee_department . '</div>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Employee information not found.
                      </div>';
            }
            
            // Display attendance
            if ($result->num_rows > 0) {
                echo "<h3 class='text-center mb-4'><i class='fas fa-calendar-alt mr-2'></i>Attendance for " . 
                     date("F Y", mktime(0, 0, 0, $selected_month, 1, $selected_year)) . "</h3>";
                
                // Display summary statistics
                echo '<div class="alert alert-info mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <strong><i class="fas fa-calendar-day"></i> Working Days:</strong> ' . $total_working_days . ' days
                        </div>
                        <div class="col-md-3">
                            <strong><i class="fas fa-bed"></i> Rest Days:</strong> ' . $rest_days . ' days
                        </div>
                        <div class="col-md-3">
                            <strong><i class="fas fa-user-times"></i> Absences:</strong> ' . $absences . ' days
                        </div>
                        <div class="col-md-3">
                            <strong><i class="fas fa-clock"></i> Total Hours:</strong> ' . floor($total_hours / 3600) . ' hours ' . floor(($total_hours / 60) % 60) . ' minutes
                        </div>
                    </div>
                </div>';
                
                echo '<div class="table-responsive">';
                echo "<table class='table'>";
                echo "<thead><tr>
                        <th><i class='fas fa-calendar-day mr-2'></i>Date</th>
                        <th><i class='fas fa-clock mr-2'></i>Time In</th>
                        <th><i class='fas fa-clock mr-2'></i>Time Out</th>
                        <th><i class='fas fa-clock mr-2'></i>Work Hours</th>
                        <th><i class='fas fa-info-circle mr-2'></i>Status</th>
                      </tr></thead>";
                echo "<tbody>";
                
                // Reset result pointer
                $result->data_seek(0);
                
                while ($row = $result->fetch_assoc()) {
                    $work_hours = '-';
                    if ($row['TIMEOUT'] !== null) {
                        $time_diff = strtotime($row['work_hours']) - strtotime('TODAY');
                        $hours = floor($time_diff / 3600);
                        $minutes = floor(($time_diff % 3600) / 60);
                        $work_hours = $hours . ' hrs ' . $minutes . ' mins';
                    }
                    echo "<tr>";
                    echo "<td style='font-family: Poppins;'>" . $row['LOGDATE'] . "</td>";
                    echo "<td style='font-family: Poppins;'>" . $row['TIMEIN'] . "</td>";
                    echo "<td style='font-family: Poppins;'>" . $row['TIMEOUT'] . "</td>";
                    echo "<td style='font-family: Poppins;'>" . $work_hours . "</td>";
                    echo "<td style='font-family: Poppins;'>" . $row['STATUS'] . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
                echo '</div>';
            } else {
                echo '<div class="alert alert-info no-records" role="alert">
                        <i class="fas fa-info-circle mr-2"></i>No attendance records found for ' . 
                        date("F Y", mktime(0, 0, 0, $selected_month, 1, $selected_year)) . '.
                      </div>';
            }
            
            // Close database connection
            $conn->close();
        }
        ?>
        <div class="text-center">
            <button onclick="window.print()" class="print-btn">
                <i class="fas fa-print mr-2"></i>Print Report
            </button>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
