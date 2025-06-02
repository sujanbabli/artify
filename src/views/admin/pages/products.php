<?php
// Get product controller
$productController = new ProductController();

// Get all products
$products = $productController->getAllProducts();

// Check if delete request
if (isset($_GET['delete'])) {
    $productId = (int)$_GET['delete'];
    
    if ($productController->deleteProduct($productId)) {
        Session::setFlash('success', 'Product deleted successfully');
    } else {
        Session::setFlash('error', 'Failed to delete product');
    }
    
    header('Location: ' . BASE_URL . '/admin/index.php?page=products');
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Manage Products</h4>
    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=product-form" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Product
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($products)): ?>
            <div class="text-center p-4">
                <p class="text-muted">No products found. Click the "Add New Product" button to add one.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="80">Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($product->ImagePath); ?>" 
                                         class="img-thumbnail" 
                                         alt="<?php echo htmlspecialchars($product->Description); ?>" 
                                         width="60">
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($product->Description); ?></div>
                                    <small class="text-muted">ID: <?php echo $product->ProductNo; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($product->Category); ?></td>
                                <td>$<?php echo number_format($product->Price, 2); ?></td>
                                <td>
                                    <?php if ($product->Active): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=product-form&id=<?php echo $product->ProductNo; ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=products&delete=<?php echo $product->ProductNo; ?>" class="btn btn-outline-danger confirm-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
