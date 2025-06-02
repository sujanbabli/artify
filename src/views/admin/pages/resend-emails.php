<?php
// Check if admin is logged in
if (!Session::get('admin_logged_in')) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// Initialize
$purchaseModel = new PurchaseModel();
$customerModel = new CustomerModel();
$message = '';
$status = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_email'])) {
    $purchaseId = isset($_POST['purchase_id']) ? (int)$_POST['purchase_id'] : 0;
    
    if ($purchaseId > 0) {
        // Get purchase details
        $purchase = $purchaseModel->getPurchaseById($purchaseId);
        
        if ($purchase) {
            // Get purchase items and customer details
            $purchaseItems = $purchaseModel->getPurchaseItems($purchaseId);
            $customer = $customerModel->getCustomerByEmail($purchase->CustEmail);
            
            if ($customer && !empty($purchaseItems)) {
                // Force enable emails temporarily if they're disabled
                $emailsEnabled = get_site_setting('email_notifications_enabled', true);
                $customerEmailsEnabled = get_site_setting('email_notifications_to_customer', true);
                
                update_site_setting('email_notifications_enabled', true);
                update_site_setting('email_notifications_to_customer', true);
                
                // Send confirmation email
                require_once(ROOT_DIR . '/src/utils/EmailHelper.php');
                $emailSent = EmailHelper::sendOrderConfirmation($customer, $purchase, $purchaseItems);
                
                // Restore settings
                update_site_setting('email_notifications_enabled', $emailsEnabled);
                update_site_setting('email_notifications_to_customer', $customerEmailsEnabled);
                
                if ($emailSent) {
                    $status = 'success';
                    $message = "Confirmation email successfully resent for Order #{$purchaseId}.";
                } else {
                    $status = 'danger';
                    $message = "Failed to send confirmation email for Order #{$purchaseId}. Check email logs for details.";
                }
            } else {
                $status = 'warning';
                $message = "Could not find customer details or order items for Order #{$purchaseId}.";
            }
        } else {
            $status = 'warning';
            $message = "Could not find order with ID #{$purchaseId}.";
        }
    } else {
        $status = 'warning';
        $message = "Invalid order ID provided.";
    }
}

// Get all purchases
$purchases = $purchaseModel->getAllPurchases();
?>

<!-- Email System Section -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Resend Order Emails</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $status; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Resend Order Confirmation Email</h6>
        </div>
        <div class="card-body">
            <p>Use this form to manually resend order confirmation emails to customers. This is useful if the original email failed to send.</p>
            
            <form method="post" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="purchase_id" class="form-label">Select Order</label>
                            <select class="form-select" name="purchase_id" id="purchase_id" required>
                                <option value="">-- Select an Order --</option>
                                <?php foreach($purchases as $purchase): ?>
                                    <option value="<?php echo $purchase->PurchaseNo; ?>">
                                        Order #<?php echo $purchase->PurchaseNo; ?> - 
                                        <?php echo date('M j, Y', strtotime($purchase->Date)); ?> - 
                                        <?php echo htmlspecialchars($purchase->CustEmail); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="resend_email" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Resend Confirmation Email
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-1"></i> Note:</h5>
                <p class="mb-0">This will send a new copy of the order confirmation email to the customer, even if they've already received the original email. Use with caution to avoid confusing customers with duplicate emails.</p>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between">
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=email-settings" class="btn btn-outline-primary">
            <i class="fas fa-cog me-1"></i> Email Settings
        </a>
    </div>
</div>
