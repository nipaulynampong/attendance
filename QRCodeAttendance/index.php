<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>QR Code Attendance</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script type="text/javascript" src="js/instascan.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #4F6F52;
            --secondary-color: #739072;
            --light-color: #E8F5E9;
            --dark-color: #3A4D3B;
            --background-color: #F5F5F5;
            --card-color: #FFFFFF;
            --text-color: #333333;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar styles - reduced height and padding */
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2px 0 !important; /* Further reduced from 4px to 2px */
            text-align: center !important;
            min-height: 40px !important; /* Set a smaller min-height */
        }
        
        .navbar .text-center {
            text-align: center !important;
            width: 100% !important;
            line-height: 1 !important; /* Reduce line height */
        }
        
        .navbar .navbar-brand {
            color: white !important;
            font-weight: 600 !important;
            font-size: 22px !important; /* Further reduced from 24px to 22px */
            display: inline-block !important;
            float: none !important;
            margin: 0 auto !important;
            padding: 6px 15px !important; /* Custom padding */
            height: auto !important; /* Let height be determined by content */
        }
        
        .navbar .navbar-brand i {
            font-size: 26px !important;
            margin-right: 8px !important;
            vertical-align: middle !important;
        }
        
        .navbar-header {
            float: none;
            display: block;
            text-align: center;
            width: 100%;
        }
        
        .navbar .container-fluid {
            display: block;
            text-align: center;
        }
        
        .card {
            background-color: var(--card-color);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 10px;
            overflow: hidden;
            border: none;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 15px; /* Reduced from 12px 15px to 8px 15px */
            font-weight: 500;
            border-bottom: none;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            text-align: center;
        }
        
        .card-body {
            padding: 12px; /* Reduced from 15px to 12px */
            text-align: center;
        }
        
        #preview {
            border-radius: 8px;
            width: 100%;
            border: 1px solid #eee;
            min-height: 320px; /* Smaller to match table height */
            max-height: 330px;
            object-fit: cover;
            margin: 0 auto;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 8px 10px;
            border: 1px solid #ddd;
            margin-top: 8px;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(79, 111, 82, 0.25);
        }
        
        .alert {
            border-radius: 8px;
            padding: 8px; /* Reduced from 12px */
            margin-bottom: 10px; /* Reduced from 15px */
            border: none;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .close {
            color: inherit;
            opacity: 0.8;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        table.dataTable {
            border-collapse: collapse !important;
            width: 100% !important;
            margin: 0 auto !important;
            table-layout: fixed;
        }
        
        /* Define column widths for better alignment */
        table.dataTable th:nth-child(1), /* Name */
        table.dataTable td:nth-child(1) {
            width: 16%;
        }
        
        table.dataTable th:nth-child(2), /* ID */
        table.dataTable td:nth-child(2) {
            width: 10%;
        }
        
        table.dataTable th:nth-child(3), /* Department */
        table.dataTable td:nth-child(3) {
            width: 10%;
        }
        
        table.dataTable th:nth-child(4), /* Time In */
        table.dataTable td:nth-child(4),
        table.dataTable th:nth-child(5), /* Time Out */
        table.dataTable td:nth-child(5) {
            width: 12%;
        }
        
        table.dataTable th:nth-child(6), /* Date */
        table.dataTable td:nth-child(6) {
            width: 12%;
        }
        
        table.dataTable th:nth-child(7), /* Status */
        table.dataTable td:nth-child(7) {
            width: 28%; /* Increased from 18% to 28% */
        }
        
        table.dataTable th {
            background-color: #4F6F52 !important;
            color: white !important;
            font-weight: 500;
            padding: 12px !important;
            border: none !important;
            text-align: center;
        }
        
        table.dataTable td {
            padding: 8px !important;
            border: none;
            border-bottom: 1px solid #f2f2f2;
            vertical-align: middle;
            text-align: center;
        }
        
        table.dataTable tr:hover {
            background-color: rgba(79, 111, 82, 0.05);
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 13px;
            display: inline-block;
            margin: 2px 0;
            min-width: 90px;
        }
        
        .badge-success {
            background-color: #A6E3A1; /* Pastel green */
            color: #2D5E2D; /* Darker green for text */
        }
        
        .badge-warning {
            background-color: #FFECD6; /* Pastel yellow */
            color: #996600; /* Darker yellow for text */
        }
        
        .badge-info {
            background-color: #ADE8F4; /* Pastel blue */
            color: #1A5F7A; /* Darker blue for text */
        }
        
        .badge-danger {
            background-color: #FFB5B5; /* Pastel red */
            color: #9A0000; /* Darker red for text */
        }
        
        .badge-holiday {
            background-color: #E9D8FD; /* Pastel purple */
            color: #6B46C1; /* Darker purple for text */
        }

        .badge-Event {
            background-color: #E9D8FD;
            color: #6B46C1;
        }

        .event-info {
            margin-top: 4px;
            font-size: 12px;
            color: #6B46C1;
            background-color: rgba(233, 216, 253, 0.2);
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .dataTables_wrapper .dataTables_length, 
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: none;
            margin: 0;
            padding: 0;
        }
        
        .dataTables_wrapper .row:last-child {
            display: none;
        }
        
        .container {
            padding: 5px 20px 20px;
            max-width: 1500px; /* Increased from 1300px to 1500px */
            margin: -5px auto 0; 
            flex: 1;
        }
        
        .row {
            margin-top: 0;
            display: flex;
            flex-wrap: nowrap; /* Prevent wrapping to new line */
        }
        
        /* Column sizing */
        .col-md-4 {
            width: 31%; /* Scanner column */
            padding-right: 10px;
        }
        
        .col-md-8 {
            width: 69%; /* Table column */
            padding-left: 10px;
        }
        
        .row-centered {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        
        .current-time {
            font-size: 13px;
            background-color: rgba(79, 111, 82, 0.1);
            padding: 6px 10px;
            border-radius: 8px;
            margin-top: 8px;
            display: inline-block;
            text-align: center;
        }
        
        .status-cell {
            text-align: center;
            padding: 4px;
            width: 100%;
        }

        /* Add the modal styles */
        .employee-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .employee-modal.show {
            display: flex;
            animation: fadeIn 0.3s;
        }

        .employee-card {
            width: 450px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transform: translateY(0);
            animation: slideIn 0.3s;
            margin: 0 auto;
        }

        .employee-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .employee-header h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 500;
        }

        .employee-content {
            padding: 30px;
            text-align: center;
        }

        .employee-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid var(--primary-color);
            object-fit: cover;
            margin: 0 auto 20px;
            background-color: #f2f2f2;
        }

        .employee-name {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark-color);
        }

        .employee-id {
            font-size: 18px;
            color: #666;
            margin-bottom: 12px;
        }

        .employee-dept {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .scan-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: left;
        }

        .scan-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .scan-info strong {
            color: var(--dark-color);
        }

        .scan-status {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            font-weight: 600;
            text-align: center;
            font-size: 18px;
        }

        .status-present {
            background-color: rgba(166, 227, 161, 0.3); /* Pastel green */
            color: #68B984;
        }

        .status-late {
            background-color: rgba(255, 236, 174, 0.3); /* Pastel yellow */
            color: #FFC23C;
        }

        .status-rest-day {
            background-color: rgba(173, 216, 230, 0.3); /* Pastel blue */
            color: #5DA3FA;
        }

        .status-early {
            background-color: rgba(255, 181, 181, 0.3); /* Pastel red */
            color: #FF6B6B;
        }
        
        .status-holiday {
            background-color: rgba(233, 216, 253, 0.3); /* Pastel purple */
            color: #9F7AEA;
        }

        .status-no-timeout {
            background-color: rgba(255, 165, 0, 0.3); /* Light orange background */
            color: #FF8C00; /* Dark orange text */
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Add a holiday info display */
        .holiday-info {
            margin-top: 10px;
            font-size: 14px;
            color: #6B46C1;
            background-color: rgba(233, 216, 253, 0.2);
            padding: 8px 12px;
            border-radius: 8px;
            display: none;
        }

        /* Additional space optimizations */
        .row > div {
            padding-top: 0;
            padding-bottom: 0;
        }

        /* Card container modifications */
        .col-md-7 {
            width: 60%; /* Expand the width of the right column for the table */
        }
        
        /* Status cell and badge styling improvements */
        .status-cell {
            text-align: center;
            padding: 4px;
            width: 100%;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 13px;
            display: inline-block;
            margin: 2px 0;
            min-width: 90px;
        }
    </style>
</head>
<body>
<!-- Employee Modal -->
<div id="employeeModal" class="employee-modal">
    <div class="employee-card">
        <div class="employee-header">
            <h3>Employee Scan Info</h3>
        </div>
        <div class="employee-content">
            <img id="employeeImage" class="employee-img" src="" alt="Employee Photo">
            <div class="employee-name" id="employeeName"></div>
            <div class="employee-id" id="employeeId"></div>
            <div class="employee-dept" id="employeeDept"></div>
            
            <div class="scan-details">
                <div class="scan-info">
                    <span>Type:</span>
                    <strong id="scanType"></strong>
                </div>
                <div class="scan-info">
                    <span>Time:</span>
                    <strong id="scanTime"></strong>
                </div>
                <div class="scan-info">
                    <span>Date:</span>
                    <strong id="scanDate"></strong>
                </div>
                <div class="holiday-info" id="holidayInfo">
                    <i class="fas fa-calendar-day"></i> <span id="holidayName"></span>
                </div>
            </div>
            
            <div id="scanStatus" class="scan-status">
                <span id="statusText"></span>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="employee-modal">
    <div class="employee-card">
        <div class="employee-header" style="background-color: #e74c3c;">
            <h3>Error</h3>
        </div>
        <div class="employee-content">
            <div style="font-size: 50px; color: #e74c3c; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div id="errorMessage" style="font-size: 18px; margin-bottom: 20px; color: #333;"></div>
            <button onclick="document.getElementById('errorModal').classList.remove('show')" class="btn btn-danger">OK</button>
        </div>
    </div>
</div>

<nav class="navbar">
    <div class="text-center w-100">
        <span class="navbar-brand">
            <i class="fas fa-qrcode"></i> QR Code Attendance
        </span>
    </div>
</nav>

<div class="container">
    <div class="row">
        <!-- Scanner column -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-camera"></i> Scanner
                </div>
                <div class="card-body">
                    <form action="CheckInOut.php" method="post" class="form-horizontal mb-3">
                        <div class="form-group mb-0">
                            <input type="text" name="EmployeeID" id="text" placeholder="Employee ID will appear here" class="form-control" autofocus disabled>
                        </div>
                    </form>
                    
                    <video id="preview"></video>
                    <div class="mt-3 d-flex justify-content-center align-items-center">
                        <span class="current-time" id="time"></span>
                    </div>
                    
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo "
                            <div class='alert alert-danger alert-dismissible mt-2'>
                              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                              <i class='fas fa-exclamation-circle'></i> " . $_SESSION['error'] . "
                            </div>
                          ";
                        unset($_SESSION['error']);
                    }
                    if (isset($_SESSION['success'])) {
                        echo "
                            <div class='alert alert-success alert-dismissible mt-2'>
                              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                              <i class='fas fa-check-circle'></i> " . $_SESSION['success'] . "
                            </div>
                          ";
                        unset($_SESSION['success']);
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Attendance table column -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list"></i> Latest 10 Attendance Records
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="example1" class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>ID</th>
                                <th>Department</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $server = "localhost";
                            $username="root";
                            $password="";
                            $dbname="hris";

                            $conn = new mysqli($server,$username,$password,$dbname);
                            $current_date = date('Y-m-d'); 
                            if($conn->connect_error){
                                die("Connection failed" .$conn->connect_error);
                            }
                            
                            $sql ="SELECT attendance.*, employee.`Last Name`, employee.`First Name`, department.Department 
                                  FROM attendance 
                                  LEFT JOIN employee ON attendance.EMPLOYEEID=employee.EmployeeID 
                                  LEFT JOIN department ON employee.Department=department.Department
                                  WHERE attendance.LOGDATE = '$current_date'
                                  ORDER BY attendance.ID DESC, attendance.LOGDATE DESC LIMIT 10";
                                  
                            $query = $conn->query($sql);
                            while ($row = $query->fetch_assoc()){
                                $statusClass = "badge-success";
                                if($row['STATUS'] == "Late"){
                                    $statusClass = "badge-warning";
                                } else if($row['STATUS'] == "Early Out"){
                                    $statusClass = "badge-danger";
                                } else if($row['STATUS'] == "Rest Day"){
                                    $statusClass = "badge-info";
                                } else if($row['STATUS'] == "Holiday"){
                                    $statusClass = "badge-holiday";
                                } else if($row['STATUS'] == "Event"){
                                    $statusClass = "badge-Event";
                                } else if($row['STATUS'] == "Present - No TimeOut"){
                                    $statusClass = "badge-no-timeout";
                                } else if($row['STATUS'] == "Late - No TimeOut"){
                                    $statusClass = "badge-late-no-timeout";
                                }
                                
                                // Handle case where employee data is missing
                                $employeeName = 'Unknown';
                                if (!empty($row['Last Name']) || !empty($row['First Name'])) {
                                    $employeeName = $row['Last Name'].', '.$row['First Name'];
                                }
                            ?>
                            <tr>
                                <td><?php echo $employeeName; ?></td>
                                <td><?php echo $row['EMPLOYEEID']; ?></td>
                                <td><?php echo $row['Department'] ? $row['Department'] : 'Not assigned'; ?></td>
                                <td><?php echo $row['TIMEIN']; ?></td>
                                <td><?php echo $row['TIMEOUT']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['LOGDATE'])); ?></td>
                                <td>
                                    <div class="status-cell">
                                        <div style="margin-bottom: 4px;"><span class="badge <?php echo $statusClass; ?>"><?php echo $row['STATUS']; ?></span></div>
                                        <?php if (!empty($row['HolidayInfo'])): ?>
                                            <div style="margin-bottom: 2px;"><span class="badge badge-holiday"><i class="fas fa-calendar-day"></i> <?php echo htmlspecialchars($row['HolidayInfo']); ?></span></div>
                                        <?php endif; ?>
                                        <?php if (!empty($row['EventInfo'])): ?>
                                            <div><span class="badge badge-Event"><i class="fas fa-calendar-check"></i> <?php echo htmlspecialchars($row['EventInfo']); ?></span></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            }
                            $conn->close();
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<script>
    function updateTime() {
        // Set options to format date and time
        const options = {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true, // Use 12-hour format with AM/PM
            timeZone: 'Asia/Manila' // Use Philippine timezone
        };
        
        const now = new Date();
        document.getElementById('time').textContent = now.toLocaleString('en-US', options);
    }
    
    // Update time immediately and then every second
    updateTime();
    setInterval(updateTime, 1000);
    
    // Scanner setup
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview')});
    Instascan.Camera.getCameras().then(function(cameras){
        if(cameras.length > 0){
            scanner.start(cameras[0]);
        } else{
            alert('No cameras found');
        }
    }).catch(function(e) {
        console.error(e);
    });

    scanner.addListener('scan', function(c){
        document.getElementById('text').value = c;
        // Submit form programmatically even though the field is disabled
        const form = document.querySelector('form');
        const input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('name', 'EmployeeID');
        input.setAttribute('value', c);
        form.appendChild(input);
        form.submit();
    });
    
    // DataTables initialization
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "autoWidth": false,
            "ordering": false, // Disable client-side ordering to preserve server-side sort
            "language": {
                "emptyTable": "No attendance records for today",
                "info": "", // Remove "Showing X to Y of Z entries" text
                "infoEmpty": "", // Remove empty info text
                "zeroRecords": "No matching records found"
            },
            "columnDefs": [
                { "className": "status-cell", "targets": 6 }
            ],
            "searching": false, // Remove search functionality
            "lengthChange": false, // Remove entries per page option
            "paging": false, // Remove pagination
            "pageLength": 5, // Limit to 5 rows (though paging is disabled, this still limits initial display)
            "dom": 't' // Only show the table, no other controls
        });
    });

    <?php if (isset($_SESSION['scan_success']) && $_SESSION['scan_success']): ?>
        document.addEventListener('DOMContentLoaded', function() {
           
            const employeeId = "<?php echo $_SESSION['employee_id']; ?>";
            const employeeName = "<?php echo $_SESSION['employee_name']; ?>";
            const department = "<?php echo $_SESSION['department']; ?>";
            const scanTime = "<?php echo $_SESSION['scan_time']; ?>";
            const scanDate = "<?php echo $_SESSION['scan_date']; ?>";
            const scanType = "<?php echo $_SESSION['scan_type']; ?>";
            const status = "<?php echo $_SESSION['status']; ?>";
            const isRestDay = <?php echo $_SESSION['is_rest_day'] ? 'true' : 'false'; ?>;
            const isHoliday = <?php echo isset($_SESSION['is_holiday']) && $_SESSION['is_holiday'] ? 'true' : 'false'; ?>;
            const holidayName = "<?php echo isset($_SESSION['holiday_name']) ? $_SESSION['holiday_name'] : ''; ?>";
            
           
            document.getElementById('employeeName').innerText = employeeName;
            document.getElementById('employeeId').innerText = "ID: " + employeeId;
            document.getElementById('employeeDept').innerText = department;
            document.getElementById('scanType').innerText = scanType;
            document.getElementById('scanTime').innerText = scanTime;
            document.getElementById('scanDate').innerText = scanDate;
            
          
            if (isHoliday) {
                document.getElementById('holidayInfo').style.display = 'block';
                document.getElementById('holidayName').innerText = holidayName;
            } else {
                document.getElementById('holidayInfo').style.display = 'none';
            }
            
          
            const statusElement = document.getElementById('scanStatus');
            const statusTextElement = document.getElementById('statusText');
            
            statusTextElement.innerText = status;
            
            // Handle various status types for proper styling
            if (status === 'Holiday') {
                statusElement.className = 'scan-status status-holiday';
            } else if (isRestDay) {
                statusElement.className = 'scan-status status-rest-day';
                statusTextElement.innerText = 'Rest Day';
            } else if (status === 'Present') {
                statusElement.className = 'scan-status status-present';
            } else if (status === 'Late') {
                statusElement.className = 'scan-status status-late';
            } else if (status === 'Present - Early Out' || status === 'Late - Early Out') {
                statusElement.className = 'scan-status status-early';
            } else if (status === 'Present - Complete' || status === 'Late - Complete') {
                statusElement.className = 'scan-status status-present';
            } else if (status === 'Rest Day') {
                statusElement.className = 'scan-status status-rest-day';
            }
            
            // Try to load employee image (if available)
            const imageElement = document.getElementById('employeeImage');
            imageElement.src = 'get_employee_image.php?id=' + employeeId;
            imageElement.onerror = function() {
                // If image loading fails, replace with our default user icon
                const parentElement = this.parentElement;
                this.style.display = 'none';
                
                // Create a div with the green circle and user icon
                const iconDiv = document.createElement('div');
                iconDiv.style.width = '120px';
                iconDiv.style.height = '120px';
                iconDiv.style.borderRadius = '50%';
                iconDiv.style.backgroundColor = '#4F6F52';
                iconDiv.style.display = 'flex';
                iconDiv.style.alignItems = 'center';
                iconDiv.style.justifyContent = 'center';
                iconDiv.style.margin = '0 auto';
                iconDiv.style.boxShadow = '0 3px 10px rgba(0,0,0,0.2)';
                
                // Create the icon
                const icon = document.createElement('i');
                icon.className = 'fas fa-user';
                icon.style.color = 'white';
                icon.style.fontSize = '50px';
                
                // Append icon to div and div to parent
                iconDiv.appendChild(icon);
                parentElement.insertBefore(iconDiv, this);
            };
            
            // Show the modal
            const modal = document.getElementById('employeeModal');
            modal.classList.add('show');
            
            // Auto-close after 3 seconds
            setTimeout(function() {
                modal.classList.remove('show');
                
                // Clear the session variables
                <?php 
                unset($_SESSION['scan_success']);
                unset($_SESSION['employee_id']);
                unset($_SESSION['employee_name']);
                unset($_SESSION['department']);
                unset($_SESSION['scan_time']);
                unset($_SESSION['scan_date']);
                unset($_SESSION['scan_type']);
                unset($_SESSION['status']);
                unset($_SESSION['is_rest_day']);
                unset($_SESSION['is_holiday']);
                unset($_SESSION['holiday_name']);
                ?>
            }, 5000);
        });
    <?php endif; ?>

    // Check for department not found error and show modal
    <?php if (isset($_SESSION['error_type']) && in_array($_SESSION['error_type'], ['department_not_found', 'employee_not_found', 'already_timed_out'])): ?>
    // Store the error message in a JavaScript variable
    const errorMsg = "<?php echo addslashes($_SESSION['error_message']); ?>";
    
    // Use window.onload to ensure the DOM is fully loaded
    window.onload = function() {
        // Access the modal elements
        const errorModal = document.getElementById('errorModal');
        const errorMessage = document.getElementById('errorMessage');
        const errorHeader = document.querySelector('#errorModal .employee-header h3');
        const errorIcon = document.querySelector('#errorModal .employee-content > div > i');
        
        // Set message and styling based on error type
        errorMessage.innerText = errorMsg;
        
        if ("<?php echo $_SESSION['error_type']; ?>" === "employee_not_found") {
            errorHeader.innerText = "Employee Not Found";
            errorIcon.className = "fas fa-user-slash";
            errorHeader.parentElement.style.backgroundColor = "#e74c3c"; // Red
        } else if ("<?php echo $_SESSION['error_type']; ?>" === "already_timed_out") {
            errorHeader.innerText = "Time Out Notice";
            errorIcon.className = "fas fa-clock";
            errorHeader.parentElement.style.backgroundColor = "#3498db"; // Blue
        } else {
            errorHeader.innerText = "Department Error";
            errorIcon.className = "fas fa-exclamation-circle";
            errorHeader.parentElement.style.backgroundColor = "#e74c3c"; // Red
        }
        
        // Show the modal
        errorModal.classList.add('show');
        
        // Auto-close after 5 seconds
        setTimeout(function() {
            errorModal.classList.remove('show');
        }, 5000);
    };
    
    <?php
    // Clear the session variables
    unset($_SESSION['error_type']);
    unset($_SESSION['error_message']);
    ?>
    <?php endif; ?>
