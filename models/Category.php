<?php
require_once MODELS_PATH . '/Database.php';

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all categories
    public function getCategories() {
        try {
            $sql = "SELECT id, name FROM categories ORDER BY name ASC";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            error_log('Error fetching categories: ' . $e->getMessage());
            return [];
        }
    }
    
    // Get a single category by ID
    public function getCategory($id) {
        try {
            $sql = "SELECT id, name, description, created_at FROM categories WHERE id = :id";
            return $this->db->fetchOne($sql, [':id' => $id]);
        } catch (Exception $e) {
            error_log('Error fetching category: ' . $e->getMessage());
            return null;
        }
    }
}
?>
