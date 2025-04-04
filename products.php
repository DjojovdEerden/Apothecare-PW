<?php
// Bootstrap the application
require_once 'config/config.php';
require_once UTILS_PATH . '/Helpers.php';
require_once CONTROLLERS_PATH . '/ProductController.php';

// Set current page for menu highlighting
$current_page = 'products';
$page_title = 'All Products';

// Create controller instance
$productController = new ProductController();

// Display the products page
$productController->index();
?>
