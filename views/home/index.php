<?php require_once VIEWS_PATH . '/components/header.php'; ?>

<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1>Welcome to <?= APP_NAME ?></h1>
                <p class="lead">Your trusted pharmacy for health and wellness products.</p>
                <a href="<?= APP_URL ?>/products.php" class="btn btn-primary btn-lg">Shop Now</a>
            </div>
            <div class="col-md-6 text-center">
                <img src="<?= APP_URL ?>/assets/images/pharmacy-hero.jpg" alt="Pharmacy" class="img-fluid rounded shadow" 
                     style="max-height: 400px;">
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <!-- Featured Products -->
    <h2 class="mb-4">Featured Products</h2>
    <div class="row">
        <?php if (!empty($featured_products)): ?>
            <?php foreach($featured_products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="product-card h-100">
                        <img src="<?= $product['image_url'] ?? 'https://via.placeholder.com/200x200?text=No+Image' ?>" 
                             alt="<?= Helpers::escape($product['product_name']) ?>" 
                             class="img-fluid product-image mb-3 mx-auto d-block">
                        <h5><?= Helpers::escape($product['product_name']) ?></h5>
                        <p class="product-price"><?= Helpers::formatPrice($product['price']) ?></p>
                        <p class="small text-muted"><?= Helpers::truncateText($product['description'], 80) ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <?php if ($product['in_stock'] > 0): ?>
                                <span class="in-stock small">In Stock</span>
                                <?php else: ?>
                                <span class="out-of-stock small">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= APP_URL ?>/product.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No featured products found.</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Categories Section -->
    <h2 class="mb-4 mt-5">Shop by Category</h2>
    <div class="row">
        <?php if (!empty($categories)): ?>
            <?php foreach($categories as $category): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <h3 class="card-title"><?= Helpers::escape($category['name']) ?></h3>
                            <p class="card-text text-muted">
                                <?= isset($category['description']) ? Helpers::truncateText($category['description'], 100) : 'Browse our collection' ?>
                            </p>
                            <a href="<?= APP_URL ?>/products.php?category=<?= urlencode($category['name']) ?>" class="btn btn-outline-primary">
                                Browse Products
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No categories found.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/components/footer.php'; ?>
