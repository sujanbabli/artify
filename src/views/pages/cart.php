<?php
// Get cart controller
$cartController = new CartController();
$cartItems = $cartController->getCartItems();
$cartSummary = $cartController->getCartSummary();
?>

<!-- Cart Section -->
<section class="py-5">
    <div class="container">
        <h1 class="mb-4">Shopping Cart</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">
                <p>Your cart is empty.</p>
                <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-primary mt-3">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Cart Items (<?php echo $cartSummary['total_items']; ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item row align-items-center">
                                    <div class="col-md-2 col-4">
                                        <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($item['product']->ImagePath); ?>" 
                                             class="cart-item-img img-fluid rounded" 
                                             alt="<?php echo htmlspecialchars($item['product']->Description); ?>">
                                    </div>
                                    <div class="col-md-4 col-8">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($item['product']->Description); ?></h5>
                                        <p class="text-muted small mb-0">Category: <?php echo htmlspecialchars($item['product']->Category); ?></p>
                                        <?php if ($item['product']->Size): ?>
                                            <p class="text-muted small mb-0">Size: <?php echo htmlspecialchars($item['product']->Size); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-2 col-4 mt-3 mt-md-0">
                                        <span class="d-block d-md-none small mb-1">Price:</span>
                                        <span class="text-primary">$<?php echo number_format($item['product']->Price, 2); ?></span>
                                    </div>
                                    <div class="col-md-2 col-4 mt-3 mt-md-0">
                                        <span class="d-block d-md-none small mb-1">Quantity:</span>
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                   class="form-control cart-quantity-input" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="1" 
                                                   max="10" 
                                                   data-product-id="<?php echo $item['product']->ProductNo; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-4 mt-3 mt-md-0 text-end">
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="d-block d-md-none small mb-1">Subtotal:</span>
                                            <span class="text-primary fw-bold">$<?php echo number_format($item['subtotal'], 2); ?></span>
                                            <a href="#" class="text-danger small mt-2 remove-from-cart" data-product-id="<?php echo $item['product']->ProductNo; ?>">
                                                <i class="fas fa-trash-alt"></i> Remove
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php if (next($cartItems)): ?>
                                    <hr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($cartSummary['total_price'], 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold text-primary">$<?php echo number_format($cartSummary['total_price'], 2); ?></span>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="<?php echo BASE_URL; ?>/public/index.php?page=checkout" class="btn btn-primary">
                                    Proceed to Checkout
                                </a>
                                <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-outline-secondary">
                                    Continue Shopping
                                </a>
                                
                                <?php if (!Session::get('customer_logged_in')): ?>
                                <div class="alert alert-info mt-3 mb-0">
                                    <small><i class="fas fa-info-circle me-1"></i> You'll need to login or create an account to complete checkout.</small>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!Session::get('customer_logged_in')): ?>
                            <div class="alert alert-info mt-3 mb-0">
                                <small><i class="fas fa-info-circle me-1"></i> You'll need to login or create an account to complete your purchase.</small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
