<?php
// Application configuration
define('APP_NAME', 'Apothecare');

// Dynamic APP_URL detection (works across different environments)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base_path = str_replace('/config', '', $path); // Remove '/config' if it's in the path
define('APP_URL', $protocol . $domain . $base_path);

define('APP_VERSION', '1.0.0');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'apothecare-pw');

// Path definitions - use __DIR__ for more reliable paths
define('BASE_PATH', dirname(__DIR__));
define('VIEWS_PATH', BASE_PATH . '/views');
define('MODELS_PATH', BASE_PATH . '/models');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');
define('UTILS_PATH', BASE_PATH . '/utils');
define('ASSETS_PATH', BASE_PATH . '/assets');

// Cart settings
define('CART_TAX_RATE', 0.21); // 21% VAT
define('CART_SHIPPING_THRESHOLD', 0); // Free shipping threshold
define('CART_SHIPPING_COST', 4.99); // Default shipping cost

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/error.log');

// Create the logs directory if it doesn't exist
if (!file_exists(BASE_PATH . '/logs')) {
    mkdir(BASE_PATH . '/logs', 0755, true);
}

// Session configuration - MUST come before session_start
if (session_status() == PHP_SESSION_NONE) {
    // Only set these if session hasn't started yet
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    // Start the session
    session_start();
}
?>
