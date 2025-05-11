<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Authentication check
if (!isset($_SESSION['admin_id'])) {
    header('Location: homepage.php');
    exit();
}

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Check if we need to reset the daily view
if (!isset($_SESSION['last_attendance_date']) || $_SESSION['last_attendance_date'] != date('Y-m-d')) {
    // It's a new day, update the session variable
    $_SESSION['last_attendance_date'] = date('Y-m-d');
    
    // Clear any cached attendance data if exists
    if (isset($_SESSION['attendance_cache'])) {
        unset($_SESSION['attendance_cache']);
    }
}

$server = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Auto-timeout logic moved to the end of file

// Get the admin ID from the session to track who makes changes
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0;
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Unknown';

// Retrieve admin name from database if not in session
if ($admin_name == 'Unknown' && $admin_id > 0) {
    $admin_query = "SELECT full_name FROM admin WHERE id = ?";
    $admin_stmt = $conn->prepare($admin_query);
    $admin_stmt->bind_param("i", $admin_id);
    $admin_stmt->execute();
    $admin_result = $admin_stmt->get_result();
    if ($admin_result && $admin_result->num_rows > 0) {
        $admin_row = $admin_result->fetch_assoc();
        $admin_name = $admin_row['full_name'];
        // Store in session for future use
        $_SESSION['admin_name'] = $admin_name;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeID = $_POST['employeeID'];
    $date = date('Y-m-d'); // Always use current date
    $timeType = $_POST['timeType']; // Time in or time out
    $reasonType = $_POST['reasonType']; // Reason type
    $eventDetails = isset($_POST['eventDetails']) ? $_POST['eventDetails'] : ''; // Event details
    $otherReason = isset($_POST['otherReason']) ? $_POST['otherReason'] : ''; // Other reason
    
    // Set time value based on selected option
    $timeValue = '';
    if (strpos($timeType, 'timeIn') === 0) {
        $timeValue = substr($timeType, 6); // Extract time from the option value
        $timeValue = match($timeValue) {
            '8' => '08:00:00',
            '830' => '08:30:00',
            '9' => '09:00:00',
            '930' => '09:30:00',
            '10' => '10:00:00',
            '1030' => '10:30:00',
            '11' => '11:00:00',
            '1130' => '11:30:00',
            '12' => '12:00:00',
            '1230' => '12:30:00',
            '1' => '13:00:00',
            '130' => '13:30:00',
            default => '08:00:00'
        };
        $timeType = 'timeIn'; // Standardize time type
    } else if (strpos($timeType, 'timeOut') === 0) {
        $timeValue = substr($timeType, 7); // Extract time from the option value
        
        // Map to 24-hour format first
        $timeValue = match($timeValue) {
            '5' => '17:00:00',
            '530' => '17:30:00',
            '6' => '18:00:00',
            '630' => '18:30:00',
            '7' => '19:00:00',
            '730' => '19:30:00',
            '8' => '20:00:00',
            '830' => '20:30:00',
            default => '17:00:00'
        };
        
        // Then convert to 12-hour format for storage
        $displayTime = date('h:i:s A', strtotime($timeValue));
        
        // Log the exact timeValue for debugging
        error_log("TIMEOUT VALUE - Selected: " . $timeType . ", Converted to 24h: " . $timeValue . ", For display: " . $displayTime);
        
        // Store 12-hour format in timeValue for database insertion
        $timeValue = $displayTime;
        
        $timeType = 'timeOut'; // Standardize time type
    }
    
    // Set status based on time type
    $status = ($timeType == 'timeIn') ? 'Present' : 'Present';
    
    // If this is a time out, calculate appropriate status (Early Out vs Complete)
    if ($timeType == 'timeOut') {
        // We'll update this status later if a time in record exists
        $status = 'Present';
    }
    
    // Determine which reason details to use
    $reasonDetails = '';
    if ($reasonType == 'event') {
        $reasonDetails = $eventDetails;
    } else if ($reasonType == 'other') {
        $reasonDetails = $otherReason;
    }

    // Check if attendance already exists for this employee on this date
    $check_sql = "SELECT * FROM attendance WHERE EMPLOYEEID = ? AND LOGDATE = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("is", $employeeID, $date);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Get existing record data
        $row = $result->fetch_assoc();
        
        // Update existing record
        if ($timeType == 'timeIn') {
            // Prevent multiple time-in attempts
            if (!empty($row['TIMEIN'])) {
                $error = "Time In for this employee already exists for today. Only one Time In is allowed per day.";
                goto skipUpdate;
            }
            
            // Save the existing timeout to ensure it's preserved
            $existingTimeout = $row['TIMEOUT'];
            
            // Only update TIMEIN and related fields, preserve TIMEOUT
            $sql = "UPDATE attendance SET TIMEIN = ?, STATUS = ?, REASON_TYPE = ?, REASON_DETAILS = ?, 
                   MODIFIED_BY = CONCAT(IFNULL(MODIFIED_BY, ''), 'Time-In by ', ?, ' at ', NOW(), '; ')
                   WHERE EMPLOYEEID = ? AND LOGDATE = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $timeValue, $status, $reasonType, $reasonDetails, $admin_name, $employeeID, $date);
            
            // Add debug logging for time-in updates
            error_log("TIME-IN UPDATE - Employee: $employeeID, New Time: $timeValue, Preserving timeout: " . ($existingTimeout ?: 'NULL'));
            
            if ($stmt->execute()) {
                $success = "Time In updated successfully!";
                
                // Verify the TIMEOUT value hasn't changed
                $verify_sql = "SELECT TIMEOUT FROM attendance WHERE EMPLOYEEID = ? AND LOGDATE = ?";
                $verify_stmt = $conn->prepare($verify_sql);
                $verify_stmt->bind_param("is", $employeeID, $date);
                $verify_stmt->execute();
                $verify_result = $verify_stmt->get_result();
                if ($verify_result && $verify_result->num_rows > 0) {
                    $verify_row = $verify_result->fetch_assoc();
                    $newTimeout = $verify_row['TIMEOUT'];
                    
                    // If timeout changed unexpectedly, restore it
                    if ($existingTimeout != $newTimeout) {
                        error_log("TIMEOUT CHANGED: Was: " . ($existingTimeout ?: 'NULL') . ", Now: " . ($newTimeout ?: 'NULL') . " - Restoring");
                        
                        // Restore original timeout
                        $restore_sql = "UPDATE attendance SET TIMEOUT = ? WHERE EMPLOYEEID = ? AND LOGDATE = ?";
                        $restore_stmt = $conn->prepare($restore_sql);
                        $restore_stmt->bind_param("sis", $existingTimeout, $employeeID, $date);
                        $restore_stmt->execute();
                    }
                }
                
                // Log successful update for debugging
                $log_message = "UPDATE SUCCESS - Type: " . $timeType . ", Time Value: " . $timeValue . ", Employee ID: " . $employeeID;
                error_log($log_message);
        } else {
                $error = "Error: " . $stmt->error;
                // Log error for debugging
                error_log("UPDATE ERROR - " . $stmt->error . " - Type: " . $timeType . ", Time Value: " . $timeValue . ", Employee ID: " . $employeeID);
            }
            
            // Skip the standard execution flow to avoid potential issues
            goto skipUpdate;
        } else {
            // Prevent multiple time-out attempts
            if (!empty($row['TIMEOUT'])) {
                // Check if this was an auto-timeout (which can be overridden)
                if (strpos($row['STATUS'], 'Auto TimeOut') !== false) {
                    // Allow manual timeout to override auto-timeout
                    error_log("Allowing manual timeout to override auto-timeout for employee $employeeID");
                    // Set a success message about overriding auto-timeout
                    $override_message = true;
                } else {
                    // Regular timeout exists, prevent override
                    $error = "Time Out for this employee already exists for today. Only one Time Out is allowed per day.";
                    goto skipUpdate;
                }
            }
            
            // If TIMEIN is NULL, show error
            if (empty($row['TIMEIN'])) {
                $error = "Cannot record Time Out without Time In first.";
                goto skipUpdate; // Skip the update process
            }
            
            // Add debug logging for the current record
            error_log("TIMEOUT DEBUG - Current record: TIMEIN=" . $row['TIMEIN'] . ", TIMEOUT=" . $row['TIMEOUT'] . ", ID=" . $employeeID);
            
           
            $timeout_values = [
                'timeOut5' => '05:00:00 PM',
                'timeOut530' => '05:30:00 PM',
                'timeOut6' => '06:00:00 PM',
                'timeOut630' => '06:30:00 PM',
                'timeOut7' => '07:00:00 PM',
                'timeOut730' => '07:30:00 PM',
                'timeOut8' => '08:00:00 PM',
                'timeOut830' => '08:30:00 PM',
            ];
            
            // Get the appropriate display time from our hardcoded array
            $display_timeout = isset($timeout_values[$timeType]) ? $timeout_values[$timeType] : '05:00:00 PM';
            
            // Log the value we're going to use
            error_log("Using exact hardcoded timeout value: " . $display_timeout);
            
            // Try the update with direct mysqli methods
            $update_stmt = $conn->prepare("UPDATE attendance SET TIMEOUT = ?, STATUS = REPLACE(STATUS, ' - Auto TimeOut', ''),
                                          MODIFIED_BY = CONCAT(IFNULL(MODIFIED_BY, ''), 'Time-Out by ', ?, ' at ', NOW(), '; ')
                                          WHERE EMPLOYEEID = ? AND LOGDATE = ?");
            $update_stmt->bind_param("ssis", $display_timeout, $admin_name, $employeeID, $date);
            
            if ($update_stmt->execute()) {
                error_log("SUCCESS: Timeout updated for employee $employeeID on $date");
                
                if (isset($override_message)) {
                    $success = "Auto-timed out entry has been overridden with your manual Time Out.";
                } else {
                $success = "Time Out recorded successfully!";
                }
                
                // Verify the update
                $check_sql = "SELECT TIMEOUT, STATUS FROM attendance WHERE EMPLOYEEID = ? AND LOGDATE = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("is", $employeeID, $date);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result && $check_result->num_rows > 0) {
                    $check_row = $check_result->fetch_assoc();
                    error_log("VERIFICATION - TIMEOUT now = " . ($check_row['TIMEOUT'] ?? 'NULL') . ", STATUS = " . $check_row['STATUS']);
                }
            } else {
                error_log("ERROR: Failed to update timeout: " . $update_stmt->error);
                $error = "Failed to record Time Out: " . $update_stmt->error;
            }
            
            goto skipUpdate; // Skip the normal processing
        }
    } else {
        // Insert new attendance record with timeIn or timeOut based on selection
        if ($timeType == 'timeIn') {
            // We're creating a new record, so time-in is implicitly empty
            $sql = "INSERT INTO attendance (EMPLOYEEID, `Last Name`, `First Name`, `Middle Name`, TIMEIN, LOGDATE, STATUS, Department, 
                   REASON_TYPE, REASON_DETAILS, MODIFIED_BY) 
                   SELECT EmployeeID, `Last Name`, `First Name`, `Middle Name`, ?, ?, ?, Department, ?, ?, 
                   CONCAT('Created by ', ?, ' at ', NOW())
                    FROM employee 
                    WHERE EmployeeID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $timeValue, $date, $status, $reasonType, $reasonDetails, $admin_name, $employeeID);
        } else {
            // Show error for time out without time in
            $error = "Cannot record Time Out without Time In first.";
            goto skipInsert; // Skip the insert process
        }
        
        if ($stmt->execute()) {
            $success = "Attendance record added successfully!";
            // Log successful insert for debugging
            $log_message = "INSERT SUCCESS - Type: " . $timeType . ", Time Value: " . $timeValue . ", Employee ID: " . $employeeID;
            error_log($log_message);
        } else {
            $error = "Error: " . $stmt->error;
            // Log error for debugging
            error_log("INSERT ERROR - " . $stmt->error . " - Type: " . $timeType . ", Time Value: " . $timeValue . ", Employee ID: " . $employeeID);
        }
    }
    
    skipUpdate: // Label for skipping update/insert
    skipInsert: // Label for skipping insert process
}

