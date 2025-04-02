<?php
// Start session for cart functionality
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sample product data (same as in product.php)
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

// Get unique categories
$categories = [];
foreach ($products as $product) {
    if (!in_array($product['category'], $categories)) {
        $categories[] = $product['category'];
    }
}

// Filter by category if set
$filter_category = isset($_GET['category']) ? $_GET['category'] : null;

// Handle search
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Apothecare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .product-card { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 20px; 
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .navbar { background-color: #2c3e50; }
        .product-price { font-size: 1.2rem; color: #27ae60; font-weight: bold; }
        .product-image { height: 200px; object-fit: contain; }
        .in-stock { color: #27ae60; }
        .out-of-stock { color: #e74c3c; }
        .search-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
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

    <!-- Products Section -->
    <div class="container mb-5">
        <h1 class="mb-4">All Products</h1>
        
        <!-- Combined Search and Filter -->
        <div class="search-container">
            <form action="products.php" method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search products..." 
                                value="<?= htmlspecialchars($search_term) ?>" aria-label="Search products">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="category" class="form-select" aria-label="Filter by category">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $category): ?>
                            <option value="<?= $category ?>" <?= $filter_category === $category ? 'selected' : '' ?>>
                                <?= $category ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <?php if ($search_term || $filter_category): ?>
                        <a href="products.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle"></i> Clear All
                        </a>
                        <?php else: ?>
                        <button type="submit" class="btn btn-outline-primary w-100">Apply</button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <?php 
            $found_products = false;
            foreach($products as $product): 
                // Apply category filter if set
                if ($filter_category !== null && $product['category'] !== $filter_category) {
                    continue;
                }
                
                // Apply search filter if term is provided
                if ($search_term !== '' && 
                    stripos($product['name'], $search_term) === false && 
                    stripos($product['description'], $search_term) === false &&
                    stripos($product['category'], $search_term) === false) {
                    continue;
                }
                
                $found_products = true;
            ?>
                <div class="col-md-4 mb-4">
                    <div class="product-card h-100">
                        <img src="<?= $product['image_url'] ?? 'https://via.placeholder.com/200x200?text=No+Image' ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="img-fluid product-image mb-3 mx-auto d-block">
                        <h5><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="product-price">€<?= number_format($product['price'], 2) ?></p>
                        <p class="small text-muted"><?= htmlspecialchars(substr($product['description'], 0, 80)) . (strlen($product['description']) > 80 ? '...' : '') ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <?php if ($product['in_stock'] > 0): ?>
                                <span class="in-stock small">In Stock</span>
                                <?php else: ?>
                                <span class="out-of-stock small">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                            <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (!$found_products): ?>
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info">
                        <i class="bi bi-search" style="font-size: 2rem;"></i>
                        <h4 class="mt-3">No products found</h4>
                        <p>
                            <?php if ($search_term): ?>
                                No products match your search "<?= htmlspecialchars($search_term) ?>".
                                <?php if ($filter_category): ?>
                                    Try searching in all categories.
                                <?php endif; ?>
                            <?php else: ?>
                                No products found in this category.
                            <?php endif; ?>
                        </p>
                        <a href="products.php" class="btn btn-outline-primary mt-2">View all products</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
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
