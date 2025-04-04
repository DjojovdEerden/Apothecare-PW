<?php
// Bootstrap the application
require_once 'config/config.php';
require_once UTILS_PATH . '/Helpers.php';
require_once CONTROLLERS_PATH . '/AccountController.php';

// Set current page for menu highlighting
$current_page = 'account';
$page_title = 'My Account';

// Create controller instance
$accountController = new AccountController();

// Display the account page
$accountController->index();
?>