// Now run the auto-timeout logic at the end of the script, but only if it's genuinely after hours
// and not during form processing
$currentTime = date('H:i:s');
$currentDate = date('Y-m-d');
$defaultTimeOut = '17:00:00'; 

// Only run auto-timeout if explicitly requested via a special parameter or admin action
// NOT on every page load or refresh
$runAutoTimeout = false;

// Check if admin manually requested auto-timeout
if (isset($_GET['triggerAutoTimeout']) && $_GET['triggerAutoTimeout'] == 'true') {
    $runAutoTimeout = true;
    // Store in session that we've run it so we don't run it again
    $_SESSION['auto_timeout_run_' . $currentDate] = true;
}

// Check if it's after 5PM and we haven't run auto-timeout for today yet
$currentHour = (int)date('H');
if ($currentHour >= 17 && !isset($_SESSION['auto_timeout_run_' . $currentDate])) {
    // For a properly timed auto-timeout that doesn't run on every refresh,
    // this should be moved to a separate cronjob script
    // But for now, we'll just check if it's the first load after 5PM
    if (!isset($_SESSION['page_loaded_after_5pm'])) {
        $_SESSION['page_loaded_after_5pm'] = true;
        $runAutoTimeout = true;
        // Store in session that we've run it so we don't run it again
        $_SESSION['auto_timeout_run_' . $currentDate] = true;
    }
}

