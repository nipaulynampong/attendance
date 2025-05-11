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

// Set the character set
$conn->set_charset("utf8mb4");

// Initialize variables
$success_message = '';
$error_message = '';
$row = [];
$updateSuccess = false;

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        if ($_POST["action"] == "add") {
            // Add new department
            if (isset($_POST["department"]) && isset($_POST["description"]) && isset($_POST["head"]) && isset($_POST["contact"]) && isset($_POST["status"])) {
                $stmt = $conn->prepare("INSERT INTO department (`Department`, `Description`, `Head`, `Contact`, `Status`) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $department, $description, $head, $contact, $status);

                $department = $_POST["department"];
                $description = $_POST["description"];
                $head = $_POST["head"];
                $contact = $_POST["contact"];
                $status = $_POST["status"];

                if ($stmt->execute()) {
                    $success_message = "Department added successfully!";
                } else {
                    $error_message = "Error adding department: " . $stmt->error;
                }
                $stmt->close();
            }
        } elseif ($_POST["action"] == "update") {
            // Update existing department
            if (isset($_POST["department"]) && isset($_POST["description"]) && isset($_POST["head"]) && isset($_POST["contact"]) && isset($_POST["status"])) {
                $stmt = $conn->prepare("UPDATE department SET `Description`=?, `Head`=?, `Contact`=?, `Status`=? WHERE Department=?");
                $stmt->bind_param("sssss", $description, $head, $contact, $status, $department);

                $department = $_POST["department"];
                $description = $_POST["description"];
                $head = $_POST["head"];
                $contact = $_POST["contact"];
                $status = $_POST["status"];

                if ($stmt->execute()) {
                    $success_message = "Department updated successfully!";
                    $updateSuccess = true;
                } else {
                    $error_message = "Error updating department: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Handle update form display
if (isset($_GET['update']) && isset($_GET['department'])) {
    $department = trim($_GET['department']);
    $department = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $department);
    
    $sql = "SELECT * FROM department WHERE Department = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Return JSON response for AJAX request
        header('Content-Type: application/json');
        echo json_encode($row);
        exit();
    }
    $stmt->close();
}

// Fetch all departments for listing
$sql = "SELECT * FROM department";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Departments</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5efe6; /* Match the container background color */
      color: #333;
      margin: 0;
      padding: 20px;
      min-height: 100vh;
      overflow: visible; /* Remove scrollbar from body */
    }

    /* Content styling without container */
    .content-wrapper {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 20px;
    }

    .header {
      margin-bottom: 25px;
      border-bottom: 2px solid rgba(79, 111, 82, 0.2);
      padding-bottom: 15px;
    }

    .page-title {
      color: #4F6F52;
      font-size: 28px;
      font-weight: 700;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .content-wrapper {
      display: grid;
      grid-template-columns: 1fr 1.5fr;
      gap: 30px;
    }

    @media (max-width: 1100px) {
      .content-wrapper {
        grid-template-columns: 1fr;
      }
    }

    .card {
      background-color: white;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      padding: 25px;
      position: relative;
    }

    .card-title {
      color: #4F6F52;
      font-size: 20px;
      font-weight: 600;
      margin-top: 0;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
      border-bottom: 1px solid #eee;
      padding-bottom: 12px;
    }

    form label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #444;
    }

    form input[type="text"],
    form textarea {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 18px;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    form textarea {
      min-height: 100px;
      resize: vertical;
    }

    form input[type="text"]:focus,
    form textarea:focus {
      outline: none;
      border-color: #4F6F52;
      box-shadow: 0 0 0 3px rgba(79, 111, 82, 0.1);
    }

    .form-group {
      margin-bottom: 15px;
    }

    form select {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 18px;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234F6F52' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: calc(100% - 15px) center;
      cursor: pointer;
    }

    form select:focus {
      outline: none;
      border-color: #4F6F52;
      box-shadow: 0 0 0 3px rgba(79, 111, 82, 0.1);
    }

    .submit-btn {
      width: 100%;
      padding: 14px;
      margin-top: 10px;
      border: none;
      border-radius: 8px;
      background-color: #4F6F52;
      color: white;
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
    }

    .submit-btn:hover {
      background-color: #3D5941;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(79, 111, 82, 0.3);
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      margin-top: 10px;
      overflow: hidden;
      border-radius: 8px;
    }

    th, td {
      padding: 15px;
      text-align: left;
      vertical-align: middle;
    }

    th {
      background-color: #4F6F52;
      color: white;
      font-weight: 500;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    th:first-child {
      border-top-left-radius: 8px;
    }

    th:last-child {
      border-top-right-radius: 8px;
    }

    td {
      background-color: white;
      border-bottom: 1px solid #eee;
      font-size: 14px;
    }

    tr:nth-child(even) td {
      background-color: #f8f8f8;
    }

    tr:hover td {
      background-color: #f0f7f0;
    }

    tr:last-child td:first-child {
      border-bottom-left-radius: 8px;
    }

    tr:last-child td:last-child {
      border-bottom-right-radius: 8px;
    }

    .update-button {
      padding: 8px 12px;
      border: none;
      border-radius: 6px;
      background-color: #4F6F52;
      color: white;
      cursor: pointer;
      transition: all 0.3s ease;
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .update-button:hover {
      background-color: #3D5941;
      transform: translateY(-2px);
      box-shadow: 0 2px 5px rgba(79, 111, 82, 0.2);
    }

    .status-badge {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 50px;
      font-size: 12px;
      font-weight: 500;
    }

    .status-active {
      background-color: #d1e7dd;
      color: #0f5132;
    }

    .status-inactive {
      background-color: #f8d7da;
      color: #842029;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 999;
      backdrop-filter: blur(3px);
    }

    .overlay-content {
      width: 90%;
      max-width: 500px;
      background-color: white;
      border-radius: 12px;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 25px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
      animation: fadeIn 0.3s;
      max-height: 80vh;
      overflow-y: auto;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translate(-50%, -60%); }
      to { opacity: 1; transform: translate(-50%, -50%); }
    }

    .close-btn {
      position: absolute;
      top: 15px;
      right: 15px;
      background: none;
      border: none;
      font-size: 22px;
      color: #aaa;
      cursor: pointer;
      transition: color 0.3s;
    }

    .close-btn:hover {
      color: #4F6F52;
    }

    .table-container {
      overflow-x: auto;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }

    .truncate {
      max-width: 250px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 12px 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      border-left: 4px solid #28a745;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .error-message {
      background-color: #f8d7da;
      color: #721c24;
      padding: 12px 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      border-left: 4px solid #dc3545;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .button-group {
      margin-top: 20px;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .cancel-btn {
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      background-color: #dc3545;
      color: white;
      cursor: pointer;
      transition: all 0.3s ease;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .cancel-btn:hover {
      background-color: #bb2d3b;
      transform: translateY(-2px);
      box-shadow: 0 2px 5px rgba(220, 53, 69, 0.2);
    }
  </style>
</head>
<body>
    <div class="header">
      <h1 class="page-title"><i class="fas fa-building"></i> Manage Department</h1>
    </div>
    
    <?php if ($success_message): ?>
    <div class="success-message">
      <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="error-message">
      <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    
    <div class="content-wrapper">
      <div class="card">
        <h2 class="card-title"><i class="fas fa-plus-circle"></i> Add Department</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <input type="hidden" name="action" value="add">
          <div class="form-group">
            <label for="department">Department Name:</label>
            <input type="text" id="department" name="department" placeholder="Enter department name" required>
          </div>

          <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" placeholder="Enter department description" required></textarea>
          </div>

          <div class="form-group">
            <label for="head">Head/Officer-in-Charge:</label>
            <input type="text" id="head" name="head" placeholder="Enter name of department head" required pattern="[A-Za-z\- ]+" title="Only letters, dashes, and spaces are allowed" oninput="validateHeadField(this)">
            <small style="color: #666; font-style: italic;">Only letters, dashes, and spaces are allowed</small>
          </div>

          <div class="form-group">
            <label for="contact">Contact Information:</label>
            <input type="text" id="contact" name="contact" maxlength="11" placeholder="Enter contact number (Optional)" oninput="validateContactField(this)">
            <small style="color: #666; font-style: italic;">Numbers only (max 11 digits)</small>
          </div>

          <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status" required>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

          <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Add Department</button>
        </form>
      </div>
      
      <div class="card">
        <h2 class="card-title"><i class="fas fa-list"></i> Department List</h2>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Department</th>
                <th>Description</th>
                <th>Head/OIC</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      $statusClass = ($row["Status"] == "Active") ? "status-active" : "status-inactive";
                      $departmentName = htmlspecialchars($row["Department"], ENT_QUOTES);
                      
                      echo "<tr>";
                      echo "<td>" . $departmentName . "</td>";
                      echo "<td class='truncate'>" . htmlspecialchars($row["Description"]) . "</td>";
                      echo "<td>" . htmlspecialchars($row["Head"]) . "</td>";
                      echo "<td>" . htmlspecialchars($row["Contact"]) . "</td>";
                      echo "<td><span class='status-badge " . $statusClass . "'>" . htmlspecialchars($row["Status"]) . "</span></td>";
                      echo "<td><button type='button' class='update-button' onclick='showUpdateForm(\"" . $departmentName . "\")'><i class='fas fa-edit'></i> Update</button></td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='6' style='text-align:center;padding:20px;'>No departments found</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  <!-- Update Form Overlay -->
  <div class="overlay" id="overlay">
    <div class="overlay-content">
      <button class="close-btn" onclick="hideOverlay()"><i class="fas fa-times"></i></button>
      <h2 class="card-title"><i class="fas fa-edit"></i> Update Department</h2>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="department" id="update-department">
        
        <div class="form-group">
          <label for="update-description">Description:</label>
          <textarea id="update-description" name="description" required></textarea>
        </div>
        
        <div class="form-group">
          <label for="update-head">Head / Officer-in-Charge:</label>
          <input type="text" id="update-head" name="head" required pattern="[A-Za-z\- ]+" title="Only letters, dashes, and spaces are allowed" oninput="validateHeadField(this)">
          <small style="color: #666; font-style: italic;">Only letters, dashes, and spaces are allowed</small>
        </div>
        
        <div class="form-group">
          <label for="update-contact">Contact Information:</label>
          <input type="text" id="update-contact" name="contact" maxlength="11" placeholder="Enter contact number (Optional)" oninput="validateContactField(this)">
          <small style="color: #666; font-style: italic;">Numbers only (max 11 digits)</small>
        </div>
        
        <div class="form-group">
          <label for="update-status">Status:</label>
          <select id="update-status" name="status" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
        
        <div class="button-group">
          <button type="button" class="cancel-btn" onclick="hideOverlay()"><i class="fas fa-times"></i> Cancel</button>
          <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Function to validate contact field - limit to 11 digits, numbers only
    function validateContactField(input) {
      // Remove any non-numeric characters
      input.value = input.value.replace(/\D/g, '');
      
      // Enforce maximum length of 11 digits
      if (input.value.length > 11) {
        input.value = input.value.slice(0, 11);
      }
    }
    
    // Function to validate Head/OIC field - only letters, dashes, and spaces
    function validateHeadField(input) {
      // Remove any characters that are not letters, dashes, or spaces
      input.value = input.value.replace(/[^a-zA-Z\- ]/g, '');
    }
    
    // Function to display the overlay
    function showOverlay() {
      document.getElementById('overlay').style.display = 'block';
    }

    // Function to hide the overlay
    function hideOverlay() {
      document.getElementById('overlay').style.display = 'none';
    }

    // Function to show update form
    function showUpdateForm(department) {
      // Set the department value
      document.getElementById('update-department').value = department;
      
      // Fetch department details
      fetch('department.php?update=1&department=' + encodeURIComponent(department))
        .then(response => response.json())
        .then(data => {
          if (data) {
            document.getElementById('update-description').value = data.Description;
            document.getElementById('update-head').value = data.Head;
            document.getElementById('update-contact').value = data.Contact;
            document.getElementById('update-status').value = data.Status;
            showOverlay();
          }
        })
        .catch(error => {
          console.error('Error fetching department details:', error);
        });
    }

    // Function to prevent typing unwanted characters
    function restrictInput(event) {
      var keyCode = event.keyCode || event.which;
      
      if (event.target.id === 'department') {
        if (!((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 32 || keyCode === 45 || keyCode === 95)) {
          event.preventDefault();
        }
      }
      
      if (event.target.id === 'head' || event.target.id === 'update-head') {
        // Allow only letters, spaces, and dashes
        if (!((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 32 || keyCode === 45)) {
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
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('#department, #head, #contact, #update-head, #update-contact').forEach(function(input) {
        input.addEventListener('keypress', restrictInput);
      });
    });
  </script>
</body>
</html>
