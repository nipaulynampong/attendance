<?php
// List all images in the employee_images directory
$directory = 'employee_images/';

if (is_dir($directory)) {
    $files = scandir($directory);
    
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $filePath = $directory . $file;
            $fileSize = filesize($filePath);
            $fileDate = date("Y-m-d H:i:s", filemtime($filePath));
            
            // Extract employee ID from filename (assuming format like 2_1747152329.jpg)
            $employeeId = '';
            if (preg_match('/^(\d+)_/', $file, $matches)) {
                $employeeId = $matches[1];
            }
            
            echo "<li>";
            echo "<strong>File:</strong> " . htmlspecialchars($file) . "<br>";
            echo "<strong>Size:</strong> " . number_format($fileSize / 1024, 2) . " KB<br>";
            echo "<strong>Modified:</strong> " . $fileDate . "<br>";
            
            if ($employeeId) {
                echo "<strong>Employee ID:</strong> " . $employeeId . "<br>";
            }
            
            echo "<img src='" . htmlspecialchars($filePath) . "' style='max-width: 100px; margin: 5px 0;'><br>";
            echo "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>The employee_images directory does not exist or is not accessible.</p>";
}
?>
