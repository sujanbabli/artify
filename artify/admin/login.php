<?php
// Initialize the application
require_once '../includes/init.php';

// Check if admin is already logged in
if (Session::get('admin_logged_in')) {
    // Redirect to admin dashboard
    header('Location: index.php');
    exit;
}

// Define variables and set to empty values
$username = $password = '';
$usernameErr = $passwordErr = $loginErr = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $validation = new Validation();
    
    // Validate username
    if ($validation->required($_POST['username'], 'Username')) {
        $username = Validation::sanitize($_POST['username']);
    } else {
        $usernameErr = $validation->getError('Username');
    }
    
    // Validate password
    if ($validation->required($_POST['password'], 'Password')) {
        $password = $_POST['password'];
    } else {
        $passwordErr = $validation->getError('Password');
    }
    
    // If validation passed, attempt login
    if ($validation->passed()) {
        $adminModel = new AdminModel();
        $admin = $adminModel->login($username, $password);
        
        if ($admin) {
            // Set session variables
            Session::set('admin_logged_in', true);
            Session::set('admin_id', $admin->AdminNo);
            Session::set('admin_username', $admin->Username);
            Session::set('admin_name', $admin->Name);
            Session::set('admin_role', $admin->Role);
            
            // Redirect to admin dashboard
            header('Location: index.php');
            exit;
        } else {
            $loginErr = 'Invalid username or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/admin.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Admin Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($loginErr)): ?>
                            <div class="alert alert-danger"><?php echo $loginErr; ?></div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control <?php echo (!empty($usernameErr)) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo $username; ?>">
                                <div class="invalid-feedback"><?php echo $usernameErr; ?></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control <?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>" id="password" name="password">
                                <div class="invalid-feedback"><?php echo $passwordErr; ?></div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Need an account? <a href="register.php" class="fw-bold">Register here</a></p>
                            <p class="mt-2 small text-muted">Admin access is restricted to authorized personnel only.</p>
                            <p class="mt-3">
                                <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> Return to Shop
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
