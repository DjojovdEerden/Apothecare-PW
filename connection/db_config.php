<?php
// Database connection configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'apothecare');

// Create database connection
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
        } catch (PDOException $e) {
            // Log the error and display a user-friendly message
            error_log('Database Connection Error: ' . $e->getMessage());
            die('Sorry, there was a problem connecting to the database. Please try again later.');
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
    
    // Filter by category
    if (!empty($options['category'])) {
        $where_clauses[] = "c.name = :category";
        $params[':category'] = $options['category'];
    }
    
    // Filter by search term
    if (!empty($options['search'])) {
        $where_clauses[] = "(p.name LIKE :search OR p.description LIKE :search OR c.name LIKE :search)";
        $params[':search'] = '%' . $options['search'] . '%';
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

// Get all product categories
function get_categories() {
    $conn = get_db_connection();
    
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name ASC");
    return $stmt->fetchAll();
}

// Get reviews for a product
function get_product_reviews($product_id) {
    $conn = get_db_connection();
    
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = :product_id ORDER BY created_at DESC");
    $stmt->execute([':product_id' => $product_id]);
    
    return $stmt->fetchAll();
}

// Add a review for a product
function add_review($product_id, $author, $rating, $comment) {
    $conn = get_db_connection();
    
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, author, rating, comment) 
                           VALUES (:product_id, :author, :rating, :comment)");
    
    return $stmt->execute([
        ':product_id' => $product_id,
        ':author' => $author,
        ':rating' => $rating,
        ':comment' => $comment
    ]);
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