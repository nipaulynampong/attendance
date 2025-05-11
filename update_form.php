<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

// Get the department from URL parameter
$department = '';

if (isset($_GET['department'])) {
    // For text data type, we need to handle it carefully
    $department = trim($_GET['department']);
    // Remove any potential harmful characters but preserve the text content
    $department = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $department);
    
    // Debug logging
    error_log("Received department parameter: " . $_GET['department']);
    error_log("Processed department value: " . $department);
}

if (empty($department)) {
    error_log("No department specified in update_form.php");
    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> No department specified.</div>";
    exit();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to handle text properly
$conn->set_charset("utf8mb4");

// Initialize variables
$row = [];
$updateSuccess = false;

// Fetch specific department details
$sql = "SELECT * FROM department WHERE Department = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    error_log("Found department in database: " . $row['Department']);
} else {
    error_log("Department not found in database: " . $department);
    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Department not found.</div>";
    exit();
}

$stmt->close();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_POST["department"]) && isset($_POST["description"]) && isset($_POST["head"]) && isset($_POST["contact"]) && isset($_POST["status"])) {
        // Prepare and bind parameters for update
        $stmt = $conn->prepare("UPDATE department SET `Description`=?, `Head`=?, `Contact`=?, `Status`=? WHERE Department=?");
        $stmt->bind_param("sssss", $description, $head, $contact, $status, $department);

        // Set parameters and execute
        $department = $_POST["department"];
        $description = $_POST["description"];
        $head = $_POST["head"];
        $contact = $_POST["contact"];
        $status = $_POST["status"];

        if ($stmt->execute()) {
            $updateSuccess = true;
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
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
  <title>Update Department</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {margin: 0; padding: 0; box-sizing: border-box;}
    body {font-family: 'Poppins', sans-serif; background-color: transparent; color: #333; font-size: 14px;}
    
    .update-form {width: 100%;}
    
    .form-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      padding-bottom: 8px;
      border-bottom: 1px solid #eee;
    }
    
    .form-title {
      color: #4F6F52;
      font-size: 18px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0;
    }
    
    .form-group {margin-bottom: 12px;}
    
    .form-group label {
      display: block;
      margin-bottom: 4px;
      font-weight: 500;
      color: #444;
      font-size: 13px;
    }
    
    .form-control {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      transition: all 0.2s ease;
    }
    
    .form-control:focus {
      outline: none;
      border-color: #4F6F52;
      box-shadow: 0 0 0 2px rgba(79, 111, 82, 0.1);
    }
    
    textarea.form-control {
      min-height: 80px;
      resize: vertical;
    }
    
    .form-select {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234F6F52' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: calc(100% - 10px) center;
      background-color: white;
    }
    
    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 6px;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }
    
    .btn-primary {
      background-color: #4F6F52;
      color: white;
      margin-top: 5px;
      width: 100%;
    }
    
    .btn-primary:hover {
      background-color: #3D5941;
      transform: translateY(-1px);
    }
    
    .readonly-field {
      background-color: #f9f9f9;
      cursor: not-allowed;
    }
    
    small {
      color: #777;
      font-size: 11px;
      margin-top: 2px;
      display: block;
    }
    
    .alert {
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .alert-success {
      background-color: rgba(79, 111, 82, 0.15);
      color: #4F6F52;
      border: 1px solid rgba(79, 111, 82, 0.3);
    }
    
    .alert i {
      font-size: 14px;
    }
  </style>
</head>
<body>

<?php if($updateSuccess): ?>
<div class="alert alert-success">
  <i class="fas fa-check-circle"></i>
  Department details updated successfully! The changes will be reflected in the system.
</div>
<script>
  // Automatically redirect after showing success message for a few seconds
  setTimeout(function() {
    window.parent.hideOverlay();
    window.parent.location.reload();
  }, 2000);
</script>
<?php endif; ?>

<form class="update-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <div class="form-group">
    <label for="department">Department Name</label>
    <input type="text" id="department" name="department" class="form-control readonly-field" value="<?php echo isset($row['Department']) ? $row['Department'] : ''; ?>" readonly>
    <small>Department name cannot be changed</small>
  </div>
  
  <div class="form-group">
    <label for="description">Description</label>
    <textarea id="description" name="description" class="form-control" required><?php echo isset($row['Description']) ? $row['Description'] : ''; ?></textarea>
  </div>
  
  <div class="form-group">
    <label for="head">Head / Officer-in-Charge</label>
    <input type="text" id="head" name="head" class="form-control" value="<?php echo isset($row['Head']) ? $row['Head'] : ''; ?>" required>
  </div>
  
  <div class="form-group">
    <label for="contact">Contact Information</label>
    <input type="text" id="contact" name="contact" class="form-control" value="<?php echo isset($row['Contact']) ? $row['Contact'] : ''; ?>" required>
  </div>
  
  <div class="form-group">
    <label for="status">Status</label>
    <select id="status" name="status" class="form-select" required>
      <option value="Active" <?php echo (isset($row['Status']) && $row['Status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
      <option value="Inactive" <?php echo (isset($row['Status']) && $row['Status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
    </select>
  </div>
  
  <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
</form>

<script>
  // Function to hide overlay - needs to be accessible to parent window
  function hideOverlay() {
    if (window.parent && window.parent.hideOverlay) {
      window.parent.hideOverlay();
    }
  }
  
  // Function to prevent typing unwanted characters
  function restrictInput(event) {
    var keyCode = event.keyCode || event.which;
    
    if (event.target.id === 'head') {
      if (!((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 32)) {
        event.preventDefault();
      }
    }
    
    if (event.target.id === 'contact') {
      if (!((keyCode >= 48 && keyCode <= 57) || keyCode === 43 || keyCode === 64 || keyCode === 46)) {
        event.preventDefault();
      }
    }
  }
  
  // Add event listeners
  document.querySelectorAll('#head, #contact').forEach(function(input) {
    input.addEventListener('keypress', restrictInput);
  });
</script>

</body>
</html>

