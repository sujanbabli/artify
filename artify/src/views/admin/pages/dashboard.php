<?php
// Get models
$productModel = new ProductModel();
$purchaseModel = new PurchaseModel();
$customerModel = new CustomerModel();
$testimonialModel = new TestimonialModel();

// Get counts
$totalProducts = count($productModel->getAllProducts());
$totalOrders = count($purchaseModel->getAllPurchases());
$totalCustomers = count($customerModel->getAllCustomers());
$pendingTestimonials = count($testimonialModel->getPendingTestimonials());

// Get recent orders
$recentOrders = array_slice($purchaseModel->getAllPurchases(), 0, 5);
?>

<div class="row">
    <!-- Dashboard Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="card-icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-box"></i>
            </div>
            <div class="card-value"><?php echo $totalProducts; ?></div>
            <div class="card-label">Total Products</div>
            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=products" class="stretched-link"></a>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="card-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-value"><?php echo $totalOrders; ?></div>
            <div class="card-label">Total Orders</div>
            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders" class="stretched-link"></a>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="card-icon bg-info bg-opacity-10 text-info">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-value"><?php echo $totalCustomers; ?></div>
            <div class="card-label">Total Customers</div>
            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=customers" class="stretched-link"></a>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="card-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-comment"></i>
            </div>
            <div class="card-value"><?php echo $pendingTestimonials; ?></div>
            <div class="card-label">Pending Testimonials</div>
            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials" class="stretched-link"></a>
        </div>
    </div>
</div>

<!-- Quick Access Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Access</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=product-form" class="quick-action-card">
                            <div class="icon-container bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-text">Add New Product</div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=products" class="quick-action-card">
                            <div class="icon-container bg-success bg-opacity-10 text-success">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div class="action-text">Manage Products</div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders" class="quick-action-card">
                            <div class="icon-container bg-info bg-opacity-10 text-info">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <div class="action-text">Process Orders</div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials" class="quick-action-card">
                            <div class="icon-container bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="action-text">Approve Testimonials</div>
                        </a>
                    </div>
                    <!-- Add Customer Quick Action -->
                    <div class="col-lg-3 col-md-6">
                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=customer-form" class="quick-action-card">
                            <div class="icon-container bg-info bg-opacity-10 text-info">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-text">Add New Customer</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-xl-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentOrders)): ?>
                    <div class="text-center p-4">
                        <p class="text-muted mb-0">No orders yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><?php echo $order->PurchaseNo; ?></td>
                                        <td><?php echo htmlspecialchars($order->CustFName . ' ' . $order->CustLName); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($order->Date)); ?></td>
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
                                            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders&view=<?php echo $order->PurchaseNo; ?>" class="btn btn-sm btn-outline-primary">
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
    
    <!-- Quick Links -->
    <div class="col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=product-form" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-plus text-success me-3"></i>
                        <div>
                            <div class="fw-bold">Add New Product</div>
                            <small class="text-muted">Add a new product to your store</small>
                        </div>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=news" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-newspaper text-primary me-3"></i>
                        <div>
                            <div class="fw-bold">Update News</div>
                            <small class="text-muted">Update the latest news on the homepage</small>
                        </div>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-comment text-warning me-3"></i>
                        <div>
                            <div class="fw-bold">Moderate Testimonials</div>
                            <small class="text-muted">Review and approve customer testimonials</small>
                        </div>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=customers" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-users text-info me-3"></i>
                        <div>
                            <div class="fw-bold">Manage Customers</div>
                            <small class="text-muted">View and manage customer accounts</small>
                        </div>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/public/index.php" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-external-link-alt text-info me-3"></i>
                        <div>
                            <div class="fw-bold">View Store</div>
                            <small class="text-muted">See your store as customers see it</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
