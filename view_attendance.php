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
            max-height: 85vh;
            overflow-y: auto;
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

        /* Scrollbar styling */
        .container::-webkit-scrollbar {
            width: 8px;
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

        .table-responsive {
            max-height: 50vh;
            overflow-y: auto;
        }
        
        .table-responsive::-webkit-scrollbar {
            width: 8px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #4F6F52;
            border-radius: 10px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #3A4D39;
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
                    <?php
                    $current_month = date('n'); // Current month number (1-12)
                    for($m = 1; $m <= 12; $m++) {
                        $month_name = date('F', mktime(0, 0, 0, $m, 1));
                        $selected = ($m == $current_month) ? 'selected' : '';
                        echo "<option value='$m' $selected>$month_name</option>";
                    }
                    ?>
                </select>

                <label for="year">Select Year:</label>
                <select name="year" id="year" class="form-control-sm">
                    <?php
                    $currentYear = date('Y');
                    $startYear = 2020; // You can adjust the start year as needed
                    for($year = $currentYear; $year >= $startYear; $year--) {
                        $selected = ($year == $currentYear) ? 'selected' : '';
                        echo "<option value='$year' $selected>$year</option>";
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
            $total_days = cal_days_in_month(CAL_GREGORIAN, $selected_month, $selected_year);
            
            // Get employee's configured rest days
            $sql_rest_days = "SELECT Monday_Rest, Tuesday_Rest, Wednesday_Rest, Thursday_Rest, 
                                    Friday_Rest, Saturday_Rest, Sunday_Rest 
                             FROM employee 
                             WHERE EmployeeID = $employee_id";
            $rest_days_result = $conn->query($sql_rest_days);
            $rest_days_config = $rest_days_result->fetch_assoc();
            
            // Count rest days in the month
            $rest_days = 0;
            for ($day = 1; $day <= $total_days; $day++) {
                $date = date('Y-m-d', strtotime("$selected_year-$selected_month-$day"));
                $day_of_week = date('N', strtotime($date)); // 1-7 for Monday-Sunday
                
                switch ($day_of_week) {
                    case 1: if ($rest_days_config['Monday_Rest'] == 1) $rest_days++; break;
                    case 2: if ($rest_days_config['Tuesday_Rest'] == 1) $rest_days++; break;
                    case 3: if ($rest_days_config['Wednesday_Rest'] == 1) $rest_days++; break;
                    case 4: if ($rest_days_config['Thursday_Rest'] == 1) $rest_days++; break;
                    case 5: if ($rest_days_config['Friday_Rest'] == 1) $rest_days++; break;
                    case 6: if ($rest_days_config['Saturday_Rest'] == 1) $rest_days++; break;
                    case 7: if ($rest_days_config['Sunday_Rest'] == 1) $rest_days++; break;
                }
            }
            
            // Get all holidays in the selected month
            $holiday_dates = array();
            $regular_holidays = array();
            $special_holidays = array();
            $sql_holidays = "SELECT holiday_date, holiday_type FROM holidays 
                             WHERE MONTH(holiday_date) = $selected_month 
                             AND YEAR(holiday_date) = $selected_year";
            $holidays_result = $conn->query($sql_holidays);
            
            if ($holidays_result && $holidays_result->num_rows > 0) {
                while ($holiday_row = $holidays_result->fetch_assoc()) {
                    $holiday_date = $holiday_row['holiday_date'];
                    $holiday_type = $holiday_row['holiday_type'];
                    
                    $holiday_dates[] = $holiday_date;
                    
                    if (strtolower($holiday_type) == 'regular') {
                        $regular_holidays[] = $holiday_date;
                    } else if (strtolower($holiday_type) == 'special') {
                        $special_holidays[] = $holiday_date;
                    }
                }
            }
            
            // Get all dates with recorded attendance for the employee
            $sql_attendance = "SELECT COUNT(DISTINCT DATE(LOGDATE)) as attendance_count
                             FROM attendance 
                             WHERE MONTH(LOGDATE) = $selected_month 
                             AND YEAR(LOGDATE) = $selected_year
                             AND EMPLOYEEID = $employee_id
                             AND TIMEIN IS NOT NULL";
            $attendance_result = $conn->query($sql_attendance);
            
            // Debug the query
            error_log("Attendance Query: " . $sql_attendance);
            error_log("Employee ID: " . $employee_id);
            error_log("Selected Month: " . $selected_month);
            error_log("Selected Year: " . $selected_year);
            
            $days_with_attendance = 0;
            if ($attendance_result) {
                $row = $attendance_result->fetch_assoc();
                $days_with_attendance = $row['attendance_count'];
                error_log("Attendance Count: " . $days_with_attendance);
            } else {
                error_log("Query Error: " . $conn->error);
            }
            
            // Calculate working days (total days - rest days - holidays)
            $working_days = $total_days - $rest_days - count($holiday_dates);
            
            // Calculate absences (working days - days with attendance)
            $absences = $working_days - $days_with_attendance;

            // Calculate total hours
            $total_hours = 0;
            $work_hours_data = array();
            $sql = "SELECT a.*, e.`First Name`, e.`Last Name`, e.Department,
                   TIMEDIFF(a.TIMEOUT, a.TIMEIN) as work_hours
                   FROM attendance a 
                   LEFT JOIN employee e ON a.EMPLOYEEID = e.EmployeeID 
                   WHERE MONTH(a.LOGDATE) = $selected_month 
                   AND YEAR(a.LOGDATE) = $selected_year
                   AND a.EMPLOYEEID = $employee_id
                   ORDER BY a.LOGDATE DESC, a.TIMEIN DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $work_hours = '-';
                    $diff_seconds = 0;
                    
                    if ($row['TIMEOUT'] !== null) {
                        // Debug the raw values for troubleshooting
                        error_log("Raw TIMEIN: " . $row['TIMEIN'] . ", Raw TIMEOUT: " . $row['TIMEOUT'] . ", Date: " . $row['LOGDATE']);
                        
                        // Use strtotime directly on the datetime values
                        $time_in_seconds = strtotime($row['TIMEIN']);
                        $time_out_seconds = strtotime($row['TIMEOUT']);
                        
                        // If conversion failed, try with explicit format
                        if (!$time_in_seconds || !$time_out_seconds) {
                            error_log("Time conversion failed, trying with explicit format");
                            // Extract time parts using a more reliable method
                            if (preg_match('/(\d{1,2}):(\d{1,2}):(\d{1,2})/', $row['TIMEIN'], $time_in_parts) && 
                                preg_match('/(\d{1,2}):(\d{1,2}):(\d{1,2})/', $row['TIMEOUT'], $time_out_parts)) {
                                
                                $time_in_seconds = strtotime("1970-01-01 " . $time_in_parts[0]);
                                $time_out_seconds = strtotime("1970-01-01 " . $time_out_parts[0]);
                            }
                        }
                        
                        // Calculate difference in seconds if both times are valid
                        if ($time_in_seconds && $time_out_seconds) {
                            $diff_seconds = $time_out_seconds - $time_in_seconds;
                            
                            // Handle potential negative values (if timeout is earlier than timein)
                            if ($diff_seconds < 0) {
                                error_log("Negative time difference detected, adjusting calculation");
                                $diff_seconds += 86400;
                            }
                            
                            // Log raw time difference for debugging
                            error_log("Raw time difference in seconds: " . $diff_seconds);
                            
                            // Only subtract break time if worked more than 4 hours (14400 seconds)
                            if ($diff_seconds > 14400) {
                                $diff_seconds = max(0, $diff_seconds - 3600); // Subtract 1 hour break
                                error_log("Subtracting break time, new diff: " . $diff_seconds);
                            }
                            
                            // Convert to hours and minutes
                            $hours = floor($diff_seconds / 3600);
                            $minutes = floor(($diff_seconds % 3600) / 60);
                            
                            $work_hours = $hours . ' hrs ' . $minutes . ' mins';
                            error_log("Calculated work hours: " . $work_hours . " from diff_seconds: " . $diff_seconds);
                        } else {
                            error_log("Could not convert time values: TIMEIN=" . $row['TIMEIN'] . ", TIMEOUT=" . $row['TIMEOUT']);
                            $work_hours = '0 hrs 0 mins';
                        }
                    } else {
                        $work_hours = 'No Timeout';
                    }
                    
                    $work_hours_data[] = $work_hours;
                    $total_hours += $diff_seconds;
                }
            }

            // Calculate total hours formatted
            $total_hours_formatted = sprintf(
                '%d hours %d minutes',
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
                // Check if image exists and is not empty
                if (!empty($employee_image) && $employee_image != null) {
                    $imagePath = $employee_image;
                    
                    // Check if the file exists
                    if (file_exists($imagePath)) {
                        // Display the image from the file path
                        echo '<img src="' . $imagePath . '" alt="Employee Image">';
                    } else {
                        // Display default image if file doesn't exist
                        echo '<div style="width:100px; height:100px; border-radius:50%; background-color:#4F6F52; display:flex; align-items:center; justify-content:center; margin:0 auto;">';
                        echo '<i class="fas fa-user" style="color:white; font-size:40px;"></i>';
                        echo '</div>';
                    }
                } else {
                    // Display default image if no image is available
                    echo '<div style="width:100px; height:100px; border-radius:50%; background-color:#4F6F52; display:flex; align-items:center; justify-content:center; margin:0 auto;">';
                    echo '<i class="fas fa-user" style="color:white; font-size:40px;"></i>';
                    echo '</div>';
                }
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
                            <strong><i class="fas fa-calendar-day"></i> Working Days:</strong> ' . $working_days . ' days
                        </div>
                        <div class="col-md-3">
                            <strong><i class="fas fa-bed"></i> Rest Days:</strong> ' . $rest_days . ' days
                        </div>
                        <div class="col-md-2">
                            <strong><i class="fas fa-star"></i> Holidays:</strong> ' . count($holiday_dates) . ' days
                        </div>
                        <div class="col-md-2">
                            <strong><i class="fas fa-user-times"></i> Absences:</strong> ' . $absences . ' days
                            <small class="d-block text-muted">Work: ' . $working_days . ', Present: ' . $days_with_attendance . '</small>
                        </div>
                        <div class="col-md-2">
                            <strong><i class="fas fa-clock"></i> Total Hours:</strong> ' . $total_hours_formatted . '
                            <small class="d-block text-muted">(Break time excluded)</small>
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
                        // Debug the raw values for troubleshooting
                        error_log("Raw TIMEIN: " . $row['TIMEIN'] . ", Raw TIMEOUT: " . $row['TIMEOUT'] . ", Date: " . $row['LOGDATE']);
                        
                        // Use strtotime directly on the datetime values
                        $time_in_seconds = strtotime($row['TIMEIN']);
                        $time_out_seconds = strtotime($row['TIMEOUT']);
                        
                        // If conversion failed, try with explicit format
                        if (!$time_in_seconds || !$time_out_seconds) {
                            error_log("Time conversion failed, trying with explicit format");
                            // Extract time parts using a more reliable method
                            if (preg_match('/(\d{1,2}):(\d{1,2}):(\d{1,2})/', $row['TIMEIN'], $time_in_parts) && 
                                preg_match('/(\d{1,2}):(\d{1,2}):(\d{1,2})/', $row['TIMEOUT'], $time_out_parts)) {
                                
                                $time_in_seconds = strtotime("1970-01-01 " . $time_in_parts[0]);
                                $time_out_seconds = strtotime("1970-01-01 " . $time_out_parts[0]);
                            }
                        }
                        
                        // Calculate difference in seconds if both times are valid
                        if ($time_in_seconds && $time_out_seconds) {
                            $diff_seconds = $time_out_seconds - $time_in_seconds;
                            
                            // Handle potential negative values (if timeout is earlier than timein)
                            if ($diff_seconds < 0) {
                                error_log("Negative time difference detected, adjusting calculation");
                                $diff_seconds += 86400;
                            }
                            
                            // Log raw time difference for debugging
                            error_log("Raw time difference in seconds: " . $diff_seconds);
                            
                            // Only subtract break time if worked more than 4 hours (14400 seconds)
                            if ($diff_seconds > 14400) {
                                $diff_seconds = max(0, $diff_seconds - 3600); // Subtract 1 hour break
                                error_log("Subtracting break time, new diff: " . $diff_seconds);
                            }
                            
                            // Convert to hours and minutes
                            $hours = floor($diff_seconds / 3600);
                            $minutes = floor(($diff_seconds % 3600) / 60);
                            
                            $work_hours = $hours . ' hrs ' . $minutes . ' mins';
                            error_log("Calculated work hours: " . $work_hours . " from diff_seconds: " . $diff_seconds);
                        } else {
                            error_log("Could not convert time values: TIMEIN=" . $row['TIMEIN'] . ", TIMEOUT=" . $row['TIMEOUT']);
                            $work_hours = '0 hrs 0 mins';
                        }
                    } else {
                        $work_hours = 'No Timeout';
                    }
                    echo "<tr>";
                    echo "<td style='font-family: Poppins;'>" . $row['LOGDATE'] . "</td>";
                    echo "<td style='font-family: Poppins;'>" . $row['TIMEIN'] . "</td>";
                    echo "<td style='font-family: Poppins;'>" . ($row['TIMEOUT'] ? $row['TIMEOUT'] : 'Not recorded') . "</td>";
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
    
    <script>
        // Auto-submit the form when the page loads if it hasn't been submitted before
        $(document).ready(function() {
            <?php if (!isset($_POST['submit'])) { ?>
                // Submit the form automatically
                $('form').submit();
            <?php } ?>
        });
    </script>
</body>
</html>
