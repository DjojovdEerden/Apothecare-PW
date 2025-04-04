<?php
// Bootstrap the application
require_once 'config/config.php';
require_once UTILS_PATH . '/Helpers.php';
require_once CONTROLLERS_PATH . '/CartController.php';

// Set current page for menu highlighting
$current_page = 'cart';
$page_title = 'Your Shopping Cart';

// Create controller instance
$cartController = new CartController();

// Process cart actions
$cartController->removeFromCart();
$cartController->updateQuantity();
$cartController->clearCart();

// Display the cart page
$cartController->index();
?>
