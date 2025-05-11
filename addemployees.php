<?php
// Start session at the very beginning
session_start();

// Function defined at the top to avoid redeclaration
function generateQRCodeURL($employeeID) {
    return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . $employeeID;
}

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

    // Save QR code to local directory
    $qrCodeDir = 'qrcodes/';
    if (!file_exists($qrCodeDir)) {
        mkdir($qrCodeDir, 0777, true);
    }
    $qrCodeFileName = $qrCodeDir . $employeeID . '.png';
    file_put_contents($qrCodeFileName, $qrCodeImage);

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

            // Clean up temporary file
            unlink($qrCodeURL);
            
            $sql = "INSERT INTO employee (`EmployeeID`, `Last Name`, `First Name`, `Middle Name`, `Suffix`, `Age`, `Birthday`, `Address`, `Gender`, `Contact Number`, `Email Address`, `Department`, `Monday_Rest`, `Tuesday_Rest`, `Wednesday_Rest`, `Thursday_Rest`, `Friday_Rest`, `Saturday_Rest`, `Sunday_Rest`, `Image`, `QRCode`)
            VALUES ('$employeeID', '$lastName', '$firstName', '$middleName', '$suffix', '$age', '$birthday', '$address', '$gender', '$contactNumber', '$emailAddress', '$department', '$mondayRest', '$tuesdayRest', '$wednesdayRest', '$thursdayRest', '$fridayRest', '$saturdayRest', '$sundayRest', '$escapedImageData', '$escapedQRCodeImage')";

            
            if ($conn->query($sql) === TRUE) {
               
                $_SESSION['employee_added'] = true;
                
              
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Invalid file type. Allowed types: jpg, jpeg, png";
        }
    } else {
        // No image uploaded, insert with NULL image
        $sql = "INSERT INTO employee (`EmployeeID`, `Last Name`, `First Name`, `Middle Name`, `Suffix`, `Age`, `Birthday`, `Address`, `Gender`, `Contact Number`, `Email Address`, `Department`, `Monday_Rest`, `Tuesday_Rest`, `Wednesday_Rest`, `Thursday_Rest`, `Friday_Rest`, `Saturday_Rest`, `Sunday_Rest`, `QRCode`)
        VALUES ('$employeeID', '$lastName', '$firstName', '$middleName', '$suffix', '$age', '$birthday', '$address', '$gender', '$contactNumber', '$emailAddress', '$department', '$mondayRest', '$tuesdayRest', '$wednesdayRest', '$thursdayRest', '$fridayRest', '$saturdayRest', '$sundayRest', '$escapedQRCodeImage')";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['employee_added'] = true;
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

   
    $conn->close();
}

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
$sqlDepartments = "SELECT * FROM department WHERE Status = 'Active'";
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
            background-color: #f5efe6;
            color: black;
            margin: 0;
            padding: 10px;
            box-sizing: border-box;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            font-size: 14px;
        }

        h2 {
            margin: 0 0 15px 0;
            padding: 10px 0;
            font-size: 20px;
            color: #3A4D39;
            border-bottom: 2px solid #4F6F52;
            text-align: left;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap; /* Allow wrapping on small screens */
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 5px 0;
        }

        .form-row .input-group {
            width: calc(25% - 8px);
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .form-row .input-group {
                width: calc(33.33% - 8px);
            }
        }

        @media (max-width: 992px) {
            .form-row .input-group {
                width: calc(50% - 8px);
            }
        }

        @media (max-width: 768px) {
            .form-row .input-group {
                width: 100%;
            }
        }

        .form-row label {
            margin-bottom: 6px;
            text-align: left;
            color: #3A4D39;
            font-weight: 600;
            font-size: 16px;
        }

        .form-row input,
        .form-row select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-family: 'Poppins', Arial, sans-serif;
            font-size: 15px;
        }

        .form-row input[type="file"] {
            width: 100%;
            max-width: 500px;
            padding: 8px;
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

        button {
            width: 100%;
            padding: 14px 0;
            border: none;
            border-radius: 8px;
            background-color: #4F6F52;
            color: white;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: -10px; /* Move button up a bit */
        }

        button:hover {
            background-color: #3A4D39;
        }

        #success-message {
            background-color: #d4edda; 
            color: #155724; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px; 
            text-align: center; 
            border-left: 5px solid #28a745;
        }
    </style>