// Actually run the auto-timeout if conditions are met
if ($runAutoTimeout) {
    error_log("Running automatic timeout update for date: " . $currentDate . " at hour " . $currentHour);
    
    $auto_timeout_sql = "UPDATE attendance SET 
                         TIMEOUT = ?, 
                         STATUS = CASE 
                            WHEN STATUS = 'Present' THEN 'Present - Auto TimeOut' 
                            WHEN STATUS = 'Late' THEN 'Late - Auto TimeOut'
                            ELSE STATUS 
                         END 
                         WHERE LOGDATE = ? 
                         AND (TIMEOUT IS NULL OR TIMEOUT = '') 
                         AND TIMEIN IS NOT NULL";
    $auto_stmt = $conn->prepare($auto_timeout_sql);
    $auto_stmt->bind_param("ss", $defaultTimeOut, $currentDate);
    
    if ($auto_stmt->execute()) {
        error_log("Auto timeout update successful: " . $auto_stmt->affected_rows . " records updated");
        if (isset($_GET['triggerAutoTimeout'])) {
            $success = "Auto-timeout successfully applied to " . $auto_stmt->affected_rows . " employee(s) without time-out records.";
        }
    } else {
        error_log("Auto timeout update error: " . $auto_stmt->error);
        if (isset($_GET['triggerAutoTimeout'])) {
            $error = "Error applying auto-timeout: " . $auto_stmt->error;
        }
    }
}

