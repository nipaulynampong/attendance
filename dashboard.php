<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Management System</title>
  <link rel="icon" href="1.png" type="image/png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="styles/dashboard.css">

</head>
<body>

<div class="topnav" id="myTopnav">
  <div class="toggle-btn" onclick="toggleNav()">☰</div>
  <h2>ATTENDANCE MANAGEMENT SYSTEM</h2>
  <button class="logout-btn" onclick="openModal()"><i class="fas fa-sign-out-alt"></i></button>
</div>

<div class="sidenav" id="mySidenav">
  <div class="admin-section">
    <div class="admin-picture"></div>
    <div class="admin-details">
      <div class="admin-text">Administrator</div>
      <div class="online-status">
        <div class="status-circle"></div>
        <div class="status-text">Online</div>
      </div>
    </div>
  </div>

  <a href="dashboardd.php" target="mainFrame"><i class="fas fa-tachometer-alt"></i> DASHBOARD</a>
  
  <!-- Employee Dropdown -->
  <div id="employeeDropdown" class="dropdown" onclick="toggleEmployeeDropdown()">
    <a href="#" id="employee-link"><i class="fas fa-user"></i> EMPLOYEE ▼</a>
    <div class="dropdown-content">
      <a href="addemployees.php" target="mainFrame">Add Employees</a>
      <a href="employee.php" target="mainFrame">View Employees</a>
      <a href="archive.php" target="mainFrame">Archived Employees</a>
    </div>
  </div>
  
  <!-- Department Dropdown -->
  <div id="departmentDropdown" class="dropdown" onclick="toggleDepartmentDropdown()">
    <a href="#" id="department-link"><i class="fas fa-users"></i> DEPARTMENT ▼</a>
    <div class="dropdown-content">
      <a href="department.php" target="mainFrame">Add Department</a>
      
    </div>
  </div>
  
   <a href="holidays.php" target="mainFrame"><i class="fas fa-calendar-alt"></i> HOLIDAYS</a>
  
  <a href="events.php" target="mainFrame"><i class="fas fa-calendar-check"></i> EVENTS</a>
  
  <!-- Attendance Dropdown -->
  <div id="attendanceDropdown" class="dropdown" onclick="toggleAttendanceDropdown()">
    <a href="#" id="attendance-link"><i class="fas fa-clipboard-check"></i> ATTENDANCE ▼</a>
    <div class="dropdown-content">
      <a href="attendance_view.php" target="mainFrame">View Attendance</a>
    </div>
  </div>
  
  <!-- Reports Dropdown -->
  <div id="reportsDropdown" class="dropdown" onclick="toggleReportsDropdown()">
    <a href="#" id="reports-link"><i class="fas fa-table"></i> REPORTS ▼</a>
    <div class="dropdown-content">
      <a href="reportemployee.php" target="mainFrame">Employee Report</a>
      <a href="attendancereport.php" target="mainFrame">Attendance Report</a>
    </div>
  </div>
  
  <a href="settings.php" target="mainFrame"><i class="fas fa-cog"></i> SETTINGS</a>
</div>


<div class="main" id="mainContent">
  <iframe name="mainFrame" src="dashboardd.php" style="width:100%;height:800px;border:none;"></iframe>

</div>

<!-- Overlay -->
<div class="overlay" id="overlay" onclick="closeModal()"></div>

<!-- Logout Modal -->
<div id="logoutModal" class="modal">
  <p>Are you sure you want to logout?</p>
  <button class="btn-yes" onclick="logout()">Yes</button>
  <button class="btn-cancel" onclick="closeModal()">Cancel</button>
</div>

<script>
// Toggle Side Navigation
function toggleNav() {
  var sidenav = document.getElementById("mySidenav");
  var topnav = document.getElementById("myTopnav");
  var mainContent = document.getElementById("mainContent");

  if (sidenav.style.left === "-250px") {
    sidenav.style.left = "0";
    topnav.style.marginLeft = "250px";
    mainContent.style.marginLeft = "250px";
  } else {
    sidenav.style.left = "-250px";
    topnav.style.marginLeft = "0";
    mainContent.style.marginLeft = "0";
  }
}

// Toggle Dropdown Visibility and Close Other Dropdowns
function toggleDropdown(dropdownId) {
  var allDropdowns = document.querySelectorAll('.dropdown');
  
  // Close all dropdowns except the one clicked
  allDropdowns.forEach(function(dropdown) {
    if (dropdown.id !== dropdownId) {
      dropdown.classList.remove('open');
    }
  });
  
  // Toggle the clicked dropdown
  var dropdown = document.getElementById(dropdownId);
  dropdown.classList.toggle('open');
}

// Employee Dropdown Toggle
function toggleEmployeeDropdown() {
  toggleDropdown("employeeDropdown");
}

// Department Dropdown Toggle
function toggleDepartmentDropdown() {
  toggleDropdown("departmentDropdown");
}

// Attendance Dropdown Toggle
function toggleAttendanceDropdown() {
  toggleDropdown("attendanceDropdown");
}

// Reports Dropdown Toggle
function toggleReportsDropdown() {
  toggleDropdown("reportsDropdown");
}

// Open Modal
function openModal() {
  var overlay = document.getElementById("overlay");
  var modal = document.getElementById("logoutModal");
  overlay.style.display = "block";
  modal.style.display = "block";
}

// Close Modal
function closeModal() {
  var overlay = document.getElementById("overlay");
  var modal = document.getElementById("logoutModal");
  overlay.style.display = "none";
  modal.style.display = "none";
}

// Logout Function
function logout() {
  alert("Logged out!");
  window.location.href = "homepage.php";
}

</script>

</body>
</html>
