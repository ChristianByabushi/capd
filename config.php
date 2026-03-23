<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'capd_db');

// App config
define('BASE_URL', 'http://localhost/capd');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
define('DEFAULT_LANG', 'fr');

// Supported languages
define('LANGUAGES', ['fr' => 'Français', 'en' => 'English', 'sw' => 'Kiswahili']);

// Timezone
date_default_timezone_set('Africa/Kinshasa');

// Environment: set to 'production' on cPanel
define('APP_ENV', 'development');

// Error reporting — never expose errors in production
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// CSRF secret — change this to a long random string on production
define('CSRF_SECRET', 'capd_csrf_s3cr3t_change_me_2024');
