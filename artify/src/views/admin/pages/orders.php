<?php
// Get purchase model
$purchaseModel = new PurchaseModel();

// Check if viewing a specific order
$viewOrderId = isset($_GET['view']) ? (int)$_GET['view'] : 0;

// Check if updating order status
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = Validation::sanitize($_POST['status']);
    
    if ($purchaseModel->updateOrderStatus($orderId, $status)) {
        Session::setFlash('success', 'Order status updated successfully');
    } else {
        Session::setFlash('error', 'Failed to update order status');
    }
    
    header('Location: ' . BASE_URL . '/admin/index.php?page=orders&view=' . $orderId);
    exit;
}

// If viewing a specific order
if ($viewOrderId > 0) {
    $order = $purchaseModel->getPurchaseByIdWithItems($viewOrderId);
    
    if (!$order) {
        Session::setFlash('error', 'Order not found');
        header('Location: ' . BASE_URL . '/admin/index.php?page=orders');
        exit;
    }
    
    // Get status options
    $statusOptions = [
        'Pending',
        'Processing',
        'Shipped',
        'Delivered',
        'Cancelled'
    ];
    
    // Include order detail view
    include 'order-detail.php';
} else {
    // Get all orders
    $orders = $purchaseModel->getAllPurchases();
    
    // Check for filter
    $statusFilter = isset($_GET['status']) ? Validation::sanitize($_GET['status']) : '';
    
    if ($statusFilter) {
        $orders = array_filter($orders, function($order) use ($statusFilter) {
            return $order->Status === $statusFilter;
        });
    }
    
    // Sort orders by date (most recent first)
    usort($orders, function($a, $b) {
        return strtotime($b->Date) - strtotime($a->Date);
    });
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Manage Orders</h4>
    <div class="btn-group">
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders" class="btn <?php echo !$statusFilter ? 'btn-primary' : 'btn-outline-primary'; ?>">All</a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders&status=Pending" class="btn <?php echo $statusFilter == 'Pending' ? 'btn-primary' : 'btn-outline-primary'; ?>">Pending</a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders&status=Processing" class="btn <?php echo $statusFilter == 'Processing' ? 'btn-primary' : 'btn-outline-primary'; ?>">Processing</a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders&status=Shipped" class="btn <?php echo $statusFilter == 'Shipped' ? 'btn-primary' : 'btn-outline-primary'; ?>">Shipped</a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders&status=Delivered" class="btn <?php echo $statusFilter == 'Delivered' ? 'btn-primary' : 'btn-outline-primary'; ?>">Delivered</a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders&status=Cancelled" class="btn <?php echo $statusFilter == 'Cancelled' ? 'btn-primary' : 'btn-outline-primary'; ?>">Cancelled</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
            <div class="text-center p-4">
                <p class="text-muted">No orders found<?php echo $statusFilter ? ' with status "' . $statusFilter . '"' : ''; ?>.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
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
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($order->CustFName . ' ' . $order->CustLName); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($order->CustEmail); ?></small>
                                </td>
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
                                    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders&view=<?php echo $order->PurchaseNo; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
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

<?php
}
?>
