<?php
// Initialize customer model
$customerModel = new CustomerModel();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = isset($_POST['firstName']) ? Validation::sanitize($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? Validation::sanitize($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? Validation::sanitize($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';
    
    // Validate form data
    $errors = [];
    
    if (empty($firstName)) {
        $errors[] = 'First name is required';
    }
    
    if (empty($lastName)) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    } elseif ($customerModel->getCustomerByEmail($email)) {
        $errors[] = 'Email already exists';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    // If no validation errors, register user
    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Register customer
        if ($customerModel->registerCustomer($firstName, $lastName, $email, $hashedPassword)) {
            // Registration successful
            Session::setFlash('success', 'Registration successful! Please login.');
            header('Location: ' . BASE_URL . '/public/index.php?page=login');
            exit;
        } else {
            // Registration failed
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>

<!-- Register Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (isset($_SESSION['redirect_after_login']) && strpos($_SESSION['redirect_after_login'], 'checkout') !== false): ?>
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i> Create an account to complete your purchase. Your cart items will be saved.
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Create an Account</h2>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo BASE_URL; ?>/public/index.php?page=register">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required 
                                           value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required 
                                           value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Password must be at least 6 characters long.</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0">Already have an account? <a href="<?php echo BASE_URL; ?>/public/index.php?page=login" class="fw-bold">Login Here</a></p>
                            <?php if (isset($_SESSION['redirect_after_login']) && strpos($_SESSION['redirect_after_login'], 'checkout') !== false): ?>
                            <p class="mt-2 small text-muted">Once registered, you'll be able to complete your purchase and track your orders.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
