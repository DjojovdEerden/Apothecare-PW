<?php
// Start session for cart functionality
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration and functions
require_once 'connection/db_config.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Get current product from database
$product = get_product($product_id);

// If product not found, redirect to products page
if (!$product) {
    header('Location: products.php');
    exit;
}

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
$related_products = get_related_products($product_id, $product['category_id']);

// Include the reviews system (Using database functions instead of session storage)
// Handle form submissions for reviews
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle new review submission
    if (isset($_POST['submit_review'])) {
        $author = isset($_POST['author']) ? trim($_POST['author']) : '';
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
        
        // Simple validation
        $errors = [];
        if (empty($author)) {
            $errors[] = 'Please enter your name';
        }
        if (empty($comment)) {
            $errors[] = 'Please enter a comment';
        }
        if ($rating < 1 || $rating > 5) {
            $errors[] = 'Please select a valid rating';
        }
        
        if (empty($errors)) {
            add_review($product_id, $author, $rating, $comment);
            $success_message = 'Your review has been submitted successfully!';
        }
    }
    
    // Handle marking a review as helpful
    if (isset($_POST['mark_helpful'])) {
        $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
        if ($review_id > 0) {
            mark_review_helpful($review_id);
            
            // Remember which reviews the user has marked as helpful
            if (!isset($_SESSION['helpful_reviews'])) {
                $_SESSION['helpful_reviews'] = [];
            }
            $_SESSION['helpful_reviews'][] = $review_id;
        }
    }
}

// Check if user has already marked a review as helpful
function user_marked_helpful($review_id) {
    return isset($_SESSION['helpful_reviews']) && in_array($review_id, $_SESSION['helpful_reviews']);
}

// Get reviews for this product
$reviews = get_product_reviews($product_id);
$avg_rating = get_average_rating($product_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']) ?> - Apothecare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .product-image { height: 300px; object-fit: contain; }
        .product-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; }
        .navbar { background-color: #2c3e50; }
        .product-price { font-size: 1.5rem; color: #27ae60; font-weight: bold; }
        .in-stock { color: #27ae60; }
        .out-of-stock { color: #e74c3c; }
        .review-card { transition: transform 0.2s; }
        .review-card:hover { transform: translateY(-3px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .star-rating { display: inline-flex; }
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
                     alt="<?= htmlspecialchars($product['product_name']) ?>" 
                     class="img-fluid product-image border p-2">
            </div>
            <div class="col-md-6">
                <h1><?= htmlspecialchars($product['product_name']) ?></h1>
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
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($success_message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="product.php?id=<?= $product_id ?>" method="post">
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
                                <div class="star-rating">
                                    <?php 
                                    // Render stars based on average rating
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= floor($avg_rating)) {
                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                        } elseif ($i - 0.5 <= $avg_rating) {
                                            echo '<i class="bi bi-star-half text-warning"></i>';
                                        } else {
                                            echo '<i class="bi bi-star text-warning"></i>';
                                        }
                                    }
                                    ?>
                                </div>
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
                                            <h5 class="card-title mb-0"><?= htmlspecialchars($review['author']) ?></h5>
                                            <div class="text-muted small">
                                                <?= date('F j, Y', strtotime($review['created_at'])) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="star-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?php if ($i <= $review['rating']): ?>
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-star text-warning"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <p class="card-text mt-3"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                    
                                    <div class="d-flex align-items-center mt-3">
                                        <form action="product.php?id=<?= $product_id ?>" method="post" class="me-3">
                                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                            <button type="submit" name="mark_helpful" class="btn btn-sm btn-outline-secondary <?= user_marked_helpful($review['id']) ? 'disabled' : '' ?>">
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
                             alt="<?= htmlspecialchars($related['product_name']) ?>" 
                             class="img-fluid mb-3" style="height: 150px; object-fit: contain;">
                        <h5><?= htmlspecialchars($related['product_name']) ?></h5>
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