<?php
// Load configuration
require_once '../includes/init.php';
require_once ROOT_DIR . '/src/utils/EmailHelper.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $result = EmailHelper::testEmailConfiguration($email);
    
    // Set flash message
    if ($result['success']) {
        Session::setFlash('success', $result['message']);
    } else {
        Session::setFlash('error', $result['message']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Configuration - Artify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Test Email Configuration</h4>
                    </div>
                    <div class="card-body">
                        <?php if (Session::hasFlash('success')): ?>
                            <div class="alert alert-success">
                                <?php echo Session::getFlash('success')['message']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (Session::hasFlash('error')): ?>
                            <div class="alert alert-danger">
                                <?php echo Session::getFlash('error')['message']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="form-text">
                                    Enter the email address where you want to receive the test email.
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Send Test Email</button>
                            </div>
                        </form>
                        
                        <div class="mt-4">
                            <h5>Current Email Configuration:</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>SMTP Enabled:</strong> <?php echo USE_SMTP ? 'Yes' : 'No'; ?>
                                </li>
                                <?php if (USE_SMTP): ?>
                                <li class="list-group-item">
                                    <strong>SMTP Host:</strong> <?php echo SMTP_HOST; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>SMTP Port:</strong> <?php echo SMTP_PORT; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>SMTP Username:</strong> <?php echo SMTP_USERNAME; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>SMTP Security:</strong> <?php echo SMTP_SECURE; ?>
                                </li>
                                <?php endif; ?>
                                <li class="list-group-item">
                                    <strong>From Email:</strong> <?php echo EMAIL_FROM; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Admin Email:</strong> <?php echo ADMIN_EMAIL; ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn btn-outline-secondary">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
