<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $department = $_POST['department'];
    $timeIn = $_POST['timeIn'];
    $timeOut = $_POST['timeOut'];

    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "hris");

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    // Escape special characters to prevent SQL injection
    $department = mysqli_real_escape_string($conn, $department);
    $timeIn = mysqli_real_escape_string($conn, $timeIn);
    $timeOut = mysqli_real_escape_string($conn, $timeOut);

    // Prepare insert query
    $insert_query = "INSERT INTO schedule (Department, TimeIn, TimeOut) VALUES ('$department', '$timeIn', '$timeOut')";

    // Execute insert query
    if (mysqli_query($conn, $insert_query)) {
        // If insertion is successful, redirect back to the schedule page
        header("Location: schedule.php");
        exit();
    } else {
        // If there's an error, display an error message
        echo "Error: " . $insert_query . "<br>" . mysqli_error($conn);
    }

    // Close database connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Form</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap">
    <style>
        body {
            font-family: 'Poppins';
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .container {
            float: left;
            width: 400px;
            margin-left: 20px;
            margin-top: 10px;
            height: 420px;
            padding: 40px;
            border-radius: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .con-container {
            float: left;
            width: 570px;
            margin-left: 30px;
            margin-top: 10px;
            height: 420px;
            padding: 40px;
            border-radius: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 18px;
        }

        select, input[type="time"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 20px;
            cursor: pointer;
            text-align: center;
            font-family: 'Poppins';
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #4F6F52;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #4F6F40;
        }

        .big-container {
            max-width: 1500px;
            height: 602px;
            border: 2px solid #f5efe6;
            margin-top: 7px;
            margin-left: 5px;
            border-radius: 45px;
            background-color: #f5efe6;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: auto; /* To add scrollbar if content exceeds container height */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-family: 'Poppins';
        }

        th {
            background-color: #4F6F52;
            color: white;
        }
        td {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="big-container">
    <h2 style="text-align: left; margin-left: 40px;">Manage Department Schedule</h2>
    <div class="container">
        <h2>Schedule Form</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="department">Department:</label>
                <select name="department" id="department">
                    <?php
                    // Database connection
                    $conn = mysqli_connect("localhost", "root", "", "hris");
                    // Fetch departments from database
                    $query = "SELECT * FROM department";
                    $result = mysqli_query($conn, $query);
                    // Populate options dynamically from database
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['Department'] . "'>" . $row['Department'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="timeIn">Time In:</label>
                <input type="time" id="timeIn" name="timeIn" required>
            </div>
            <div class="form-group">
                <label for="timeOut">Time Out:</label>
                <input type="time" id="timeOut" name="timeOut" required>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <div class="con-container">
        <h2>Schedule Information</h2>
        <table>
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Schedule Time-In</th>
                    <th>Schedule Time-Out</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Retrieve schedule information from the database
                $conn = mysqli_connect("localhost", "root", "", "hris");
                $query = "SELECT * FROM schedule";
                $result = mysqli_query($conn, $query);
                // Display schedule information in table rows
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['Department'] . "</td>";
                    echo "<td>" . $row['TimeIn'] . "</td>";
                    echo "<td>" . $row['TimeOut'] . "</td>";
                    echo "<td><form action='delete_schedule.php' method='post'><input type='hidden' name='schedule_id' value='" . $row['ID'] . "'><button type='submit'>Delete</button></form></td>";
                    echo "</tr>";
                }
                // Close database connection
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
