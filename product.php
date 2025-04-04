<?php
// Bootstrap the application
require_once 'config/config.php';
require_once UTILS_PATH . '/Helpers.php';
require_once CONTROLLERS_PATH . '/ProductController.php';
require_once CONTROLLERS_PATH . '/CartController.php';

// Set current page for menu highlighting
$current_page = 'products';
$page_title = 'Product Details';

// Create controller instances
$productController = new ProductController();
$cartController = new CartController();

// Process cart action first
$cartController->addToCart();

// Process any form submissions for reviews
$errors = $productController->submitReview();
$productController->markReviewHelpful();

// Get the product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Display the product view
$productController->view($product_id);
?>