<?php
// Check if user is logged in
if (!Session::get('customer_logged_in')) {
    // Redirect to login page
    header('Location: ' . BASE_URL . '/public/index.php?page=login');
    exit;
}

// Get customer email from session
$customerEmail = Session::get('customer_email');

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to orders page
    header('Location: ' . BASE_URL . '/public/index.php?page=orders');
    exit;
}

$orderId = (int)$_GET['id'];

// Get purchase model
$purchaseModel = new PurchaseModel();

// Get order details
$order = $purchaseModel->getPurchaseByIdWithItems($orderId);

// Check if order exists and belongs to the customer
if (!$order || $order->CustEmail !== $customerEmail) {
    Session::setFlash('error', 'Order not found or access denied');
    header('Location: ' . BASE_URL . '/public/index.php?page=orders');
    exit;
}
?>

<!-- Order Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1>Order #<?php echo $order->PurchaseNo; ?></h1>
            <a href="<?php echo BASE_URL; ?>/public/index.php?page=orders" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
        
        <div class="row">
            <!-- Order Details -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order->Date)); ?></p>
                        <p>
                            <strong>Status:</strong> 
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
                        </p>
                        <p><strong>Total:</strong> $<?php echo number_format($order->TotalAmount, 2); ?></p>
                        
                        <?php if ($order->TrackingNumber): ?>
                            <p><strong>Tracking Number:</strong> <?php echo htmlspecialchars($order->TrackingNumber); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($order->PaymentMethod): ?>
                            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order->PaymentMethod); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Shipping & Billing -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Shipping & Billing</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($order->ShippingAddress): ?>
                            <div class="mb-3">
                                <h6>Shipping Address:</h6>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($order->ShippingAddress)); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($order->BillingAddress): ?>
                            <div>
                                <h6>Billing Address:</h6>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($order->BillingAddress)); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Customer Info -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order->customer->CustFName . ' ' . $order->customer->CustLName); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order->customer->CustEmail); ?></p>
                        <?php if ($order->customer->Phone): ?>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order->customer->Phone); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="card mt-2">
            <div class="card-header bg-white">
                <h5 class="mb-0">Order Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="80">Image</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order->items as $item): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($item->ImagePath); ?>" 
                                             class="img-thumbnail" 
                                             alt="<?php echo htmlspecialchars($item->Description); ?>" 
                                             width="60">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($item->Description); ?></div>
                                        <?php if ($item->Size || $item->Colour): ?>
                                            <small class="text-muted">
                                                <?php 
                                                    $specs = [];
                                                    if ($item->Size) $specs[] = 'Size: ' . $item->Size;
                                                    if ($item->Colour) $specs[] = 'Color: ' . $item->Colour;
                                                    echo implode(', ', $specs);
                                                ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>$<?php echo number_format($item->Price, 2); ?></td>
                                    <td><?php echo $item->Quantity; ?></td>
                                    <td>$<?php echo number_format($item->Subtotal, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>$<?php echo number_format($order->TotalAmount, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <?php if ($order->Notes): ?>
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Notes</h5>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($order->Notes)); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
