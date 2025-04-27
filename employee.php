<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags and title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employee Details</title>
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- CSS styles -->
    <style>
      /* CSS styles for layout and design */
      body {
        font-family: 'Poppins', sans-serif;
        background-color: white;
        color: #333;
        margin: 0;
        padding: 0;
      }

      .container {
        max-width: 1420px;
        position: relative;
        margin: 20px auto;
        padding: 25px;
        background-color: #f5efe6;
        color: #333;
        border-radius: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      }

      .section {
        margin-bottom: 20px;
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      }

      .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
      }

      .section-title {
        margin: 0;
        color: #4F6F52;
        font-weight: 600;
        font-size: 1.3rem;
      }

      .page-title {
        color: #4F6F52;
        font-weight: 700;
        margin-bottom: 25px;
        font-size: 1.8rem;
        border-left: 5px solid #4F6F52;
        padding-left: 15px;
      }

      .button-container {
        display: flex;
        align-items: center;
      }

      .button-container button {
        background-color: #4F6F52;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        margin-right: 15px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .button-container button:hover {
        background-color: #3D5941;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      /* Overlay styles */
      .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 999;
        color: #333;
        backdrop-filter: blur(5px);
      }

      .overlay-content {
        background-color: white;
        padding: 30px;
        border-radius: 15px;
        width: 80%;
        max-width: 600px;
        max-height: 80%;
        overflow-y: auto;
        overflow-x: hidden;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.3s ease-in-out;
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
      }

      .overlay-content h2 {
        margin-top: 0;
        margin-bottom: 25px;
        font-size: 1.5rem;
        color: #4F6F52;
        border-bottom: 2px solid #4F6F52;
        padding-bottom: 10px;
      }

      .overlay-content .detail {
        margin-bottom: 20px;
      }

      .overlay-content button {
        background-color: #4F6F52;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .overlay-content button:hover {
        background-color: #3D5941;
      }

      /* Search bar styles */
      .search-container {
        position: relative;
        width: 400px;
      }

      .search-container input[type="text"] {
        width: 100%;
        padding: 12px 20px;
        border-radius: 30px;
        border: 1px solid #ddd;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
      }

      .search-container input[type="text"]:focus {
        outline: none;
        border-color: #4F6F52;
        box-shadow: 0 2px 10px rgba(79, 111, 82, 0.2);
      }

      .search-container button[type="submit"] {
        position: absolute;
        right: 5px;
        top: 5px;
        background-color: #4F6F52;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
      }

      .search-container button[type="submit"]:hover {
        background-color: #3D5941;
      }

      /* Table styles */
      table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 20px; 
        overflow: hidden;
      }

      th, td {
        padding: 15px;
        text-align: left;
        font-family: 'Poppins', sans-serif;
      }

      th {
        background-color: #4F6F52;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
      }

      th:first-child {
        border-top-left-radius: 10px;
      }

      th:last-child {
        border-top-right-radius: 10px;
      }

      tr:nth-child(even) {
        background-color: #f8f8f8;
      }

      tr:last-child td:first-child {
        border-bottom-left-radius: 10px;
      }

      tr:last-child td:last-child {
        border-bottom-right-radius: 10px;
      }

      td {
        border-bottom: 1px solid #eee;
        white-space: nowrap;  /* Prevent line breaks between dashes and buttons */
      }

      tr:hover {
        background-color: #f0f7f0;
      }

      /* Action buttons in table */
      .action-btn {
        background-color: #4F6F52;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin: 0 5px;  /* Add space between button and dashes */
      }

      .action-btn:hover {
        background-color: #3D5941;
        transform: translateY(-2px);
      }

      .action-btn i {
        font-size: 0.8rem;
      }

      /* Employee image */
      .employee-img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #4F6F52;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      }

      /* Show entries dropdown */
      .entries-container {
        display: flex;
        align-items: center;
        gap: 10px;
      }

      #entries {
        padding: 8px 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 0.9rem;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        transition: border-color 0.3s ease;
        background-color: white;
      }

      #entries:focus {
        outline: none;
        border-color: #4F6F52;
      }

      /* Entry counter */
      #entries-info {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        color: #666;
        font-size: 0.9rem;
        padding: 10px 0;
      }

      /* Tag styles for rest days */
      .tag {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        margin-right: 3px;
        margin-bottom: 3px;
        background-color: #f3d7d7;
        color: #cc5757;
        border: 1px solid #cc5757;
      }

      /* No results message */
      .no-results {
        text-align: center;
        padding: 30px;
        color: #666;
        font-style: italic;
      }
    </style>
  </head>
  <body>
  <div class="container" id="main-container">
    <div class="header-section" style="display: flex; align-items: center; margin-bottom: 25px; gap: 20px;">
      <h1 class="page-title" style="margin-bottom: 0; margin-right: 20px; white-space: nowrap;"><i class="fas fa-users"></i> Manage Employee Details</h1>
      <div class="search-container" style="margin: 0; flex-grow: 1; max-width: 500px;">
        <form method="GET" action="">
          <input type="text" name="search" placeholder="Search by name, ID or department...">
          <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
      </div>
    </div>
      
    <div class="button-container" style="margin-bottom: 20px;">
      <button id="refresh-btn" style="display: none;"><i class="fas fa-sync-alt"></i> Refresh</button>
    </div>

  <!-- Table Section -->
  <div class="section">
    <div class="section-header">
      <h3 class="section-title"><i class="fas fa-list"></i> List of Government Employees</h3>
      <div class="entries-container">
        <label for="entries">Show entries:</label>
        <select id="entries" onchange="changeEntries(this.value)">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="15">15</option>
          <option value="20">20</option>
          <option value="50">50</option>
        </select>
      </div>
    </div>
    
    <table id="employeeTable">
      <thead>
        <tr>
          <th>Image</th>
          <th>Employee ID</th>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Middle Name</th>
          <th>Department</th>
          <th>Rest Days</th>
          <th>View Details</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="employeeTableBody">
      <?php
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

      // Initialize the SQL query
      $sql = "SELECT * FROM employee";

      // Check if a search query is provided
      if(isset($_GET['search'])) {
          $search = $_GET['search'];
          // Add conditions to filter by Employee ID, Last Name, First Name, or Department
          $sql .= " WHERE EmployeeID LIKE '%$search%' OR `Last Name` LIKE '%$search%' OR `First Name` LIKE '%$search%' OR Department LIKE '%$search%'";
      }

      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          // Output data of each row
          while ($row = $result->fetch_assoc()) {
              // Get rest days
              $restDays = [];
              if($row["Monday_Rest"] == 1) $restDays[] = "Mon";
              if($row["Tuesday_Rest"] == 1) $restDays[] = "Tue";
              if($row["Wednesday_Rest"] == 1) $restDays[] = "Wed";
              if($row["Thursday_Rest"] == 1) $restDays[] = "Thu";
              if($row["Friday_Rest"] == 1) $restDays[] = "Fri";
              if($row["Saturday_Rest"] == 1) $restDays[] = "Sat";
              if($row["Sunday_Rest"] == 1) $restDays[] = "Sun";
              
              $restDaysDisplay = '';
              if(!empty($restDays)) {
                  foreach($restDays as $day) {
                      $restDaysDisplay .= '<span class="tag">' . $day . '</span>';
                  }
              } else {
                  $restDaysDisplay = '<span style="color: #666; font-style: italic;">None</span>';
              }
              
              echo "<tr>";
              echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['Image']) . "' alt='Employee Image' class='employee-img'></td>";
              echo "<td>" . $row["EmployeeID"] . "</td>";
              echo "<td>" . $row["Last Name"] . "</td>";
              echo "<td>" . $row["First Name"] . "</td>";
              echo "<td>" . $row["Middle Name"] . "</td>";
              echo "<td>" . $row["Department"] . "</td>";
              echo "<td>" . $restDaysDisplay . "</td>";
              echo "<td>";
              echo "<button class='action-btn' onclick='showQRDetails(" . $row["EmployeeID"] . ")'><i class='fas fa-qrcode'></i> QR</button>";
              echo "<button class='action-btn' onclick='showDetails(" . $row["EmployeeID"] . ")'><i class='fas fa-eye'></i> View</button>";
              echo "</td>";
              echo "<td>";
              echo "<button class='action-btn' onclick='updateEmployee(" . $row["EmployeeID"] . ")'><i class='fas fa-edit'></i> Update</button>";
              echo "<button class='action-btn' onclick='archiveEmployee(" . $row["EmployeeID"] . ")'><i class='fas fa-archive'></i> Archive</button>";
              echo "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='9' class='no-results'>No results found</td></tr>";
      }

      $conn->close();
      ?>
      </tbody>
    </table>
    <div id="entries-info">
      <div id="shown-entries">Showing 0 of 0 entries</div>
      <div></div>
    </div>
  </div>


 <!-- Employee QR Code Overlay -->
