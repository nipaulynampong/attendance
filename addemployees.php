<?php
// Start session at the very beginning
session_start();

// PHP code to insert new employee data into the database

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Escape user inputs for security
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $middleName = $conn->real_escape_string($_POST['middleName']);
    $suffix = $conn->real_escape_string($_POST['suffix']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $age = $conn->real_escape_string($_POST['age']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $emailAddress = $conn->real_escape_string($_POST['emailAddress']);
    $address = $conn->real_escape_string($_POST['address']);
    $contactNumber = $conn->real_escape_string($_POST['contactNumber']);
    $department = $conn->real_escape_string($_POST['Department']);
    $employeeID = $conn->real_escape_string($_POST['employeeID']);

    // Get rest days from form
    $mondayRest = isset($_POST['Monday_Rest']) ? 1 : 0;
    $tuesdayRest = isset($_POST['Tuesday_Rest']) ? 1 : 0;
    $wednesdayRest = isset($_POST['Wednesday_Rest']) ? 1 : 0;
    $thursdayRest = isset($_POST['Thursday_Rest']) ? 1 : 0;
    $fridayRest = isset($_POST['Friday_Rest']) ? 1 : 0;
    $saturdayRest = isset($_POST['Saturday_Rest']) ? 1 : 0;
    $sundayRest = isset($_POST['Sunday_Rest']) ? 1 : 0;

    // Generate QR Code
    $qrCodeURL = generateQRCodeURL($employeeID);
    $qrCodeImage = file_get_contents($qrCodeURL);
    $escapedQRCodeImage = $conn->real_escape_string($qrCodeImage);

    // Check if file is uploaded successfully
    if (isset($_FILES['employeeImage']) && $_FILES['employeeImage']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['employeeImage']['tmp_name'];
        $fileName = $_FILES['employeeImage']['name'];
        $fileSize = $_FILES['employeeImage']['size'];
        $fileType = $_FILES['employeeImage']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allowed file extensions
        $allowedExtensions = array('jpg', 'jpeg', 'png');

        // Check if the uploaded file has an allowed extension
        if (in_array($fileExtension, $allowedExtensions)) {
            // Read file data
            $imageData = file_get_contents($fileTmpPath);
            // Escape image data for database insertion
            $escapedImageData = $conn->real_escape_string($imageData);

            // SQL insert query
            $sql = "INSERT INTO employee (`EmployeeID`, `Last Name`, `First Name`, `Middle Name`, `Suffix`, `Age`, `Birthday`, `Address`, `Gender`, `Contact Number`, `Email Address`, `Department`, `Monday_Rest`, `Tuesday_Rest`, `Wednesday_Rest`, `Thursday_Rest`, `Friday_Rest`, `Saturday_Rest`, `Sunday_Rest`, `Image`, `QRCode`)
            VALUES ('$employeeID', '$lastName', '$firstName', '$middleName', '$suffix', '$age', '$birthday', '$address', '$gender', '$contactNumber', '$emailAddress', '$department', '$mondayRest', '$tuesdayRest', '$wednesdayRest', '$thursdayRest', '$fridayRest', '$saturdayRest', '$sundayRest', '$escapedImageData', '$escapedQRCodeImage')";

            // Execute query
            if ($conn->query($sql) === TRUE) {
                // Set success flag in session
                $_SESSION['employee_added'] = true;
                
                // Redirect to prevent form resubmission
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Invalid file type. Allowed types: jpg, jpeg, png";
        }
    } else {
        echo "Error uploading file";
    }

    // Close connection
    $conn->close();
}

// Function to generate QR Code URL
function generateQRCodeURL($employeeID) {
    // Generate QR Code URL using an online QR code generation service
    return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . $employeeID;
}

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

// Query to get departments
$sqlDepartments = "SELECT * FROM department";
$resultDepartments = $conn->query($sqlDepartments);
$departments = array();
if ($resultDepartments->num_rows > 0) {
    while($row = $resultDepartments->fetch_assoc()) {
        $departments[] = $row['Department'];
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information and Attendance Management System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins';
            background-color: white;
            color: black;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 1100px;
            padding: 40px;
            background-color: #f5efe6;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            position: relative;
            margin: 10px auto;
        }

        h2 {
            position: absolute;
            top: 0;
            left: 0;
            margin: 0;
            padding: 20px;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start; /* Align items to the top */
            margin-bottom: 20px;
            padding: 10px 0; /* Add padding */
        }

        .form-row .input-group {
            width: calc(25% - 10px);
            display: flex;
            flex-direction: column;
        }

        .form-row label {
            margin-bottom: 5px;
            text-align: left; /* Align label text to the left */
        }

        .form-row input,
        .form-row select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
        }

        .form-row input[type="file"] {
        width: 500px; /* Increase the width */
        }


        .circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 2px solid black;
            margin: 0 auto;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .circle img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .form-container button {
            width: 100%;
            padding: 15px 0;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Employee</h2><br><br>
        
        <?php
        // Display success message if employee was added successfully
        if (isset($_SESSION['employee_added']) && $_SESSION['employee_added'] === true) {
            echo '<div id="success-message" style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; border-left: 5px solid #28a745;">
                <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                <strong>Success!</strong> The employee was successfully added to the database.
            </div>';
            
            // Clear the session variable
            unset($_SESSION['employee_added']);
            
            // Add JavaScript to auto-hide the message after 5 seconds
            echo '<script>
                setTimeout(function() {
                    var successMessage = document.getElementById("success-message");
                    if (successMessage) {
                        successMessage.style.opacity = "1";
                        var fadeEffect = setInterval(function() {
                            if (successMessage.style.opacity > 0) {
                                successMessage.style.opacity -= 0.1;
                            } else {
                                clearInterval(fadeEffect);
                                successMessage.style.display = "none";
                            }
                        }, 100);
                    }
                }, 5000);
            </script>';
        }
        ?>
        
        <!-- Employee Form -->
        <form id="employeeForm" enctype="multipart/form-data" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-row">
                <div class="input-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Enter Last Name" required>
                </div>
                <div class="input-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" placeholder="Enter First Name" required>
                </div>
                <div class="input-group">
                    <label for="middleName">Middle Name:</label>
                    <input type="text" id="middleName" name="middleName" placeholder="Type N/A if none" >
                </div>
                <div class="input-group">
                    <label for="suffix">Suffix:</label>
                    <select id="suffix" name="suffix">
                        <option value="N/A">N/A</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                    </select>
                </div>
            </div>
            <!-- Add more form rows here -->
            <div class="form-row">
                <div class="input-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" placeholder="Enter Age" required>
                </div>
                <div class="input-group">
                    <label for="birthday">Birthday:</label>
                    <input type="date" id="birthday" name="birthday" required>
                </div>
                <div class="input-group">
                    <label for="emailAddress">Email Address:</label>
                    <input type="email" id="emailAddress" name="emailAddress" placeholder="Enter Email Addess" required>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" placeholder="Enter Address" required>
                </div>
                <div class="input-group">
                    <label for="contactNumber">Contact Number:</label>
                    <input type="text" id="contactNumber" name="contactNumber" placeholder="Enter Contact Number" required>
                </div>
                <div class="input-group">
                    <label for="Department">Department:</label>
                    <select id="Department" name="Department">
                        <?php foreach($departments as $dept): ?>
                            <option value="<?php echo $dept; ?>"><?php echo $dept; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="employeeID">Employee ID:</label>
                    <input type="number" id="employeeID" name="employeeID" placeholder="Enter Employee ID" required>
                    <button type="button" onclick="generateQRCode()">Generate QR Code</button>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="employeeImage">Upload Employee Image:</label>
                    <input type="file" id="employeeImage" name="employeeImage" accept="image/*">
                </div>
                <div class="input-group">
                    <label for="qrCode">QR Code:</label>
                    <img id="qrCode" src="" alt="QR Code" style="display: none;">
                    <!-- Preview button temporarily hidden -->
                    <!-- <button type="button" onclick="previewID()" style="margin-top: 10px; background-color: #007bff;">Preview ID Card</button> -->
                </div>
            </div>
            
            <!-- Rest Days Section -->
            <div class="form-row" style="margin-top: 20px;">
                <div class="input-group" style="width: 60%; background-color: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <label style="font-size: 18px; color: #4F6F52; margin-bottom: 15px;">
                        <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>
                        <strong>Rest Days Configuration</strong>
                    </label>
                    <p style="margin-bottom: 15px; color: #666; font-size: 14px;">Select the days when the employee will NOT work. These will be considered as their rest days.</p>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                        <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 12px; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                            <input type="checkbox" id="Monday_Rest" name="Monday_Rest" style="width: auto; margin-right: 5px; cursor: pointer;">
                            <label for="Monday_Rest" style="margin-bottom: 0; cursor: pointer;">Monday</label>
                        </div>
                        <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 12px; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                            <input type="checkbox" id="Tuesday_Rest" name="Tuesday_Rest" style="width: auto; margin-right: 5px; cursor: pointer;">
                            <label for="Tuesday_Rest" style="margin-bottom: 0; cursor: pointer;">Tuesday</label>
                        </div>
                        <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 12px; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                            <input type="checkbox" id="Wednesday_Rest" name="Wednesday_Rest" style="width: auto; margin-right: 5px; cursor: pointer;">
                            <label for="Wednesday_Rest" style="margin-bottom: 0; cursor: pointer;">Wednesday</label>
                        </div>
                        <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 12px; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                            <input type="checkbox" id="Thursday_Rest" name="Thursday_Rest" style="width: auto; margin-right: 5px; cursor: pointer;">
                            <label for="Thursday_Rest" style="margin-bottom: 0; cursor: pointer;">Thursday</label>
                        </div>
                        <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 12px; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                            <input type="checkbox" id="Friday_Rest" name="Friday_Rest" style="width: auto; margin-right: 5px; cursor: pointer;">
                            <label for="Friday_Rest" style="margin-bottom: 0; cursor: pointer;">Friday</label>
                        </div>
                        <div style="display: flex; align-items: center; background-color: #4F6F52; color: white; padding: 8px 12px; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #4F6F52;" class="checked">
                            <input type="checkbox" id="Saturday_Rest" name="Saturday_Rest" style="width: auto; margin-right: 5px; cursor: pointer;" checked>
                            <label for="Saturday_Rest" style="margin-bottom: 0; cursor: pointer;">Saturday</label>
                        </div>
                        <div style="display: flex; align-items: center; background-color: #4F6F52; color: white; padding: 8px 12px; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #4F6F52;" class="checked">
                            <input type="checkbox" id="Sunday_Rest" name="Sunday_Rest" style="width: auto; margin-right: 5px; cursor: pointer;" checked>
                            <label for="Sunday_Rest" style="margin-bottom: 0; cursor: pointer;">Sunday</label>
                        </div>
                    </div>
                </div>
                
                <!-- Rest Days Summary -->
                <div class="input-group" style="width: 35%; margin-left: 20px; background-color: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <label style="font-size: 18px; color: #4F6F52; margin-bottom: 15px;">
                        <i class="fas fa-calendar-check" style="margin-right: 8px;"></i>
                        <strong>Work Schedule Summary</strong>
                    </label>
                    
                    <div id="schedulePreview" style="color: #333;">
                        <p style="font-weight: bold; color: #4F6F52; margin-bottom: 15px;">Employee's Schedule Preview</p>
                        
                        <div id="restDaysList" style="margin-bottom: 15px; background-color: #f8f9fa; padding: 10px; border-radius: 8px; border-left: 4px solid #dc3545;">
                            <p style="margin-bottom: 5px;"><i class="fas fa-moon" style="color: #dc3545; margin-right: 5px;"></i> <b>Rest Days:</b></p>
                            <div id="restDaysText" style="padding: 5px 10px; color: #666;">None selected</div>
                        </div>
                        
                        <div id="workDaysList" style="margin-bottom: 15px; background-color: #f8f9fa; padding: 10px; border-radius: 8px; border-left: 4px solid #28a745;">
                            <p style="margin-bottom: 5px;"><i class="fas fa-briefcase" style="color: #28a745; margin-right: 5px;"></i> <b>Work Days:</b></p>
                            <div id="workDaysText" style="padding: 5px 10px;">Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday</div>
                        </div>
                        
                        <div style="margin-top: 15px; padding: 10px; background-color: #f0f7ff; border-radius: 8px; border-left: 4px solid #0d6efd;">
                            <p style="font-size: 14px; color: #0d6efd; margin-bottom: 0;">
                                <i class="fas fa-info-circle"></i> <b>Note:</b> Employee attendance will be validated against this schedule.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit">Submit</button>
        </form>
    </div>
    
    <script>
        function generateQRCode() {
            var employeeID = document.getElementById('employeeID').value;
            if (!employeeID) {
                alert('Please enter an Employee ID first');
                return;
            }

            fetch('process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    qr_data: employeeID
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update QR code image
                    var qrCodeURL = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + employeeID;
                    document.getElementById('qrCode').src = qrCodeURL;
                    document.getElementById('qrCode').style.display = 'block';
                } else {
                    throw new Error(data.message || 'Failed to generate QR code');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Error generating QR code. Please try again.');
            });
        }

        function generateQRCodeURL(employeeID) {
            // Generate QR Code URL using an online QR code generation service
            return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + employeeID;
        }
         // Function to validate input fields
         function validateForm() {
            var lastName = document.getElementById('lastName').value;
            var firstName = document.getElementById('firstName').value;
            var middleName = document.getElementById('middleName').value;
            var suffix = document.getElementById('suffix').value;
            var gender = document.getElementById('gender').value;
            var age = document.getElementById('age').value;
            var birthday = document.getElementById('birthday').value;
            var emailAddress = document.getElementById('emailAddress').value;
            var address = document.getElementById('address').value;
            var contactNumber = document.getElementById('contactNumber').value;
            var employeeID = document.getElementById('employeeID').value;
            
            // Regular expressions for validation
            var lettersOnly = /^[A-Za-z]+$/;
            var numbersOnly = /^[0-9]+$/;
            
            // Validate Last Name, First Name, Middle Name
            if (lastName.match(numbersOnly) || firstName.match(numbersOnly) || middleName.match(numbersOnly)) {
                alert('Last Name, First Name, and Middle Name must contain only letters.');
                return false;
            }
            
            // Validate EmployeeID, Contact Number, Age
            if (!employeeID.match(numbersOnly) || !contactNumber.match(numbersOnly) || !age.match(numbersOnly)) {
                alert('Employee ID, Contact Number, and Age must contain only numbers.');
                return false;
            }
            
            // If all validations pass, return true
            return true;
        }
        
        // Add event listener to the form for validation on submit
        document.getElementById('employeeForm').addEventListener('submit', function(event) {
            // Prevent form submission if validation fails
            if (!validateForm()) {
                event.preventDefault();
            }
        });
        
        // Function to restrict input to letters only for certain fields
        function restrictLetters(event) {
            var input = event.target.value;
            var regex = /^[A-Za-z]+$/;
            if (!regex.test(input)) {
                event.target.value = input.replace(/[^A-Za-z]/g, '');
            }
        }
        
        // Function to restrict input to numbers only for certain fields
        function restrictNumbers(event) {
            var input = event.target.value;
            var regex = /^[0-9]+$/;
            if (!regex.test(input)) {
                event.target.value = input.replace(/[^0-9]/g, '');
            }
        }
        
        // Add event listeners to input fields for restricting input
        document.getElementById('lastName').addEventListener('input', restrictLetters);
        document.getElementById('firstName').addEventListener('input', restrictLetters);
        document.getElementById('middleName').addEventListener('input', restrictLetters);
        document.getElementById('age').addEventListener('input', restrictNumbers);
        document.getElementById('contactNumber').addEventListener('input', restrictNumbers);
        document.getElementById('employeeID').addEventListener('input', restrictNumbers);
    
        // Make the rest day checkboxes more interactive
        document.addEventListener('DOMContentLoaded', function() {
            // Get all rest day checkboxes
            const restDayCheckboxes = document.querySelectorAll('[id$="_Rest"]');
            
            // Function to update the schedule summary
            function updateScheduleSummary() {
                const allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                const restDays = [];
                const workDays = [];
                
                // Check which days are selected as rest days
                allDays.forEach(day => {
                    const checkbox = document.getElementById(day + '_Rest');
                    if (checkbox.checked) {
                        restDays.push(day);
                    } else {
                        workDays.push(day);
                    }
                });
                
                // Update the rest days text
                const restDaysText = document.getElementById('restDaysText');
                if (restDays.length === 0) {
                    restDaysText.textContent = 'None selected';
                    restDaysText.style.color = '#666';
                } else {
                    restDaysText.textContent = restDays.join(', ');
                    restDaysText.style.color = '#4F6F52';
                }
                
                // Update the work days text
                const workDaysText = document.getElementById('workDaysText');
                if (workDays.length === 0) {
                    workDaysText.textContent = 'None (employee has no work days!)';
                    workDaysText.style.color = 'red';
                } else {
                    workDaysText.textContent = workDays.join(', ');
                    workDaysText.style.color = '#333';
                }
            }
            
            // Add event listener to each checkbox
            restDayCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const container = this.parentElement;
                    
                    if (this.checked) {
                        // Styling for selected rest day
                        container.style.backgroundColor = '#4F6F52';
                        container.style.color = 'white';
                        container.style.borderColor = '#4F6F52';
                    } else {
                        // Reset styling when unchecked
                        container.style.backgroundColor = '#f8f8f8';
                        container.style.color = 'black';
                        container.style.borderColor = '#ddd';
                    }
                    
                    // Update the schedule summary
                    updateScheduleSummary();
                });
                
                // Apply initial styling based on default checked state
                if (checkbox.checked) {
                    checkbox.parentElement.style.backgroundColor = '#4F6F52';
                    checkbox.parentElement.style.color = 'white';
                    checkbox.parentElement.style.borderColor = '#4F6F52';
                }
            });
            
            // Initialize the schedule summary
            updateScheduleSummary();
        });

        function previewID() {
            // Get form values
            const employeeID = document.getElementById('employeeID').value;
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const department = document.getElementById('Department').value;
            
            // Basic validation
            if (!employeeID || !firstName || !lastName || !department) {
                alert('Please fill in the required fields (Employee ID, First Name, Last Name, and Department) before previewing the ID card.');
                return;
            }

            // Open ID preview in a new window
            const previewWindow = window.open(`id.php?id=${employeeID}`, 'ID Preview', 'width=800,height=600');
        }
    </script>
</body>
</html>
