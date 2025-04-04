<?php
require_once MODELS_PATH . '/Product.php';
require_once MODELS_PATH . '/Category.php';
require_once MODELS_PATH . '/Review.php';

class ProductController {
    private $product;
    private $category;
    private $review;
    
    public function __construct() {
        $this->product = new Product();
        $this->category = new Category();
        $this->review = new Review();
    }
    
    // Display all products with filtering options
    public function index() {
        // Get filter parameters
        $filter_category = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : null;
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        // Apply filters
        $filter_options = [];
        if ($filter_category) {
            $filter_options['category'] = $filter_category;
        }
        if ($search_term) {
            $filter_options['search'] = $search_term;
        }
        
        // Get data for view
        $products = $this->product->getProducts($filter_options);
        $categories = $this->category->getCategories();
        
        // Include view
        require VIEWS_PATH . '/products/index.php';
    }
    
    // Display single product details
    public function view($id) {
        // Get parameters and flags
        $product_id = (int)$id;
        $added_to_cart = isset($_GET['added']) && $_GET['added'] == 1;
        $review_added = isset($_GET['review_added']) && $_GET['review_added'] == 1;
        $helpful_marked = isset($_GET['helpful']) && $_GET['helpful'] == 1;
        
        // Get the product
        $product = $this->product->getProduct($product_id);
        
        // If product not found, redirect to products page
        if (!$product) {
            Helpers::redirect(APP_URL . '/products.php');
        }
        
        // Get related products
        $related_products = $this->product->getRelatedProducts($product_id, $product['category_id']);
        
        // Get reviews and average rating
        $reviews = $this->review->getProductReviews($product_id);
        $avg_rating = $this->review->getAverageRating($product_id);
        
        // Updated function to use Helpers::renderStars instead of render_stars
        // in the template file, render_stars is referenced, so we'll create an alias function
        if (!function_exists('render_stars')) {
            function render_stars($rating, $max = 5) {
                return Helpers::renderStars($rating, $max);
            }
        }
        
        // If user_marked_helpful is used, also create an alias for it
        if (!function_exists('user_marked_helpful')) {
            function user_marked_helpful($review_id) {
                return Helpers::userMarkedHelpful($review_id);
            }
        }
        
        // Include view
        require VIEWS_PATH . '/products/view.php';
    }
    
    // Handle review submission
    public function submitReview() {
        // Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit_review'])) {
            return false;
        }
        
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
            $this->review->addReview($product_id, $author, $rating, $comment);
            Helpers::redirect(APP_URL . "/product.php?id=$product_id&review_added=1");
        }
        
        return $errors;
    }
    
    // Handle marking a review as helpful
    public function markReviewHelpful() {
        // Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['mark_helpful'])) {
            return false;
        }
        
        $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        
        if ($review_id > 0) {
            $this->review->markHelpful($review_id);
            
            // Remember which reviews the user has marked as helpful
            if (!isset($_SESSION['helpful_reviews'])) {
                $_SESSION['helpful_reviews'] = [];
            }
            $_SESSION['helpful_reviews'][] = $review_id;
            
            Helpers::redirect(APP_URL . "/product.php?id=$product_id&helpful=1");
        }
        
        return false;
    }
}
?>
