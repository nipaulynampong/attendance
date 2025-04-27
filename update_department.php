<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $department = $_POST['department'];
    $description = $_POST['description'];
    $head = $_POST['head'];
    $contact = $_POST['contact'];
    $status = $_POST['status'];

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

    // Prepare SQL statement to update department details
    $sql = "UPDATE department SET `Description`='$description', `Head`='$head', `Contact`='$contact', `Status`='$status' WHERE `Department`='$department'";

    // Execute SQL statement and check for errors
    if ($conn->query($sql) === TRUE) {
        // If update successful, redirect back to some page with success message
        echo "success";
    } else {
        // If there's an error, you can handle it here
        exit();
    }

} else {
    // If the form is not submitted properly, exit or redirect
    exit();
}
?>
