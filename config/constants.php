<?php
// System Constants
define('SYSTEM_NAME', 'HRIS System');
define('SYSTEM_VERSION', '1.0.0');

// Session Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'HRIS_SESSION');

// Attendance Status Constants
define('STATUS_PRESENT', 'Present');
define('STATUS_LATE', 'Late');
define('STATUS_ABSENT', 'Absent');
define('STATUS_EARLY_OUT', 'Early Out');
define('STATUS_AUTO_TIMEOUT', 'Auto TimeOut');

// Time Settings
define('DEFAULT_TIMEIN', '08:00:00');
define('DEFAULT_TIMEOUT', '17:00:00');
define('LATE_THRESHOLD', '08:30:00');
define('EARLY_OUT_THRESHOLD', '16:30:00');

// Error Logging
define('LOG_PATH', __DIR__ . '/../logs');
define('MAX_LOG_FILES', 7); // Keep logs for a week

// Form Settings
define('CSRF_TOKEN_NAME', 'hris_token');
define('CSRF_TOKEN_LENGTH', 32);
define('FORM_MAX_ATTEMPTS', 3); 