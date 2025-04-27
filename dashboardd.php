<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"="width=device-width, initial-scale=1.0">
    <title>Attendance Monitoring System</title>
    <link rel="icon" href="1.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      body {
        font-family: 'Poppins';
        color: black;
        margin: 0;
        padding: 0;
        background-color: #ffffff;
      }

      .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 20px auto;
        background-color: #f5efe6;
        min-height: 100vh;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        width: 95%;
        padding: 20px;
      }

      .separator {
        width: 100%;
        border-bottom: 2px solid #bbb;
        margin-bottom: 30px;
      }

      .dashboard {
        font-size: 28px;
        color: #333;
        margin: 10px 0;
        font-weight: 600;
      }

      .big-box {
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        width: 90%;
        padding: 30px;
        margin: 20px 0;
        border-radius: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
      }

      .welcome-text {
        font-size: 1.5em;
        max-width: 60%;
      }

      .boxes {
        display: flex;
        justify-content: space-between;
        width: 90%;
        margin: 20px 0;
        flex-wrap: wrap;
      }

      .box {
        flex: 1;
        min-width: 200px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin: 10px;
        transition: transform 0.3s ease;
      }

      .box:nth-child(1) {
        border-left: 5px solid #4CAF50;
        color: #2E7D32;
      }

      .box:nth-child(2) {
        border-left: 5px solid #FF5722;
        color: #D84315;
      }

      .box:nth-child(3) {
        border-left: 5px solid #2196F3;
        color: #1976D2;
      }

      .box:nth-child(4) {
        border-left: 5px solid #9C27B0;
        color: #7B1FA2;
      }

      .box:hover {
        transform: translateY(-5px);
      }

      .box h2 {
        font-size: 1.1em;
        margin-bottom: 10px;
        opacity: 0.8;
      }

      .box p {
        font-size: 1.8em;
        font-weight: bold;
        margin: 0;
      }

      .charts-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        width: 90%;
        margin: 20px 0;
      }

      .chart-wrapper {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
        height: 400px;
      }

      .chart-wrapper.full-width {
        grid-column: 1 / -1;
      }

      @media (max-width: 1200px) {
        .charts-container {
          grid-template-columns: 1fr;
        }
        
        .chart-wrapper.full-width {
          grid-column: auto;
        }
      }

      .top-employees {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin: 20px 0;
        width: 90%;
      }

      .top-employees h2 {
        color: #333;
        font-size: 24px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #4CAF50;
        position: relative;
      }

      .top-employees h2:after {
        content: '🏆';
        position: absolute;
        right: 0;
        top: 0;
      }

      .employee-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 20px;
      }

      .employee-card {
        background: linear-gradient(145deg, #ffffff, #f5f5f5);
        border-radius: 15px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
        border: 1px solid #eee;
        position: relative;
        overflow: hidden;
      }

      .employee-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      }

      .employee-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #4CAF50;
      }

      .employee-card img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      }

      .employee-info {
        flex: 1;
      }

      .employee-info h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
        font-weight: 600;
        margin-bottom: 5px;
      }

      .employee-info .department {
        color: #666;
        font-size: 14px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .employee-info .attendance {
        background: #E8F5E9;
        color: #2E7D32;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
      }

      .time {
        font-size: 2em;
        color: white;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
      }

      @media (max-width: 1200px) {
        .charts-container {
          grid-template-columns: repeat(2, 1fr);
        }
      }

      @media (max-width: 768px) {
        .charts-container {
          grid-template-columns: 1fr;
        }
        
        .boxes {
          flex-direction: column;
        }
        
        .box {
          margin: 10px 0;
        }
        
        .employee-card {
          width: 100%;
        }
      }
    </style>
</head>
<body>

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
// Get today's date
$currentDate = date('Y-m-d');

// Query to get attendance count for today
$sqlAttendanceToday = "SELECT COUNT(*) as presentToday FROM attendance WHERE LOGDATE = '$currentDate'";
$resultAttendanceToday = $conn->query($sqlAttendanceToday);
$presentToday = 0;
if ($resultAttendanceToday && $resultAttendanceToday->num_rows > 0) {
    $row = $resultAttendanceToday->fetch_assoc();
    $presentToday = $row['presentToday'];
}

// Query to get department data
$sqlDepartmentData = "SELECT * FROM department LIMIT 10";
$resultDepartmentData = $conn->query($sqlDepartmentData);

// Query to get employee data
$sqlEmployeeData = "SELECT * FROM employee LIMIT 10";
$resultEmployeeData = $conn->query($sqlEmployeeData);

// Query to get department data with employee count
$sqlDepartmentCount = "SELECT department.Department, COUNT(employee.EmployeeID) as employee_count 
                       FROM department 
                       LEFT JOIN employee ON department.Department = employee.Department 
                       GROUP BY department.Department";
$resultDepartmentCount = $conn->query($sqlDepartmentCount);

// Query to get attendance data for the past 7 days
$sqlWeeklyAttendance = "SELECT DATE(LOGDATE) as date, COUNT(*) as attendance_count 
                        FROM attendance 
                        WHERE LOGDATE >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
                        GROUP BY DATE(LOGDATE)
                        ORDER BY DATE(LOGDATE)";
$resultWeeklyAttendance = $conn->query($sqlWeeklyAttendance);

// Prepare data for charts
$departmentLabels = [];
$employeeCounts = [];
while($row = $resultDepartmentCount->fetch_assoc()) {
    $departmentLabels[] = $row['Department'];
    $employeeCounts[] = $row['employee_count'];
}

$dateLabels = [];
$attendanceCounts = [];
while($row = $resultWeeklyAttendance->fetch_assoc()) {
    $dateLabels[] = date('M d', strtotime($row['date']));
    $attendanceCounts[] = $row['attendance_count'];
}

// Query for monthly attendance statistics
$sqlMonthlyStats = "SELECT 
    YEAR(LOGDATE) as year,
    MONTH(LOGDATE) as month,
    COUNT(*) as attendance_count
FROM attendance 
WHERE LOGDATE >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
GROUP BY YEAR(LOGDATE), MONTH(LOGDATE)
ORDER BY year ASC, month ASC";
$resultMonthlyStats = $conn->query($sqlMonthlyStats);

// Query for top 5 employees with best attendance
$sqlTopEmployees = "SELECT 
    e.`Last Name` as LastName,
    e.`First Name` as FirstName,
    e.Department,
    e.EmployeeID,
    e.Image,
    COUNT(a.LOGDATE) as attendance_count 
FROM employee e 
LEFT JOIN attendance a ON e.EmployeeID = a.EMPLOYEEID 
WHERE a.LOGDATE >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) 
GROUP BY e.EmployeeID 
ORDER BY attendance_count DESC 
LIMIT 5";

