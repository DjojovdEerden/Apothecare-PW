<?php
// Database connection configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'apothecare-pw');

// Create database connection with enhanced debugging
function get_db_connection() {
    static $conn;
    
    if (!isset($conn)) {
        try {
            $conn = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Test connection by checking if the categories table exists
            $test = $conn->query("SHOW TABLES LIKE 'categories'");
            if ($test->rowCount() === 0) {
                error_log('Error: Categories table does not exist in database ' . DB_NAME);
            }
        } catch (PDOException $e) {
            // Log the error and display a user-friendly message
            error_log('Database Connection Error: ' . $e->getMessage());
            die('Sorry, there was a problem connecting to the database. Please try again later.<br>Error: ' . $e->getMessage());
        }
    }
    
    return $conn;
}

// Get all products with optional filtering
function get_products($options = []) {
    $conn = get_db_connection();
    
    // Modified query to explicitly select product fields to avoid column name conflicts
    $sql = "SELECT p.id, p.name AS product_name, p.price, p.description, p.image_url, 
                   p.category_id, p.in_stock, p.created_at, p.updated_at, 
                   c.name AS category 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id";
    
    $params = [];
    $where_clauses = [];
    
    // Filter by category - improved case-insensitive comparison
    if (!empty($options['category'])) {
        $where_clauses[] = "LOWER(c.name) = LOWER(:category)";
        $params[':category'] = $options['category'];
    }
    
    // Filter by search term - fixed by using unique parameter names
    if (!empty($options['search'])) {
        $where_clauses[] = "(LOWER(p.name) LIKE LOWER(:search_name) OR LOWER(p.description) LIKE LOWER(:search_desc) OR LOWER(c.name) LIKE LOWER(:search_cat))";
        $search_term = '%' . $options['search'] . '%';
        $params[':search_name'] = $search_term;
        $params[':search_desc'] = $search_term;
        $params[':search_cat'] = $search_term;
    }
    
    // Filter by product ID
    if (!empty($options['id'])) {
        $where_clauses[] = "p.id = :id";
        $params[':id'] = $options['id'];
    }
    
    // Add WHERE clause if we have filters
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    // Add order by
    $sql .= " ORDER BY p.name ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

// Get a single product by ID
function get_product($id) {
    $products = get_products(['id' => $id]);
    return !empty($products) ? $products[0] : null;
}

// Get all product categories with enhanced debugging
function get_categories() {
    $conn = get_db_connection();
    
    try {
        // Query with simpler SELECT to avoid potential issues
        $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll();
        
        // Enhanced debug output
        if (empty($categories)) {
            error_log('Warning: No categories found in the database. Check if categories table is populated.');
            // Display debug message on screen (remove in production)
            echo '<!-- DEBUG: No categories found in database -->';
        } else {
            // Display count (remove in production)
            echo '<!-- DEBUG: Found ' . count($categories) . ' categories -->';
        }
        
        return $categories;
    } catch (PDOException $e) {
        error_log('Error fetching categories: ' . $e->getMessage());
        // Output error to help debug (remove in production)
        echo '<!-- DEBUG Error: ' . $e->getMessage() . ' -->';
        return [];
    }
}

// Get reviews for a product - Enhanced with debugging
function get_product_reviews($product_id) {
    $conn = get_db_connection();
    
    try {
        // Debug all columns from reviews table
        $debug_stmt = $conn->query("DESCRIBE reviews");
        $columns = $debug_stmt->fetchAll();
        error_log("Reviews table columns: " . json_encode($columns));
        
        $stmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = :product_id ORDER BY created_at DESC");
        $stmt->execute([':product_id' => $product_id]);
        
        $reviews = $stmt->fetchAll();
        
        // Debug the first review
        if (!empty($reviews)) {
            error_log("First review data: " . json_encode($reviews[0]));
        } else {
            error_log("No reviews found for product ID: $product_id");
        }
        
        return $reviews;
    } catch (PDOException $e) {
        error_log("Error fetching reviews: " . $e->getMessage());
        return [];
    }
}

// Add a review for a product - Fixed to use correct uppercase COMMENT field
function add_review($product_id, $author, $rating, $comment) {
    $conn = get_db_connection();
    
    try {
        // Debug the parameters
        error_log("Adding review - Product ID: $product_id, Author: $author, Rating: $rating, Comment: $comment");
        
        // Note: COMMENT is uppercase to match the database column name
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, author, rating, COMMENT) 
                               VALUES (:product_id, :author, :rating, :comment)");
        
        $result = $stmt->execute([
            ':product_id' => $product_id,
            ':author' => $author,
            ':rating' => $rating,
            ':comment' => $comment
        ]);
        
        if (!$result) {
            error_log("Failed to insert review: " . implode(', ', $stmt->errorInfo()));
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error adding review: " . $e->getMessage());
        return false;
    }
}

// Mark a review as helpful
function mark_review_helpful($review_id) {
    $conn = get_db_connection();
    
    $stmt = $conn->prepare("UPDATE reviews SET helpful_count = helpful_count + 1 WHERE id = :review_id");
    return $stmt->execute([':review_id' => $review_id]);
}

// Calculate average rating for a product
function get_average_rating($product_id) {
    $conn = get_db_connection();
    
    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = :product_id");
    $stmt->execute([':product_id' => $product_id]);
    
    $result = $stmt->fetch();
    return $result ? round($result['avg_rating'], 1) : 0;
}

// Get related products (same category but different ID)
function get_related_products($product_id, $category_id, $limit = 3) {
    $conn = get_db_connection();
    
    $stmt = $conn->prepare("SELECT p.*, c.name AS category 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id != :product_id AND p.category_id = :category_id 
                           LIMIT :limit");
    
    $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}
?>