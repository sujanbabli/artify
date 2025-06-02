<?php
// Check if admin is logged in
if (!Session::get('admin_logged_in')) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_email_settings'])) {
        // Update email settings
        $emailEnabled = isset($_POST['email_notifications_enabled']);
        $emailToAdmin = isset($_POST['email_notifications_to_admin']);
        $emailToCustomer = isset($_POST['email_notifications_to_customer']);
        
        // Update settings
        update_site_setting('email_notifications_enabled', $emailEnabled);
        update_site_setting('email_notifications_to_admin', $emailToAdmin);
        update_site_setting('email_notifications_to_customer', $emailToCustomer);
        
        Session::setFlash('success', 'Email settings have been updated.');
        header('Location: ' . BASE_URL . '/admin/index.php?page=email-settings');
        exit;
    } elseif (isset($_POST['send_test_email']) && !empty($_POST['test_email'])) {
        // Send test email
        $testEmail = Validation::sanitize($_POST['test_email']);
        require_once(ROOT_DIR . '/src/utils/EmailHelper.php');
        $testResult = EmailHelper::testEmailConfiguration($testEmail);
        
        if ($testResult['success']) {
            Session::setFlash('success', $testResult['message']);
        } else {
            Session::setFlash('error', $testResult['message']);
        }
        
        header('Location: ' . BASE_URL . '/admin/index.php?page=email-settings');
        exit;
    }
}

// Get current settings
$emailEnabled = get_site_setting('email_notifications_enabled', true);
$emailToAdmin = get_site_setting('email_notifications_to_admin', true);
$emailToCustomer = get_site_setting('email_notifications_to_customer', true);
?>

<!-- Email Settings Section -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Email Settings</h1>
    </div>
    
    <?php if (Session::hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo Session::getFlash('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo Session::getFlash('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Notification Settings</h6>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_notifications_enabled" name="email_notifications_enabled" <?php echo $emailEnabled ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="email_notifications_enabled">Enable Email Notifications</label>
                    <div class="form-text">Master toggle for all email notifications</div>
                </div>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_notifications_to_admin" name="email_notifications_to_admin" <?php echo $emailToAdmin ? 'checked' : ''; ?> <?php echo !$emailEnabled ? 'disabled' : ''; ?>>
                    <label class="form-check-label" for="email_notifications_to_admin">Send Admin Notifications</label>
                    <div class="form-text">Send order and testimonial notifications to admin</div>
                </div>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_notifications_to_customer" name="email_notifications_to_customer" <?php echo $emailToCustomer ? 'checked' : ''; ?> <?php echo !$emailEnabled ? 'disabled' : ''; ?>>
                    <label class="form-check-label" for="email_notifications_to_customer">Send Customer Notifications</label>
                    <div class="form-text">Send order confirmations and other emails to customers</div>
                </div>
                
                <button type="submit" name="update_email_settings" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Test Email Configuration</h6>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="test_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="test_email" name="test_email" required 
                                   placeholder="Enter email address to test">
                            <div class="form-text">A test email will be sent to this address.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="send_test_email" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Send Test Email
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Email Configuration</h6>
        </div>
        <div class="card-body">
            <p>Current email configuration:</p>
            
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 200px;">From Address:</th>
                        <td><?php echo htmlspecialchars(EMAIL_FROM); ?></td>
                    </tr>
                    <tr>
                        <th>Admin Email:</th>
                        <td><?php echo htmlspecialchars(ADMIN_EMAIL); ?></td>
                    </tr>
                    <tr>
                        <th>Method:</th>
                        <td><?php echo defined('USE_SMTP') && USE_SMTP ? 'SMTP' : 'PHP mail()'; ?></td>
                    </tr>
                    <?php if (defined('USE_SMTP') && USE_SMTP): ?>
                    <tr>
                        <th>SMTP Host:</th>
                        <td><?php echo htmlspecialchars(SMTP_HOST); ?></td>
                    </tr>
                    <tr>
                        <th>SMTP Port:</th>
                        <td><?php echo htmlspecialchars(SMTP_PORT); ?></td>
                    </tr>
                    <tr>
                        <th>SMTP Security:</th>
                        <td><?php echo htmlspecialchars(SMTP_SECURE); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="alert alert-info mt-3">
                <p><strong>Note:</strong> To change these settings, edit the <code>config.php</code> file in the config directory.</p>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between mt-4">
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=resend-emails" class="btn btn-outline-primary">
            <i class="fas fa-paper-plane me-1"></i> Resend Order Emails
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const masterToggle = document.getElementById('email_notifications_enabled');
    const adminToggle = document.getElementById('email_notifications_to_admin');
    const customerToggle = document.getElementById('email_notifications_to_customer');
    
    // Add event listener to master toggle
    masterToggle.addEventListener('change', function() {
        adminToggle.disabled = !this.checked;
        customerToggle.disabled = !this.checked;
    });
});
</script>
