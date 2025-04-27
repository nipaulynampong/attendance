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
                TIMEOUT = '7:00 PM'
                WHERE DATE(LOGDATE) <= '$yesterday'
                AND (TIMEOUT IS NULL OR TIMEOUT = '')
                AND STATUS IN ('Present', 'Late')";
        
        $conn->query($sql);
        
        // Also handle current date after 7 PM
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        
        if (strtotime($current_time) >= strtotime('19:00:00')) {
            $sql = "UPDATE attendance 
                    SET STATUS = CASE 
                        WHEN STATUS = 'Present' THEN 'Present - No TimeOut'
                        WHEN STATUS = 'Late' THEN 'Late - No TimeOut'
                        ELSE STATUS 
                    END,
                    TIMEOUT = '7:00 PM'
                    WHERE LOGDATE = '$current_date' 
                    AND (TIMEOUT IS NULL OR TIMEOUT = '')
                    AND STATUS IN ('Present', 'Late')";
            
            $conn->query($sql);
        }
        
        // Commit the transaction
        $conn->commit();
        
    } catch (Exception $e) {
        // If there's an error, rollback the changes
        $conn->rollback();
        error_log("Auto timeout error: " . $e->getMessage());
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
    $standard_end_time = '17:00:00'; // 5 PM
    
    // Initialize display time to actual time by default
    $display_time = $actual_time;
    
    // Apply time rounding logic for TIME IN
    if ($actual_time24 < $standard_start_time || 
        ($actual_time24 >= $standard_start_time && $actual_time24 <= $grace_period_end)) {
        // If before 8 AM or within grace period (8:00-8:15), record as 8 AM
        $display_time = '08:00:00 AM';
    }
    // Otherwise, use the actual time

    $sql = "SELECT * FROM employee WHERE EmployeeID = '$employeeID'";
    $query = $conn->query($sql);

    if ($query->num_rows < 1) {
        $_SESSION['error'] = 'Cannot find Employee ID ' . $employeeID;
    } else {
        $row = $query->fetch_assoc();
        $employeeid = $row['EmployeeID'];
        $firstName = $row['First Name'];
        $lastName = $row['Last Name'];
        $department = $row['Department'];

        // Check for events
        $event = checkEvents($current_date, $department);
        
        // Check if today is a rest day for the employee
        $is_rest_day = false;
        $rest_day_field = $day_of_week . '_Rest'; // e.g., Monday_Rest
        
        if (isset($row[$rest_day_field]) && $row[$rest_day_field] == 1) {
            $is_rest_day = true;
        }

        // Check if the employee has already clocked in for the current day
        $check_clock_in_sql = "SELECT * FROM attendance WHERE EMPLOYEEID='$employeeid' AND LOGDATE='$current_date'";
        $check_clock_in_query = $conn->query($check_clock_in_sql);

        if ($check_clock_in_query->num_rows == 0) {
            // First scan of the day - TIME IN
            
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
            
            // Insert time in record with the display time (which may be rounded)
            $insert_sql = "INSERT INTO attendance(EMPLOYEEID, TIMEIN, LOGDATE, STATUS, Department, HolidayInfo, EventInfo) 
                          VALUES ('$employeeid', '$display_time', '$current_date', '$status', '$department', '$holiday_info', '$event_info')";
            
            if ($conn->query($insert_sql) === TRUE) {
                // Store employee details in session for display
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
            // Employee has already clocked in - process TIME OUT
            $row = $check_clock_in_query->fetch_assoc();
            $time_in = $row['TIMEIN'];
            $current_status = $row['STATUS'];
            
            // Only process timeout if there isn't one already
            if (!$row['TIMEOUT']) {
                // Calculate the required work duration (in hours)
                $required_work_hours = 8; // Standard 8-hour workday
                
                // Convert time in to 24-hour format for calculation
                $time_in_obj = DateTime::createFromFormat('h:i:s A', $time_in);
                $time_in_24 = $time_in_obj ? $time_in_obj->format('H:i:s') : '';
                
                // Calculate the time difference
                $time_in_seconds = strtotime($time_in_24);
                $time_out_seconds = strtotime($time_for_comparison);
                $hours_worked = ($time_out_seconds - $time_in_seconds) / 3600;
                
                // Update status based on hours worked
                $new_status = $current_status;
                if ($current_status == "Present" || $current_status == "Late") {
                    if ($hours_worked < $required_work_hours) {
                        $new_status = $current_status . " - Early Out";
                    } else {
                        $new_status = $current_status . " - Complete";
                    }
                }
                
                // Update the record with timeout and new status
                $update_sql = "UPDATE attendance 
                             SET TIMEOUT='$current_time', 
                                 STATUS='$new_status' 
                             WHERE EMPLOYEEID='$employeeid' 
                             AND LOGDATE='$current_date'";
                             
                if ($conn->query($update_sql) === TRUE) {
                    $_SESSION['success'] = "Time out recorded successfully!";
                    $_SESSION['scan_success'] = true;
                    $_SESSION['scan_type'] = "Time Out";
                    $_SESSION['scan_time'] = $current_time;
                    $_SESSION['scan_date'] = $current_date;
                    $_SESSION['status'] = $new_status;
                } else {
                    $_SESSION['error'] = "Error updating record: " . $conn->error;
                }
            } else {
                $_SESSION['error'] = "You have already timed out for today.";
            }
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
