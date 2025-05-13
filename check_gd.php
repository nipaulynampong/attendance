<?php
// Check if GD is enabled
echo "<h1>PHP GD Library Check</h1>";

if (extension_loaded('gd')) {
    echo "<p style='color:green'>GD library is enabled!</p>";
    echo "<p>GD Version: " . gd_info()['GD Version'] . "</p>";
    
    // List all GD functions
    echo "<h2>Available GD Functions:</h2>";
    $gd_functions = get_extension_funcs('gd');
    echo "<ul>";
    foreach ($gd_functions as $function) {
        echo "<li>" . $function . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>GD library is NOT enabled!</p>";
    echo "<p>You need to enable the GD library in your PHP configuration.</p>";
}
?>
