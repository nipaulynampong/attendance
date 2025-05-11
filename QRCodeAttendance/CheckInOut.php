<?php
session_start();

// Set timezone to Philippine time
date_default_timezone_set('Asia/Manila');

$server = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if HolidayInfo column exists in attendance table
$check_column_sql = "SELECT COUNT(*) as column_exists 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = '$dbname' 
                    AND TABLE_NAME = 'attendance' 
                    AND COLUMN_NAME = 'HolidayInfo'";
                    
$column_result = $conn->query($check_column_sql);
$column_data = $column_result->fetch_assoc();

// If HolidayInfo column doesn't exist, create it
if ($column_data['column_exists'] == 0) {
    $add_column_sql = "ALTER TABLE attendance ADD COLUMN HolidayInfo VARCHAR(255) DEFAULT NULL";
    $conn->query($add_column_sql);
}

// Check if EventInfo column exists in attendance table
$check_column_sql = "SELECT COUNT(*) as column_exists 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = '$dbname' 
                    AND TABLE_NAME = 'attendance' 
                    AND COLUMN_NAME = 'EventInfo'";
                    
$column_result = $conn->query($check_column_sql);
$column_data = $column_result->fetch_assoc();

// If EventInfo column doesn't exist, create it
if ($column_data['column_exists'] == 0) {
    $add_column_sql = "ALTER TABLE attendance ADD COLUMN EventInfo VARCHAR(255) DEFAULT NULL";
    $conn->query($add_column_sql);
}

$current_date = date('Y-m-d');
$current_time = date('h:i:s A'); // 12-hour format with AM/PM
$time_for_comparison = date('H:i:s'); // 24-hour format for comparisons
$day_of_week = date('l'); // Get the current day of the week (Monday, Tuesday, etc.)

// Check if today is a holiday
$is_holiday = false;
$holiday_name = "";
$holiday_type = "";

$check_holiday_sql = "SELECT * FROM holidays WHERE holiday_date = '$current_date'";
$holiday_result = $conn->query($check_holiday_sql);

if ($holiday_result && $holiday_result->num_rows > 0) {
    $holiday_data = $holiday_result->fetch_assoc();
    $is_holiday = true;
    $holiday_name = $holiday_data['holiday_name'];
    $holiday_type = $holiday_data['holiday_type'];
}

// Check if there are any events today
function checkEvents($date, $department = 'All') {
    global $conn;
    $query = "SELECT * FROM events 
              WHERE '$date' BETWEEN start_date AND end_date 
              AND (departments = 'All' OR FIND_IN_SET('$department', departments))";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return false;
}

// Function to handle automatic timeout for missing timeouts
function handleAutoTimeout($conn) {
    // Get dates up to yesterday
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    try {
        // Start transaction for better data consistency
        $conn->begin_transaction();
        
        // Update all records from past dates that don't have a timeout
        $sql = "UPDATE attendance 
                SET STATUS = CASE 
                    WHEN STATUS = 'Present' THEN 'Present - No TimeOut'
                    WHEN STATUS = 'Late' THEN 'Late - No TimeOut'
                    ELSE STATUS 
                END,
                TIMEOUT = '19:00:00'
                WHERE LOGDATE < '$yesterday' 
                AND TIMEOUT IS NULL 
                AND (STATUS = 'Present' OR STATUS = 'Late')";
        
        $conn->query($sql);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error in handleAutoTimeout: " . $e->getMessage());
    }
}

// Call the auto timeout function before processing new attendance
handleAutoTimeout($conn);

