<?php
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

    // SQL query to select employee details based on ID
    $sql = "SELECT * FROM employee WHERE EmployeeID = $employeeID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of the employee
        $row = $result->fetch_assoc();
        // Format the output as bio-data
        echo '<div class="bio-data">';
        // Display image if available
        if (!empty($row["Image"])) {
            $imageData = base64_encode($row["Image"]);
            echo '<div class="section image">';
            echo '<img src="data:image/jpeg;base64,'.$imageData.'">';
            echo '</div>';
        }
        echo '<div class="section">';
        echo '<div class="label">Employee ID:</div>';
        echo '<div class="value">' . $row["EmployeeID"] . '</div>';
        echo '</div>';
        echo '<div class="section">';
        echo '<div class="label">Full Name:</div>';
        echo '<div class="value">' . $row["First Name"] . ' ' . $row["Middle Name"] . ' ' . $row["Last Name"] . '</div>';
        echo '</div>';
        echo '<div class="section">';
        echo '<div class="label">Suffix:</div>';
        echo '<div class="value">' . $row["Suffix"] . '</div>';
        echo '</div>';
        echo '<div class="section">';
        echo '<div class="label">Age:</div>';
        echo '<div class="value">' . $row["Age"] . '</div>';
        echo '</div>';
        echo '<div class="section">';
        echo '<div class="label">Birthday:</div>';
        echo '<div class="value">' . $row["Birthday"] . '</div>';
        echo '</div>';
        echo '<div class="section">';
        echo '<div class="label">Email Address:</div>';
        echo '<div class="value">' . $row["Email Address"] . '</div>';
        echo '</div>';
        echo '<div class="section">';
        echo '<div class="label">Address:</div>';
        echo '<div class="value">' . $row["Address"] . '</div>';
        echo '</div>';
        echo '<div class="section">';
        echo '<div class="label">Contact Number:</div>';
        echo '<div class="value">' . $row["Contact Number"] . '</div>';
        echo '</div>';
        echo '<div class="section">';
        echo '<div class="label">Department:</div>';
        echo '<div class="value">' . $row["Department"] . '</div>';
        echo '</div>';
        echo '</div>';
    } else {
        echo "0 results";
    }
    $conn->close();
} else {
    echo "Employee ID not provided";
}
?>
