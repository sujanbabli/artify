<?php
// Check if user is logged in
if (!Session::get('customer_logged_in')) {
    // Store the current page URL to redirect back after login
    Session::set('redirect_after_login', BASE_URL . '/public/index.php?page=orders');
    
    // Redirect to login page
    header('Location: ' . BASE_URL . '/public/index.php?page=login');
    exit;
}

// Get customer email from session
$customerEmail = Session::get('customer_email');

// Get purchase model
$purchaseModel = new PurchaseModel();

// Get customer's orders
$orders = $purchaseModel->getPurchasesByCustomer($customerEmail);
?>

<!-- Orders Section -->
<section class="py-5">
    <div class="container">
        <h1 class="mb-4">My Orders</h1>
        
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                <p>You haven't placed any orders yet.</p>
                <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-primary mt-3">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo $order->PurchaseNo; ?></td>
                                        <td><?php echo date('M j, Y', strtotime($order->Date)); ?></td>
                                        <td>$<?php echo number_format($order->TotalAmount, 2); ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                switch ($order->Status) {
                                                    case 'Pending':
                                                        echo 'bg-warning';
                                                        break;
                                                    case 'Processing':
                                                        echo 'bg-info';
                                                        break;
                                                    case 'Shipped':
                                                        echo 'bg-primary';
                                                        break;
                                                    case 'Delivered':
                                                        echo 'bg-success';
                                                        break;
                                                    case 'Cancelled':
                                                        echo 'bg-danger';
                                                        break;
                                                    default:
                                                        echo 'bg-secondary';
                                                }
                                            ?>">
                                                <?php echo $order->Status; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/public/index.php?page=order-detail&id=<?php echo $order->PurchaseNo; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
