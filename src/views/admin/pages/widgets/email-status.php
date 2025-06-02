<?php
// Get recent email errors
$logFile = ROOT_DIR . '/logs/email_errors.log';
$recentErrors = [];

if (file_exists($logFile)) {
    $allLines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recentErrors = array_slice($allLines, 0, 5); // Get 5 most recent errors
}

// Get email settings
$emailEnabled = get_site_setting('email_notifications_enabled', true);
$adminNotifications = get_site_setting('email_notifications_to_admin', true);
$customerNotifications = get_site_setting('email_notifications_to_customer', true);
?>

<div class="card shadow mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Email System Status</h6>
        <div>
            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=email-settings" class="btn btn-sm btn-primary">
                <i class="fas fa-cog me-1"></i> Settings
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=email-logs" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-history me-1"></i> Logs
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <h6>Email Notifications Status:</h6>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge <?php echo $emailEnabled ? 'bg-success' : 'bg-danger'; ?> me-2">
                            <?php echo $emailEnabled ? 'Enabled' : 'Disabled'; ?>
                        </span>
                        <span>Email Notifications</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge <?php echo ($emailEnabled && $customerNotifications) ? 'bg-success' : 'bg-danger'; ?> me-2">
                            <?php echo ($emailEnabled && $customerNotifications) ? 'Enabled' : 'Disabled'; ?>
                        </span>
                        <span>Customer Order Confirmations</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge <?php echo ($emailEnabled && $adminNotifications) ? 'bg-success' : 'bg-danger'; ?> me-2">
                            <?php echo ($emailEnabled && $adminNotifications) ? 'Enabled' : 'Disabled'; ?>
                        </span>
                        <span>Admin Notifications</span>
                    </div>
                </div>
                
                <h6>Configuration:</h6>
                <div class="small text-muted">
                    From: <?php echo htmlspecialchars(EMAIL_FROM); ?><br>
                    Admin: <?php echo htmlspecialchars(ADMIN_EMAIL); ?><br>
                    Method: <?php echo defined('USE_SMTP') && USE_SMTP ? 'SMTP' : 'PHP mail()'; ?>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Recent Email Errors:</h6>
                <?php if (empty($recentErrors)): ?>
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle me-1"></i> No recent errors
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <div class="small mb-1"><strong>Most recent errors:</strong></div>
                        <ul class="list-unstyled small m-0">
                            <?php foreach($recentErrors as $i => $error): 
                                if ($i >= 3) break; // Show max 3 errors
                                
                                // Extract date and message
                                preg_match('/\[(.*?)\](.*)/', $error, $matches);
                                if (count($matches) >= 3):
                                    $date = $matches[1];
                                    $message = $matches[2];
                            ?>
                                <li class="text-truncate">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <?php echo htmlspecialchars(substr($message, 0, 70) . (strlen($message) > 70 ? '...' : '')); ?>
                                </li>
                            <?php 
                                endif;
                            endforeach; ?>
                            
                            <?php if (count($recentErrors) > 3): ?>
                                <li class="text-center mt-1">
                                    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=email-logs" class="small">
                                        View all <?php echo count($recentErrors); ?> errors
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
