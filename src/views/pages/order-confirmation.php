<?php
// Get purchase ID from session
$purchaseId = Session::get('last_purchase_id');

// If no purchase ID, redirect to home
if (!$purchaseId) {
    header('Location: ' . BASE_URL . '/public/index.php');
    exit;
}

// Get purchase details
$purchaseModel = new PurchaseModel();
$purchase = $purchaseModel->getPurchaseById($purchaseId);
$items = $purchaseModel->getPurchaseItems($purchaseId);

// Get customer details
$customerModel = new CustomerModel();
$customer = $customerModel->getCustomerByEmail($purchase->CustEmail);

// Calculate total
$total = 0;
foreach ($items as $item) {
    $total += ($item->Price * $item->Quantity);
}
?>

<!-- Order Confirmation Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
            <h1>Thank You for Your Order!</h1>
            <p class="lead">Your order has been placed successfully.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order #<?php echo $purchaseId; ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Date: <?php echo date('F j, Y, g:i a', strtotime($purchase->Date)); ?></p>
                        
                        <h6 class="mt-4">Items Ordered:</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item->Description); ?></td>
                                            <td><?php echo $item->Quantity; ?></td>
                                            <td class="text-end">$<?php echo number_format($item->Price, 2); ?></td>
                                            <td class="text-end">$<?php echo number_format($item->Price * $item->Quantity, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th class="text-end">$<?php echo number_format($total, 2); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6>Shipping Address:</h6>
                                <address>
                                    <?php echo htmlspecialchars($customer->Title . ' ' . $customer->CustFName . ' ' . $customer->CustLName); ?><br>
                                    <?php echo htmlspecialchars($customer->Address); ?><br>
                                    <?php echo htmlspecialchars($customer->City . ', ' . $customer->State . ' ' . $customer->PostCode); ?><br>
                                    <?php echo htmlspecialchars($customer->Country); ?><br>
                                    <strong>Email:</strong> <?php echo htmlspecialchars($customer->CustEmail); ?><br>
                                    <strong>Phone:</strong> <?php echo htmlspecialchars($customer->Phone); ?>
                                </address>
                            </div>
                            <div class="col-md-6">
                                <h6>Order Information:</h6>
                                <p>
                                    <strong>Order Status:</strong> <?php echo htmlspecialchars($purchase->Status); ?><br>
                                    <strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($purchase->Date)); ?><br>
                                    <strong>Order Number:</strong> #<?php echo $purchaseId; ?>
                                </p>
                                
                                <?php
                                // Check email status from session
                                $customerEmailSent = Session::get('customer_email_sent');
                                $emailsEnabled = get_site_setting('email_notifications_enabled', true) && 
                                                get_site_setting('email_notifications_to_customer', true);
                                
                                if ($emailsEnabled && $customerEmailSent === true): ?>
                                    <p>A confirmation email has been sent to your email address.</p>
                                <?php elseif ($emailsEnabled && $customerEmailSent === false): ?>
                                    <div class="alert alert-warning mt-2">
                                        <small>Note: We couldn't send the confirmation email. Please save your order number for reference.</small>
                                    </div>
                                <?php elseif (!$emailsEnabled): ?>
                                    <p>Please keep your order number for reference.</p>
                                <?php else: ?>
                                    <p>Please save your order number for reference.</p>
                                <?php endif; ?>
                                
                                <?php 
                                // Clear email status from session
                                Session::remove('customer_email_sent');
                                Session::remove('admin_email_sent');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <p>Thank you for shopping with Artify. If you have any questions about your order, please contact us.</p>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?page=submit-testimonial" class="btn btn-outline-primary me-2">
                        <i class="fas fa-comment me-2"></i>Leave a Testimonial
                    </a>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
