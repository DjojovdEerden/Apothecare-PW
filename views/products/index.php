<?php require_once VIEWS_PATH . '/components/header.php'; ?>

    <!-- Products Section -->
    <div class="container mb-5">
        <h1 class="mb-4">All Products</h1>
        
        <!-- Combined Search and Filter -->
        <div class="search-container">
            <form action="<?= APP_URL ?>/products.php" method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="input-group mb-2 mb-md-0">
                            <input type="text" name="search" class="form-control" placeholder="Search products..." 
                                value="<?= Helpers::escape($search_term) ?>" aria-label="Search products">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i><span class="d-none d-md-inline"> Search</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-8 col-md-4">
                        <select name="category" class="form-select" aria-label="Filter by category">
                            <option value="">All Categories</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach($categories as $category): ?>
                                    <?php if(isset($category['name'])): ?>
                                    <option value="<?= Helpers::escape($category['name']) ?>" 
                                        <?= ($filter_category && strcasecmp($filter_category, $category['name']) === 0) ? 'selected' : '' ?>>
                                        <?= Helpers::escape($category['name']) ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No categories found in database</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-4 col-md-2">
                        <?php if ($search_term || $filter_category): ?>
                        <a href="<?= APP_URL ?>/products.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle"></i><span class="d-none d-md-inline"> Clear</span>
                        </a>
                        <?php else: ?>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-filter"></i><span class="d-none d-md-inline"> Apply</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Current Filter Display -->
        <?php if ($search_term || $filter_category): ?>
        <div class="mb-4 p-2 bg-light rounded">
            <p class="mb-0">
                <strong>Current filters:</strong>
                <?php if ($search_term): ?>
                    <span class="badge bg-primary me-2">Search: <?= Helpers::escape($search_term) ?></span>
                <?php endif; ?>
                <?php if ($filter_category): ?>
                    <span class="badge bg-secondary">Category: <?= Helpers::escape($filter_category) ?></span>
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php if (empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info">
                        <i class="bi bi-search" style="font-size: 2rem;"></i>
                        <h4 class="mt-3">No products found</h4>
                        <p>
                            <?php if ($search_term): ?>
                                No products match your search "<?= Helpers::escape($search_term) ?>".
                                <?php if ($filter_category): ?>
                                    Try searching in all categories.
                                <?php endif; ?>
                            <?php else: ?>
                                No products found in this category.
                            <?php endif; ?>
                        </p>
                        <a href="<?= APP_URL ?>/products.php" class="btn btn-outline-primary mt-2">View all products</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($products as $product): ?>
                    <div class="col">
                        <div class="product-card h-100">
                            <img src="<?= $product['image_url'] ?? 'https://via.placeholder.com/200x200?text=No+Image' ?>" 
                                 alt="<?= Helpers::escape($product['product_name']) ?>" 
                                 class="img-fluid product-image mb-3 mx-auto d-block" loading="lazy">
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
            <?php endif; ?>
        </div>
    </div>

<?php require_once VIEWS_PATH . '/components/footer.php'; ?>