</head>
<body>
    <h2>Add New Employee</h2>
        
        <?php
        // Display success message if employee was added successfully
        if (isset($_SESSION['employee_added']) && $_SESSION['employee_added'] === true) {
        echo '<div id="success-message">
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
                    <input type="text" id="lastName" name="lastName" placeholder="Enter Last Name" pattern="[A-Za-z\s\-]+" title="Only letters, spaces, and hyphens are allowed" oninput="validateName(this, 'Last Name')" required>
                </div>
                <div class="input-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" placeholder="Enter First Name" pattern="[A-Za-z\s\-]+" title="Only letters, spaces, and hyphens are allowed" oninput="validateName(this, 'First Name')" required>
                </div>
                <div class="input-group">
                    <label for="middleName">Middle Name:</label>
                    <div style="display: flex; gap: 10px; width: 100%;">
                        <select id="middleNameOption" style="width: 80px;" onchange="handleMiddleNameOption(this.value)">
                            <option value="custom">Custom</option>
                            <option value="N/A">N/A</option>
                        </select>
                        <input type="text" id="middleName" name="middleName" placeholder="Enter Middle Name" pattern="[A-Za-z\s\-\/]+" title="Only letters, spaces, hyphens, and forward slash (/) for N/A are allowed" oninput="validateMiddleName(this, 'Middle Name')" style="flex: 1;">
                    </div>
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
                    <input type="text" id="age" name="age" maxlength="3" oninput="validateAge(this)" required>
                </div>
                <div class="input-group">
                    <label for="birthday">Birthday:</label>
                    <input type="date" id="birthday" name="birthday" onchange="validateBirthday(this)" required>
                </div>
                <div class="input-group">
                    <label for="emailAddress">Email Address:</label>
                    <input type="email" id="emailAddress" name="emailAddress" placeholder="Enter Email Address (Optional)">
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" placeholder="Enter Address" required>
                </div>
                <div class="input-group">
                    <label for="contactNumber">Contact Number:</label>
                    <input type="text" id="contactNumber" name="contactNumber" maxlength="11" placeholder="Enter 11-digit number" oninput="validateContactNumber(this)" required>
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
                    <input type="text" id="employeeID" name="employeeID" oninput="formatEmployeeID(this)" placeholder="Enter numbers only (max 20 chars)" required>
                    <button type="button" onclick="generateQRCode()">Generate QR Code</button>
                    <div class="qr-note">
                        <small><i class="fas fa-info-circle"></i> Note: QR Code generation may take a moment. The QR Code image will be saved in the 'qrcodes' folder.</small>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="employeeImage">Upload Employee Image:</label>
                    <div class="custom-file-upload">
                        <input type="file" id="employeeImage" name="employeeImage" accept="image/jpeg, image/png, image/jpg" onchange="validateImageFile(this)" class="custom-file-input">
                        <label for="employeeImage" class="custom-file-label" style="color: white !important;">Choose image</label>
                        <span class="file-chosen">No image chosen</span>
                    </div>
                    <div class="file-info-container">
                        <small class="file-info">Accepted formats: JPG, JPEG, PNG (Max: 2MB)</small>
                        <small class="file-info">Recommended dimensions: 300x300 pixels (1:1 ratio for ID photo)</small>
                        <small class="file-info note">Note: Employee photo submission is optional and may be provided at a later date if needed.</small>
                    </div>
                </div>
                <div class="input-group">
                    <label for="qrCode">QR Code:</label>
                    <img id="qrCode" src="" alt="QR Code" style="display: none;">
                    <!-- Preview button temporarily hidden -->
                    <!-- <button type="button" onclick="previewID()" style="margin-top: 10px; background-color: #007bff;">Preview ID Card</button> -->
                </div>
            </div>
            
            <!-- Rest Days Section -->
        <div class="form-row" style="margin-top: 15px;">
            <div class="input-group" style="width: 100%; background-color: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <label style="font-size: 18px; color: #4F6F52; margin-bottom: 15px;">
                    <i class="fas fa-calendar-alt" style="margin-right: 10px;"></i>
                        <strong>Rest Days Configuration</strong>
                    </label>
                <p style="margin-bottom: 15px; color: #666; font-size: 15px; line-height: 1.4;">Select the days when the employee will NOT work. These will be considered as their rest days.</p>
                
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
                    <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 15px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                        <input type="checkbox" id="Monday_Rest" name="Monday_Rest" style="width: auto; margin-right: 8px; cursor: pointer; transform: scale(1.2);">
                        <label for="Monday_Rest" style="margin-bottom: 0; cursor: pointer; font-size: 15px;">Monday</label>
                        </div>
                    <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 15px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                        <input type="checkbox" id="Tuesday_Rest" name="Tuesday_Rest" style="width: auto; margin-right: 8px; cursor: pointer; transform: scale(1.2);">
                        <label for="Tuesday_Rest" style="margin-bottom: 0; cursor: pointer; font-size: 15px;">Tuesday</label>
                        </div>
                    <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 15px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                        <input type="checkbox" id="Wednesday_Rest" name="Wednesday_Rest" style="width: auto; margin-right: 8px; cursor: pointer; transform: scale(1.2);">
                        <label for="Wednesday_Rest" style="margin-bottom: 0; cursor: pointer; font-size: 15px;">Wednesday</label>
                        </div>
                    <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 15px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                        <input type="checkbox" id="Thursday_Rest" name="Thursday_Rest" style="width: auto; margin-right: 8px; cursor: pointer; transform: scale(1.2);">
                        <label for="Thursday_Rest" style="margin-bottom: 0; cursor: pointer; font-size: 15px;">Thursday</label>
                        </div>
                    <div style="display: flex; align-items: center; background-color: #f8f8f8; padding: 8px 15px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #ddd;">
                        <input type="checkbox" id="Friday_Rest" name="Friday_Rest" style="width: auto; margin-right: 8px; cursor: pointer; transform: scale(1.2);">
                        <label for="Friday_Rest" style="margin-bottom: 0; cursor: pointer; font-size: 15px;">Friday</label>
                        </div>
                    <div style="display: flex; align-items: center; background-color: #4F6F52; color: white; padding: 8px 15px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #4F6F52;" class="checked">
                        <input type="checkbox" id="Saturday_Rest" name="Saturday_Rest" style="width: auto; margin-right: 8px; cursor: pointer; transform: scale(1.2);" checked>
                        <label for="Saturday_Rest" style="margin-bottom: 0; cursor: pointer; font-size: 15px; color: white !important; font-weight: 600; text-shadow: 0 1px 1px rgba(0,0,0,0.2);">Saturday</label>
                        </div>
                    <div style="display: flex; align-items: center; background-color: #4F6F52; color: white; padding: 8px 15px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #4F6F52;" class="checked">
                        <input type="checkbox" id="Sunday_Rest" name="Sunday_Rest" style="width: auto; margin-right: 8px; cursor: pointer; transform: scale(1.2);" checked>
                        <label for="Sunday_Rest" style="margin-bottom: 0; cursor: pointer; font-size: 15px; color: white !important; font-weight: 600; text-shadow: 0 1px 1px rgba(0,0,0,0.2);">Sunday</label>
                    </div>
                </div>
                    </div>
                </div>
                
                <!-- Rest Days Summary -->
        <div class="form-row" style="margin-top: 15px;">
            <div class="input-group" style="width: 100%; background-color: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <label style="font-size: 18px; color: #4F6F52; margin-bottom: 15px;">
                    <i class="fas fa-calendar-check" style="margin-right: 10px;"></i>
                        <strong>Work Schedule Summary</strong>
                    </label>
                    
                    <div id="schedulePreview" style="color: #333;">
                    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                        <div id="restDaysList" style="flex: 1; min-width: 250px; margin-bottom: 15px; background-color: #f8f9fa; padding: 15px; border-radius: 10px; border-left: 4px solid #dc3545;">
                            <p style="margin-bottom: 10px; font-size: 16px;"><i class="fas fa-moon" style="color: #dc3545; margin-right: 8px;"></i> <b>Rest Days:</b></p>
                            <div id="restDaysText" style="padding: 5px 10px; color: #666; font-size: 15px;">None selected</div>
                        </div>
                        
                        <div id="workDaysList" style="flex: 1; min-width: 250px; margin-bottom: 15px; background-color: #f8f9fa; padding: 15px; border-radius: 10px; border-left: 4px solid #28a745;">
                            <p style="margin-bottom: 10px; font-size: 16px;"><i class="fas fa-briefcase" style="color: #28a745; margin-right: 8px;"></i> <b>Work Days:</b></p>
                            <div id="workDaysText" style="padding: 5px 10px; font-size: 15px;">Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday</div>
                        </div>
                        </div>
                        
                    <div style="margin-top: 15px; padding: 15px; background-color: #f0f7ff; border-radius: 10px; border-left: 4px solid #0d6efd;">
                        <p style="font-size: 15px; color: #0d6efd; margin-bottom: 0;">
                                <i class="fas fa-info-circle"></i> <b>Note:</b> Employee attendance will be validated against this schedule.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit">Submit</button>
        </form>
    
    <script>
        function generateQRCode() {
            var employeeID = document.getElementById('employeeID').value;
            if (!employeeID) {
                alert('Please enter an Employee ID first');
                return;
            }

            fetch('save_qr.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    employeeID: employeeID
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('qrCode').src = data.qr_url;
                    document.getElementById('qrCode').style.display = 'block';
                    // Show success message with saved path
                    alert('QR code generated and saved to: ' + data.saved_path);
                } else {
                    throw new Error(data.message || 'Failed to generate QR code');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Error generating QR code. Please try again.');
            });
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
            var lettersOnly = /^[A-Z\s]+$/; // Allow spaces and uppercase letters
            var numbersOnly = /^[0-9]+$/;
            var emailPattern = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
            var employeeIDPattern = /^[A-Z]+-\d{4}-\d{4}$/; // Format: BSIT-2021-0991
            
            // Validate Middle Name (allow / and spaces)
            if (middleName && !middleName.match(/^[A-Za-z\s/]+$/)) {
                alert('Middle Name must contain only letters, spaces, and forward slashes.');
                return false;
            }
            
            // Validate Age (must be between 18 and 100)
            if (!age.match(/^\d{1,3}$/)) {
                alert('Age must be a number between 18 and 100.');
                return false;
            }
            
            // Validate if age is reasonable (between 18 and 100)
            var ageNum = parseInt(age);
            if (ageNum < 18 || ageNum > 100) {
                alert('Age must be between 18 and 100 years.');
                return false;
            }
            
            // Validate Contact Number (11 digits)
            if (!contactNumber.match(/^\d{11}$/)) {
                alert('Contact Number must be exactly 11 digits.');
                return false;
            }
            
            // Validate Employee ID format
            if (!employeeID.match(/^\d+(-\d+)*$/)) {
                alert('Employee ID must contain only numbers and dashes');
                return false;
            }
            
            // If all validations pass, return true
            return true;
        }
        
        // Function to automatically set birthday based on age
        function updateBirthday() {
            var age = document.getElementById('age').value;
            if (age && age.match(/^\d{1,3}$/)) {
                var currentDate = new Date();
                var ageNum = parseInt(age);
                
                // Only proceed if age is between 18 and 100
                if (ageNum >= 18 && ageNum <= 100) {
                    var currentYear = currentDate.getFullYear();
                    var birthYear = currentYear - ageNum;
                    
                    // Set birthday to January 1st of the calculated year
                    var birthday = new Date(birthYear, 0, 1);
                    document.getElementById('birthday').value = birthday.toISOString().split('T')[0];
                }
            }
        }
        
        // Function to format Employee ID
        function formatEmployeeID(input) {
            var value = input.value;
            // Remove any characters except numbers and dash
            value = value.replace(/[^\d-]/g, '');
            
            // Limit total length to 20 characters
            if (value.length > 20) {
                value = value.slice(0, 20);
            }
            
            input.value = value;
        }

        // Add event listeners when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize middle name dropdown
            const middleNameOption = document.getElementById('middleNameOption');
            if (middleNameOption) {
                // Set up initial state based on dropdown value
                handleMiddleNameOption(middleNameOption.value);
                
                // Add change event listener
                middleNameOption.addEventListener('change', function() {
                    handleMiddleNameOption(this.value);
                });
            }
            // Add input event listeners for number restrictions
            document.getElementById('age').addEventListener('input', function(e) {
                var input = e.target.value;
                // Only allow numbers
                e.target.value = input.replace(/\D/g, '');
                // Update birthday when age changes
                updateBirthday();
            });
            
            document.getElementById('contactNumber').addEventListener('input', function(e) {
                var input = e.target.value;
                // Only allow numbers
                e.target.value = input.replace(/\D/g, '');
                // Limit to 11 digits
                if (e.target.value.length > 11) {
                    e.target.value = e.target.value.slice(0, 11);
                }
            });
            
            // Add event listener to the form for validation on submit
            document.getElementById('employeeForm').addEventListener('submit', function(event) {
                if (!validateForm()) {
                    event.preventDefault();
                }
            });
            
            // Check if page is in an iframe and make minimal adjustments
            if (window.self !== window.top) {
                // Just adjust some spacing, no containers
                const title = document.querySelector('h2');
                if (title) {
                    title.style.marginBottom = '15px';
                }
                
                // Remove any shadows that might look like containers
                const whiteBoxes = document.querySelectorAll('.input-group[style*="box-shadow"]');
                whiteBoxes.forEach(box => {
                    box.style.boxShadow = 'none';
                    box.style.border = '1px solid #eee';
                });
            }
            
            // Add event listeners for rest day checkboxes
            const restDayCheckboxes = [
                'Monday_Rest', 'Tuesday_Rest', 'Wednesday_Rest', 
                'Thursday_Rest', 'Friday_Rest', 'Saturday_Rest', 'Sunday_Rest'
            ];
            
            // Function to update the work schedule summary
            function updateWorkScheduleSummary() {
                const restDays = [];
                const workDays = [];
                const allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                
                // Check which days are selected as rest days
                allDays.forEach(day => {
                    if (document.getElementById(day + '_Rest').checked) {
                        restDays.push(day);
                    } else {
                        workDays.push(day);
                    }
                });
                
                // Update the rest days display
                const restDaysText = document.getElementById('restDaysText');
                if (restDays.length > 0) {
                    restDaysText.textContent = restDays.join(', ');
                } else {
                    restDaysText.textContent = 'None selected';
                }
                
                // Update the work days display
                const workDaysText = document.getElementById('workDaysText');
                if (workDays.length > 0) {
                    workDaysText.textContent = workDays.join(', ');
                } else {
                    workDaysText.textContent = 'None - Employee will be on rest all days';
                }
            }
            
            // Add event listeners to all rest day checkboxes
            restDayCheckboxes.forEach(id => {
                document.getElementById(id).addEventListener('change', updateWorkScheduleSummary);
            });
            
            // Initialize the summary on page load
            updateWorkScheduleSummary();
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
    
    <!-- New script for iframe styling with larger fonts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.self !== window.top) {
                // Add custom styling for iframe view
                const customStyles = document.createElement('style');
                customStyles.innerHTML = `
                    /* Form container without scrolling */
                    #employeeForm {
                        padding: 15px;
                        height: auto;
                        overflow: visible;
                        background-color: #fff;
                        border-radius: 10px;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    }
                    
                    /* Larger heading */
                    h2 {
                        position: sticky;
                        top: 0;
                        background-color: #f5efe6;
                        z-index: 10;
                        font-size: 24px !important;
                        padding: 15px 0 !important;
                        margin-bottom: 20px !important;
                    }
                    
                    /* Larger labels and inputs */
                    .form-row label {
                        font-size: 16px !important;
                        margin-bottom: 8px !important;
                        color: #3A4D39 !important;
                        font-weight: 600 !important;
                    }
                    
                    .form-row input, .form-row select {
                        font-size: 15px !important;
                        padding: 10px 12px !important;
                        border-radius: 6px !important;
                    }
                    
                    /* Rest days section with larger text */
                    .form-row:nth-of-type(5) .input-group label {
                        font-size: 18px !important;
                    }
                    
                    .form-row:nth-of-type(5) .input-group p {
                        font-size: 15px !important;
                        line-height: 1.4;
                    }
                    
                    .form-row:nth-of-type(5) .input-group div[style*="display: flex"] label {
                        font-size: 14px !important;
                    }
                    
                    /* Work schedule section with larger text */
                    .form-row:nth-of-type(6) .input-group label {
                        font-size: 18px !important;
                    }
                    
                    .form-row:nth-of-type(6) .input-group p {
                        font-size: 15px !important;
                    }
                    
                    /* Custom file upload styling */
                    .custom-file-upload {
                        position: relative;
                        display: flex;
                        align-items: center;
                        width: 100%;
                        margin-bottom: 10px;
                    }
                    
                    .custom-file-input {
                        position: absolute;
                        width: 0.1px;
                        height: 0.1px;
                        opacity: 0;
                        overflow: hidden;
                        z-index: -1;
                    }
                    
                    .custom-file-label {
                        display: inline-block;
                        padding: 8px 16px;
                        background-color: #3A4D39; /* Darker background */
                        color: #FFFFFF; /* Pure white */
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        font-weight: 700; /* Bolder text */
                        transition: all 0.3s ease;
                        margin-right: 10px;
                        text-shadow: none; /* Remove text shadow */
                        letter-spacing: 0.5px;
                    }
                    
                    .custom-file-label:hover {
                        background-color: #3A4D39;
                    }
                    
                    .file-chosen {
                        color: #666;
                        font-size: 14px;
                    }
                    
                    /* File upload styling */
                    .file-info-container {
                        margin-top: 5px;
                        padding: 5px 8px;
                        background-color: #f8f9fa;
                        border-left: 3px solid #4F6F52;
                        border-radius: 3px;
                    }
                    
                    .file-info {
                        display: block;
                        font-size: 12px;
                        color: #666;
                        line-height: 1.4;
                    }
                    
                    .file-info.note, .qr-note small {
                        margin-top: 5px;
                        font-style: italic;
                        color: #4F6F52;
                    }
                    
                    .qr-note {
                        margin-top: 8px;
                        padding: 5px 8px;
                        background-color: #f8f9fa;
                        border-left: 3px solid #4F6F52;
                        border-radius: 3px;
                        width: 100%;
                    }
                    
                    .qr-note i {
                        margin-right: 5px;
                        color: #4F6F52;
                    }
                    
                    /* Make submit button larger and sticky */
                    button[type="submit"] {
                        position: sticky;
                        bottom: 0;
                        margin-top: 20px;
                        padding: 15px 0 !important;
                        font-size: 18px !important;
                        background-color: #4F6F52;
                        border-radius: 8px;
                    }
                `;
                document.head.appendChild(customStyles);
            }
        });
        
        // Function to validate contact number - limit to 11 digits, numbers only
        function validateContactNumber(input) {
            // Remove any non-numeric characters
            input.value = input.value.replace(/\D/g, '');
            
            // Enforce maximum length of 11 digits
            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
            }
            
            // Format hint for Philippine mobile numbers
            if (input.value.length > 0 && !input.value.startsWith('09')) {
                // Check if it's not already a valid format
                if (!(input.value.length === 11 && input.value.startsWith('63'))) {
                    // Provide a hint for proper format
                    const hint = document.getElementById('contact-number-hint');
                    if (!hint) {
                        const hintElement = document.createElement('small');
                        hintElement.id = 'contact-number-hint';
                        hintElement.style.color = '#dc3545';
                        hintElement.style.display = 'block';
                        hintElement.style.marginTop = '5px';
                        hintElement.innerHTML = '<i class="fas fa-info-circle"></i> Philippine mobile numbers typically start with 09';
                        input.parentNode.appendChild(hintElement);
                    }
                } else {
                    // Remove hint if it's now valid
                    const hint = document.getElementById('contact-number-hint');
                    if (hint) hint.remove();
                }
            } else {
                // Remove hint if it's now valid or empty
                const hint = document.getElementById('contact-number-hint');
                if (hint) hint.remove();
            }
        }
        
        // Function to validate age - limit to 3 numbers only
        function validateAge(input) {
            // Remove any non-numeric characters
            input.value = input.value.replace(/\D/g, '');
            
            // Enforce maximum length of 3 digits
            if (input.value.length > 3) {
                input.value = input.value.slice(0, 3);
            }
            
            // Validate reasonable age range (1-150)
            const age = parseInt(input.value);
            if (age > 150) {
                alert('Please enter a valid age (maximum 150)');
                input.value = '150';
            }
            
            // Update birthday if the function exists
            if (typeof updateBirthday === 'function') {
                updateBirthday();
            }
        }
        
        // Function to handle middle name dropdown selection
        function handleMiddleNameOption(value) {
            const middleNameInput = document.getElementById('middleName');
            
            if (value === 'N/A') {
                // Set the input value to N/A and make it readonly (not disabled)
                // Using readonly instead of disabled ensures the value is submitted with the form
                middleNameInput.value = 'N/A';
                middleNameInput.readOnly = true;
                middleNameInput.style.backgroundColor = '#f0f0f0';
            } else {
                // Enable the input and clear it if it was N/A
                middleNameInput.readOnly = false;
                middleNameInput.style.backgroundColor = '';
                if (middleNameInput.value === 'N/A') {
                    middleNameInput.value = '';
                }
            }
        }
        
        // Function to validate name fields - only allow letters, spaces, and hyphens
        function validateName(input, fieldName) {
            // Remove any characters that aren't letters, spaces, or hyphens
            const invalidChars = /[^A-Za-z\s\-]/g;
            if (invalidChars.test(input.value)) {
                // Store cursor position
                const cursorPos = input.selectionStart;
                
                // Replace invalid characters
                const oldValue = input.value;
                const newValue = oldValue.replace(invalidChars, '');
                input.value = newValue;
                
                // Adjust cursor position if characters were removed
                const cursorAdjust = oldValue.length - newValue.length;
                input.setSelectionRange(cursorPos - cursorAdjust, cursorPos - cursorAdjust);
                
                // Show warning message
                alert(fieldName + ' can only contain letters, spaces, and hyphens.');
            }
        }
        
        // Function to validate middle name - allow letters, spaces, hyphens, and forward slash for N/A
        function validateMiddleName(input, fieldName) {
            // Remove any characters that aren't letters, spaces, hyphens, or forward slash
            const invalidChars = /[^A-Za-z\s\-\/]/g;
            if (invalidChars.test(input.value)) {
                // Store cursor position
                const cursorPos = input.selectionStart;
                
                // Replace invalid characters
                const oldValue = input.value;
                const newValue = oldValue.replace(invalidChars, '');
                input.value = newValue;
                
                // Adjust cursor position if characters were removed
                const cursorAdjust = oldValue.length - newValue.length;
                input.setSelectionRange(cursorPos - cursorAdjust, cursorPos - cursorAdjust);
                
                // Show warning message
                alert(fieldName + ' can only contain letters, spaces, hyphens, and forward slash (/) for N/A.');
            }
        }
        
        // Function to validate birthday - ensure employee is at least 18 years old
        function validateBirthday(input) {
            const selectedDate = new Date(input.value);
            const today = new Date();
            
            // Calculate age
            let age = today.getFullYear() - selectedDate.getFullYear();
            const monthDiff = today.getMonth() - selectedDate.getMonth();
            
            // Adjust age if birthday hasn't occurred yet this year
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < selectedDate.getDate())) {
                age--;
            }
            
            if (age < 18) {
                alert('Error: You cannot enter dates that were less than 18 years ago.');
                input.value = ''; // Clear the input
                return false;
            }
            
            return true;
        }
        
        // Function to validate image file size and type
        function validateImageFile(input) {
            const fileUploadContainer = input.closest('.custom-file-upload');
            const fileChosen = fileUploadContainer.querySelector('.file-chosen');
            const fileInfoContainer = fileUploadContainer.nextElementSibling;
            
            // Create or get status element for showing selected file
            let statusElement = fileInfoContainer.querySelector('.file-status');
            if (!statusElement) {
                statusElement = document.createElement('small');
                statusElement.className = 'file-info file-status';
                fileInfoContainer.appendChild(statusElement);
            }
            
            // Check if a file is selected
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Update the file chosen text
                fileChosen.textContent = file.name;
                fileChosen.style.color = '#4F6F52';
                
                // Check file size (max 2MB)
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    alert('Error: Image size exceeds 2MB limit. Please choose a smaller image.');
                    input.value = ''; // Clear the file input
                    fileChosen.textContent = 'No image chosen';
                    fileChosen.style.color = '#666';
                    statusElement.textContent = '';
                    return false;
                }
                
                // Check file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Error: Invalid file type. Please select a JPG, JPEG, or PNG image.');
                    input.value = ''; // Clear the file input
                    fileChosen.textContent = 'No image chosen';
                    fileChosen.style.color = '#666';
                    statusElement.textContent = '';
                    return false;
                }
                
                // Show file size
                const fileSize = (file.size / 1024).toFixed(2); // Convert to KB
                statusElement.textContent = `Image size: ${fileSize} KB`;
                statusElement.style.color = '#4F6F52';
                statusElement.style.fontWeight = 'bold';
                
                return true;
            } else {
                // No file selected
                fileChosen.textContent = 'No image chosen';
                fileChosen.style.color = '#666';
                statusElement.textContent = '';
            }
        }
    </script>
</body>
</html>
<!-- End of file -->
