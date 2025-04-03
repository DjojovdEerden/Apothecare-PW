<?php
// Start session for cart functionality
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration and functions
require_once 'connection/db_config.php';

// Initialize cart if needed
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Remove item from cart
    if (isset($_POST['remove_item'])) {
        $remove_id = (int)$_POST['remove_id'];
        if (isset($_SESSION['cart'][$remove_id])) {
            unset($_SESSION['cart'][$remove_id]);
            $item_removed = true;
        }
    }
    
    // Update quantity
    if (isset($_POST['update_quantity'])) {
        $update_id = (int)$_POST['update_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity > 0 && isset($_SESSION['cart'][$update_id])) {
            $_SESSION['cart'][$update_id] = $quantity;
            $quantity_updated = true;
        }
    }
    
    // Clear cart
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        $cart_cleared = true;
    }
}

// Get products in cart
$cart_items = [];
$subtotal = 0;
$total_items = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $product = get_product($id);
        if ($product) {
            $cart_items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'total' => $product['price'] * $quantity
            ];
            $subtotal += $product['price'] * $quantity;
            $total_items += $quantity;
        }
    }
}

// Calculate shipping and tax
$shipping = $subtotal > 0 ? 4.99 : 0;
$tax_rate = 0.21; // 21% VAT
$tax = $subtotal * $tax_rate;
$total = $subtotal + $shipping + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart - Apothecare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .navbar { background-color: #2c3e50; }
        .cart-item-img { height: 80px; width: 80px; object-fit: contain; }
        .cart-summary { background-color: #f8f9fa; border-radius: 8px; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Apothecare</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <div class="d-flex">
                    <a href="cart.php" class="btn btn-outline-light active">
                        Cart
                        <?php if (!empty($_SESSION['cart'])): ?>
                        <span class="badge bg-danger"><?= array_sum($_SESSION['cart']) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Cart Contents -->
    <div class="container mb-5">
        <h1 class="mb-4">Your Shopping Cart</h1>
        
        <?php if (isset($item_removed) && $item_removed): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Item removed from your cart.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($quantity_updated) && $quantity_updated): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Cart updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($cart_cleared) && $cart_cleared): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Your cart has been cleared.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info">
                <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                <p class="mt-3">Your shopping cart is empty.</p>
                <a href="products.php" class="btn btn-primary mt-2">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <!-- Cart Items -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Cart Items (<?= $total_items ?>)</h5>
                            <form action="" method="post" onsubmit="return confirm('Are you sure you want to clear your cart?')">
                                <button type="submit" name="clear_cart" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Clear Cart
                                </button>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px">Product</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($cart_items as $item): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= $item['product']['image_url'] ?? 'https://via.placeholder.com/80x80?text=No+Image' ?>" 
                                                         alt="<?= htmlspecialchars($item['product']['product_name']) ?>" 
                                                         class="cart-item-img border">
                                                </td>
                                                <td>
                                                    <a href="product.php?id=<?= $item['product']['id'] ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($item['product']['product_name']) ?>
                                                    </a>
                                                </td>
                                                <td>€<?= number_format($item['product']['price'], 2) ?></td>
                                                <td>
                                                    <form action="" method="post" class="quantity-form">
                                                        <input type="hidden" name="update_id" value="<?= $item['product']['id'] ?>">
                                                        <div class="input-group" style="width: 100px">
                                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                                                   min="1" max="<?= min($item['product']['in_stock'], 10) ?>" 
                                                                   class="form-control form-control-sm">
                                                            <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-secondary">
                                                                <i class="bi bi-arrow-repeat"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td>€<?= number_format($item['total'], 2) ?></td>
                                                <td>
                                                    <form action="" method="post" onsubmit="return confirm('Remove this item from cart?')">
                                                        <input type="hidden" name="remove_id" value="<?= $item['product']['id'] ?>">
                                                        <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="card cart-summary">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>€<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>€<?= number_format($shipping, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (21%):</span>
                                <span>€<?= number_format($tax, 2) ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong>€<?= number_format($total, 2) ?></strong>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-primary">Proceed to Checkout</a>
                                <a href="products/products.php" class="btn btn-outline-secondary">Continue Shopping</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Apothecare. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
