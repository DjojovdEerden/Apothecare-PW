<?php require_once VIEWS_PATH . '/components/header.php'; ?>

    <!-- Product Details -->
    <div class="container mb-5">
        <?php if ($added_to_cart): ?>
        <div class="alert alert-success">
            Product added to your cart! <a href="<?= APP_URL ?>/cart.php">View Cart</a>
        </div>
        <?php endif; ?>
        
        <?php if ($review_added): ?>
        <div class="alert alert-success">
            Your review has been submitted successfully!
        </div>
        <?php endif; ?>
        
        <?php if ($helpful_marked): ?>
        <div class="alert alert-success">
            Thank you for marking this review as helpful!
        </div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="<?= APP_URL ?>/products.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <img src="<?= $product['image_url'] ?? 'https://via.placeholder.com/400x400?text=No+Image' ?>" 
                     alt="<?= Helpers::escape($product['product_name']) ?>" 
                     class="img-fluid product-detail-image border p-2">
            </div>
            <div class="col-md-6">
                <h1><?= Helpers::escape($product['product_name']) ?></h1>
                <p class="product-price"><?= Helpers::formatPrice($product['price']) ?></p>
                
                <div class="mb-3">
                    <?php if ($product['in_stock'] > 0): ?>
                    <span class="in-stock">In Stock (<?= $product['in_stock'] ?>)</span>
                    <?php else: ?>
                    <span class="out-of-stock">Out of Stock</span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?= nl2br(Helpers::escape($product['description'])) ?></p>
                </div>
                
                <?php if ($product['in_stock'] > 0): ?>
                <form action="<?= APP_URL ?>/product.php?id=<?= $product_id ?>" method="post">
                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-auto">
                            <label for="quantity" class="col-form-label">Quantity:</label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group" style="width: 130px;">
                                <button type="button" id="decrease-quantity" class="btn btn-outline-secondary">-</button>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                       max="<?= min($product['in_stock'], 10) ?>" class="form-control text-center">
                                <button type="button" id="increase-quantity" class="btn btn-outline-secondary">+</button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </form>
                <?php else: ?>
                <button class="btn btn-secondary" disabled>Out of Stock</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="row mt-5">
            <div class="col-12">
                <hr>
                <h2>Product Reviews</h2>
                
                <!-- Review Form -->
                <div class="review-form-container mt-4 p-4 bg-light rounded mb-5">
                    <h3>Write a Review</h3>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= Helpers::escape($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= APP_URL ?>/product.php?id=<?= $product_id ?>" method="post">
                        <input type="hidden" name="product_id" value="<?= $product_id ?>">
                        <div class="mb-3">
                            <label for="author" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="rating-select">
                                <div class="btn-group" role="group">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <input type="radio" class="btn-check" name="rating" id="rating<?= $i ?>" value="<?= $i ?>" <?= $i == 5 ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-warning" for="rating<?= $i ?>">
                                            <?= $i ?> <i class="bi bi-star-fill"></i>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comment" class="form-label">Your Review</label>
                            <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                        </div>
                        
                        <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
                
                <!-- Reviews List -->
                <div class="reviews-container">
                    <?php if (!empty($reviews)): ?>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3>Customer Feedback</h3>
                            <div class="d-flex align-items-center">
                                <span class="h4 mb-0 me-2"><?= $avg_rating ?></span>
                                <?= Helpers::renderStars($avg_rating) ?>
                                <span class="text-muted ms-2">(<?= count($reviews) ?> reviews)</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <h3 class="mb-3">Customer Feedback</h3>
                    <?php endif; ?>
                    
                    <?php if (empty($reviews)): ?>
                        <div class="alert alert-info">
                            There are no reviews yet. Be the first to review this product!
                        </div>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="card mb-3 review-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <h5 class="card-title mb-0"><?= Helpers::escape($review['author']) ?></h5>
                                            <div class="text-muted small">
                                                <?= date('F j, Y', strtotime($review['created_at'])) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <?= Helpers::renderStars($review['rating']) ?>
                                        </div>
                                    </div>
                                    
                                    <p class="card-text mt-3">
                                        <?php if(isset($review['COMMENT']) && !empty($review['COMMENT'])): ?>
                                            <?= nl2br(Helpers::escape($review['COMMENT'])) ?>
                                        <?php elseif(isset($review['comment']) && !empty($review['comment'])): ?>
                                            <?= nl2br(Helpers::escape($review['comment'])) ?>
                                        <?php else: ?>
                                            <em>No comment provided for this review.</em>
                                        <?php endif; ?>
                                    </p>
                                    
                                    <div class="d-flex align-items-center mt-3">
                                        <form action="<?= APP_URL ?>/product.php?id=<?= $product_id ?>" method="post" class="me-3">
                                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                            <button type="submit" name="mark_helpful" class="btn btn-sm btn-outline-secondary <?= Helpers::userMarkedHelpful($review['id']) ? 'disabled' : '' ?>">
                                                <i class="bi bi-hand-thumbs-up"></i> Helpful
                                                <?php if ($review['helpful_count'] > 0): ?>
                                                    <span class="ms-1">(<?= $review['helpful_count'] ?>)</span>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
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
                             alt="<?= Helpers::escape($related['product_name'] ?? $related['name']) ?>" 
                             class="img-fluid mb-3" style="height: 150px; object-fit: contain;">
                        <h5><?= Helpers::escape($related['product_name'] ?? $related['name']) ?></h5>
                        <p class="fw-bold"><?= Helpers::formatPrice($related['price']) ?></p>
                        <a href="<?= APP_URL ?>/product.php?id=<?= $related['id'] ?>" class="btn btn-outline-primary">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php require_once VIEWS_PATH . '/components/footer.php'; ?>
