<?php
// Check if form is submitted with POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Retrieve form data
        $employeeID = isset($_POST['employeeID']) ? $_POST['employeeID'] : null;
        $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
        $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
        $middleName = isset($_POST['middleName']) ? $_POST['middleName'] : '';
        $suffix = isset($_POST['suffix']) ? $_POST['suffix'] : '';
        $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
        $age = isset($_POST['age']) ? $_POST['age'] : '';
        $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : '';
        $emailAddress = isset($_POST['emailAddress']) ? $_POST['emailAddress'] : '';
        $address = isset($_POST['address']) ? $_POST['address'] : '';
        $contactNumber = isset($_POST['contactNumber']) ? $_POST['contactNumber'] : '';
        $department = isset($_POST['department']) ? $_POST['department'] : '';
        
        // Validate that employee ID is not null
        if ($employeeID === null) {
            throw new Exception("Employee ID is missing");
        }
        
        // Get rest days data (using 0 as default if not checked)
        $mondayRest = isset($_POST['Monday_Rest']) ? 1 : 0;
        $tuesdayRest = isset($_POST['Tuesday_Rest']) ? 1 : 0;
        $wednesdayRest = isset($_POST['Wednesday_Rest']) ? 1 : 0;
        $thursdayRest = isset($_POST['Thursday_Rest']) ? 1 : 0;
        $fridayRest = isset($_POST['Friday_Rest']) ? 1 : 0;
        $saturdayRest = isset($_POST['Saturday_Rest']) ? 1 : 0;
        $sundayRest = isset($_POST['Sunday_Rest']) ? 1 : 0;

        // Database connection parameters
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "hris";
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Prepare SQL statement to update employee details including rest days
        $sql = "UPDATE employee SET 
                `Last Name`='" . $conn->real_escape_string($lastName) . "', 
                `First Name`='" . $conn->real_escape_string($firstName) . "', 
                `Middle Name`='" . $conn->real_escape_string($middleName) . "', 
                `Suffix`='" . $conn->real_escape_string($suffix) . "', 
                `Gender`='" . $conn->real_escape_string($gender) . "', 
                `Age`='" . $conn->real_escape_string($age) . "', 
                `Birthday`='" . $conn->real_escape_string($birthday) . "', 
                `Email Address`='" . $conn->real_escape_string($emailAddress) . "', 
                `Address`='" . $conn->real_escape_string($address) . "', 
                `Contact Number`='" . $conn->real_escape_string($contactNumber) . "', 
                `Department`='" . $conn->real_escape_string($department) . "',
                `Monday_Rest`=$mondayRest,
                `Tuesday_Rest`=$tuesdayRest,
                `Wednesday_Rest`=$wednesdayRest,
                `Thursday_Rest`=$thursdayRest,
                `Friday_Rest`=$fridayRest,
                `Saturday_Rest`=$saturdayRest,
                `Sunday_Rest`=$sundayRest
                WHERE `EmployeeID`='" . $conn->real_escape_string($employeeID) . "'";

        // Execute SQL statement and check for errors
        if ($conn->query($sql) === TRUE) {
            // Output success message with corrected paths
            echo '<script>
                alert("Employee information updated successfully!");
                if(window.parent && window.parent.document.getElementById("updateOverlay")) {
                    window.parent.document.getElementById("updateOverlay").style.display = "none";
                    window.parent.location.reload();
                } else {
                    window.location.href = "/capstone/employee.php";
                }
            </script>';
            exit;
        } else {
            $error = "Error updating record: " . $conn->error;
            throw new Exception($error);
        }

        $conn->close();
        
    } catch (Exception $e) {
        echo '<div style="background-color: #ffebee; color: #c62828; padding: 15px; margin-bottom: 20px; border-radius: 5px;">';
        echo '<strong>Error updating employee details:</strong> ' . $e->getMessage();
        echo '</div>';
    }
}

