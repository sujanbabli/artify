<?php
// Get purchase controller and models
$purchaseModel = new PurchaseModel();
$customerModel = new CustomerModel();

// Get order ID from URL
$orderId = isset($_GET['view']) ? (int)$_GET['view'] : 0;

// Get order with items
$order = $purchaseModel->getPurchaseByIdWithItems($orderId);

// If order not found, redirect to orders page
if (!$order) {
    Session::setFlash('error', 'Order not found');
    header('Location: ' . BASE_URL . '/admin/index.php?page=orders');
    exit;
}

// Get customer details
$customer = $customerModel->getCustomerByEmail($order->CustEmail);

// Get order items
$items = $purchaseModel->getPurchaseItems($orderId);

// Status options for dropdown
$statusOptions = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $newStatus = $_POST['status'];
    $result = $purchaseModel->updatePurchaseStatus($orderId, $newStatus);
    
    if ($result) {
        Session::setFlash('success', 'Order status updated successfully');
        $order->Status = $newStatus; // Update status in the current view
    } else {
        Session::setFlash('error', 'Failed to update order status');
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Order #<?php echo $order->PurchaseNo; ?></h4>
    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Orders
    </a>
</div>

<div class="row">
    <!-- Order Details -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Order Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Order Number</div>
                    <div class="fw-bold">#<?php echo $order->PurchaseNo; ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Date</div>
                    <div class="fw-bold"><?php echo date('F j, Y', strtotime($order->Date)); ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Total Amount</div>
                    <div class="fw-bold text-primary">$<?php echo number_format($order->TotalAmount, 2); ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Status</div>
                    <div>
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
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-muted mb-2">Update Status</div>
                    <form action="<?php echo BASE_URL; ?>/admin/index.php?page=orders&view=<?php echo $order->PurchaseNo; ?>" method="post" class="row g-2">
                        <div class="col-8">
                            <select name="status" class="form-select">
                                <?php foreach ($statusOptions as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo ($order->Status === $status) ? 'selected' : ''; ?>>
                                        <?php echo $status; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <input type="hidden" name="order_id" value="<?php echo $order->PurchaseNo; ?>">
                            <button type="submit" name="update_status" class="btn btn-primary w-100">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Customer Details -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Name</div>
                    <div class="fw-bold">
                        <?php echo $customer ? htmlspecialchars($customer->CustFName . ' ' . $customer->CustLName) : 'N/A'; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Email</div>
                    <div class="fw-bold"><?php echo htmlspecialchars($order->CustEmail); ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Phone</div>
                    <div class="fw-bold">
                        <?php echo $customer && $customer->Phone ? htmlspecialchars($customer->Phone) : 'N/A'; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Address</div>
                    <div class="fw-bold">
                        <?php if ($customer && $customer->Address): ?>
                            <?php echo htmlspecialchars($customer->Address); ?><br>
                            <?php echo htmlspecialchars($customer->City . ', ' . $customer->State . ' ' . $customer->PostCode); ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="mailto:<?php echo htmlspecialchars($order->CustEmail); ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-envelope me-1"></i> Email Customer
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Shipping Details -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Shipping Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Shipping Address</div>
                    <div class="fw-bold">
                        <?php if ($order->ShippingAddress): ?>
                            <?php echo nl2br(htmlspecialchars($order->ShippingAddress)); ?>
                        <?php elseif ($customer && $customer->Address): ?>
                            <?php echo htmlspecialchars($customer->Address); ?><br>
                            <?php echo htmlspecialchars($customer->City . ', ' . $customer->State . ' ' . $customer->PostCode); ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Shipping Method</div>
                    <div class="fw-bold">Standard Shipping</div>
                </div>
                <?php if ($order->Status === 'Shipped' || $order->Status === 'Delivered'): ?>
                    <div class="mb-3">
                        <div class="text-muted small">Tracking Number</div>
                        <div class="fw-bold">
                            <?php echo $order->TrackingNumber ? htmlspecialchars($order->TrackingNumber) : 'Not available'; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Product items -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Order Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($items && count($items) > 0): ?>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item->ImagePath)): ?>
                                    <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($item->ImagePath); ?>" 
                                         alt="<?php echo htmlspecialchars($item->Description); ?>" 
                                         class="img-thumbnail" style="max-width: 60px;">
                                <?php else: ?>
                                    <div class="bg-light text-center p-2 rounded">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=product-form&id=<?php echo $item->ProductNo; ?>" 
                                       class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($item->Description); ?></a>
                                    <small class="text-muted">
                                        ID: <?php echo $item->ProductNo; ?> | 
                                        Category: <?php echo htmlspecialchars($item->Category); ?>
                                        <?php if (isset($item->Colour) || isset($item->Size)): ?>
                                            <br>
                                            <?php echo isset($item->Colour) ? 'Color: ' . htmlspecialchars($item->Colour) : ''; ?>
                                            <?php echo (isset($item->Colour) && isset($item->Size)) ? ' / ' : ''; ?>
                                            <?php echo isset($item->Size) ? 'Size: ' . htmlspecialchars($item->Size) : ''; ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item->Price, 2); ?></td>
                            <td><?php echo $item->Quantity; ?></td>
                            <td class="text-end">$<?php echo number_format($item->Price * $item->Quantity, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No items found for this order</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end"><strong>$<?php echo number_format($order->TotalAmount, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Order Notes -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Order Notes</h5>
    </div>
    <div class="card-body">
        <p class="mb-0"><?php echo $order->Notes ? htmlspecialchars($order->Notes) : 'No notes for this order.'; ?></p>
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-end mb-4">
    <a href="javascript:window.print();" class="btn btn-outline-secondary me-2">
        <i class="fas fa-print me-1"></i> Print Order
    </a>
    <a href="mailto:<?php echo htmlspecialchars($order->CustEmail); ?>" class="btn btn-outline-primary">
        <i class="fas fa-envelope me-1"></i> Email Customer
    </a>
</div>
