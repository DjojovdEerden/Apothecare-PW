<?php
// Initialize the session
session_start();

// Include config file
require_once '../config/config.php';
require_once UTILS_PATH . '/Helpers.php';

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// redirect URL to ensure it goes to the homepage
$home_url = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/') . '/index.php';
header("Location: $home_url");
exit;
?>
