<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to generate a unique ID for reviews (simulating a database)
function generate_review_id() {
    return uniqid('rev_');
}

// Function to get current date/time formatted
function get_current_datetime() {
    return date('Y-m-d H:i:s');
}

// Function to get product reviews (in a real app, this would be from a database)
function get_product_reviews($product_id) {
    // Initialize reviews session storage if needed
    if (!isset($_SESSION['product_reviews'])) {
        $_SESSION['product_reviews'] = [];
        
        // Add some sample reviews for demonstration
        $sample_reviews = [
            1 => [
                [
                    'id' => 'rev_sample1',
                    'product_id' => 1,
                    'author' => 'John D.',
                    'rating' => 5,
                    'comment' => 'This vitamin C supplement really helped boost my immune system during the winter months!',
                    'date' => '2023-05-15 14:30:00',
                    'helpful_count' => 7
                ],
                [
                    'id' => 'rev_sample2',
                    'product_id' => 1,
                    'author' => 'Sarah M.',
                    'rating' => 4,
                    'comment' => 'Good quality product. I take it daily and notice the difference when I forget.',
                    'date' => '2023-06-22 09:15:00',
                    'helpful_count' => 3
                ]
            ],
            2 => [
                [
                    'id' => 'rev_sample3',
                    'product_id' => 2,
                    'author' => 'Michael P.',
                    'rating' => 5,
                    'comment' => 'Best omega-3 supplement I\'ve tried. No fishy aftertaste!',
                    'date' => '2023-04-10 16:45:00',
                    'helpful_count' => 12
                ]
            ]
        ];
        
        $_SESSION['product_reviews'] = $sample_reviews;
    }
    
    // Return reviews for this product if they exist
    return isset($_SESSION['product_reviews'][$product_id]) 
        ? $_SESSION['product_reviews'][$product_id] 
        : [];
}

// Function to add a new review
function add_review($product_id, $author, $rating, $comment) {
    // Create review data
    $review = [
        'id' => generate_review_id(),
        'product_id' => $product_id,
        'author' => $author,
        'rating' => $rating,
        'comment' => $comment,
        'date' => get_current_datetime(),
        'helpful_count' => 0
    ];
    
    // Add to session storage
    if (!isset($_SESSION['product_reviews'][$product_id])) {
        $_SESSION['product_reviews'][$product_id] = [];
    }
    
    $_SESSION['product_reviews'][$product_id][] = $review;
    
    return $review;
}

// Function to mark a review as helpful
function mark_review_helpful($review_id) {
    // Loop through all products and reviews to find the one to update
    if (isset($_SESSION['product_reviews'])) {
        foreach ($_SESSION['product_reviews'] as $product_id => $reviews) {
            foreach ($reviews as $index => $review) {
                if ($review['id'] === $review_id) {
                    $_SESSION['product_reviews'][$product_id][$index]['helpful_count']++;
                    return true;
                }
            }
        }
    }
    return false;
}

// Function to calculate average rating
function get_average_rating($reviews) {
    if (empty($reviews)) {
        return 0;
    }
    
    $total = 0;
    foreach ($reviews as $review) {
        $total += $review['rating'];
    }
    
    return round($total / count($reviews), 1);
}

// Function to render star rating
function render_stars($rating, $max = 5) {
    $html = '<div class="star-rating">';
    
    // Full stars
    for ($i = 1; $i <= floor($rating); $i++) {
        $html .= '<i class="bi bi-star-fill text-warning"></i>';
    }
    
    // Half star
    if ($rating - floor($rating) >= 0.5) {
        $html .= '<i class="bi bi-star-half text-warning"></i>';
        $i++;
    }
    
    // Empty stars
    for (; $i <= $max; $i++) {
        $html .= '<i class="bi bi-star text-warning"></i>';
    }
    
    $html .= '</div>';
    return $html;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle new review submission
    if (isset($_POST['submit_review'])) {
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
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
            $review = add_review($product_id, $author, $rating, $comment);
            $success_message = 'Your review has been submitted successfully!';
        }
    }
    
    // Handle marking a review as helpful
    if (isset($_POST['mark_helpful'])) {
        $review_id = isset($_POST['review_id']) ? $_POST['review_id'] : '';
        if (!empty($review_id)) {
            mark_review_helpful($review_id);
            
            // Remember which reviews the user has marked as helpful
            if (!isset($_SESSION['helpful_reviews'])) {
                $_SESSION['helpful_reviews'] = [];
            }
            $_SESSION['helpful_reviews'][] = $review_id;
        }
    }
}

// Function to check if user has already marked a review as helpful
function user_marked_helpful($review_id) {
    return isset($_SESSION['helpful_reviews']) && in_array($review_id, $_SESSION['helpful_reviews']);
}

// Function to render the review form
function render_review_form($product_id) {
    global $errors, $success_message;
    ?>
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
        
        <form action="" method="post">
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
    <?php
}

// Function to render reviews list
function render_reviews_list($product_id) {
    $reviews = get_product_reviews($product_id);
    $avg_rating = get_average_rating($reviews);
    ?>
    <div class="reviews-container">
        <?php if (!empty($reviews)): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Customer Feedback</h3>
                <div class="d-flex align-items-center">
                    <span class="h4 mb-0 me-2"><?= $avg_rating ?></span>
                    <?= render_stars($avg_rating) ?>
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
                                    <?= date('F j, Y', strtotime($review['date'])) ?>
                                </div>
                            </div>
                            <div>
                                <?= render_stars($review['rating']) ?>
                            </div>
                        </div>
                        
                        <p class="card-text mt-3"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        
                        <div class="d-flex align-items-center mt-3">
                            <form action="" method="post" class="me-3">
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
    <?php
}

// This file can be included in product.php to display reviews
// Usage example: include('reviews.php');
// Then call: render_reviews_list($product_id); and render_review_form($product_id);
?>
