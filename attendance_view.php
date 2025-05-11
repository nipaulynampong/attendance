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
    <!-- DateRangePicker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
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
        
        .filter-section {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
            align-items: center;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #4F6F52;
        }
        
        .filter-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .filter-control:focus {
            border-color: #4F6F52;
            outline: none;
            box-shadow: 0 0 0 2px rgba(79, 111, 82, 0.2);
        }
        
        .btn-filter {
            background-color: #4F6F52;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-filter:hover {
            background-color: #3a5740;
        }
        
        .btn-reset {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-reset:hover {
            background-color: #5a6268;
        }
        
        .date-range-picker {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            width: 100%;
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
                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="get" action="">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="month-filter">
                                    <i class="fas fa-calendar-alt"></i> Month
                                </label>
                                <select class="filter-control" id="month-filter" name="month">
                                    <option value="">All Months</option>
                                    <?php
                                    $currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
                                    $months = [
                                        '01' => 'January',
                                        '02' => 'February',
                                        '03' => 'March',
                                        '04' => 'April',
                                        '05' => 'May',
                                        '06' => 'June',
                                        '07' => 'July',
                                        '08' => 'August',
                                        '09' => 'September',
                                        '10' => 'October',
                                        '11' => 'November',
                                        '12' => 'December'
                                    ];
                                    
                                    foreach ($months as $num => $name) {
                                        $selected = ($num == $currentMonth) ? 'selected' : '';
                                        echo "<option value=\"$num\" $selected>$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label class="filter-label" for="year-filter">
                                    <i class="fas fa-calendar-alt"></i> Year
                                </label>
                                <select class="filter-control" id="year-filter" name="year">
                                    <?php
                                    $currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
                                    $startYear = 2020;
                                    $endYear = date('Y') + 1;
                                    
                                    for ($year = $endYear; $year >= $startYear; $year--) {
                                        $selected = ($year == $currentYear) ? 'selected' : '';
                                        echo "<option value=\"$year\" $selected>$year</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label class="filter-label" for="date-range">
                                    <i class="fas fa-calendar-week"></i> Date Range
                                </label>
                                <input type="text" id="date-range" name="daterange" class="date-range-picker" 
                                    value="<?php echo isset($_GET['daterange']) ? $_GET['daterange'] : ''; ?>" 
                                    placeholder="Select date range">
                            </div>
                            
                            <div class="filter-group" style="display: flex; align-items: flex-end;">
                                <button type="submit" class="btn-filter mr-2">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="attendance_view.php" class="btn-reset">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="table-responsive" id="printSection">
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

                            // Initialize query parameters
                            $conditions = [];
                            $params = [];
                            $types = "";
                            
                            // Add month and year filter if selected
                            if (isset($_GET['month']) && !empty($_GET['month']) && isset($_GET['year']) && !empty($_GET['year'])) {
                                $month = $_GET['month'];
                                $year = $_GET['year'];
                                
                                $conditions[] = "MONTH(a.LOGDATE) = ?";
                                $params[] = $month;
                                $types .= "s";
                                
                                $conditions[] = "YEAR(a.LOGDATE) = ?";
                                $params[] = $year;
                                $types .= "s";
                            }
                            
                            // Add date range filter if selected
                            if (isset($_GET['daterange']) && !empty($_GET['daterange'])) {
                                $dateRange = explode(' - ', $_GET['daterange']);
                                if (count($dateRange) == 2) {
                                    $startDate = date('Y-m-d', strtotime($dateRange[0]));
                                    $endDate = date('Y-m-d', strtotime($dateRange[1]));
                                    
                                    $conditions[] = "a.LOGDATE BETWEEN ? AND ?";
                                    $params[] = $startDate;
                                    $params[] = $endDate;
                                    $types .= "ss";
                                }
                            }
                            
                            // Build the WHERE clause
                            $whereClause = "";
                            if (!empty($conditions)) {
                                $whereClause = "WHERE " . implode(" AND ", $conditions);
                            }
                            
                            // Get all attendance records with filtering
                            $sql = "SELECT a.*, e.`First Name` as emp_first_name, e.`Last Name` as emp_last_name 
                                    FROM attendance a 
                                    LEFT JOIN employee e ON a.EMPLOYEEID = e.EmployeeID
                                    $whereClause
                                    ORDER BY a.LOGDATE DESC, a.TIMEIN DESC";
                            
                            $stmt = $conn->prepare($sql);
                            
                            // Bind parameters if any
                            if (!empty($params)) {
                                $stmt->bind_param($types, ...$params);
                            }
                            
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            // Debug info for administrators
                            if (!$result) {
                                echo "<tr><td colspan='7' class='text-center text-danger'>SQL Error: " . $conn->error . "</td></tr>";
                            }
                            
                            // Get total count of records in attendance table
                            $countSql = "SELECT COUNT(*) as total FROM attendance";
                            $countResult = $conn->query($countSql);
                            $countRow = $countResult->fetch_assoc();
                            $totalAttendanceRecords = $countRow['total'];

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    
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
                                    // Use employee names from employee table, fallback to attendance table if null
                                    $lastName = !empty($row['emp_last_name']) ? $row['emp_last_name'] : $row['Last Name'];
                                    $firstName = !empty($row['emp_first_name']) ? $row['emp_first_name'] : $row['First Name'];
                                    echo "<td>" . htmlspecialchars($lastName) . ", " . htmlspecialchars($firstName) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['EMPLOYEEID']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['TIMEIN']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['TIMEOUT']) . "</td>";
                                    echo "<td>" . date('M d, Y', strtotime($row['LOGDATE'])) . "</td>";
                                    echo "<td><span class='badge {$badgeClass}'>" . htmlspecialchars($row['STATUS']) . "</span></td>";
                                    echo "<td>" . $additionalInfo . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No attendance records found for the selected criteria.</td></tr>";
                            }

                            // Close connection
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-export" onclick="exportAttendance()" style="margin-right: 10px;">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </button>
                <button type="button" class="btn btn-export" onclick="printAttendanceReport()">
                    <i class="fas fa-print"></i> Print Report
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
    
    <!-- Moment.js and DateRangePicker JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#attendanceTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "order": [[4, 'desc'], [2, 'desc']], // Sort by date desc, then time in desc
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });
            
            // Initialize DateRangePicker
            $('#date-range').daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MM/DD/YYYY'
                }
            });
            
            $('#date-range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });
            
            $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
            
            // If date range is already set, initialize the picker with those values
            var dateRangeValue = $('#date-range').val();
            if (dateRangeValue) {
                var dates = dateRangeValue.split(' - ');
                if (dates.length === 2) {
                    $('#date-range').data('daterangepicker').setStartDate(dates[0]);
                    $('#date-range').data('daterangepicker').setEndDate(dates[1]);
                }
            }
        });
        
        function exportAttendance() {
            // Get current filter values
            var month = $('#month-filter').val();
            var year = $('#year-filter').val();
            var dateRange = $('#date-range').val();
            
            // Build export URL with filters
            var exportUrl = "export_attendance.php";
            var params = [];
            
            if (month) params.push("month=" + month);
            if (year) params.push("year=" + year);
            if (dateRange) params.push("daterange=" + encodeURIComponent(dateRange));
            
            if (params.length > 0) {
                exportUrl += "?" + params.join("&");
            }
            
            if (confirm("Do you want to export the filtered attendance records to Excel?")) {
                window.location.href = exportUrl;
            }
        }
        
        function printAttendanceReport() {
            try {
                // Get the current date and time for the report
                var currentDate = new Date();
                var formattedDate = currentDate.toLocaleDateString('en-PH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                var formattedTime = currentDate.toLocaleTimeString('en-PH');
                
                // Get filter information for the header
                var filterInfo = '';
                var month = $('#month-filter').val();
                var year = $('#year-filter').val();
                var dateRange = $('#date-range').val();
                
                if (month && year) {
                    var monthName = $('#month-filter option:selected').text();
                    filterInfo = 'For ' + monthName + ' ' + year;
                } else if (dateRange) {
                    filterInfo = 'For period: ' + dateRange;
                }
                
                // Get the table data directly from DataTables API
                var dataTable = $('#attendanceTable').DataTable();
                var tableData = dataTable.rows().data();
                var tableHeaders = [];
                
                // Get the headers
                $('#attendanceTable thead th').each(function() {
                    tableHeaders.push($(this).text());
                });
                
                // Create a clean HTML table for printing
                var printTable = '<table class="print-table">';
                
                // Add table header
                printTable += '<thead><tr>';
                for (var i = 0; i < tableHeaders.length; i++) {
                    printTable += '<th>' + tableHeaders[i] + '</th>';
                }
                printTable += '</tr></thead>';
                
                // Add table body
                printTable += '<tbody>';
                
                // Get visible rows from the current view
                var visibleRows = dataTable.rows({page: 'current'}).nodes();
                $(visibleRows).each(function(index) {
                    var row = $(this);
                    printTable += '<tr>';
                    
                    // Get each cell in the row
                    row.find('td').each(function(cellIndex) {
                        // If this is the status column (index 5), extract the text from the badge
                        if (cellIndex === 5) {
                            var statusText = $(this).text().trim();
                            var statusClass = '';
                            
                            // Determine the appropriate text color based on status
                            if (statusText.includes('Present') || statusText.includes('ONTIME')) {
                                statusClass = 'text-success';
                            } else if (statusText.includes('Late') || statusText.includes('LATE')) {
                                statusClass = 'text-warning';
                            } else if (statusText.includes('No TimeOut')) {
                                statusClass = 'text-warning';
                            } else if (statusText.includes('ABSENT')) {
                                statusClass = 'text-danger';
                            }
                            
                            printTable += '<td class="' + statusClass + '">' + statusText + '</td>';
                        } else {
                            printTable += '<td>' + $(this).html() + '</td>';
                        }
                    });
                    
                    printTable += '</tr>';
                });
                
                printTable += '</tbody></table>';
                
                // Create a print-friendly version with header and footer
                var printableHTML = '<html><head><title>Attendance Report - Nipaulyn Attendance System</title>';
                printableHTML += '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">';
                printableHTML += '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">';
                printableHTML += '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">';
                printableHTML += '<style>';
                printableHTML += '@page { size: landscape; margin: 0.5in; }';
                printableHTML += 'body { font-family: "Poppins", sans-serif; padding: 0; margin: 0; }';
                printableHTML += '.container { padding: 20px; max-width: 100%; }';
                printableHTML += '.letterhead { display: flex; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #4F6F52; padding-bottom: 10px; }';
                printableHTML += '.logo { width: 60px; height: 60px; margin-right: 15px; }';
                printableHTML += '.letterhead-text { flex-grow: 1; }';
                printableHTML += '.letterhead h1 { color: #000000; font-size: 22px; margin: 0; font-weight: 600; }';
                printableHTML += '.letterhead h2 { color: #000000; font-size: 18px; margin: 5px 0 0 0; font-weight: 500; }';
                printableHTML += '.letterhead p { color: #000000; margin: 5px 0 0 0; font-size: 14px; }';
                printableHTML += '.report-title { text-align: center; margin: 20px 0; }';
                printableHTML += '.report-title h2 { color: #333; font-size: 20px; margin: 0; font-weight: 600; }';
                printableHTML += '.report-title p { color: #666; margin: 5px 0 0 0; font-size: 16px; }';
                printableHTML += '.print-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }';
                printableHTML += '.print-table th { background-color: #ffffff !important; color: #000000 !important; padding: 10px; text-align: left; font-weight: 600; border-bottom: 2px solid #000000; }';
                printableHTML += '.print-table td { padding: 8px 10px; border-bottom: 1px solid #ddd; }';
                printableHTML += '.print-table tr:nth-child(even) { background-color: #f9f9f9; }';
                printableHTML += '.text-success { color: #2D5E2D !important; font-weight: 500; }';
                printableHTML += '.text-warning { color: #996600 !important; font-weight: 500; }';
                printableHTML += '.text-danger { color: #9A0000 !important; font-weight: 500; }';
                printableHTML += '.text-info { color: #1A5F7A !important; font-weight: 500; }';
                printableHTML += '.info-text { font-size: 12px; padding: 4px 8px; border-radius: 4px; display: block; }';
                printableHTML += '.holiday-info { background-color: rgba(233, 216, 253, 0.2); color: #6B46C1; }';
                printableHTML += '.event-info { background-color: rgba(216, 191, 216, 0.2); color: #800080; }';
                printableHTML += '.footer { margin-top: 30px; font-size: 12px; color: #666; text-align: center; }';
                printableHTML += '.footer p { margin: 5px 0; }';
                printableHTML += '.signature-section { margin-top: 50px; display: flex; justify-content: space-between; }';
                printableHTML += '.signature-block { width: 200px; text-align: center; }';
                printableHTML += '.signature-line { border-top: 1px solid #333; margin-top: 40px; margin-bottom: 5px; }';
                printableHTML += '.signature-name { font-weight: 600; }';
                printableHTML += '.signature-title { font-size: 12px; color: #666; }';
                printableHTML += '</style></head><body>';
                
                // Add container
                printableHTML += '<div class="container">';
                
                // Add letterhead
                printableHTML += '<div class="letterhead">';
                printableHTML += '<div class="letterhead-text">';
                printableHTML += '<h1>Barangay Malinta</h1>';
                printableHTML += '<h2>Attendance Management System</h2>';
                printableHTML += '<p>Barangay Malinta, Valenzuela City</p>';
                printableHTML += '</div>';
                printableHTML += '</div>';
                
                // Add report title
                printableHTML += '<div class="report-title">';
                printableHTML += '<h2>ATTENDANCE SUMMARY REPORT</h2>';
                if (filterInfo) {
                    printableHTML += '<p>' + filterInfo + '</p>';
                }
                printableHTML += '</div>';
                
                // Add table content
                printableHTML += printTable;
                
                // No signature section
                
                // Add footer
                printableHTML += '<div class="footer">';
                printableHTML += '<p>Generated on ' + formattedDate + ' at ' + formattedTime + '</p>';
                printableHTML += '</div>';
                
                printableHTML += '</div>'; // Close container
                printableHTML += '</body></html>';
                
                // Open a new window for printing
                var printWindow = window.open('', '_blank', 'width=1000,height=800');
                if (printWindow) {
                    printWindow.document.open();
                    printWindow.document.write(printableHTML);
                    printWindow.document.close();
                    
                    // Wait for resources to load before printing
                    printWindow.onload = function() {
                        try {
                            setTimeout(function() {
                                printWindow.print();
                            }, 800); // Increased timeout for better resource loading
                        } catch (e) {
                            console.error('Error during print:', e);
                            alert('There was an error while trying to print. Please try again.');
                        }
                    };
                } else {
                    alert('Could not open print window. Please check if pop-ups are blocked.');
                }
            } catch (e) {
                console.error('Error in printAttendanceReport:', e);
                alert('There was an error preparing the report for printing. Please try again.');
            }
        }
    </script>
</body>
</html>