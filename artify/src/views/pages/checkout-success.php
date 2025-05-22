<?php
// Check if order details are available in session
if (!Session::get('order_completed') || !Session::get('order_id')) {
    // Redirect to home if no order was completed
    header('Location: ' . BASE_URL . '/public/index.php');
    exit;
}

// Get order details from session
$orderId = Session::get('order_id');
$orderTotal = Session::get('order_total');
$customerEmail = Session::get('customer_email');

// Clear cart after successful checkout
$cartController = new CartController();
$cartController->clearCart();

// Clean up session variables after retrieving them
Session::remove('order_completed');
Session::remove('order_id');
Session::remove('order_total');
?>

<!-- Order Success Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-success shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 72px;"></i>
                        </div>
                        <h2 class="mb-3">Thank You for Your Order!</h2>
                        <p class="lead mb-4">Your order #<?php echo htmlspecialchars($orderId); ?> has been successfully placed.</p>
                        
                        <div class="alert alert-light border mb-4">
                            <p class="mb-1"><strong>Order Total:</strong> $<?php echo number_format($orderTotal, 2); ?></p>
                            <p class="mb-0"><strong>Email:</strong> <?php echo htmlspecialchars($customerEmail); ?></p>
                        </div>
                        
                        <p class="mb-4">A confirmation email has been sent to your email address. You can track your order status in your account.</p>
                        
                        <div class="d-grid gap-2 col-md-6 mx-auto">
                            <a href="<?php echo BASE_URL; ?>/public/index.php?page=orders" class="btn btn-primary">
                                <i class="fas fa-box me-2"></i> View My Orders
                            </a>
                            <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-outline-secondary">
                                <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
