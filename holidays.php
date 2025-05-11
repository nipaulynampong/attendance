<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Holiday Management</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="QRCodeAttendance/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="QRCodeAttendance/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="QRCodeAttendance/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #4F6F52;
            --secondary-color: #739072;
            --light-color: #ECE3CE;
            --dark-color: #3A4D39;
            --border-radius-lg: 15px;
            --border-radius-md: 10px;
            --border-radius-sm: 6px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5efe6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            box-sizing: border-box;
            overflow-y: auto;
        }

        /* Scrollbar styling for body */
        body::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        body::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        body::-webkit-scrollbar-thumb:hover {
            background: var(--dark-color);
        }

        .navbar {
            background-color: var(--primary-color) !important;
            padding: 0.5rem 1rem;
            border-radius: 0;
            margin-bottom: 20px;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 600;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .container {
            margin: 0 auto;
            padding: 0 20px;
        }

        .card {
            background-color: #ffffff;
            border-radius: var(--border-radius-md);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            overflow: hidden;
            border: none;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            font-weight: 500;
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: var(--border-radius-md) var(--border-radius-md) 0 0 !important;
        }

        .card-header h4 {
            margin: 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 20px;
        }

        .form-control {
            border-radius: var(--border-radius-sm);
            padding: 10px 12px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(79, 111, 82, 0.25);
        }

        .form-group label {
            font-weight: 500;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .btn {
            border-radius: var(--border-radius-sm);
            padding: 8px 16px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .table {
            background-color: white;
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        /* Fix for DataTables dropdown menus */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius-sm);
            height: auto;
        }
        
        /* Make sure dropdown options are visible */
        .dataTables_length select option {
            padding: 8px;
            background-color: white;
        }
        
        /* Ensure dropdowns appear above other elements */
        .dataTables_wrapper .dropdown-menu,
        select, option {
            z-index: 9999 !important;
        }
        
        /* Fix for Filter by Year and Holiday Type dropdowns */
        #yearFilter, #holiday_type, #holiday_year {
            position: relative;
            z-index: 1000;
        }
        
        #yearFilter option, #holiday_type option, #holiday_year option {
            padding: 8px;
            background-color: white;
            color: #333;
        }

        .table th {
            background-color: var(--primary-color) !important;
            color: white !important;
            font-weight: 500;
            padding: 15px !important;
            border: none !important;
        }

        .table td {
            padding: 12px 15px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #eef0f3 !important;
            color: #2c3e50;
        }

        .table tr:hover {
            background-color: rgba(79, 111, 82, 0.05);
        }

        .badge {
            padding: 5px 10px;
            border-radius: var(--border-radius-sm);
            font-weight: normal;
            font-size: 12px;
        }

        .badge-regular {
            background-color: #ADE8F4;
            color: #1A5F7A;
        }

        .badge-special {
            background-color: #FFECD6;
            color: #996600;
        }

        .alert {
            border-radius: var(--border-radius-md);
            padding: 12px;
            margin-bottom: 15px;
            border: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .overlay {
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

        .overlay-content {
            width: 500px;
            background-color: white;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideIn 0.3s;
        }

        .overlay-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top-left-radius: var(--border-radius-lg);
            border-top-right-radius: var(--border-radius-lg);
        }

        .overlay-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }

        .overlay-close {
            color: white;
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
        }

        .overlay-body {
            padding: 20px;
        }

        @keyframes slideIn {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .holiday-date {
            font-weight: 500;
            color: var(--primary-color);
        }

        .holiday-actions {
            display: flex;
            gap: 5px;
        }

        .holiday-actions button {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius-sm);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .edit-btn {
            background-color: #f0ad4e;
            color: white;
        }

        .edit-btn:hover {
            background-color: #ec971f;
        }

        .delete-btn {
            background-color: #d9534f;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c9302c;
        }

        .auto-generate-section {
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .year-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .year-selector label {
            font-weight: 500;
            margin: 0;
        }

        .year-selector select {
            width: 120px;
            padding: 8px;
            border-radius: var(--border-radius-sm);
            border: 1px solid #ddd;
        }

        .auto-generate-btn {
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .auto-generate-btn:hover {
            background-color: var(--dark-color);
        }

        .holidays-preview {
            margin-top: 15px;
            display: none;
            border-radius: var(--border-radius-md);
        }

        .holidays-preview.show {
            display: block;
        }

        .preview-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 10px;
        }

        .preview-item {
            padding: 10px;
            background-color: #fff;
            border-radius: 4px;
            border-left: 3px solid var(--primary-color);
            font-size: 14px;
        }

        .preview-name {
            font-weight: 500;
            color: var(--primary-color);
        }

        .preview-date {
            font-size: 12px;
            color: #666;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons button {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius-sm);
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .edit-btn {
            background-color: var(--secondary-color);
            color: white;
        }

        .edit-btn:hover {
            background-color: var(--dark-color);
            transform: translateY(-2px);
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-calendar-alt"></i> Holiday Management</a>
        </div>
    </nav>

    <div class="container">
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

            // Check if the holiday table exists, if not create it
            $check_table_sql = "SHOW TABLES LIKE 'holidays'";
            $result = $conn->query($check_table_sql);
            
            if ($result->num_rows == 0) {
                // Table doesn't exist, create it
                $create_table_sql = "CREATE TABLE IF NOT EXISTS `holidays` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `holiday_name` varchar(255) NOT NULL,
                    `holiday_date` date NOT NULL,
                    `description` text,
                    `holiday_type` enum('Regular', 'Special') NOT NULL DEFAULT 'Regular',
                    `is_paid` tinyint(1) NOT NULL DEFAULT '1',
                    `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                
                if ($conn->query($create_table_sql) === TRUE) {
                    echo "<div class='alert alert-success'>Holiday table created successfully</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error creating holiday table: " . $conn->error . "</div>";
                }
            }

            // Function to generate annual holidays for a given year
            function generateAnnualHolidays($year, $conn) {
                // Standard Philippine Holidays
                $holidays = [
                    // Regular Holidays
                    ['New Year\'s Day', "$year-01-01", 'First day of the year', 'Regular', 1],
                    ['Maundy Thursday', calculateEasterDate($year, -3), 'Christian holiday', 'Regular', 1],
                    ['Good Friday', calculateEasterDate($year, -2), 'Christian holiday', 'Regular', 1],
                    ['Labor Day', "$year-05-01", 'International Workers\' Day', 'Regular', 1],
                    ['Independence Day', "$year-06-12", 'Philippine Independence Day', 'Regular', 1],
                    ['National Heroes Day', calculateLastMondayOfMonth($year, 8), 'Honor Filipino heroes', 'Regular', 1],
                    ['Bonifacio Day', "$year-11-30", 'Birthday of Andres Bonifacio', 'Regular', 1],
                    ['Christmas Day', "$year-12-25", 'Christmas celebration', 'Regular', 1],
                    ['Rizal Day', "$year-12-30", 'Commemorating Jose Rizal', 'Regular', 1],
                    
                    // Special Non-working Holidays
                    ['All Saints\' Day', "$year-11-01", 'Honoring Saints', 'Special', 1],
                    ['All Souls\' Day', "$year-11-02", 'Commemoration of the faithful departed', 'Special', 1],
                    ['Christmas Eve', "$year-12-24", 'Day before Christmas', 'Special', 1],
                    ['New Year\'s Eve', "$year-12-31", 'Last day of the year', 'Special', 1]
                ];
                
                return $holidays;
            }
            
            // Function to calculate Easter date for a given year (Computus algorithm)
            function calculateEasterDate($year, $offsetDays = 0) {
                $a = $year % 19;
                $b = floor($year / 100);
                $c = $year % 100;
                $d = floor($b / 4);
                $e = $b % 4;
                $f = floor(($b + 8) / 25);
                $g = floor(($b - $f + 1) / 3);
                $h = (19 * $a + $b - $d - $g + 15) % 30;
                $i = floor($c / 4);
                $k = $c % 4;
                $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
                $m = floor(($a + 11 * $h + 22 * $l) / 451);
                $month = floor(($h + $l - 7 * $m + 114) / 31);
                $day = (($h + $l - 7 * $m + 114) % 31) + 1;
                
                $easter = DateTime::createFromFormat('Y-n-j', "$year-$month-$day");
                
                if ($offsetDays != 0) {
                    $easter->modify("$offsetDays days");
                }
                
                return $easter->format('Y-m-d');
            }
            
            // Function to calculate the last Monday of a given month and year
            function calculateLastMondayOfMonth($year, $month) {
                $lastDay = date("t", strtotime("$year-$month-01"));  // Get the last day of the month
                $lastMonday = new DateTime("$year-$month-$lastDay");
                
                // Go back to the previous Monday
                while ($lastMonday->format('N') != 1) {
                    $lastMonday->modify('-1 day');
                }
                
                return $lastMonday->format('Y-m-d');
            }

           
            // Function to validate holiday name - allow only letters, spaces, dash, single quotes, double quotes, and forward slash
            function validateHolidayName($name) {
                return preg_match('/^[A-Za-z\s\-\'\"\/]+$/', $name);
            }
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['add_holiday'])) {
                    
                    $name = $_POST['holiday_name'];
                    $date = $_POST['holiday_date'];
                    $description = $_POST['description'];
                    $type = $_POST['holiday_type'];
                    $is_paid = isset($_POST['is_paid']) ? 1 : 0;
                    
                    // Validate holiday name - allow only letters, spaces, dash, single quotes, double quotes, and forward slash
                    $valid_name = validateHolidayName($name);
                    if (!$valid_name) {
                        echo "<div class='alert alert-danger'>Holiday Name can only contain letters, spaces, dash, single quotes, double quotes, and forward slash.</div>";
                    }
                    
                    // Only proceed with insert if the name is valid
                    if ($valid_name) {
                        $insert_sql = "INSERT INTO holidays (holiday_name, holiday_date, description, holiday_type, is_paid) 
                                      VALUES (?, ?, ?, ?, ?)";
                        
                        $stmt = $conn->prepare($insert_sql);
                        $stmt->bind_param("ssssi", $name, $date, $description, $type, $is_paid);
                        
                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success'>Holiday added successfully</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Error adding holiday: " . $conn->error . "</div>";
                        }
                        $stmt->close();
                    }
                } elseif (isset($_POST['update_holiday'])) {
                   
                    $id = intval($_POST['holiday_id']);
                    $name = $_POST['holiday_name'];
                    $date = $_POST['holiday_date'];
                    $description = $_POST['description'];
                    $type = $_POST['holiday_type'];
                    $is_paid = isset($_POST['is_paid']) ? 1 : 0;
                    
                    // Validate holiday name - allow only letters, spaces, dash, single quotes, double quotes, and forward slash
                    $valid_name = validateHolidayName($name);
                    if (!$valid_name) {
                        echo "<div class='alert alert-danger'>Holiday Name can only contain letters, spaces, dash, single quotes, double quotes, and forward slash.</div>";
                    }
                    
                    // Only proceed with update if the name is valid
                    if ($valid_name) {
                        $update_sql = "UPDATE holidays SET 
                                      holiday_name = ?, 
                                      holiday_date = ?, 
                                      description = ?, 
                                      holiday_type = ?, 
                                      is_paid = ? 
                                      WHERE id = ?";
                        
                        $stmt = $conn->prepare($update_sql);
                        if ($stmt === false) {
                            echo "<div class='alert alert-danger'>Prepare failed: " . $conn->error . "</div>";
                        } else {
                            $result = $stmt->bind_param("ssssii", $name, $date, $description, $type, $is_paid, $id);
                            if ($result === false) {
                                echo "<div class='alert alert-danger'>Binding parameters failed</div>";
                            } else {
                                if ($stmt->execute()) {
                                    echo "<div class='alert alert-success'>Holiday updated successfully</div>";
                                } else {
                                    echo "<div class='alert alert-danger'>Error updating holiday: " . $stmt->error . "</div>";
                                }
                                $stmt->close();
                            }
                        }
                    }
                } elseif (isset($_POST['delete_holiday'])) {
                    // Delete holiday
                    $id = $_POST['holiday_id'];
                    
                    $delete_sql = "DELETE FROM holidays WHERE id = ?";
                    
                    $stmt = $conn->prepare($delete_sql);
                    $stmt->bind_param("i", $id);
                    
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Holiday deleted successfully</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error deleting holiday: " . $conn->error . "</div>";
                    }
                    $stmt->close();
                } elseif (isset($_POST['auto_generate_holidays'])) {
                    // Handle auto-generate holidays submission
                    $year = $_POST['holiday_year'];
                    $annualHolidays = generateAnnualHolidays($year, $conn);
                    $successCount = 0;
                    $skippedCount = 0;
                    
                    foreach ($annualHolidays as $holiday) {
                        $name = $holiday[0];
                        $date = $holiday[1];
                        $description = $holiday[2];
                        $type = $holiday[3];
                        $is_paid = $holiday[4];
                        
                        // Check if holiday already exists - using prepared statement to avoid SQL injection
                        $check_sql = "SELECT COUNT(*) as count FROM holidays WHERE holiday_date = ? AND holiday_name = ?";
                        $stmt = $conn->prepare($check_sql);
                        $stmt->bind_param("ss", $date, $name);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $check_data = $result->fetch_assoc();
                        $stmt->close();
                        
                        if ($check_data['count'] == 0) {
                            // Insert holiday if it doesn't exist - using prepared statement
                            $insert_sql = "INSERT INTO holidays (holiday_name, holiday_date, description, holiday_type, is_paid) 
                                          VALUES (?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($insert_sql);
                            $stmt->bind_param("ssssi", $name, $date, $description, $type, $is_paid);
                            
                            if ($stmt->execute()) {
                                $successCount++;
                            }
                            $stmt->close();
                        } else {
                            $skippedCount++;
                        }
                    }
                    
                    if ($successCount > 0) {
                        echo "<div class='alert alert-success'>Successfully added $successCount standard holidays for the year $year.</div>";
                    }
                    
                    if ($skippedCount > 0) {
                        echo "<div class='alert alert-info'>Skipped $skippedCount holidays that already exist in the database.</div>";
                    }
                    
                    if ($successCount == 0 && $skippedCount == 0) {
                        echo "<div class='alert alert-warning'>No holidays were added. Please try a different year.</div>";
                    }
                }
            }
            ?>

            <div class="row">
                <div class="col-md-4">
                    <!-- Add Holiday Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-plus-circle"></i> Add Holiday</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="form-group">
                                    <label for="holiday_name">Holiday Name</label>
                                    <input type="text" class="form-control" id="holiday_name" name="holiday_name" pattern="[A-Za-z\s\-'\"/]+" title="Only letters, spaces, dash, single quotes, double quotes, and forward slash are allowed" required>
                                </div>
                                <div class="form-group">
                                    <label for="holiday_date">Date</label>
                                    <input type="date" class="form-control" id="holiday_date" name="holiday_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="holiday_type">Holiday Type</label>
                                    <select class="form-control" id="holiday_type" name="holiday_type">
                                        <option value="Regular">Regular Holiday</option>
                                        <option value="Special">Special Non-working Holiday</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_paid" name="is_paid" checked>
                                        <label class="custom-control-label" for="is_paid">Paid Holiday</label>
                                    </div>
                                </div>
                                <button type="submit" name="add_holiday" class="btn btn-success btn-block">
                                    <i class="fas fa-plus-circle"></i> Add Holiday
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Auto-Generate Holidays Section -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4><i class="fas fa-calendar-plus"></i> Auto-Generate Holidays</h4>
                        </div>
                        <div class="card-body">
                            <div class="auto-generate-section">
                                <p>Quickly add standard Philippine holidays for a specific year.</p>
                                
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="year-selector">
                                        <label for="holiday_year">Select Year:</label>
                                        <select id="holiday_year" name="holiday_year" class="form-control">
                                            <?php
                                            $currentYear = date('Y');
                                            
                                            // Reset pointer to beginning if needed
                                            if ($yearsResult->num_rows > 0) {
                                                $yearsResult->data_seek(0);
                                            }
                                            
                                            // Get existing years from result set
                                            $existingYears = array();
                                            if ($yearsResult->num_rows > 0) {
                                                while ($yearRow = $yearsResult->fetch_assoc()) {
                                                    $existingYears[] = $yearRow['year'];
                                                }
                                            }
                                            
                                            // Add years up to 2040 if not already in database
                                            for ($year = $currentYear; $year <= 2040; $year++) {
                                                if (!in_array($year, $existingYears)) {
                                                    $selected = ($year == 2025) ? 'selected' : '';
                                                    echo "<option value=\"$year\" $selected>$year</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <button type="button" id="preview-btn" class="btn btn-info" onclick="previewHolidays()">
                                        <i class="fas fa-eye"></i> Preview Holidays
                                    </button>
                                    
                                    <button type="submit" name="auto_generate_holidays" class="auto-generate-btn">
                                        <i class="fas fa-calendar-plus"></i> Generate Holidays
                                    </button>
                                    
                                    <div id="holidays-preview" class="holidays-preview">
                                        <h5>Holidays that will be added:</h5>
                                        <div id="preview-list" class="preview-list">
                                            <!-- Preview items will be added here dynamically -->
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <p class="text-muted mt-3">
                                <small>
                                    <i class="fas fa-info-circle"></i> This will add standard Philippine holidays including:
                                    New Year's Day, Holy Week, Labor Day, Independence Day, National Heroes Day, Christmas, etc.
                                    <br>Existing holidays will not be duplicated.
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <!-- Holidays List -->
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-calendar-alt"></i> Holidays List</h4>
                        </div>
                        <div class="card-body">
                            <!-- Year Filter Dropdown -->
                            <div class="mb-3">
                                <label for="yearFilter" class="font-weight-bold">Filter by Year:</label>
                                <select id="yearFilter" class="form-control" style="width: 150px;">
                                    <option value="">All Years</option>
                                    <?php
                                    // Get unique years from database
                                    $yearsSql = "SELECT DISTINCT YEAR(holiday_date) as year FROM holidays ORDER BY year DESC";
                                    $yearsResult = $conn->query($yearsSql);
                                    
                                    if ($yearsResult->num_rows > 0) {
                                        while ($yearRow = $yearsResult->fetch_assoc()) {
                                            $selected = ($yearRow['year'] == 2025) ? 'selected' : '';
                                            echo "<option value=\"" . $yearRow['year'] . "\" $selected>" . $yearRow['year'] . "</option>";
                                        }
                                    }
                                    // Add future years for convenience
                                    $currentYear = date('Y');
                                    
                                    // Reset pointer to beginning if needed
                                    if ($yearsResult->num_rows > 0) {
                                        $yearsResult->data_seek(0);
                                    }
                                    
                                    // Get existing years from result set
                                    $existingYears = array();
                                    if ($yearsResult->num_rows > 0) {
                                        while ($yearRow = $yearsResult->fetch_assoc()) {
                                            $existingYears[] = $yearRow['year'];
                                        }
                                    }
                                    
                                    // Add years up to 2040 if not already in database
                                    for ($year = $currentYear; $year <= 2040; $year++) {
                                        if (!in_array($year, $existingYears)) {
                                            $selected = ($year == 2025) ? 'selected' : '';
                                            echo "<option value=\"$year\" $selected>$year</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="table-responsive">
                                <table id="holidays-table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Holiday Name</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Paid</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch all holidays
                                        $sql = "SELECT * FROM holidays ORDER BY holiday_date DESC";
                                        $result = $conn->query($sql);
                                        
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $holiday_date = date('M d, Y', strtotime($row['holiday_date']));
                                                $badge_class = ($row['holiday_type'] == 'Regular') ? 'badge-regular' : 'badge-special';
                                                $paid_status = $row['is_paid'] ? 'Yes' : 'No';
                                                
                                                echo "<tr>";
                                                echo "<td>" . $row['holiday_name'] . "</td>";
                                                echo "<td class='holiday-date'>" . $holiday_date . "</td>";
                                                echo "<td><span class='badge " . $badge_class . "'>" . $row['holiday_type'] . "</span></td>";
                                                echo "<td>" . $paid_status . "</td>";
                                                echo "<td class='holiday-actions'>
                                                        <button class='edit-btn' data-holiday='" . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . "' onclick='openEditFormFromButton(this)'>
                                                            <i class='fas fa-edit'></i>
                                                        </button>
                                                        <button class='delete-btn' onclick='confirmDelete(" . $row['id'] . ", \"" . $row['holiday_name'] . "\")'>
                                                            <i class='fas fa-trash'></i>
                                                        </button>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5' class='text-center'>No holidays found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Holiday Overlay -->
    <div id="editOverlay" class="overlay">
        <div class="overlay-content">
            <div class="overlay-header">
                <h4><i class="fas fa-edit"></i> Edit Holiday</h4>
                <button class="overlay-close" onclick="closeEditForm()">&times;</button>
            </div>
            <div class="overlay-body">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" id="edit_holiday_id" name="holiday_id">
                    <div class="form-group">
                        <label for="edit_holiday_name">Holiday Name</label>
                        <input type="text" class="form-control" id="edit_holiday_name" name="holiday_name" pattern="[A-Za-z\s\-'\"/]+" title="Only letters, spaces, dash, single quotes, double quotes, and forward slash are allowed" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_holiday_date">Date</label>
                        <input type="date" class="form-control" id="edit_holiday_date" name="holiday_date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_holiday_type">Holiday Type</label>
                        <select class="form-control" id="edit_holiday_type" name="holiday_type">
                            <option value="Regular">Regular Holiday</option>
                            <option value="Special">Special Non-working Holiday</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_is_paid" name="is_paid">
                            <label class="form-check-label" for="edit_is_paid">Paid Holiday</label>
                        </div>
                    </div>
                    <button type="submit" name="update_holiday" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Update Holiday
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Holiday Form (Hidden) -->
    <div style="display:none;">
        <form id="deleteForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" id="delete_holiday_id" name="holiday_id">
            <input type="hidden" name="delete_holiday" value="1">
        </form>
    </div>

    <script src="QRCodeAttendance/plugins/jquery/jquery.min.js"></script>
    <script src="QRCodeAttendance/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="QRCodeAttendance/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="QRCodeAttendance/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="QRCodeAttendance/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="QRCodeAttendance/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script>
        // Function to validate holiday name input fields
        function validateHolidayNameInput(input) {
            // Only allow letters, spaces, dash, single quotes, double quotes, and forward slash
            const validPattern = /^[A-Za-z\s\-'"/]*$/;
            const value = input.value;
            
            if (!validPattern.test(value)) {
                // Find the invalid character
                for (let i = 0; i < value.length; i++) {
                    const char = value.charAt(i);
                    if (!validPattern.test(char)) {
                        // Remove the invalid character
                        input.value = value.substring(0, i) + value.substring(i + 1);
                        
                        // Show alert
                        alert('Invalid character: "' + char + '". Holiday Name can only contain letters, spaces, dash, single quotes, double quotes, and forward slash.');
                        
                        // Validate again in case there are more invalid characters
                        validateHolidayNameInput(input);
                        break;
                    }
                }
            }
        }
        // Function to validate holiday name input fields
        function validateHolidayNameInput(input) {
            // Pattern to allow only letters, spaces, dash, single quotes, double quotes, and forward slash
            const pattern = /^[A-Za-z\s\-\'\"\/]*$/;
            const errorMsg = 'Holiday Name can only contain letters, spaces, dash, single quotes, double quotes, and forward slash.';
            
            // Remove any disallowed characters immediately
            const sanitizedValue = input.value.replace(/[^A-Za-z\s\-\'\"\/]/g, '');
            
            // If the sanitized value is different from the current value, update the input
            if (sanitizedValue !== input.value) {
                input.value = sanitizedValue;
            }
            
            // Check if the input value matches the pattern
            if (!pattern.test(input.value)) {
                // Invalid input - show error message
                input.setCustomValidity(errorMsg);
                input.reportValidity();
                
                // Highlight the field with red border
                input.style.borderColor = 'red';
            } else {
                // Valid input - clear error message
                input.setCustomValidity('');
                
                // Reset border color
                input.style.borderColor = '';
            }
        }
        
        // Add event listeners for holiday name fields
        document.addEventListener('DOMContentLoaded', function() {
            // Add input validation for holiday name fields
            const holidayNameInput = document.getElementById('holiday_name');
            const editHolidayNameInput = document.getElementById('edit_holiday_name');
            
            if (holidayNameInput) {
                holidayNameInput.addEventListener('input', function() {
                    validateHolidayNameInput(this);
                });
            }
            
            if (editHolidayNameInput) {
                editHolidayNameInput.addEventListener('input', function() {
                    validateHolidayNameInput(this);
                });
            }
        });
        
        $(function () {
            // Initialize DataTable
            var holidaysTable = $("#holidays-table").DataTable({
                "responsive": true,
                "autoWidth": false,
                "order": [[ 1, "desc" ]], // Sort by date descending
                "language": {
                    "emptyTable": "No holidays found"
                }
            });
            
            // Year filter functionality
            $('#yearFilter').on('change', function() {
                var year = $(this).val();
                
                // Custom filter for finding year in date column
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        if (!year) {
                            return true; // Show all if no year selected
                        }
                        
                        var holidayDate = data[1]; // Second column has the date (M d, Y format)
                        return holidayDate.includes(year);
                    }
                );
                
                holidaysTable.draw(); // Redraw the table with the filter
                
                // Clean up the filter function
                $.fn.dataTable.ext.search.pop();
            });
            
            // Trigger initial filter to show 2025 by default
            $('#yearFilter').trigger('change');
        });
        
        // Open edit form overlay
        function openEditForm(holiday) {
            document.getElementById('edit_holiday_id').value = holiday.id;
            document.getElementById('edit_holiday_name').value = holiday.holiday_name;
            document.getElementById('edit_holiday_date').value = holiday.holiday_date;
            document.getElementById('edit_description').value = holiday.description;
            document.getElementById('edit_holiday_type').value = holiday.holiday_type;
            document.getElementById('edit_is_paid').checked = holiday.is_paid == 1;
            
            document.getElementById('editOverlay').style.display = 'flex';
        }
        
        // Handle opening edit form from button with data attribute
        function openEditFormFromButton(button) {
            const holidayData = JSON.parse(button.getAttribute('data-holiday'));
            openEditForm(holidayData);
        }
        
        // Close edit form overlay
        function closeEditForm() {
            document.getElementById('editOverlay').style.display = 'none';
        }
        
        // Confirm delete
        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete the holiday "' + name + '"?')) {
                document.getElementById('delete_holiday_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Function to preview holidays
        function previewHolidays() {
            const selectedYear = document.getElementById('holiday_year').value;
            const previewContainer = document.getElementById('holidays-preview');
            const previewList = document.getElementById('preview-list');
            
            // Show the preview container
            previewContainer.classList.add('show');
            
            // Clear previous preview
            previewList.innerHTML = '';
            
            // Get holidays for the selected year
            const holidays = getAnnualHolidays(selectedYear);
            
            // Add each holiday to the preview list
            holidays.forEach(holiday => {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                
                const dateObj = new Date(holiday[1]);
                const formattedDate = dateObj.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric',
                    year: 'numeric'
                });
                
                previewItem.innerHTML = `
                    <div class="preview-name">${holiday[0]}</div>
                    <div class="preview-date">${formattedDate} (${holiday[3]})</div>
                `;
                
                previewList.appendChild(previewItem);
            });
        }
        
       
        function getAnnualHolidays(year) {

            return [
               
                ['New Year\'s Day', `${year}-01-01`, 'First day of the year', 'Regular'],
                ['Labor Day', `${year}-05-01`, 'International Workers\' Day', 'Regular'],
                ['Independence Day', `${year}-06-12`, 'Philippine Independence Day', 'Regular'],
                ['Bonifacio Day', `${year}-11-30`, 'Birthday of Andres Bonifacio', 'Regular'],
                ['Christmas Day', `${year}-12-25`, 'Christmas celebration', 'Regular'],
                ['Rizal Day', `${year}-12-30`, 'Commemorating Jose Rizal', 'Regular'],
                
               
                ['All Saints\' Day', `${year}-11-01`, 'Honoring Saints', 'Special'],
                ['All Souls\' Day', `${year}-11-02`, 'Commemoration of the faithful departed', 'Special'],
                ['Christmas Eve', `${year}-12-24`, 'Day before Christmas', 'Special'],
                ['New Year\'s Eve', `${year}-12-31`, 'Last day of the year', 'Special']
            ];
            

        }
    </script>
</body>
</html>