// Get list of employees for dropdown
$emp_sql = "SELECT EmployeeID, `First Name`, `Last Name`, Department FROM employee ORDER BY `Last Name`, `First Name`";
$emp_result = $conn->query($emp_sql);

// Get today's attendance summary
$today = date('Y-m-d');
$summary_sql = "SELECT a.EMPLOYEEID, a.TIMEIN, a.TIMEOUT, a.STATUS, a.REASON_TYPE, 
                       a.REASON_DETAILS, e.`First Name`, e.`Last Name`, e.Department, a.MODIFIED_BY
                FROM attendance a 
                JOIN employee e ON a.EMPLOYEEID = e.EmployeeID 
                WHERE a.LOGDATE = ?";
$summary_stmt = $conn->prepare($summary_sql);

// Add error checking before bind_param
if ($summary_stmt === false) {
    // Log error for debugging
    error_log("Prepare statement failed: " . $conn->error);
    $summary_result = false;
} else {
    $summary_stmt->bind_param("s", $today);
    $summary_stmt->execute();
    $summary_result = $summary_stmt->get_result();
}

// Display time formats
$displayTimeIn = '8:00 AM';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Attendance Entry</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="QRCodeAttendance/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="iframe-style.css">
    <style>
        :root {
            --primary: #4F6F52;
            --primary-dark: #3A4D39;
            --primary-light: #739072;
            --background: #E9F1E5;
            --light-mint: #ECE3CE;
            --text-light: #ffffff;
            --text-dark: #333333;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            min-height: 100vh;
            height: auto;
            font-size: 1.5rem;
            line-height: 1.7;
        }
        
        /* Header removed as requested */
        
        /* Title bar */
        .page-title-bar {
            background-color: var(--primary-dark);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .page-title-bar i {
            font-size: 30px;
            margin-right: 15px;
        }
        
        .page-title-bar h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        
        /* Date display */
        .date-display {
            background-color: #d4e3d2;
            border-radius: 10px;
            padding: 12px 15px;
            margin: 0 15px 15px 15px;
            text-align: center;
            color: var(--primary-dark);
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .date-display i {
            margin-right: 10px;
            color: var(--primary);
            font-size: 1.8rem;
        }
        
        /* Form styling */
        .form-section {
            padding: 10px 20px;
        }
        
        .form-section .form-group {
            margin-bottom: 18px;
        }
        
        .form-section label {
            display: flex;
            align-items: center;
            color: var(--primary-dark);
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 1.7rem;
        }
        
        .form-section label i {
            color: var(--primary);
            margin-right: 10px;
            font-size: 1.7rem;
        }
        
        .form-control, .select2-container--default .select2-selection--single {
            border-radius: 8px;
            border: 1px solid #c0d4c4;
            height: calc(1.7em + 1.3rem + 2px);
            padding: 0.7rem 1rem;
            font-size: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        textarea.form-control {
            height: auto;
            min-height: 100px;
        }
        
        .select2-container--default .select2-selection--single {
            height: calc(1.7em + 0.7rem + 2px);
            padding: 0.35rem 0.7rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.7;
            padding-left: 0;
            font-size: 1.5rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(1.7em + 0.7rem);
        }
        
        /* Button styling */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 12px 24px;
            margin-bottom: 20px;
            font-size: 1.6rem;
            font-weight: 500;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* Table section */
        .table-section {
            margin-top: 15px;
            padding: 0 15px 20px 15px;
        }
        
        .table-section h4 {
            color: var(--primary-dark);
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            font-size: 2.0rem;
        }
        
        .table-section h4 i {
            margin-right: 10px;
            color: var(--primary);
            font-size: 2.0rem;
        }
        
        .table-responsive {
            background-color: white;
            border-radius: 10px;
            overflow-x: auto;
            width: 100%;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .table {
            width: 100%;
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--primary);
            color: white;
            border: none;
            white-space: nowrap;
            padding: 16px 20px;
            font-size: 1.7rem;
            font-weight: 500;
        }
        
        .table tbody td {
            padding: 14px 20px;
            font-size: 1.6rem;
            vertical-align: middle;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(79, 111, 82, 0.05);
        }
        
        /* Status badges */
        .status-badge {
            padding: 8px 14px;
            border-radius: 25px;
            font-size: 1.4rem;
            font-weight: 500;
            text-align: center;
            display: inline-block;
            white-space: nowrap;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .status-present {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-late {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-early-out {
            background-color: #ffe8d9;
            color: #ff5722;
        }
        
        .status-absent {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-no-timeout {
            background-color: #e7f3ff;
            color: #004085;
        }
        
        .status-auto-timeout {
            background-color: #d8e5f3;
            color: #0056b3;
        }
        
        /* Refresh button */
        .refresh-btn {
            background-color: var(--primary-light);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            margin-left: 12px;
            cursor: pointer;
            font-size: 1.4rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        
        .refresh-btn:hover {
            background-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        }
        
        /* Alerts */
        .alert {
            margin: 0 15px 15px 15px;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .alert-info {
            background-color: #e7f3ff;
            border-color: #b8daff;
            color: #004085;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        /* Form container */
        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .table-responsive {
                max-width: 100%;
                overflow-x: auto;
            }
            
            .form-section, .table-section {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .page-title-bar {
                padding: 10px 15px;
            }
            
            .form-section .form-group {
                margin-bottom: 12px;
            }
            
            body {
                font-size: 1.4rem;
            }
            
            .table thead th, .table tbody td {
                padding: 12px 15px;
            }
        }
        
        /* Select2 dropdown enhancements */
        .select2-container--default .select2-results__option {
            padding: 10px 12px;
            font-size: 1.5rem;
        }
        
        .select2-dropdown {
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }
        
        .select2-search__field {
            padding: 8px 10px !important;
            font-size: 1.5rem !important;
            height: auto !important;
        }
        
        /* Additional styles for admin badge */
        .admin-badge {
            background-color: #17a2b8;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 1.3rem;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="page-title-bar">
        <i class="fas fa-user-clock"></i>
        <h1>Manual Attendance</h1>
    </div>

    <div class="date-display">
        <i class="fas fa-calendar-day"></i> Today's Date: <?php echo date('F d, Y'); ?> (Manila, Philippines)
    </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
        <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
    <div class="container-fluid">
        <div class="row">
            <!-- Left column for form -->
            <div class="col-md-4 form-column">
                <div class="form-container">
                    <div class="form-section">
                        <form method="POST">
                            <div class="alert alert-info small">
                                <i class="fas fa-info-circle"></i> <strong>Note:</strong> Time In and Time Out can only be recorded once per employee per day. <br>
                                <span class="text-muted">(Exception: Automatic timeout entries at 5:00 PM can be manually overridden)</span>
            </div>

                <div class="form-group">
                                <label><i class="fas fa-id-card"></i> Select Employee</label>
                    <select name="employeeID" id="employeeID" required class="form-control select2-search">
                        <option value="">Search for an employee...</option>
                        <?php while ($emp = $emp_result->fetch_assoc()): ?>
                            <option value="<?php echo $emp['EmployeeID']; ?>">
                                <?php echo $emp['Last Name'] . ', ' . $emp['First Name'] . ' (' . $emp['Department'] . ')'; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                                <label><i class="fas fa-clock"></i> Time Type</label>
                        <select name="timeType" id="timeType" required class="form-control">
                            <option value="timeIn8">Time In (8:00 AM)</option>
                            <option value="timeIn830">Time In (8:30 AM)</option>
                            <option value="timeIn9">Time In (9:00 AM)</option>
                            <option value="timeIn930">Time In (9:30 AM)</option>
                            <option value="timeIn10">Time In (10:00 AM)</option>
                            <option value="timeIn1030">Time In (10:30 AM)</option>
                            <option value="timeIn11">Time In (11:00 AM)</option>
                            <option value="timeIn1130">Time In (11:30 AM)</option>
                            <option value="timeIn12">Time In (12:00 PM)</option>
                            <option value="timeIn1230">Time In (12:30 PM)</option>
                            <option value="timeIn1">Time In (1:00 PM)</option>
                            <option value="timeIn130">Time In (1:30 PM)</option>
                            <option value="timeOut5">Time Out (5:00 PM)</option>
                            <option value="timeOut530">Time Out (5:30 PM)</option>
                            <option value="timeOut6">Time Out (6:00 PM)</option>
                            <option value="timeOut630">Time Out (6:30 PM)</option>
                            <option value="timeOut7">Time Out (7:00 PM)</option>
                            <option value="timeOut730">Time Out (7:30 PM)</option>
                            <option value="timeOut8">Time Out (8:00 PM)</option>
                            <option value="timeOut830">Time Out (8:30 PM)</option>
                        </select>
                </div>

                <div class="form-group">
                                <label><i class="fas fa-question-circle"></i> Reason</label>
                    <select name="reasonType" id="reasonType" required class="form-control">
                        <option value="none">No specific reason</option>
                        <option value="event">Event</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                            <div id="eventField" class="form-group" style="display: none;">
                                <label><i class="fas fa-calendar-day"></i> Event Details</label>
                    <input type="text" name="eventDetails" id="eventDetails" class="form-control" placeholder="Specify event details...">
                </div>

                            <div id="otherField" class="form-group" style="display: none;">
                                <label><i class="fas fa-comment"></i> Other Reason</label>
                    <textarea name="otherReason" id="otherReason" class="form-control" rows="3" placeholder="Specify other reason..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Attendance
                </button>
            </form>
                    </div>
                </div>
            </div>
            
            <!-- Right column for table -->
            <div class="col-md-8 table-column">
                <div class="table-section">
                <h4>
                    <i class="fas fa-list-alt"></i> Today's Attendance Summary
                        <?php if ((int)date('H') >= 17): // Only show after 5PM ?>
                        <a href="?triggerAutoTimeout=true" class="refresh-btn" style="background-color: #ffc107; color: #212529;" title="Apply 5PM auto-timeout to all employees without timeout">
                            <i class="fas fa-clock"></i> Apply Auto-Timeout
                        </a>
                        <?php endif; ?>
                </h4>
                <div class="table-responsive">
                    <table class="table table-striped" id="attendanceTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                                <th>Reason</th>
                                    <th>Modified By</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <?php if ($summary_result && $summary_result->num_rows > 0): ?>
                                <?php while ($row = $summary_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['Last Name'] . ', ' . $row['First Name']; ?></td>
                                        <td><?php echo $row['Department']; ?></td>
                                        <td><?php echo $row['TIMEIN'] ? date('h:i A', strtotime($row['TIMEIN'])) : '-'; ?></td>
                                        <td><?php 
                                            if ($row['TIMEOUT']) {
                                                // Ensure proper time display regardless of format
                                                try {
                                                    echo date('h:i A', strtotime($row['TIMEOUT']));
                                                    error_log("Timeout displayed for " . $row['EMPLOYEEID'] . ": " . $row['TIMEOUT']);
                                                } catch(Exception $e) {
                                                    echo $row['TIMEOUT']; // Fallback to original format
                                                    error_log("Error converting timeout: " . $e->getMessage());
                                                }
                                            } else {
                                                echo '-';
                                            }
                                        ?></td>
                                        <td>
                                            <?php 
                                            $statusClass = 'status-present';
                                            if (strpos($row['STATUS'], 'Late') !== false) {
                                                $statusClass = 'status-late';
                                            } else if (strpos($row['STATUS'], 'Early Out') !== false) {
                                                $statusClass = 'status-early-out';
                                            } else if (strpos($row['STATUS'], 'Absent') !== false) {
                                                $statusClass = 'status-absent';
                                            } else if (strpos($row['STATUS'], 'No TimeOut') !== false) {
                                                $statusClass = 'status-no-timeout';
                                            } else if (strpos($row['STATUS'], 'Auto TimeOut') !== false) {
                                                $statusClass = 'status-auto-timeout';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo $row['STATUS']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($row['REASON_TYPE'] && $row['REASON_TYPE'] != 'none') {
                                                echo ucfirst($row['REASON_TYPE']) . ': ' . $row['REASON_DETAILS'];
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                            <td>
                                                <?php if (!empty($row['MODIFIED_BY'])): ?>
                                                    <?php 
                                                        // Extract admin name if possible
                                                        if (preg_match('/by (.*?) at/', $row['MODIFIED_BY'], $matches)) {
                                                            $adminName = $matches[1];
                                                        } else {
                                                            $adminName = 'Unknown';
                                                        }
                                                    ?>
                                                    <span class="admin-badge"><?php echo htmlspecialchars($adminName); ?></span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No attendance records for today.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="QRCodeAttendance/plugins/jquery/jquery.min.js"></script>
    <script src="QRCodeAttendance/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for employee search
            $('.select2-search').select2({
                placeholder: 'Search for an employee...',
                allowClear: true
            });
            
            // Show/hide reason fields based on selection
            $('#reasonType').change(function() {
                var selected = $(this).val();
                
                // Hide all conditional fields first
                $('#eventField, #otherField').hide();
                
                // Show the appropriate field based on selection
                if (selected === 'event') {
                    $('#eventField').show();
                } else if (selected === 'other') {
                    $('#otherField').show();
                }
            });
            
            // Trigger change on page load to handle initial state
            $('#reasonType').trigger('change');
            
            // Function to adjust iframe height if this page is loaded in an iframe
            function adjustToParentFrame() {
                if (window.parent && window.parent !== window) {
                    // If in iframe, send height to parent
                    var height = document.body.scrollHeight;
                    window.parent.postMessage({ type: 'resize', height: height }, '*');
                }
            }
            
            // Adjust on load, after table refresh, and when window resizes
            $(window).on('load resize', adjustToParentFrame);
            
            // Additional resize after all content is loaded
            setTimeout(adjustToParentFrame, 500);
            
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Tooltip for view history buttons
            $(document).on('mouseenter', '.view-history', function() {
                $(this).tooltip('show');
            });
        });
    </script>
    
    <!-- Include specialized iframe sizing script -->
    <script src="iframe-exact.js"></script>
</body>
</html>