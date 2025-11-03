<?php
/**
 * Application Configuration File
 * Configure database connection and application settings
 */

// ==========================================
// DATABASE CONFIGURATION (XAMPP Default)
// ==========================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'mvc_crud_app');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default password is empty
define('DB_CHARSET', 'utf8mb4');

// ==========================================
// APPLICATION SETTINGS
// ==========================================
define('APP_NAME', 'MVC CRUD Application');
define('APP_URL', 'http://localhost/swap2025'); // Adjust based on your XAMPP htdocs path
define('APP_ROOT', dirname(__DIR__));

// ==========================================
// SESSION SETTINGS
// ==========================================
define('SESSION_NAME', 'mvc_app_session');
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('SESSION_SECURE', false); // Set to true if using HTTPS
define('SESSION_HTTPONLY', true);

// ==========================================
// EMAIL SETTINGS (For Password Reset)
// ==========================================
// Using PHP mail() function - for production use SMTP
define('EMAIL_FROM', 'noreply@example.com');
define('EMAIL_FROM_NAME', APP_NAME);
define('EMAIL_ENABLED', true); // Set to false to disable email sending during testing

// For Gmail SMTP (optional - uncomment and configure)
// define('SMTP_HOST', 'smtp.gmail.com');
// define('SMTP_PORT', 587);
// define('SMTP_USERNAME', 'your-email@gmail.com');
// define('SMTP_PASSWORD', 'your-app-password');
// define('SMTP_ENCRYPTION', 'tls');

// ==========================================
// SECURITY SETTINGS
// ==========================================
define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);
define('PASSWORD_MIN_LENGTH', 8);
define('TOKEN_EXPIRY', 3600); // Password reset token expiry (1 hour)

// ==========================================
// PAGINATION
// ==========================================
define('ITEMS_PER_PAGE', 10);

// ==========================================
// TIMEZONE
// ==========================================
date_default_timezone_set('UTC');

// ==========================================
// ERROR REPORTING (For Development)
// ==========================================
// Set to false in production
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ==========================================
// AUTO-LOADER
// ==========================================
spl_autoload_register(function ($class) {
    $paths = [
        APP_ROOT . '/app/models/',
        APP_ROOT . '/app/controllers/',
        APP_ROOT . '/config/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
