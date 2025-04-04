<?php
require_once MODELS_PATH . '/Database.php';

class Review {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all reviews for a product
    public function getProductReviews($product_id) {
        try {
            $sql = "SELECT * FROM reviews WHERE product_id = :product_id ORDER BY created_at DESC";
            return $this->db->fetchAll($sql, [':product_id' => $product_id]);
        } catch (Exception $e) {
            error_log('Error fetching reviews: ' . $e->getMessage());
            return [];
        }
    }
    
    // Add a new review
    public function addReview($product_id, $author, $rating, $comment) {
        try {
            $sql = "INSERT INTO reviews (product_id, author, rating, COMMENT) 
                    VALUES (:product_id, :author, :rating, :comment)";
            
            return $this->db->insert($sql, [
                ':product_id' => $product_id,
                ':author' => $author,
                ':rating' => $rating,
                ':comment' => $comment
            ]);
        } catch (Exception $e) {
            error_log('Error adding review: ' . $e->getMessage());
            return false;
        }
    }
    
    // Mark a review as helpful
    public function markHelpful($review_id) {
        try {
            $sql = "UPDATE reviews SET helpful_count = helpful_count + 1 WHERE id = :review_id";
            $this->db->query($sql, [':review_id' => $review_id]);
            return true;
        } catch (Exception $e) {
            error_log('Error marking review as helpful: ' . $e->getMessage());
            return false;
        }
    }
    
    // Get average rating for a product
    public function getAverageRating($product_id) {
        try {
            $sql = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = :product_id";
            $result = $this->db->fetchOne($sql, [':product_id' => $product_id]);
            
            // Check if result exists and avg_rating is not null
            if ($result && isset($result['avg_rating']) && $result['avg_rating'] !== null) {
                return round((float)$result['avg_rating'], 1);
            }
            return 0;
        } catch (Exception $e) {
            error_log('Error calculating average rating: ' . $e->getMessage());
            return 0;
        }
    }
}
?>
