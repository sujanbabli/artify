<?php
// Initialize customer model
$customerModel = new CustomerModel();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = isset($_POST['email']) ? Validation::sanitize($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $isAdmin = isset($_POST['is_admin']) ? true : false;

    // Validate form data
    $errors = [];

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !$isAdmin) {
        $errors[] = 'Invalid email format';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    // If no validation errors, attempt login
    if (empty($errors)) {
        if ($isAdmin) {
            // Try admin login
            $adminModel = new AdminModel();
            $admin = $adminModel->login($email, $password); // Using email field for username

            if ($admin) {
                // Admin login successful
                Session::set('admin_logged_in', true);
                Session::set('admin_id', $admin->AdminNo);
                Session::set('admin_username', $admin->Username);
                Session::set('admin_name', $admin->Name);
                Session::set('admin_role', $admin->Role);

                // Redirect to admin dashboard
                header('Location: ' . BASE_URL . '/admin/index.php');
                exit;
            } else {
                // Admin login failed, but don't reveal specifics
                $errors[] = 'Invalid admin credentials';
            }
        } else {
            // Try customer login
            $customer = $customerModel->getCustomerByEmail($email);

            if ($customer && password_verify($password, $customer->CustPassword)) {
                // Customer login successful
                Session::set('customer_logged_in', true);
                Session::set('customer_email', $customer->CustEmail);
                Session::set('customer_name', $customer->CustFName . ' ' . $customer->CustLName);

                // Redirect to previous page or home
                $redirectUrl = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : BASE_URL . '/public/index.php';
                unset($_SESSION['redirect_after_login']);

                header('Location: ' . $redirectUrl);
                exit;
            } else {
                // Login failed
                $errors[] = 'Invalid email or password';
            }
        }
    }
}
?>

<!-- Login Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if (isset($_SESSION['redirect_after_login']) && strpos($_SESSION['redirect_after_login'], 'checkout') !== false): ?>
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i> Please login or create an account to complete your purchase.
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Login to Your Account</h2>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo BASE_URL; ?>/public/index.php?page=login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-0">Don't have an account? <a href="<?php echo BASE_URL; ?>/public/index.php?page=register" class="fw-bold">Register Now</a></p>
                            <?php if (isset($_SESSION['redirect_after_login']) && strpos($_SESSION['redirect_after_login'], 'checkout') !== false): ?>
                                <p class="mt-2 small text-muted">Creating an account only takes a minute and will let you complete your purchase.</p>
                            <?php endif; ?>

                            <p class="mt-3">
                                <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> Continue Shopping
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/login.php" class="btn btn-outline-dark btn-sm ms-2">
                                    <i class="fas fa-user-shield me-1"></i> Admin Login
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>