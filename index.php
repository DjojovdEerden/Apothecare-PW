<?php
// Bootstrap the application
require_once 'config/config.php';
require_once UTILS_PATH . '/Helpers.php';
require_once MODELS_PATH . '/Product.php';
require_once MODELS_PATH . '/Category.php';

// Set current page for menu highlighting
$current_page = 'home';
$page_title = 'Home';

// Get featured products and categories
$productModel = new Product();
$categoryModel = new Category();

$featured_products = $productModel->getProducts(['limit' => 3]);
$categories = $categoryModel->getCategories();

// Include the view
require_once VIEWS_PATH . '/home/index.php';
?>
