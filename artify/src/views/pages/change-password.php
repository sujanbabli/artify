<?php
// Check if user is logged in
if (!Session::get('customer_logged_in')) {
    // Store the current page URL to redirect back after login
    Session::set('redirect_after_login', BASE_URL . '/public/index.php?page=change-password');
    
    // Redirect to login page
    header('Location: ' . BASE_URL . '/public/index.php?page=login');
    exit;
}

// Get customer email from session
$customerEmail = Session::get('customer_email');

// Get customer model
$customerModel = new CustomerModel();

// Process form submission
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $currentPassword = isset($_POST['currentPassword']) ? $_POST['currentPassword'] : '';
    $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';
    
    // Validate form data
    if (empty($currentPassword)) {
        $errors[] = 'Current password is required';
    }
    
    if (empty($newPassword)) {
        $errors[] = 'New password is required';
    } elseif (strlen($newPassword) < 6) {
        $errors[] = 'New password must be at least 6 characters';
    }
    
    if ($newPassword !== $confirmPassword) {
        $errors[] = 'New passwords do not match';
    }
    
    // If no validation errors, verify current password and update
    if (empty($errors)) {
        // Get customer data
        $customer = $customerModel->getCustomerByEmail($customerEmail);
        
        // Verify current password
        if (!password_verify($currentPassword, $customer->CustPassword)) {
            $errors[] = 'Current password is incorrect';
        } else {
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            if ($customerModel->updateCustomerPassword($customerEmail, $hashedPassword)) {
                $success = true;
            } else {
                $errors[] = 'Failed to update password. Please try again.';
            }
        }
    }
}
?>

<!-- Change Password Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Change Password</h5>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?page=profile" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Profile
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success mb-4">
                                Your password has been updated successfully.
                            </div>
                            <div class="text-center">
                                <a href="<?php echo BASE_URL; ?>/public/index.php?page=profile" class="btn btn-primary">
                                    Return to Profile
                                </a>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?php echo BASE_URL; ?>/public/index.php?page=change-password">
                                <div class="mb-3">
                                    <label for="currentPassword" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                    <div class="form-text">Password must be at least 6 characters.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
