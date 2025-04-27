<?php
// Check if schedule_id is set and not empty
if(isset($_POST['schedule_id']) && !empty($_POST['schedule_id'])) {
    // Get the schedule ID from the form
    $schedule_id = $_POST['schedule_id'];

    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "hris");

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    // Prepare SQL statement to delete the schedule record
    $delete_query = "DELETE FROM schedule WHERE ID = $schedule_id";

    // Execute the delete query
    if (mysqli_query($conn, $delete_query)) {
        // If deletion is successful, redirect back to the schedule page
        header("Location: schedule.php");
        exit();
    } else {
        // If there's an error, display an error message
        echo "Error deleting schedule: " . mysqli_error($conn);
    }

    // Close database connection
    mysqli_close($conn);
} else {
    // If schedule_id is not set or empty, redirect back to the schedule page
    header("Location: schedule.php");
    exit();
}
?>
