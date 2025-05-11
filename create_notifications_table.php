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

// SQL to create notifications table
$sql = "
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `event_type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "Notifications table created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Add foreign key constraint if admin table exists
$sql = "
ALTER TABLE `notifications` 
ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;
";

if ($conn->query($sql) === TRUE) {
    echo "Foreign key constraint added successfully<br>";
} else {
    echo "Error adding foreign key constraint: " . $conn->error . "<br>";
}

// Create a test notification
$sql = "
INSERT INTO `notifications` (`user_id`, `event_type`, `message`, `reference_id`, `timestamp`, `is_read`) 
VALUES (NULL, 'system', 'Notification system has been set up successfully', NULL, CURRENT_TIMESTAMP, 0);
";

if ($conn->query($sql) === TRUE) {
    echo "Test notification created successfully<br>";
} else {
    echo "Error creating test notification: " . $conn->error . "<br>";
}

$conn->close();

echo "Done!";
?>