<div class="overlay" id="QROverlay">
    <div class="overlay-content">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2><i class="fas fa-qrcode"></i> QR Code Image</h2>
            <button id="back-btn-qr"><i class="fas fa-times"></i> Close</button>
        </div>
        <!-- Display QR code here -->
        <div class="qr-code" id="QRDetails" style="text-align: center;"></div>
    </div>
</div>

  <!-- Employee Overlay -->
  <div class="overlay" id="employeeOverlay">
    <div class="overlay-content" style="width: 80%; max-width: 800px; max-height: 90vh;">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2><i class="fas fa-id-card"></i> Employee Details</h2>
        <button id="back-btn-emp"><i class="fas fa-times"></i> Close</button>
      </div>
      <!-- Display employee details here -->
      <div id="employeeDetails" style="height: 100%;"></div>
    </div>
  </div>


  <!-- Update Form Overlay -->
  <div class="overlay" id="updateOverlay">
    <div class="overlay-content">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2><i class="fas fa-user-edit"></i> Update Employee Details</h2>
        <button id="back-btn-update"><i class="fas fa-times"></i> Close</button>
      </div>
      <!-- Update Form -->
      <iframe id="updateIframe" src="" style="width: 100%; height: 500px; border: none;"></iframe>
    </div>
  </div>

  <script>
  // Initialize entries display when page loads
  document.addEventListener('DOMContentLoaded', function() {
    changeEntries(document.getElementById('entries').value);
  });
  
  function showDetails(employeeID) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("employeeDetails").innerHTML = this.responseText;
        document.getElementById("employeeOverlay").style.display = "flex";
      }
    };
    xhttp.open("GET", "employeedetails.php?id=" + employeeID, true);
    xhttp.send();
  }

  // Function to handle back button click in employee overlay
  document.getElementById("back-btn-emp").addEventListener("click", function() {
    document.getElementById("employeeOverlay").style.display = "none"; // Hide the overlay
  });

  // Function to show the update overlay
  function updateEmployee(employeeID) {
    // Construct the URL for update_employee.php with the employee ID as a parameter
    var updateUrl = "update_employee.php?id=" + employeeID;
    // Set the src attribute of the iframe to the update URL
    document.getElementById("updateIframe").src = updateUrl;
    // Display the update overlay
    document.getElementById("updateOverlay").style.display = "flex";
  }

  // Function to handle back button click in update overlay
  document.getElementById("back-btn-update").addEventListener("click", function() {
    document.getElementById("updateOverlay").style.display = "none"; // Hide the update overlay
  });

  // Function to delete an employee
  function deleteEmployee(employeeID) {
    if (confirm("Are you sure you want to delete this employee?")) {
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          alert("Employee deleted successfully!");
          // Refresh the table after deletion
          location.reload();
        }
      };
      xhttp.open("GET", "delete_employee.php?id=" + employeeID, true);
      xhttp.send();
    }
  }

  // Function to change the number of entries displayed in the table
  function changeEntries(value) {
    var tableRows = document.querySelectorAll('#employeeTable tbody tr');
    var numRows = tableRows.length;
    var numToShow = parseInt(value);

    // First hide all rows
    for (var i = 0; i < numRows; i++) {
      tableRows[i].style.display = 'none';
    }
    
    // Then show only the number selected
    for (var i = 0; i < Math.min(numToShow, numRows); i++) {
      tableRows[i].style.display = '';
    }
    
    // Update a counter to show how many entries are displayed
    var shownEntries = Math.min(numToShow, numRows);
    var totalEntries = numRows;
    document.getElementById('shown-entries').textContent = 'Showing ' + shownEntries + ' of ' + totalEntries + ' entries';
  }

 // Function to display QR code within overlay
function showQRDetails(employeeID) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("QRDetails").innerHTML = this.responseText;
            document.getElementById("QROverlay").style.display = "flex";
        }
    };
    xhttp.open("GET", "showqrcode.php?id=" + employeeID, true);
    xhttp.send();
}

  // Function to handle back button click in employee overlay
  document.getElementById("back-btn-qr").addEventListener("click", function() {
    document.getElementById("QROverlay").style.display = "none"; // Hide the overlay
  });

  // Function to archive an employee
  function archiveEmployee(employeeID) {
    if (confirm("Are you sure you want to archive this employee?")) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                alert("Employee archived successfully!");
                location.reload();
            }
        };
        xhttp.open("GET", "archive_employee.php?id=" + employeeID, true);
        xhttp.send();
    }
}
  </script>

  </body>
</html>
