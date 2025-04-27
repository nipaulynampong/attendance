<?php
// Connect to the database (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_POST["department"]) && isset($_POST["description"]) && isset($_POST["head"]) && isset($_POST["contact"]) && isset($_POST["status"])) {
        // Prepare and bind parameters
        $stmt = $conn->prepare("INSERT INTO department (`Department`, `Description`, `Head`, `Contact`, `Status`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $department, $description, $head, $contact, $status);

        // Set parameters and execute
        $department = $_POST["department"];
        $description = $_POST["description"];
        $head = $_POST["head"];
        $contact = $_POST["contact"];
        $status = $_POST["status"];

        if ($stmt->execute()) {
            // Redirect to prevent form resubmission
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "<p>All fields are required.</p>";
    }
}

// Fetch and display existing departments
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
      background-color: #f9f9f9;
      color: #333;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    .container {
      max-width: 1400px;
      margin: 20px auto;
      padding: 25px;
      background-color: #f5efe6;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
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

    /* Style for the Update button */
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

    /* Status badges */
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

    /* Overlay styles */
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

    /* Description truncation */
    .truncate {
      max-width: 250px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* Success message */
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
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1 class="page-title"><i class="fas fa-building"></i> Manage Department</h1>
    </div>
    
    <?php
    // Check if form was just submitted successfully
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($error_message)) {
        echo '<div class="success-message"><i class="fas fa-check-circle"></i> Department added successfully!</div>';
    }
    ?>
    
    <div class="content-wrapper">
      <div class="card">
        <h2 class="card-title"><i class="fas fa-plus-circle"></i> Add Department</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
            <input type="text" id="head" name="head" placeholder="Enter name of department head" required>
          </div>

          <div class="form-group">
            <label for="contact">Contact Information:</label>
            <input type="text" id="contact" name="contact" placeholder="Enter contact details" required>
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
                  // Output data of each row
                  while ($row = $result->fetch_assoc()) {
                      $statusClass = ($row["Status"] == "Active") ? "status-active" : "status-inactive";
                      
                      echo "<tr>";
                      echo "<td>" . $row["Department"] . "</td>";
                      echo "<td class='truncate'>" . $row["Description"] . "</td>";
                      echo "<td>" . $row["Head"] . "</td>";
                      echo "<td>" . $row["Contact"] . "</td>";
                      echo "<td><span class='status-badge " . $statusClass . "'>" . $row["Status"] . "</span></td>";
                      echo "<td><button class='update-button'><i class='fas fa-edit'></i> Update</button></td>";
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
  </div>

  <!-- Overlay for update -->
  <div class="overlay" id="overlay">
    <div class="overlay-content">
      <button class="close-btn" onclick="hideOverlay()"><i class="fas fa-times"></i></button>
      <h2 class="card-title"><i class="fas fa-edit"></i> Update Department</h2>
      <!-- Update form will be loaded here -->
    </div>
  </div>

  <script>
    // Function to display the overlay
    function showOverlay() {
      document.getElementById('overlay').style.display = 'block';
    }

    // Function to hide the overlay
    function hideOverlay() {
      document.getElementById('overlay').style.display = 'none';
    }

    // Add event listener to update buttons
    var updateButtons = document.querySelectorAll('.update-button');
    updateButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        // Show the overlay when update button is clicked
        showOverlay();
        // Fetch and load the update form dynamically
        fetch('update_form.php')
          .then(response => response.text())
          .then(data => {
            document.querySelector('.overlay-content').innerHTML = data;
          })
          .catch(error => console.error('Error fetching update form:', error));
      });
    });
  </script>

  <script>
    // Function to prevent typing unwanted characters
    function restrictInput(event) {
        // Get the pressed key code
        var keyCode = event.keyCode || event.which;
        
        // Allow only specific characters for each input field
        if (event.target.id === 'department') {
            // Allow only letters, space, and certain special characters like hyphen (-) and underscore (_) for department name
            if (!((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 32 || keyCode === 45 || keyCode === 95)) {
                event.preventDefault();
            }
        }
        
        if (event.target.id === 'description') {
            // Allow any character for description
            return true;
        }
        
        if (event.target.id === 'head') {
            // Allow only letters and space for head
            if (!((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 32)) {
                event.preventDefault();
            }
        }
        
        if (event.target.id === 'contact') {
            // Allow only numbers and the plus symbol (+) for contact
            if (!((keyCode >= 48 && keyCode <= 57) || keyCode === 43)) {
                event.preventDefault();
            }
        }
    }
    
    // Add event listener to the form inputs for input restriction
    var inputs = document.querySelectorAll('input[type="text"], textarea');
    inputs.forEach(function(input) {
        input.addEventListener('keypress', restrictInput);
    });
  </script>
</body>
</html>