</script>

<!-- Dedicated script for error modal -->
<?php if (isset($_SESSION['error_type']) && in_array($_SESSION['error_type'], ['department_not_found', 'employee_not_found', 'already_timed_out'])): ?>
<script>
    // Execute immediately
    (function() {
        console.log("Showing error modal");
        
        // Set modal elements
        const errorModal = document.getElementById('errorModal');
        const errorMessage = document.getElementById('errorMessage');
        const errorHeader = document.querySelector('#errorModal .employee-header h3');
        const errorIcon = document.querySelector('#errorModal .employee-content > div > i');
        
        // Set message and icon based on error type
        errorMessage.innerText = "<?php echo addslashes($_SESSION['error_message']); ?>";
        
        <?php if ($_SESSION['error_type'] === 'employee_not_found'): ?>
        errorHeader.innerText = "Employee Not Found";
        errorIcon.className = "fas fa-user-slash";
        errorHeader.parentElement.style.backgroundColor = "#e74c3c"; // Red
        <?php elseif ($_SESSION['error_type'] === 'already_timed_out'): ?>
        errorHeader.innerText = "Time Out Notice";
        errorIcon.className = "fas fa-clock";
        errorHeader.parentElement.style.backgroundColor = "#3498db"; // Blue
        <?php else: ?>
        errorHeader.innerText = "Department Error";
        errorIcon.className = "fas fa-exclamation-circle";
        errorHeader.parentElement.style.backgroundColor = "#e74c3c"; // Red
        <?php endif; ?>
        
        // Show the modal
        errorModal.classList.add('show');
        
        // Auto-close after 5 seconds
        setTimeout(function() {
            errorModal.classList.remove('show');
        }, 5000);
    })();
</script>
<?php 
// Clear session variables
unset($_SESSION['error_type']);
unset($_SESSION['error_message']);
endif; 
?>
</body>
</html>
