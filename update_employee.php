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
        
        // Validate name fields (only allow letters, dashes, and spaces)
        $namePattern = '/^[a-zA-Z\- ]+$/';
        
        if (!empty($lastName) && !preg_match($namePattern, $lastName)) {
            throw new Exception("Last Name can only contain letters, dashes, and spaces.");
        }
        
        if (!empty($firstName) && !preg_match($namePattern, $firstName)) {
            throw new Exception("First Name can only contain letters, dashes, and spaces.");
        }
        
        // Special pattern for middle name to allow forward slash for N/A
        $middleNamePattern = '/^[a-zA-Z\-\/ ]+$/';
        if (!empty($middleName) && !preg_match($middleNamePattern, $middleName)) {
            throw new Exception("Middle Name can only contain letters, dashes, spaces, and forward slash (/) for N/A.");
        }
        
        // Validate age (limit to 3 digits)
        if (!empty($age)) {
            // Check if age is numeric and has at most 3 digits
            if (!is_numeric($age) || strlen($age) > 3) {
                throw new Exception("Age must be a number with at most 3 digits.");
            }
            
            // Check if age is within reasonable range (1-999)
            if ($age < 1 || $age > 999) {
                throw new Exception("Age must be between 1 and 999.");
            }
        }
        
        // Validate contact number (must be 11 digits and start with 09)
        if (!empty($contactNumber)) {
            // Remove any non-digit characters first
            $contactNumber = preg_replace('/[^0-9]/', '', $contactNumber);
            
            // Check if contact number is exactly 11 digits and starts with 09
            if (!preg_match('/^09\d{9}$/', $contactNumber)) {
                throw new Exception("Contact Number must be 11 digits and start with 09.");
            }
            
            // Store the full contact number as a string (for VARCHAR field)
            $_POST['contactNumber'] = $contactNumber;
        }
        
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

        // Check if a new image was uploaded
        $imageSQL = "";
        if(isset($_FILES['employeeImage']) && $_FILES['employeeImage']['size'] > 0) {
            // Get file information
            $fileTmpPath = $_FILES['employeeImage']['tmp_name'];
            $fileName = $_FILES['employeeImage']['name'];
            $fileSize = $_FILES['employeeImage']['size'];
            $fileType = $_FILES['employeeImage']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            // Allowed file extensions
            $allowedExtensions = array('jpg', 'jpeg', 'png');
            
            // Validate file type
            if(!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Invalid file type. Only JPG, JPEG, and PNG files are allowed.");
            }
            
            // Validate file size (max 2MB)
            if($fileSize > 2 * 1024 * 1024) {
                throw new Exception("File size exceeds the maximum limit of 2MB.");
            }
            
            // Read file data directly without processing
            // This matches how images are handled in addemployees.php
            $imageData = file_get_contents($fileTmpPath);
            
            // Prepare image data for SQL
            $imageSQL = ", `Image`='" . $conn->real_escape_string($imageData) . "'";
        }
        
        // Prepare SQL statement to update employee details including rest days and image if uploaded
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
                `Sunday_Rest`=$sundayRest" . $imageSQL . "
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
            
            <!-- Employee Image Display and Upload -->
            <div style="text-align: center; margin-bottom: 20px;">
                <?php if (!empty($row['Image']) && $row['Image'] != null): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['Image']); ?>" alt="Employee Image" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #4F6F52; margin-bottom: 10px;">
                <?php else: ?>
                    <div style="width: 120px; height: 120px; border-radius: 50%; background-color: #4F6F52; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                        <i class="fas fa-user" style="color: white; font-size: 3rem;"></i>
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 10px;">
                    <label for="employeeImage" style="display: inline-block; background-color: #4F6F52; color: white; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                        <i class="fas fa-camera"></i> Change Photo
                    </label>
                    <input type="file" id="employeeImage" name="employeeImage" style="display: none;" accept="image/jpeg, image/jpg, image/png">
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">Allowed formats: JPG, JPEG, PNG (Max: 2MB)</p>
                    <div id="selectedFileName" style="font-size: 13px; margin-top: 5px;"></div>
                </div>
            </div>
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo $row['Last Name']; ?>" pattern="[A-Za-z\- ]+" title="Only letters, dashes, and spaces are allowed" required><br>
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo $row['First Name']; ?>" pattern="[A-Za-z\- ]+" title="Only letters, dashes, and spaces are allowed" required><br>
            <label for="middleName">Middle Name:</label>
            <input type="text" id="middleName" name="middleName" value="<?php echo $row['Middle Name']; ?>" pattern="[A-Za-z\-/ ]+" title="Only letters, dashes, spaces, and forward slash (/) for N/A are allowed"><br>
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
            <input type="number" id="age" name="age" value="<?php echo $row['Age']; ?>" min="1" max="999" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" title="Age must be between 1 and 999"><br>
            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday" value="<?php echo $row['Birthday']; ?>"><br>
            <label for="emailAddress">Email Address:</label>
            <input type="email" id="emailAddress" name="emailAddress" value="<?php echo $row['Email Address']; ?>"><br>
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo $row['Address']; ?>"><br>
            <label for="contactNumber">Contact Number:</label>
            <input type="tel" id="contactNumber" name="contactNumber" value="<?php echo $row['Contact Number']; ?>" pattern="09[0-9]{9}" maxlength="11" title="Contact Number must be 11 digits and start with 09"><br>
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
        // File upload functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add validation for name fields (only letters, dashes, and spaces)
            const nameFields = ['lastName', 'firstName', 'middleName'];
            // Different patterns for different name fields
            const lastNamePattern = /^[A-Za-z\- ]*$/;
            const firstNamePattern = /^[A-Za-z\- ]*$/;
            const middleNamePattern = /^[A-Za-z\-/ ]*$/;
            
            // Add validation for contact number (11 digits starting with 09)
            const contactNumberInput = document.getElementById('contactNumber');
            contactNumberInput.addEventListener('input', function(e) {
                const value = e.target.value;
                
                // Remove any non-digit characters
                let digitsOnly = value.replace(/\D/g, '');
                
                // Ensure it starts with 09
                if (digitsOnly.length >= 2 && digitsOnly.substring(0, 2) !== '09') {
                    digitsOnly = '09' + digitsOnly.substring(2);
                }
                
                // Limit to 11 digits
                if (digitsOnly.length > 11) {
                    digitsOnly = digitsOnly.substring(0, 11);
                }
                
                // Update the input value if it's different
                if (value !== digitsOnly) {
                    e.target.value = digitsOnly;
                }
            });
            
            nameFields.forEach(field => {
                const input = document.getElementById(field);
                
                // Add input event listener for real-time validation
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    // Select the appropriate pattern based on the field
                    let pattern;
                    if (field === 'lastName') {
                        pattern = lastNamePattern;
                    } else if (field === 'firstName') {
                        pattern = firstNamePattern;
                    } else { // middleName
                        pattern = middleNamePattern;
                    }
                    
                    // If the input doesn't match our pattern
                    if (!pattern.test(value)) {
                        // Find the invalid character (the last character entered)
                        const invalidChar = value.charAt(value.length - 1);
                        
                        // Remove the invalid character
                        e.target.value = value.substring(0, value.length - 1);
                        
                        // Show a brief error message
                        const fieldName = field === 'lastName' ? 'Last Name' : 
                                         field === 'firstName' ? 'First Name' : 'Middle Name';
                        
                        if (field === 'middleName') {
                            alert(`Invalid character '${invalidChar}' in ${fieldName}. Only letters, dashes, spaces, and forward slash (/) for N/A are allowed.`);
                        } else {
                            alert(`Invalid character '${invalidChar}' in ${fieldName}. Only letters, dashes, and spaces are allowed.`);
                        }
                    }
                });
            });
            
            // Handle file selection
            const fileInput = document.getElementById('employeeImage');
            const fileNameDisplay = document.getElementById('selectedFileName');
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2); // Convert to MB
                    
                    // Display file name and size
                    fileNameDisplay.innerHTML = `<span style="color: #4F6F52;"><i class="fas fa-check-circle"></i> ${fileName} (${fileSize} MB)</span>`;
                    
                    // Check file size
                    if (this.files[0].size > 2 * 1024 * 1024) {
                        fileNameDisplay.innerHTML += `<br><span style="color: #dc3545;"><i class="fas fa-exclamation-circle"></i> File exceeds 2MB limit!</span>`;
                    }
                } else {
                    fileNameDisplay.textContent = '';
                }
            });
            
            // Rest days checkbox functionality
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
