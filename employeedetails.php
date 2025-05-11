<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F6F52;
            --secondary: #739072;
            --light: #ECE3CE;
            --dark: #3A4D39;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --white: #ffffff;
            --red: #dc3545;
            --green: #28a745;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }
        
        .profile-container {
            max-width: 100%;
            margin: 0 auto;
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid var(--white);
            object-fit: cover;
            margin: 0 auto 15px;
            display: block;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .profile-name {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-title {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 15px;
        }
        
        .profile-id {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .profile-body {
            padding: 20px;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--light);
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: 500;
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-weight: 500;
            color: var(--dark);
            font-size: 16px;
        }
        
        .schedule-section {
            background-color: var(--light-gray);
            padding: 12px;
            border-radius: 10px;
            margin-top: 15px;
        }
        
        .day-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        
        .day-tag {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .day-tag i {
            margin-right: 5px;
        }
        
        .rest-day {
            background-color: #ffeded;
            color: var(--red);
            border: 1px solid var(--red);
        }
        
        .work-day {
            background-color: #e7f5e7;
            color: var(--green);
            border: 1px solid var(--green);
        }
        
        .sub-title {
            font-weight: 600;
            color: var(--dark);
            margin: 10px 0 8px;
            font-size: 15px;
        }
        
        .print-btn {
            display: block;
            text-align: center;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            font-size: 16px;
            margin: 30px auto 0;
            transition: background-color 0.3s;
            text-decoration: none;
            width: 200px;
        }
        
        .print-btn:hover {
            background-color: var(--dark);
        }
        
        .no-data {
            color: var(--gray);
            font-style: italic;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            
            .profile-container, .profile-container * {
                visibility: visible;
            }
            
            .profile-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
            }
            
            .print-btn {
                display: none;
            }
            
            .profile-header {
                background: #f1f1f1 !important;
                color: #000 !important;
                padding: 15px !important;
            }
            
            .profile-img {
                width: 100px !important;
                height: 100px !important;
            }
            
            .profile-name {
                font-size: 22px !important;
            }
            
            .info-section {
                break-inside: avoid;
                margin-bottom: 15px !important;
            }
            
            .day-tags {
                display: flex !important;
                flex-wrap: wrap !important;
                page-break-inside: avoid !important;
                margin-top: 5px !important;
            }
            
            .day-tag {
                margin-bottom: 5px !important;
                margin-right: 5px !important;
            }
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-container {
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php
// PHP code to fetch employee details based on ID
if(isset($_GET['id'])) {
    $employeeID = $_GET['id'];

    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hris";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to select employee details based on ID
    $sql = "SELECT * FROM employee WHERE EmployeeID = $employeeID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of the employee
        $row = $result->fetch_assoc();
        
        // Get employee full name
        $fullName = $row["First Name"] . " " . $row["Middle Name"] . " " . $row["Last Name"];
        if (!empty(trim($row["Suffix"]))) {
            $fullName .= " " . $row["Suffix"];
        }
        
        // Start building the profile
        echo '<div class="profile-container">';
        
        // Profile header
        echo '<div class="profile-header">';
        
        // New layout with flexbox - reduced padding
        echo '<div style="display: flex; align-items: center; padding: 12px; flex-wrap: wrap;">';
        
        // Left side - Image - smaller size and margins
        echo '<div style="flex: 0 0 120px; margin-right: 15px;">';
        // Display image if available - using simple direct approach
        echo '<div style="width: 100px; height: 100px; margin: 0 auto;">';
        if (!empty($row["Image"])) {
            $imageData = base64_encode($row["Image"]);
            echo '<img src="data:image/jpeg;base64,'.$imageData.'" alt="Employee Image" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">';
        } else {
            // Display default image if no image is available
            echo '<div style="width: 100px; height: 100px; border-radius: 50%; background-color: #4F6F52; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">';
            echo '<i class="fas fa-user" style="color: white; font-size: 40px;"></i>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        
        // Right side - Employee info - reduced font sizes and margins
        echo '<div style="flex: 1;">';
        echo '<h1 class="profile-name" style="margin-top: 0; margin-bottom: 3px; font-size: 22px;">' . $fullName . '</h1>';
        echo '<p class="profile-title" style="margin-bottom: 3px; font-size: 16px;">' . $row["Department"] . ' Department</p>';
        echo '<div class="profile-id" style="font-size: 14px;"><i class="fas fa-id-card"></i> Employee ID: ' . $row["EmployeeID"] . '</div>';
        echo '</div>';
        
        echo '</div>'; // End of flexbox container
        echo '</div>'; // End profile header
        
        // Profile body - reduced padding
        echo '<div class="profile-body" style="padding: 15px;">';
        
        // Personal Information Section - reduced margin
        echo '<div class="info-section" style="margin-bottom: 15px;">';
        echo '<h2 class="section-title"><i class="fas fa-user"></i> Personal Information</h2>';
        echo '<div class="info-grid">';
        
        // Grid items for personal info
        echo '<div class="info-item">';
        echo '<div class="info-label">Gender</div>';
        echo '<div class="info-value">' . $row["Gender"] . '</div>';
        echo '</div>';
        
        echo '<div class="info-item">';
        echo '<div class="info-label">Age</div>';
        echo '<div class="info-value">' . $row["Age"] . '</div>';
        echo '</div>';
        
        echo '<div class="info-item">';
        echo '<div class="info-label">Birthday</div>';
        echo '<div class="info-value">' . date("F j, Y", strtotime($row["Birthday"])) . '</div>';
        echo '</div>';
        
        echo '<div class="info-item">';
        echo '<div class="info-label">Address</div>';
        echo '<div class="info-value">' . $row["Address"] . '</div>';
        echo '</div>';
        
        echo '</div>'; // End info grid
        echo '</div>'; // End personal info section
        
        // Contact Information Section - reduced margin
        echo '<div class="info-section" style="margin-bottom: 15px;">';
        echo '<h2 class="section-title"><i class="fas fa-address-book"></i> Contact Information</h2>';
        echo '<div class="info-grid">';
        
        echo '<div class="info-item">';
        echo '<div class="info-label">Email Address</div>';
        echo '<div class="info-value">' . $row["Email Address"] . '</div>';
        echo '</div>';
        
        echo '<div class="info-item">';
        echo '<div class="info-label">Contact Number</div>';
        echo '<div class="info-value">' . $row["Contact Number"] . '</div>';
        echo '</div>';
        
        echo '</div>'; // End info grid
        echo '</div>'; // End contact info section
        
        // Work Schedule Section - no top margin needed
        echo '<div class="info-section" style="margin-bottom: 10px;">';
        echo '<h2 class="section-title"><i class="fas fa-calendar-alt"></i> Work Schedule</h2>';
        
        // Get rest days
        $days = array(
            'Monday' => $row['Monday_Rest'] == 1,
            'Tuesday' => $row['Tuesday_Rest'] == 1,
            'Wednesday' => $row['Wednesday_Rest'] == 1,
            'Thursday' => $row['Thursday_Rest'] == 1,
            'Friday' => $row['Friday_Rest'] == 1,
            'Saturday' => $row['Saturday_Rest'] == 1,
            'Sunday' => $row['Sunday_Rest'] == 1
        );
        
        // More compact layout with flex display - reduced padding and gap
        echo '<div class="schedule-section" style="padding: 10px; display: flex; flex-wrap: wrap; gap: 10px;">';
        
        // Rest Days Column - reduced min-width
        echo '<div style="flex: 1; min-width: 180px;">';
        echo '<h3 class="sub-title">Rest Days</h3>';
        echo '<div class="day-tags">';
        
        $hasRestDays = false;
        foreach ($days as $day => $isRest) {
            if ($isRest) {
                echo '<span class="day-tag rest-day"><i class="fas fa-moon"></i> ' . $day . '</span>';
                $hasRestDays = true;
            }
        }
        
        if (!$hasRestDays) {
            echo '<span class="no-data">No rest days configured</span>';
        }
        
        echo '</div>'; // End day tags
        echo '</div>'; // End rest days column
        
        // Work Days Column - reduced min-width
        echo '<div style="flex: 1; min-width: 180px;">';
        echo '<h3 class="sub-title">Work Days</h3>';
        echo '<div class="day-tags">';
        
        $hasWorkDays = false;
        foreach ($days as $day => $isRest) {
            if (!$isRest) {
                echo '<span class="day-tag work-day"><i class="fas fa-briefcase"></i> ' . $day . '</span>';
                $hasWorkDays = true;
            }
        }
        
        if (!$hasWorkDays) {
            echo '<span class="no-data">No work days configured</span>';
        }
        
        echo '</div>'; // End day tags
        echo '</div>'; // End work days column
        
        echo '</div>'; // End schedule section
        echo '</div>'; // End work schedule section
        
        echo '</div>'; // End profile body
        echo '</div>'; // End profile container
        
        // Print button
        echo '<a href="#" class="print-btn" onclick="window.print()"><i class="fas fa-print"></i> Print Profile</a>';
    } else {
        echo '<div class="profile-container" style="text-align: center; padding: 50px;">';
        echo '<i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #d9534f; margin-bottom: 20px;"></i>';
        echo '<h2>Employee Not Found</h2>';
        echo '<p>The requested employee profile could not be found in the database.</p>';
        echo '</div>';
    }
    
    // Close connection
    $conn->close();
} else {
    echo '<div class="profile-container" style="text-align: center; padding: 50px;">';
    echo '<i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #d9534f; margin-bottom: 20px;"></i>';
    echo '<h2>Error</h2>';
    echo '<p>Employee ID not provided in the request.</p>';
    echo '</div>';
}
?>

</body>
</html>
