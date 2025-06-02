<?php
// Get customer model
$customerModel = new CustomerModel();

// Get all customers
$customers = $customerModel->getAllCustomers();

// Handle delete customer action
if (isset($_POST['delete_customer'])) {
    $email = $_POST['customer_email'];
    if ($customerModel->deleteCustomer($email)) {
        Session::setFlash('success', 'Customer deleted successfully');
        // Refresh the page to show updated customer list
        header('Location: ' . BASE_URL . '/admin/index.php?page=customers');
        exit;
    } else {
        Session::setFlash('error', 'Failed to delete customer');
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Customers</h4>
    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=customer-form" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Customer
    </a>
</div>

<?php if (Session::hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo Session::getFlash('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (Session::hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo Session::getFlash('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="customersTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No customers found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer->CustFName . ' ' . $customer->CustLName); ?></td>
                                <td><?php echo htmlspecialchars($customer->CustEmail); ?></td>
                                <td>
                                    <?php
                                        $location = [];
                                        if (!empty($customer->City)) $location[] = $customer->City;
                                        if (!empty($customer->State)) $location[] = $customer->State;
                                        if (!empty($customer->Country)) $location[] = $customer->Country;
                                        echo !empty($location) ? htmlspecialchars(implode(', ', $location)) : 'N/A';
                                    ?>
                                </td>
                                <td><?php echo !empty($customer->Phone) ? htmlspecialchars($customer->Phone) : 'N/A'; ?></td>
                                <td>
                                    <?php 
                                        $purchaseModel = new PurchaseModel();
                                        $customerOrders = $purchaseModel->getPurchasesByCustomer($customer->CustEmail);
                                        echo count($customerOrders);
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=customer-detail&email=<?php echo urlencode($customer->CustEmail); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=customer-form&email=<?php echo urlencode($customer->CustEmail); ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo str_replace('@', '', $customer->CustEmail); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo str_replace('@', '', $customer->CustEmail); ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete customer <strong><?php echo htmlspecialchars($customer->CustFName . ' ' . $customer->CustLName); ?></strong>?</p>
                                                    <p class="text-danger">This action cannot be undone. All orders associated with this customer will also be deleted.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form method="post">
                                                        <input type="hidden" name="customer_email" value="<?php echo $customer->CustEmail; ?>">
                                                        <button type="submit" name="delete_customer" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable for better user experience
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#customersTable').DataTable({
                responsive: true,
                order: [[0, 'asc']]
            });
        }
    });
</script>