$resultTopEmployees = $conn->query($sqlTopEmployees);

// Query for early arrivals
$sqlEarlyArrivals = "SELECT 
    COUNT(*) as early_count 
FROM attendance 
WHERE TIME(TIMEIN) <= '08:00:00' 
AND LOGDATE = CURRENT_DATE";
$resultEarlyArrivals = $conn->query($sqlEarlyArrivals);
$earlyArrivals = $resultEarlyArrivals->fetch_assoc()['early_count'];

// Query for absences today
$sqlAbsences = "SELECT 
    (SELECT COUNT(*) FROM employee) - 
    (SELECT COUNT(DISTINCT EmployeeID) FROM attendance WHERE LOGDATE = CURRENT_DATE) 
    as absent_count";
$resultAbsences = $conn->query($sqlAbsences);
$absentToday = $resultAbsences->fetch_assoc()['absent_count'];

// Prepare monthly data
$monthLabels = [];
$monthlyAttendance = [];
while($row = $resultMonthlyStats->fetch_assoc()) {
    $monthName = date("M Y", mktime(0, 0, 0, $row['month'], 1, $row['year']));
    $monthLabels[] = $monthName;
    $monthlyAttendance[] = $row['attendance_count'];
}
?>

<div class="container">
    <div class="separator">
        <h2 class="dashboard">Analytics Dashboard</h2>
    </div>
    
    <div class="big-box">
        <div class="welcome-text">
            <img src="3.png" alt="Image" style="width: 50px; height: 50px; vertical-align: middle;">
            Welcome to Attendance Management System
        </div>
        <span class="time" id="clock"></span>
    </div>

    <div class="boxes">
        <div class="box">
            <h2>Present Today</h2>
            <p><?php echo $presentToday; ?></p>
        </div>
        <div class="box">
            <h2>Early Arrivals</h2>
            <p><?php echo $earlyArrivals; ?></p>
        </div>
        <div class="box">
            <h2>Absent Today</h2>
            <p><?php echo $absentToday; ?></p>
        </div>
        <div class="box">
            <h2>Total Employees</h2>
            <p><?php echo $resultEmployeeData->num_rows; ?></p>
        </div>
    </div>

    <div class="charts-container">
        <div class="chart-wrapper">
            <canvas id="departmentChart"></canvas>
        </div>
        <div class="chart-wrapper">
            <canvas id="weeklyAttendanceChart"></canvas>
        </div>
        <div class="chart-wrapper full-width">
            <canvas id="monthlyAttendanceChart"></canvas>
        </div>
    </div>

    <div class="top-employees">
        <h2>Top Performers This Month</h2>
        <div class="employee-list">
            <?php while($employee = $resultTopEmployees->fetch_assoc()): ?>
            <div class="employee-card">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($employee['Image']); ?>" alt="Employee" onerror="this.src='pic.png'">
                <div class="employee-info">
                    <h3><?php echo $employee['LastName'] . ', ' . $employee['FirstName']; ?></h3>
                    <div class="department">
                        <i class="fas fa-building"></i>
                        <?php echo $employee['Department']; ?>
                    </div>
                    <div class="attendance">
                        <i class="fas fa-calendar-check"></i>
                        <?php echo $employee['attendance_count']; ?> days present
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
// Department Distribution Chart
const departmentCtx = document.getElementById('departmentChart').getContext('2d');
new Chart(departmentCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($departmentLabels); ?>,
        datasets: [{
            data: <?php echo json_encode($employeeCounts); ?>,
            backgroundColor: [
                '#4CAF50',
                '#66BB6A',
                '#FF5722',
                '#2196F3',
                '#9C27B0',
                '#FFC107'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Employee Distribution by Department',
                font: {
                    size: 16,
                    weight: 'bold'
                }
            },
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Weekly Attendance Chart
const weeklyCtx = document.getElementById('weeklyAttendanceChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dateLabels); ?>,
        datasets: [{
            label: 'Daily Attendance',
            data: <?php echo json_encode($attendanceCounts); ?>,
            fill: true,
            backgroundColor: 'rgba(76, 175, 80, 0.1)',
            borderColor: '#4CAF50',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Weekly Attendance Overview',
                font: {
                    size: 16,
                    weight: 'bold'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Monthly Attendance Chart
const monthlyCtx = document.getElementById('monthlyAttendanceChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($monthLabels); ?>,
        datasets: [{
            label: 'Monthly Attendance',
            data: <?php echo json_encode($monthlyAttendance); ?>,
            backgroundColor: '#FF5722',
            borderColor: '#D84315',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: '6-Month Attendance Trend',
                font: {
                    size: 16,
                    weight: 'bold'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5
                }
            }
        }
    }
});

// Update time function
function updateTime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    var timeString = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
    var dateString = now.toLocaleDateString('en-US', { 
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById('clock').textContent = timeString + ' | ' + dateString;
}

setInterval(updateTime, 1000);
updateTime();
</script>
</body>
</html>
