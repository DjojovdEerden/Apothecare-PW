<?php
// Start session for cart functionality
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sample product data (simulate database)
$products = [
    1 => [
        'id' => 1,
        'name' => 'Vitamin C 1000mg',
        'price' => 15.99,
        'description' => 'High-strength vitamin C supplement that supports immune function.',
        'image_url' => 'images/products/vitamin-c.jpg',
        'category' => 'Supplements',
        'in_stock' => 25
    ],
    2 => [
        'id' => 2,
        'name' => 'Omega-3 Fish Oil',
        'price' => 19.99,
        'description' => 'Pure fish oil supplement rich in omega-3 fatty acids for heart health.',
        'image_url' => 'images/products/omega-3.jpg',
        'category' => 'Supplements',
        'in_stock' => 15
    ],
    3 => [
        'id' => 3,
        'name' => 'Hydrating Face Cream',
        'price' => 24.99,
        'description' => 'Rich, nourishing face cream that hydrates and soothes dry skin.',
        'image_url' => 'images/products/face-cream.jpg',
        'category' => 'Skincare',
        'in_stock' => 8
    ],
    4 => [
        'id' => 4,
        'name' => 'First Aid Kit',
        'price' => 29.99,
        'description' => 'Complete first aid kit for home emergencies.',
        'image_url' => 'images/products/first-aid.jpg',
        'category' => 'Medical Supplies',
        'in_stock' => 12
    ]
];

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Get current product
$product = isset($products[$product_id]) ? $products[$product_id] : $products[1];

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Initialize cart if needed
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add to cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    $added_to_cart = true;
}

// Find related products
$related_products = [];
foreach ($products as $p) {
    if ($p['id'] != $product_id && $p['category'] == $product['category']) {
        $related_products[] = $p;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Apothecare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image { height: 300px; object-fit: contain; }
        .product-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; }
        .navbar { background-color: #2c3e50; }
        .product-price { font-size: 1.5rem; color: #27ae60; font-weight: bold; }
        .in-stock { color: #27ae60; }
        .out-of-stock { color: #e74c3c; }
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
                    <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <div class="d-flex">
                    <a href="cart.php" class="btn btn-outline-light">
                        Cart
                        <?php if (!empty($_SESSION['cart'])): ?>
                        <span class="badge bg-danger"><?= array_sum($_SESSION['cart']) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Product Details -->
    <div class="container mb-5">
        <?php if (isset($added_to_cart) && $added_to_cart): ?>
        <div class="alert alert-success">
            Product added to your cart! <a href="cart.php">View Cart</a>
        </div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="products.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <img src="<?= $product['image_url'] ?? 'https://via.placeholder.com/400x400?text=No+Image' ?>" 
                     alt="<?= htmlspecialchars($product['name']) ?>" 
                     class="img-fluid product-image border p-2">
            </div>
            <div class="col-md-6">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                <p class="product-price">€<?= number_format($product['price'], 2) ?></p>
                
                <div class="mb-3">
                    <?php if ($product['in_stock'] > 0): ?>
                    <span class="in-stock">In Stock (<?= $product['in_stock'] ?>)</span>
                    <?php else: ?>
                    <span class="out-of-stock">Out of Stock</span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
                
                <?php if ($product['in_stock'] > 0): ?>
                <form action="product.php?id=<?= $product_id ?>" method="post">
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-auto">
                            <label for="quantity" class="col-form-label">Quantity:</label>
                        </div>
                        <div class="col-auto">
                            <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                   max="<?= min($product['in_stock'], 10) ?>" class="form-control" style="width: 70px;">
                        </div>
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                </form>
                <?php else: ?>
                <button class="btn btn-secondary" disabled>Out of Stock</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
        <div class="mt-5">
            <h2>Related Products</h2>
            <div class="row">
                <?php foreach ($related_products as $related): ?>
                <div class="col-md-4">
                    <div class="product-card text-center">
                        <img src="<?= $related['image_url'] ?? 'https://via.placeholder.com/200x200?text=No+Image' ?>" 
                             alt="<?= htmlspecialchars($related['name']) ?>" 
                             class="img-fluid mb-3" style="height: 150px; object-fit: contain;">
                        <h5><?= htmlspecialchars($related['name']) ?></h5>
                        <p class="fw-bold">€<?= number_format($related['price'], 2) ?></p>
                        <a href="product.php?id=<?= $related['id'] ?>" class="btn btn-outline-primary">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
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