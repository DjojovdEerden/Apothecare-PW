<?php
require_once MODELS_PATH . '/Database.php';
require_once MODELS_PATH . '/Product.php';

class Cart {
    private $product;
    
    public function __construct() {
        $this->product = new Product();
        
        // Initialize cart if needed
        if (!isset($_SESSION['cart'])) {
            error_log('Initializing empty cart in session');
            $_SESSION['cart'] = [];
        }
    }
    
    // Add item to cart
    public function addItem($product_id, $quantity = 1) {
        error_log("Cart::addItem called with product_id=$product_id, quantity=$quantity");
        
        // Validate product exists
        $product = $this->product->getProduct($product_id);
        if (!$product) {
            error_log("Failed to add to cart: Product ID $product_id not found");
            return false;
        }
        
        // Add to cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
            error_log("Updated existing cart item: Product ID $product_id, new quantity: " . $_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
            error_log("Added new item to cart: Product ID $product_id, quantity: $quantity");
        }
        
        error_log("Cart now contains: " . json_encode($_SESSION['cart']));
        return true;
    }
    
    // Update item quantity
    public function updateItem($product_id, $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
            return true;
        }
        
        // Validate product exists
        $product = $this->product->getProduct($product_id);
        if (!$product) {
            return false;
        }
        
        $_SESSION['cart'][$product_id] = $quantity;
        return true;
    }
    
    // Remove item from cart
    public function removeItem($product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        return true;
    }
    
    // Clear entire cart
    public function clearCart() {
        $_SESSION['cart'] = [];
        return true;
    }
    
    // Get cart contents with product details
    public function getCartItems() {
        $cart_items = [];
        
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $product = $this->product->getProduct($id);
            if ($product) {
                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $product['price'] * $quantity
                ];
            }
        }
        
        return $cart_items;
    }
    
    // Get cart summary (totals)
    public function getCartSummary() {
        $cart_items = $this->getCartItems();
        $subtotal = 0;
        $total_items = 0;
        
        foreach ($cart_items as $item) {
            $subtotal += $item['total'];
            $total_items += $item['quantity'];
        }
        
        $shipping = $subtotal > CART_SHIPPING_THRESHOLD ? CART_SHIPPING_COST : 0;
        $tax = $subtotal * CART_TAX_RATE;
        $total = $subtotal + $shipping + $tax;
        
        return [
            'items' => $cart_items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'total_items' => $total_items
        ];
    }
}
?>
