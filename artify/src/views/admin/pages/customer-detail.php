<?php
// Get customer model
$customerModel = new CustomerModel();
$purchaseModel = new PurchaseModel();

// Get customer email from URL
$email = isset($_GET['email']) ? $_GET['email'] : '';

// Get customer details
$customer = $customerModel->getCustomerByEmail($email);

// If customer not found, redirect to customers page
if (!$customer) {
    Session::setFlash('error', 'Customer not found');
    header('Location: ' . BASE_URL . '/admin/index.php?page=customers');
    exit;
}

// Get customer's purchases/orders
$purchases = $purchaseModel->getPurchasesByCustomer($email);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Customer Details: <?php echo htmlspecialchars($customer->CustFName . ' ' . $customer->CustLName); ?></h4>
    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=customers" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Customers
    </a>
</div>

<div class="row">
    <!-- Customer Profile -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Profile Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar-placeholder">
                        <span><?php echo strtoupper(substr($customer->CustFName, 0, 1) . substr($customer->CustLName, 0, 1)); ?></span>
                    </div>
                    <h5 class="mt-3"><?php echo htmlspecialchars($customer->CustFName . ' ' . $customer->CustLName); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($customer->CustEmail); ?></p>
                </div>
                
                <div class="customer-info">
                    <div class="info-row">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo htmlspecialchars(($customer->Title ? $customer->Title . ' ' : '') . $customer->CustFName . ' ' . $customer->CustLName); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($customer->CustEmail); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone</div>
                        <div class="info-value"><?php echo !empty($customer->Phone) ? htmlspecialchars($customer->Phone) : 'N/A'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Address</div>
                        <div class="info-value">
                            <?php if (!empty($customer->Address)): ?>
                                <?php echo htmlspecialchars($customer->Address); ?><br>
                                <?php echo htmlspecialchars($customer->City . ', ' . $customer->State . ' ' . $customer->PostCode); ?><br>
                                <?php echo htmlspecialchars($customer->Country); ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=customer-form&email=<?php echo urlencode($customer->CustEmail); ?>" class="btn btn-primary w-100">
                        <i class="fas fa-edit me-2"></i>Edit Customer
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Customer Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order History</h5>
                <span class="badge bg-info"><?php echo count($purchases); ?> Orders</span>
            </div>
            <div class="card-body">
                <?php if (empty($purchases)): ?>
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                        </div>
                        <h6>No orders found</h6>
                        <p class="text-muted">This customer hasn't placed any orders yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($purchases as $purchase): ?>
                                    <tr>
                                        <td><?php echo $purchase->PurchaseNo; ?></td>
                                        <td><?php echo date('M j, Y', strtotime($purchase->Date)); ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                switch ($purchase->Status) {
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
                                                <?php echo $purchase->Status; ?>
                                            </span>
                                        </td>
                                        <td>$<?php echo number_format($purchase->TotalAmount, 2); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders&view=<?php echo $purchase->PurchaseNo; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-placeholder {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .customer-info {
        margin-top: 1rem;
    }
    
    .info-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-weight: 500;
    }
</style>
