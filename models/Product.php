<?php
require_once MODELS_PATH . '/Database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all products with optional filtering
    public function getProducts($options = []) {
        $sql = "SELECT p.id, p.name AS product_name, p.price, p.description, p.image_url, 
                       p.category_id, p.in_stock, p.created_at, p.updated_at, 
                       c.name AS category 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        
        $params = [];
        $where_clauses = [];
        
        // Filter by category
        if (!empty($options['category'])) {
            $where_clauses[] = "LOWER(c.name) = LOWER(:category)";
            $params[':category'] = $options['category'];
        }
        
        // Filter by search term
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
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Get a single product by ID
    public function getProduct($id) {
        $products = $this->getProducts(['id' => $id]);
        return !empty($products) ? $products[0] : null;
    }
    
    // Get related products (same category but different ID)
    public function getRelatedProducts($product_id, $category_id, $limit = 3) {
        $sql = "SELECT p.*, c.name AS category 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id != :product_id AND p.category_id = :category_id 
                LIMIT :limit";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