// Now continue with the original code to display the form
// Check if employee ID is provided
if(isset($_GET['id'])) {
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

    // Escape user input for security
    $employeeID = $conn->real_escape_string($_GET['id']);

    // SQL query to select employee data based on ID
    $sql = "SELECT * FROM employee WHERE `EmployeeID`='$employeeID'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch employee data
        $row = $result->fetch_assoc();

        // Close the database connection
        $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee Details</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS for employee details form */
        body {
            font-family: 'Poppins';
            background-color: white;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        /* Style the form container */
        .update-form-container {
            width: 540px;
            margin: 1px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.0);
        }
        
        /* Style form inputs */
        .update-form-container label {
            display: block;
            margin-bottom: 10px;
            color:black;
            font-size: 18px;
        }
        
        .update-form-container input[type="text"],
        .update-form-container input[type="number"],
        .update-form-container input[type="date"],
        .update-form-container input[type="email"],
        .update-form-container input[type="tel"],
        .update-form-container select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            box-sizing: border-box;
            font-family:'Poppins';
            font-size: 17px;
        }
        
        .update-form-container button {
            background-color: #4F6F52;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-family:'Poppins';
        }
        
        .update-form-container button:hover {
            background-color: #3D5941;
        }

        /* Styles for rest days section */
        .rest-days-container {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .rest-days-title {
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
        }

        .rest-days-title i {
            margin-right: 10px;
            color: #4F6F52;
        }

        .day-checkbox-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .day-checkbox {
            display: flex;
            align-items: center;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .day-checkbox.checked {
            background-color: #ffeded;
            border-color: #dc3545;
        }

        .day-checkbox input {
            margin-right: 10px;
        }

        .schedule-summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            font-size: 14px;
        }

        .rest-day-summary, .work-day-summary {
            margin-top: 5px;
        }

        .rest-day-summary span, .work-day-summary span {
            display: inline-block;
            padding: 3px 8px;
            margin: 2px;
            border-radius: 10px;
            font-size: 12px;
        }

        .rest-day-tag {
            background-color: #ffeded;
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .work-day-tag {
            background-color: #e7f5e7;
            color: #28a745;
            border: 1px solid #28a745;
        }

        .warning-text {
            color: #dc3545;
            font-style: italic;
            margin-top: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="update-form-container">
        <!-- Form for updating employee details -->
        <form method="post" enctype="multipart/form-data" id="updateForm">
            <input type="hidden" name="employeeID" value="<?php echo $row['EmployeeID']; ?>">
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo $row['Last Name']; ?>"><br>
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo $row['First Name']; ?>"><br>
            <label for="middleName">Middle Name:</label>
            <input type="text" id="middleName" name="middleName" value="<?php echo $row['Middle Name']; ?>"><br>
            <label for="suffix">Suffix:</label>
            <select id="suffix" name="suffix">
                <option value="Jr." <?php if($row['Suffix'] == 'Jr.') echo 'selected'; ?>>Jr.</option>
                <option value="Sr." <?php if($row['Suffix'] == 'Sr.') echo 'selected'; ?>>Sr.</option>
                <option value="III" <?php if($row['Suffix'] == 'III') echo 'selected'; ?>>III</option>
                <option value="IV" <?php if($row['Suffix'] == 'IV') echo 'selected'; ?>>IV</option>
                <option value="N/A" <?php if($row['Suffix'] == 'N/A') echo 'selected'; ?>>N/A</option>
            </select><br>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="Male" <?php if($row['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if($row['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
            </select><br>
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="<?php echo $row['Age']; ?>"><br>
            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday" value="<?php echo $row['Birthday']; ?>"><br>
            <label for="emailAddress">Email Address:</label>
            <input type="email" id="emailAddress" name="emailAddress" value="<?php echo $row['Email Address']; ?>"><br>
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo $row['Address']; ?>"><br>
            <label for="contactNumber">Contact Number:</label>
            <input type="tel" id="contactNumber" name="contactNumber" value="<?php echo $row['Contact Number']; ?>"><br>
            <label for="department">Department:</label>
            <select id="department" name="department">
                <?php
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

                // SQL query to fetch departments from the database
                $sql = "SELECT * FROM department";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($deptRow = $result->fetch_assoc()) {
                        echo '<option value="'.$deptRow['Department'].'"';
                        if($deptRow['Department'] == $row['Department']) {
                            echo ' selected';
                        }
                        echo '>'.$deptRow['Department'].'</option>';
                    }
                } else {
                    echo '<option value="">No departments found</option>';
                }

                // Close database connection
                $conn->close();
                ?>
            </select><br>

            <!-- Rest Days Section -->
            <div class="rest-days-container">
                <div class="rest-days-title">
                    <i class="fas fa-calendar-alt"></i> Configure Rest Days
                </div>
                <div class="day-checkbox-container">
                    <label class="day-checkbox <?php if($row['Monday_Rest'] == 1) echo 'checked'; ?>">
                        <input type="checkbox" name="Monday_Rest" value="1" <?php if($row['Monday_Rest'] == 1) echo 'checked'; ?>> Monday
                    </label>
                    <label class="day-checkbox <?php if($row['Tuesday_Rest'] == 1) echo 'checked'; ?>">
                        <input type="checkbox" name="Tuesday_Rest" value="1" <?php if($row['Tuesday_Rest'] == 1) echo 'checked'; ?>> Tuesday
                    </label>
                    <label class="day-checkbox <?php if($row['Wednesday_Rest'] == 1) echo 'checked'; ?>">
                        <input type="checkbox" name="Wednesday_Rest" value="1" <?php if($row['Wednesday_Rest'] == 1) echo 'checked'; ?>> Wednesday
                    </label>
                    <label class="day-checkbox <?php if($row['Thursday_Rest'] == 1) echo 'checked'; ?>">
                        <input type="checkbox" name="Thursday_Rest" value="1" <?php if($row['Thursday_Rest'] == 1) echo 'checked'; ?>> Thursday
                    </label>
                    <label class="day-checkbox <?php if($row['Friday_Rest'] == 1) echo 'checked'; ?>">
                        <input type="checkbox" name="Friday_Rest" value="1" <?php if($row['Friday_Rest'] == 1) echo 'checked'; ?>> Friday
                    </label>
                    <label class="day-checkbox <?php if($row['Saturday_Rest'] == 1) echo 'checked'; ?>">
                        <input type="checkbox" name="Saturday_Rest" value="1" <?php if($row['Saturday_Rest'] == 1) echo 'checked'; ?>> Saturday
                    </label>
                    <label class="day-checkbox <?php if($row['Sunday_Rest'] == 1) echo 'checked'; ?>">
                        <input type="checkbox" name="Sunday_Rest" value="1" <?php if($row['Sunday_Rest'] == 1) echo 'checked'; ?>> Sunday
                    </label>
                </div>

                <!-- Schedule Summary -->
                <div class="schedule-summary">
                    <div><strong><i class="fas fa-info-circle"></i> Work Schedule Summary:</strong></div>
                    <div class="rest-day-summary">
                        <strong>Rest Days:</strong> <span id="restDaysText">Loading...</span>
                    </div>
                    <div class="work-day-summary">
                        <strong>Work Days:</strong> <span id="workDaysText">Loading...</span>
                    </div>
                    <div id="warningText" class="warning-text" style="display: none;">
                        Warning: You have selected all days as rest days. Is this correct?
                    </div>
                </div>
            </div>

            <button type="submit">Update</button>
        </form>
    </div>
    <script>
        // Rest days checkbox functionality
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.day-checkbox input[type="checkbox"]');
            const dayLabels = document.querySelectorAll('.day-checkbox');
            const restDaysText = document.getElementById('restDaysText');
            const workDaysText = document.getElementById('workDaysText');
            const warningText = document.getElementById('warningText');
            
            // Days mapping
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            
            // Update the summary when page loads
            updateSummary();
            
            // Add event listeners to checkboxes
            checkboxes.forEach((checkbox, index) => {
                checkbox.addEventListener('change', function() {
                    // Toggle the 'checked' class on the parent label
                    if (this.checked) {
                        dayLabels[index].classList.add('checked');
                    } else {
                        dayLabels[index].classList.remove('checked');
                    }
                    
                    // Update the summary
                    updateSummary();
                });
            });
            
            // Function to update the summary text
            function updateSummary() {
                const selectedDays = [];
                const workDays = [];
                let allChecked = true;
                
                // Check which days are selected
                checkboxes.forEach((checkbox, index) => {
                    if (checkbox.checked) {
                        selectedDays.push(days[index]);
                    } else {
                        workDays.push(days[index]);
                        allChecked = false;
                    }
                });
                
                // Update rest days text
                if (selectedDays.length === 0) {
                    restDaysText.innerHTML = '<span style="color: #666; font-style: italic;">None selected</span>';
                } else {
                    restDaysText.innerHTML = selectedDays.map(day => 
                        `<span class="rest-day-tag"><i class="fas fa-moon"></i> ${day}</span>`
                    ).join(' ');
                }
                
                // Update work days text
                if (workDays.length === 0) {
                    workDaysText.innerHTML = '<span style="color: #666; font-style: italic;">None selected</span>';
                } else {
                    workDaysText.innerHTML = workDays.map(day => 
                        `<span class="work-day-tag"><i class="fas fa-briefcase"></i> ${day}</span>`
                    ).join(' ');
                }
                
                // Show warning if all days are selected
                if (allChecked) {
                    warningText.style.display = 'block';
                } else {
                    warningText.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>

<?php
    } else {
        echo "Employee not found";
    }
} else {
    echo "Employee ID not provided";
}
?>
