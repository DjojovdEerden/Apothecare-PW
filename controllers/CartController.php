<?php
require_once MODELS_PATH . '/Cart.php';

class CartController {
    private $cart;
    
    public function __construct() {
        $this->cart = new Cart();
    }
    
    // Display cart page
    public function index() {
        $cart_summary = $this->cart->getCartSummary();
        
        // Check for success messages
        $item_removed = isset($_GET['removed']) && $_GET['removed'] == 1;
        $quantity_updated = isset($_GET['updated']) && $_GET['updated'] == 1;
        $cart_cleared = isset($_GET['cleared']) && $_GET['cleared'] == 1;
        
        require VIEWS_PATH . '/cart/index.php';
    }
    
    // Handle add to cart action
    public function addToCart() {
        // Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['add_to_cart'])) {
            return false;
        }
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if ($product_id > 0 && $quantity > 0) {
            // For debugging
            error_log("Adding product to cart: Product ID=$product_id, Quantity=$quantity");
            
            $success = $this->cart->addItem($product_id, $quantity);
            
            // For debugging
            error_log("Add to cart success: " . ($success ? 'true' : 'false'));
            error_log("Cart contents after adding: " . json_encode($_SESSION['cart']));
            
            if ($success) {
                Helpers::redirect(APP_URL . "/product.php?id=$product_id&added=1");
                exit; // Make sure script stops after redirect
            }
        }
        
        return false;
    }
    
    // Handle remove from cart action
    public function removeFromCart() {
        // Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['remove_item'])) {
            return false;
        }
        
        $remove_id = isset($_POST['remove_id']) ? (int)$_POST['remove_id'] : 0;
        
        if ($remove_id > 0) {
            $this->cart->removeItem($remove_id);
            Helpers::redirect(APP_URL . "/cart.php?removed=1");
        }
        
        return false;
    }
    
    // Handle update quantity action
    public function updateQuantity() {
        // Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_quantity'])) {
            return false;
        }
        
        $update_id = isset($_POST['update_id']) ? (int)$_POST['update_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        
        if ($update_id > 0) {
            $this->cart->updateItem($update_id, $quantity);
            Helpers::redirect(APP_URL . "/cart.php?updated=1");
        }
        
        return false;
    }
    
    // Handle clear cart action
    public function clearCart() {
        // Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['clear_cart'])) {
            return false;
        }
        
        $this->cart->clearCart();
        Helpers::redirect(APP_URL . "/cart.php?cleared=1");
        return true;
    }
}
?>