if (isset($_POST['EmployeeID'])) {
    $employeeID = $_POST['EmployeeID'];
    
    // Get the actual scan time
    $actual_time = date('h:i:s A'); // 12-hour format with AM/PM for display
    $actual_time24 = date('H:i:s'); // 24-hour format for comparison
    
    // Standard start time and grace period
    $standard_start_time = '08:00:00'; // 8 AM
    $grace_period_end = '08:15:59'; // 8:15 AM (including seconds)
    $standard_end_time = '19:00:00'; // 7 PM (updated from 5 PM)
    
    // Initialize display time to actual time by default
    $display_time = $actual_time;
    
    // Apply time rounding logic for TIME IN
    if ($actual_time24 < $standard_start_time || 
        ($actual_time24 >= $standard_start_time && $actual_time24 <= $grace_period_end)) {
        // If before 8 AM or within grace period (8:00-8:15), record as 8 AM
        $display_time = '08:00:00 AM';
    }
    // Otherwise, use the actual time

    // First check if the employee exists in the employee table
    $employee_sql = "SELECT * FROM employee WHERE EmployeeID = '$employeeID'";
    $employee_query = $conn->query($employee_sql);

    if ($employee_query->num_rows < 1) {
        // Employee doesn't exist - display error and exit
        $_SESSION['error_type'] = "employee_not_found";
        $_SESSION['error_message'] = "Employee ID " . $employeeID . " is not registered in the system.";
        header("Location: index.php");
        exit();
    }
    
    // If we get here, employee exists - process attendance
    $employee_row = $employee_query->fetch_assoc();
    $employeeid = $employee_row['EmployeeID'];
    $firstName = $employee_row['First Name'];
    $lastName = $employee_row['Last Name'];
    $department = $employee_row['Department'];

    // Check if the employee has already clocked in for the current day
    $check_clock_in_sql = "SELECT * FROM attendance WHERE EMPLOYEEID='$employeeID' AND LOGDATE='$current_date'";
    $check_clock_in_query = $conn->query($check_clock_in_sql);
    
    if ($check_clock_in_query->num_rows > 0) {
        // Employee has already clocked in for today - process TIME OUT
        $row = $check_clock_in_query->fetch_assoc();
        $time_in = $row['TIMEIN'];
        $current_status = $row['STATUS'];
        
        // Check if today is a rest day for the employee
        $is_rest_day = false;
        $rest_day_field = $day_of_week . '_Rest'; // e.g., Monday_Rest
        
        if (isset($employee_row[$rest_day_field]) && $employee_row[$rest_day_field] == 1) {
            $is_rest_day = true;
        }
        
        // Check for events
        $event = checkEvents($current_date, $department);
        $event_info = "";
        if ($event) {
            $event_info = $event['event_name'] . " (" . $event['event_type'] . ")";
        }
            
        if (!$row['TIMEOUT']) {
            $required_work_hours = 8; 
            
            $time_in_obj = DateTime::createFromFormat('h:i:s A', $time_in);
            $time_in_24 = $time_in_obj ? $time_in_obj->format('H:i:s') : '';
            
            $time_in_seconds = strtotime($time_in_24);
            $time_out_seconds = strtotime($time_for_comparison);
            $hours_worked = ($time_out_seconds - $time_in_seconds) / 3600;
            
            $new_status = $current_status;
            if ($current_status == "Present" || $current_status == "Late") {
                if ($hours_worked < $required_work_hours) {
                    $new_status = $current_status . " - Early Out";
                } else {
                    $new_status = $current_status . " - Complete";
                }
            }
            
            $update_sql = "UPDATE attendance 
                         SET TIMEOUT='$actual_time', 
                             STATUS='$new_status' 
                         WHERE EMPLOYEEID='$employeeID' 
                         AND LOGDATE='$current_date'";
                         
            if ($conn->query($update_sql) === TRUE) {
                $_SESSION['success'] = "Time out recorded successfully!";
                $_SESSION['scan_success'] = true;
                $_SESSION['scan_type'] = "Time Out";
                $_SESSION['scan_time'] = $actual_time;
                $_SESSION['scan_date'] = $current_date;
                $_SESSION['status'] = $new_status;
                $_SESSION['employee_id'] = $employeeID;
                $_SESSION['employee_name'] = $firstName . ' ' . $lastName;
                $_SESSION['department'] = $department;
                
                // Add these for consistency with time-in overlay
                $_SESSION['is_rest_day'] = $is_rest_day; 
                $_SESSION['is_holiday'] = $is_holiday;
                if ($is_holiday) {
                    $_SESSION['holiday_name'] = $holiday_name;
                }
                $_SESSION['event_info'] = $event_info;
            } else {
                $_SESSION['error'] = "Error updating record: " . $conn->error;
            }
        } else {
            // Already timed out - use overlay message instead of regular error
            $_SESSION['error_type'] = "already_timed_out";
            $_SESSION['error_message'] = "You have already timed out for today.";
            header("Location: index.php");
            exit();
        }
        header("Location: index.php");
        exit();
    } else {
        // Employee hasn't clocked in today - process TIME IN

        // Check for events
        $event = checkEvents($current_date, $department);
        
        // Check if today is a rest day for the employee
        $is_rest_day = false;
        $rest_day_field = $day_of_week . '_Rest'; // e.g., Monday_Rest
        
        if (isset($employee_row[$rest_day_field]) && $employee_row[$rest_day_field] == 1) {
            $is_rest_day = true;
        }

        // Initialize status variables
        $status = "Present";
        $event_info = "";
        $holiday_info = "";
        
        // Priority order: Holiday > Event > Rest Day > Regular Attendance
        if ($is_holiday) {
            $status = "Holiday";
            $holiday_info = $holiday_name . " (" . $holiday_type . ")";
        } elseif ($event) {
            // If there's an event and it requires attendance
            if ($event['required_attendance']) {
                $status = "Event";
                $event_info = $event['event_name'] . " (" . $event['event_type'] . ")";
            } else {
                // Optional event - mark as present but include event info
                $status = "Present";
                $event_info = "Optional: " . $event['event_name'];
            }
        } elseif ($is_rest_day) {
            $status = "Rest Day";
        } else {
            // Regular attendance check
            if ($actual_time24 <= $grace_period_end) {
                $status = "Present";
            } else {
                $status = "Late";
            }
        }
        
        // Debug output to identify department values
        echo "Department being inserted: '" . $department . "'<br>";
        
        // Check what departments exist in the database
        $check_departments = "SELECT Department FROM department";
        $result = $conn->query($check_departments);
        echo "Existing departments in database:<br>";
        while($row = $result->fetch_assoc()) {
            echo "'" . $row['Department'] . "'<br>";
        }
       
        // Check if department exists before inserting
        $department_exists = false;
        $check_dept_sql = "SELECT COUNT(*) as dept_count FROM department WHERE Department = '$department'";
        $dept_result = $conn->query($check_dept_sql);
        $dept_data = $dept_result->fetch_assoc();
        
        if ($dept_data['dept_count'] > 0) {
            // Department exists, proceed with insert
            $middleName = isset($employee_row['Middle Name']) ? $employee_row['Middle Name'] : '';
            
            $insert_sql = "INSERT INTO attendance(EMPLOYEEID, `Last Name`, `First Name`, `Middle Name`, TIMEIN, LOGDATE, STATUS, Department, HolidayInfo, EventInfo) 
                          VALUES ('$employeeid', '$lastName', '$firstName', '$middleName', '$display_time', '$current_date', '$status', '$department', " . 
                          ($is_holiday ? "'$holiday_name ($holiday_type)'" : "NULL") . ", " .
                          ($event_info ? "'$event_info'" : "NULL") . ")";
            
            echo "SQL Query: " . $insert_sql . "<br>";
            echo "Employee ID: " . $employeeid . "<br>";
            echo "Employee Name: " . $firstName . " " . $lastName . "<br>";
            
            if ($conn->query($insert_sql) === TRUE) {
               
                $_SESSION['scan_success'] = true;
                $_SESSION['employee_id'] = $employeeid;
                $_SESSION['employee_name'] = $firstName . ' ' . $lastName;
                $_SESSION['department'] = $department;
                $_SESSION['scan_time'] = $display_time;
                $_SESSION['scan_date'] = $current_date;
                $_SESSION['scan_type'] = 'Time In';
                $_SESSION['status'] = $status;
                $_SESSION['is_rest_day'] = $is_rest_day;
                $_SESSION['is_holiday'] = $is_holiday;
                $_SESSION['holiday_name'] = $holiday_name;
                $_SESSION['event_info'] = $event_info;
                $_SESSION['success'] = 'Successfully Time In: ' . $firstName . ' ' . $lastName;
            } else {
                $_SESSION['error'] = $conn->error;
            }
        } else {
            // Department doesn't exist
            $_SESSION['error_type'] = "department_not_found";
            $_SESSION['error_message'] = "Department '$department' does not exist in the system. Please contact HR.";
            
            // Redirect immediately when department doesn't exist
            header("Location: index.php");
            exit();
        }
    }
} else {
    $_SESSION['error'] = 'Please scan your QR Code';
}

header("location: index.php");
$conn->close();
?>
