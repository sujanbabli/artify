<?php
// Initialize the application
require_once '../includes/init.php';

// Check if registration is allowed
// Typically in a production environment, you'd want to restrict admin registration
// You might want to add a config setting to control this
$allowRegistration = true; // Set to false in production

// Check if user is already logged in
if (Session::get('admin_logged_in')) {
    // Redirect to admin dashboard
    header('Location: index.php');
    exit;
}

// Define variables and set to empty values
$username = $password = $confirmPassword = $email = $name = $role = '';
$usernameErr = $passwordErr = $confirmPasswordErr = $emailErr = $nameErr = $roleErr = $registerErr = '';
$success = false;

// Process registration form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $validation = new Validation();
    
    // Validate username
    if ($validation->required($_POST['username'], 'Username')) {
        $username = Validation::sanitize($_POST['username']);
    } else {
        $usernameErr = $validation->getError('Username');
    }
    
    // Validate email
    if ($validation->required($_POST['email'], 'Email') && $validation->email($_POST['email'])) {
        $email = Validation::sanitize($_POST['email']);
    } else {
        $emailErr = $validation->getError('Email');
    }
    
    // Validate name
    if ($validation->required($_POST['name'], 'Name')) {
        $name = Validation::sanitize($_POST['name']);
    } else {
        $nameErr = $validation->getError('Name');
    }
    
    // Validate role
    if (isset($_POST['role']) && in_array($_POST['role'], ['Administrator', 'Editor', 'Moderator'])) {
        $role = Validation::sanitize($_POST['role']);
    } else {
        $roleErr = 'Please select a valid role';
    }
    
    // Validate password
    if ($validation->required($_POST['password'], 'Password')) {
        if (strlen($_POST['password']) < 8) {
            $passwordErr = 'Password must be at least 8 characters long';
        } else {
            $password = $_POST['password'];
        }
    } else {
        $passwordErr = $validation->getError('Password');
    }
    
    // Validate confirm password
    if ($validation->required($_POST['confirmPassword'], 'Confirm Password')) {
        $confirmPassword = $_POST['confirmPassword'];
        if ($password !== $confirmPassword) {
            $confirmPasswordErr = 'Passwords do not match';
        }
    } else {
        $confirmPasswordErr = $validation->getError('Confirm Password');
    }
    
    // If validation passed, attempt registration
    if (empty($usernameErr) && empty($emailErr) && empty($nameErr) && empty($roleErr) && 
        empty($passwordErr) && empty($confirmPasswordErr)) {
        
        // Create admin data array
        $adminData = [
            'username' => $username,
            'email' => $email,
            'name' => $name,
            'role' => $role,
            'password' => $password
        ];
        
        // Attempt to register admin
        $adminModel = new AdminModel();
        $result = $adminModel->registerAdmin($adminData);
        
        if ($result) {
            // Registration successful
            $success = true;
            
            // Clear form fields
            $username = $password = $confirmPassword = $email = $name = $role = '';
        } else {
            $registerErr = 'Registration failed. Username or email may already exist.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - <?php echo SITE_NAME; ?></title>
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
                        <h4>Admin Registration</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <strong>Success!</strong> Admin account created successfully. 
                                <a href="login.php" class="alert-link">Click here to login</a>.
                            </div>
                        <?php elseif (!$allowRegistration): ?>
                            <div class="alert alert-warning">
                                <strong>Notice:</strong> Admin registration is currently disabled.
                            </div>
                        <?php else: ?>
                            <?php if (!empty($registerErr)): ?>
                                <div class="alert alert-danger"><?php echo $registerErr; ?></div>
                            <?php endif; ?>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control <?php echo (!empty($usernameErr)) ? 'is-invalid' : ''; ?>" 
                                           id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
                                    <div class="invalid-feedback"><?php echo $usernameErr; ?></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control <?php echo (!empty($emailErr)) ? 'is-invalid' : ''; ?>" 
                                           id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                    <div class="invalid-feedback"><?php echo $emailErr; ?></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control <?php echo (!empty($nameErr)) ? 'is-invalid' : ''; ?>" 
                                           id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
                                    <div class="invalid-feedback"><?php echo $nameErr; ?></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select <?php echo (!empty($roleErr)) ? 'is-invalid' : ''; ?>" 
                                            id="role" name="role">
                                        <option value="" <?php echo empty($role) ? 'selected' : ''; ?>>Select Role</option>
                                        <option value="Administrator" <?php echo ($role === 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
                                        <option value="Editor" <?php echo ($role === 'Editor') ? 'selected' : ''; ?>>Editor</option>
                                        <option value="Moderator" <?php echo ($role === 'Moderator') ? 'selected' : ''; ?>>Moderator</option>
                                    </select>
                                    <div class="invalid-feedback"><?php echo $roleErr; ?></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control <?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>" 
                                           id="password" name="password">
                                    <div class="form-text">Password must be at least 8 characters long.</div>
                                    <div class="invalid-feedback"><?php echo $passwordErr; ?></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control <?php echo (!empty($confirmPasswordErr)) ? 'is-invalid' : ''; ?>" 
                                           id="confirmPassword" name="confirmPassword">
                                    <div class="invalid-feedback"><?php echo $confirmPasswordErr; ?></div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php" class="fw-bold">Login here</a></p>
                            <p class="mt-2 small text-muted">Admin accounts have access to manage products, orders, and system settings.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
