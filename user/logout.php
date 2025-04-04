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

// Redirect to home page
Helpers::redirect(APP_URL . '/index.php');
?>
