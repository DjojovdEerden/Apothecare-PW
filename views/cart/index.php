<?php require_once VIEWS_PATH . '/components/header.php'; ?>

    <!-- Cart Contents -->
    <div class="container mb-5">
        <h1 class="mb-4">Your Shopping Cart</h1>
        
        <?php if ($item_removed): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Item removed from your cart.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($quantity_updated): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Cart updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($cart_cleared): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Your cart has been cleared.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (empty($cart_summary['items'])): ?>
            <div class="alert alert-info">
                <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                <p class="mt-3">Your shopping cart is empty.</p>
                <a href="<?= APP_URL ?>/products.php" class="btn btn-primary mt-2">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <!-- Cart Items -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Cart Items (<?= $cart_summary['total_items'] ?>)</h5>
                            <form action="<?= APP_URL ?>/cart.php" method="post" data-confirm="Are you sure you want to clear your cart?">
                                <button type="submit" name="clear_cart" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Clear Cart
                                </button>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px">Product</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($cart_summary['items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= $item['product']['image_url'] ?? 'https://via.placeholder.com/80x80?text=No+Image' ?>" 
                                                         alt="<?= Helpers::escape($item['product']['product_name']) ?>" 
                                                         class="cart-item-img border">
                                                </td>
                                                <td>
                                                    <a href="<?= APP_URL ?>/product.php?id=<?= $item['product']['id'] ?>" class="text-decoration-none">
                                                        <?= Helpers::escape($item['product']['product_name']) ?>
                                                    </a>
                                                </td>
                                                <td><?= Helpers::formatPrice($item['product']['price']) ?></td>
                                                <td>
                                                    <form action="<?= APP_URL ?>/cart.php" method="post" class="quantity-form">
                                                        <input type="hidden" name="update_id" value="<?= $item['product']['id'] ?>">
                                                        <div class="input-group" style="width: 100px">
                                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                                                   min="1" max="<?= min($item['product']['in_stock'], 10) ?>" 
                                                                   class="form-control form-control-sm">
                                                            <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-secondary">
                                                                <i class="bi bi-arrow-repeat"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td><?= Helpers::formatPrice($item['total']) ?></td>
                                                <td>
                                                    <form action="<?= APP_URL ?>/cart.php" method="post" data-confirm="Remove this item from cart?">
                                                        <input type="hidden" name="remove_id" value="<?= $item['product']['id'] ?>">
                                                        <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="card cart-summary">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span><?= Helpers::formatPrice($cart_summary['subtotal']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span><?= Helpers::formatPrice($cart_summary['shipping']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (<?= CART_TAX_RATE * 100 ?>%):</span>
                                <span><?= Helpers::formatPrice($cart_summary['tax']) ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong><?= Helpers::formatPrice($cart_summary['total']) ?></strong>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-primary">Proceed to Checkout</a>
                                <a href="<?= APP_URL ?>/products.php" class="btn btn-outline-secondary">Continue Shopping</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php require_once VIEWS_PATH . '/components/footer.php'; ?>
