<?php
include 'verify_login.php';

// Initialize tempDir variable
$tempDir = "temp/";
if (!file_exists($tempDir)) {
    mkdir($tempDir);
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

$employee = null;
$error = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM employee WHERE EmployeeID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        $error = "Employee not found. Please make sure the employee exists in the database.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee ID Card</title>
    <style>
        .id-card {
            width: 3.375in;
            height: 2.125in;
            background: white;
            border: 1px solid #000;
            margin: 10px auto;
            position: relative;
            page-break-after: always;
        }
        .id-card.front {
            padding: 10px;
        }
        .id-card.back {
            padding: 10px;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            display: block;
        }
        .header {
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .photo-area {
            width: 1in;
            height: 1in;
            border: 1px solid #ccc;
            margin: 5px;
            float: left;
        }
        .details {
            margin-left: 1.2in;
            font-size: 12px;
        }
        .qr-code {
            width: 1in;
            height: 1in;
            margin: 10px auto;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            position: absolute;
            bottom: 10px;
            width: 100%;
            left: 0;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            max-width: 500px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php if ($error): ?>
        <div class="error-message">
            <?php echo $error; ?>
            <br><br>
            <button onclick="window.close()">Close Window</button>
        </div>
    <?php else: ?>
        <div class="no-print">
            <button onclick="window.print()">Print ID Card</button>
            <br><br>
        </div>

        <!-- Front of ID -->
        <div class="id-card front">
            <img src="1.png" alt="Bangon Barangay Malinta Logo" class="logo">
            <div class="header">
                <strong>BANGON BARANGAY MALINTA</strong><br>
                Employee Identification Card
            </div>
            
            <div class="photo-area">
                <?php if (!empty($employee['Image'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($employee['Image']); ?>" alt="Employee Photo" style="width: 100%; height: 100%; object-fit: cover;">
                <?php endif; ?>
            </div>
            
            <div class="details">
                <p><strong>ID No:</strong> <?php echo $employee['EmployeeID']; ?></p>
                <p><strong>Name:</strong> <?php echo $employee['First Name'] . ' ' . $employee['Last Name']; ?></p>
                <p><strong>Department:</strong> <?php echo $employee['Department']; ?></p>
            </div>
            
            <div class="footer">
                <p>Valid until: <?php echo date('F d, Y', strtotime('+1 year')); ?></p>
            </div>
        </div>

        <!-- Back of ID -->
        <div class="id-card back">
            <div style="text-align: center; margin-top: 10px;">
                <div class="qr-code">
                    <?php
                    include "phpqrcode/qrlib.php";
                    $tempDir = "temp/";
                    if (!file_exists($tempDir)) {
                        mkdir($tempDir);
                    }
                    $fileName = $tempDir . 'employee_' . $employee['EmployeeID'] . '.png';
                    $qrContent = $employee['EmployeeID'];
                    QRcode::png($qrContent, $fileName, QR_ECLEVEL_L, 3);
                    echo '<img src="' . $fileName . '" alt="QR Code">';
                    ?>
                </div>
                
                <div style="margin-top: 10px;">
                    <p><strong>If found, please return to:</strong></p>
                    <p>Barangay Malinta Office<br>
                    Malinta, Valenzuela City<br>
                    Contact: (02) 8123-4567</p>
                </div>
                
                <div class="footer">
                    <p>This ID card is property of Barangay Malinta.<br>
                    Unauthorized use is strictly prohibited.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
<?php
// Clean up old QR code files
$files = glob($tempDir . '*.png');
foreach ($files as $file) {
    if (time() - filemtime($file) > 3600) { // Delete files older than 1 hour
        unlink($file);
    }
}
?>