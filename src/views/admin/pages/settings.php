<?php
// Get admin model
$adminModel = new AdminModel();

// Get current admin
$adminId = Session::get('admin_id');
$admin = $adminModel->getAdminById($adminId);

// Initialize errors array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        // Validate form data
        $validation = new Validation();
        
        // Validate required fields
        $name = Validation::sanitize($_POST['name']);
        $email = Validation::sanitize($_POST['email']);
        $username = Validation::sanitize($_POST['username']);
        
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } else if (!$validation->email($email, 'Email')) {
            $errors['email'] = $validation->getError('Email');
        }
        
        if (empty($username)) {
            $errors['username'] = 'Username is required';
        }
        
        // If validation passed, save profile
        if (empty($errors)) {
            $result = $adminModel->updateAdmin([
                'id' => $adminId,
                'name' => $name,
                'email' => $email,
                'username' => $username
            ]);
            
            if ($result) {
                Session::set('admin_username', $username);
                Session::setFlash('success', 'Profile updated successfully');
            } else {
                Session::setFlash('error', 'Failed to update profile');
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Validate password fields
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (empty($currentPassword)) {
            $errors['current_password'] = 'Current password is required';
        } else if (!$adminModel->verifyPassword($adminId, $currentPassword)) {
            $errors['current_password'] = 'Current password is incorrect';
        }
        
        if (empty($newPassword)) {
            $errors['new_password'] = 'New password is required';
        } else if (strlen($newPassword) < 6) {
            $errors['new_password'] = 'New password must be at least 6 characters';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        // If validation passed, change password
        if (empty($errors)) {
            $result = $adminModel->changePassword($adminId, $newPassword);
            
            if ($result) {
                Session::setFlash('success', 'Password changed successfully');
            } else {
                Session::setFlash('error', 'Failed to change password');
            }
        }
    }
}
?>

<div class="row">
    <!-- Profile -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Profile Settings</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/admin/index.php?page=settings" method="post">
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($admin->Name); ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['name']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($admin->Email); ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['email']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo htmlspecialchars($admin->Username); ?>" required>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['username']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Change Password -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/admin/index.php?page=settings" method="post">
                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['current_password']) ? 'is-invalid' : ''; ?>" id="current_password" name="current_password" required>
                        <?php if (isset($errors['current_password'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['current_password']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>" id="new_password" name="new_password" required>
                        <?php if (isset($errors['new_password'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['new_password']; ?>
                            </div>
                        <?php else: ?>
                            <div class="form-text">Password must be at least 6 characters</div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['confirm_password']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Store Settings (could be expanded in the future) -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Store Settings</h5>
            </div>
            <div class="card-body">
                <form action="#" method="post">
                    <div class="row">
                        <!-- Store Name -->
                        <div class="col-md-6 mb-3">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" class="form-control" id="store_name" name="store_name" value="Artify" disabled>
                        </div>
                        
                        <!-- Store Email -->
                        <div class="col-md-6 mb-3">
                            <label for="store_email" class="form-label">Store Email</label>
                            <input type="email" class="form-control" id="store_email" name="store_email" value="info@artify.com" disabled>
                        </div>
                        
                        <!-- Store Address -->
                        <div class="col-md-6 mb-3">
                            <label for="store_address" class="form-label">Store Address</label>
                            <textarea class="form-control" id="store_address" name="store_address" rows="3" disabled>123 Art Street, Darwin NT 0800, Australia</textarea>
                        </div>
                        
                        <!-- Store Phone -->
                        <div class="col-md-6 mb-3">
                            <label for="store_phone" class="form-label">Store Phone</label>
                            <input type="text" class="form-control" id="store_phone" name="store_phone" value="+61 8 1234 5678" disabled>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Store settings are managed through the configuration file. Contact your developer to change these settings.
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Admin Management Section (Visible only to Administrators) -->
    <?php if($admin->Role === 'Administrator'): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Admin Users</h5>
                    <a href="<?php echo BASE_URL; ?>/admin/register.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New Admin
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $admins = $adminModel->getAllAdmins();
                                foreach($admins as $adminUser): 
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($adminUser->Username); ?></td>
                                    <td><?php echo htmlspecialchars($adminUser->Name); ?></td>
                                    <td><?php echo htmlspecialchars($adminUser->Email); ?></td>
                                    <td>
                                        <span class="badge <?php echo $adminUser->Role === 'Administrator' ? 'bg-danger' : 'bg-info'; ?>">
                                            <?php echo htmlspecialchars($adminUser->Role); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $adminUser->LastLogin ? date('M j, Y g:i A', strtotime($adminUser->LastLogin)) : 'Never'; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-3 bg-light border-top">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>Admin users can be added via the "Add New Admin" button. For security purposes, admin deletion must be performed directly in the database.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
