<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: white;
            font-family: 'Poppins', sans-serif;
            margin: 20px;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5efe6;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-height: 85vh;
            overflow-y: scroll;
        }

        /* Scrollbar styling for container */
        .container::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb {
            background: #4F6F52;
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb:hover {
            background: #3A4D39;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        h2 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        #searchForm {
            flex-grow: 1;
            margin: 0 20px;
            position: relative;
        }

        .search-container {
            position: relative;
            max-width: 400px;
            margin: 0 auto;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 40px 12px 15px;
            font-size: 14px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        input[type="text"]:focus {
            border-color: #4F6F52;
            box-shadow: 0 2px 8px rgba(79,111,82,0.2);
            outline: none;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .print-button {
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
            background-color: #4F6F52;
            color: white;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .print-button:hover {
            background-color: #3A4D39;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }

        table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        /* Column width control */
        th:nth-child(1), td:nth-child(1) { width: 5%; } /* ID */
        th:nth-child(2), td:nth-child(2) { width: 6%; } /* Image */
        th:nth-child(3), td:nth-child(3) { width: 15%; } /* Name */
        th:nth-child(4), td:nth-child(4) { width: 5%; } /* Age */
        th:nth-child(5), td:nth-child(5) { width: 8%; } /* Birthday */
        th:nth-child(6), td:nth-child(6) { width: 22%; } /* Address */
        th:nth-child(7), td:nth-child(7) { width: 6%; } /* Gender */
        th:nth-child(8), td:nth-child(8) { width: 10%; } /* Contact */
        th:nth-child(9), td:nth-child(9) { width: 13%; } /* Email */
        th:nth-child(10), td:nth-child(10) { width: 10%; } /* Department */

        th, td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #eef0f5;
        }

        th {
            background-color: #4F6F52;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f9fafb;
        }

        td {
            font-size: 14px;
            color: #4a5568;
        }

        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
                font-size: 11pt;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .container {
                width: 100%;
                max-width: none;
                margin: 0;
                padding: 0;
                box-shadow: none;
                background-color: white;
                overflow: visible;
                max-height: none;
            }

            .header {
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 1px solid #000;
            }

            h2 {
                font-size: 18pt;
                margin-bottom: 10px;
                text-align: center;
            }

            #searchForm, .print-button {
                display: none !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                page-break-inside: auto;
                font-size: 9pt;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            th {
                background-color: #4F6F52 !important;
                color: white !important;
                font-weight: bold;
                padding: 8px;
                text-align: left;
                border-bottom: 2px solid #000;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            td {
                padding: 6px 8px;
                border-bottom: 1px solid #ddd;
                font-size: 8pt;
                line-height: 1.2;
            }

            td img {
                max-width: 30px;
                height: 30px;
            }

            @page {
                size: landscape;
                margin: 0.5cm;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
            
            /* Add page title and date */
            .container:before {
                content: "Employee Information Report - Printed on " attr(data-print-date);
                display: block;
                text-align: center;
                font-size: 14pt;
                font-weight: bold;
                margin-bottom: 15px;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .search-container {
                width: 100%;
            }

            th, td {
                padding: 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Employee Information</h2>
            <form id="searchForm" action="" method="GET">
                <div class="search-container">
                    <input type="text" id="search" name="search" placeholder="Search by ID, Name, or Department...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </form>
            <button class="print-button" id="print-btn">
                <i class="fas fa-print"></i>
                Print Report
            </button>
        </div>
        
        <div class="table-container">
            <table id="employeeTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Birthday</th>
                        <th>Address</th>
                        <th>Gender</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "hris"; 

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM employee";
if(isset($_GET['search'])) {
    $search = $_GET['search'];
 
    
    $sql .= " WHERE EmployeeID LIKE '%$search%' OR CONCAT(`Last Name`, ' ', `First Name`, ' ', `Middle Name`, ' ', `Suffix`) LIKE '%$search%' OR Department LIKE '%$search%'";
}
$sql .= " ORDER BY `Last Name` ASC";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["EmployeeID"] . "</td>";
        // Check if image exists and is not empty
        if (!empty($row["Image"])) {
            echo "<td><img src='data:image/jpeg;base64," . base64_encode($row["Image"]) . "' alt='Employee Image' style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover;'></td>";
        } else {
            // Display default image if no image is available
            echo "<td><div style='width: 50px; height: 50px; border-radius: 50%; background-color: #4F6F52; display: flex; align-items: center; justify-content: center;'><i class='fas fa-user' style='color: white; font-size: 20px;'></i></div></td>";
        }
        echo "<td>" . $row["Last Name"] . " " . $row["First Name"] . " " . $row["Middle Name"] . " " . $row["Suffix"] . "</td>";
        echo "<td>" . $row["Age"] . "</td>";
        echo "<td>" . $row["Birthday"] . "</td>";
        echo "<td>" . $row["Address"] . "</td>";
        echo "<td>" . $row["Gender"] . "</td>";
        echo "<td>" . $row["Contact Number"] . "</td>";
        echo "<td>" . $row["Email Address"] . "</td>";
        echo "<td>" . $row["Department"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>No records found</td></tr>";
}
$conn->close();
?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Function to refresh the table
        function refreshTable() {
            var searchValue = document.getElementById('search').value.trim();
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_employees.php?search=' + searchValue, true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    document.getElementById('employeeTable').innerHTML = xhr.responseText;
                } else {
                    console.error(xhr.statusText);
                }
            };
            xhr.send();
        }

        // Event listener for changes in the search input
        document.getElementById('search').addEventListener('input', function() {
            if (this.value.trim() === '') {
                refreshTable();
            }
        });

        // Set date attribute for printing
        document.addEventListener('DOMContentLoaded', function() {
            // Format current date
            var now = new Date();
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            var formattedDate = now.toLocaleDateString('en-US', options);
            
            // Set the date attribute for printing
            document.querySelector('.container').setAttribute('data-print-date', formattedDate);
        });

        // Better print function to ensure styles are applied
        document.querySelector('#print-btn').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update date attribute before printing
            var now = new Date();
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            var formattedDate = now.toLocaleDateString('en-US', options);
            document.querySelector('.container').setAttribute('data-print-date', formattedDate);
            
            // Print after a short delay to ensure styles are applied
            setTimeout(function() {
                window.print();
            }, 200);
        });

        // Initial table load
        refreshTable();
    </script>
</body>
</html>
