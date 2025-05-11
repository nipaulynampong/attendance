<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hris');

// Database connection retry settings
define('DB_MAX_RETRIES', 3);
define('DB_RETRY_DELAY', 2); // seconds

// Connection timeout
define('DB_TIMEOUT', 30); // seconds 