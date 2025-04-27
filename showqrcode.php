<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee QR Code</title>
    <style>
        /* CSS for QR code formatting */
        body#unique-body-name {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px; /* Add padding for spacing */
            background-color: #fff; /* Set background color to white */
        }

        .qr-wrapper {
            text-align: center;
        }

        .employee-qr-code {
            display: inline-block;
            margin-top: 20px; /* Add margin for spacing */
        }

        .qr-container {
            border: 1px solid #ccc;
            padding: 20px;
        }

        .employee-qr-code img {
            width: 200px; /* Adjust image size */
            height: auto; /* Maintain aspect ratio */
            border-radius: 10px; /* Add border radius */
            border: 1px solid white; /* Add border for clarity */
        }

        .qr-button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button {
            background-color: #4F6F52;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin: 10px;
        }

        /* Hide all elements except for .employee-qr-code when printing */
        @media print {
            body#unique-body-name *:not(.employee-qr-code) {
                display: none !important;
            }
        }
    </style>
</head>
<body id="unique-body-name">

<div class="qr-wrapper">
    <div class="employee-qr-code" id="employee-qr-code">
        <?php
        // PHP code to fetch and display employee QR code based on ID
        if(isset($_GET['id'])) {
            $employeeID = $_GET['id'];

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

            // SQL query to select employee QR code based on ID
            $sql = "SELECT QRCode FROM employee WHERE EmployeeID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $employeeID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Output the QR code image
                $row = $result->fetch_assoc();
                $qrCodeData = $row["QRCode"];
                if (!empty($qrCodeData)) {
                    // Check if the data is a valid image
                    $imageData = base64_encode($qrCodeData);
                    if ($imageData !== false) {
                        // Display the QR code image
                        echo '<div class="paper qr-container"><img src="data:image/png;base64,' . $imageData . '" alt="QR Code"></div>';
                    } else {
                        echo "Error: Failed to decode QR code image data.";
                    }
                } else {
                    echo "Error: Empty QR code data retrieved from the database.";
                }
            } else {
                echo "Error: No QR code found for employee ID: " . $employeeID;
            }

            // Close prepared statement and database connection
            $stmt->close();
            $conn->close();
        } else {
            echo "Error: Employee ID not provided";
        }
        ?>
    </div>
</div>

<!-- <div class="qr-button-container">
    <button type="button" class="button" onclick="window.print()">Print QR Code</button>
</div> -->

</body>
</html>
