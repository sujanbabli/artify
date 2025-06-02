<?php
// Check if admin is logged in
if (!Session::get('admin_logged_in')) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// Define log file path
$logFile = ROOT_DIR . '/logs/email_errors.log';
$logLines = [];

// Read log file if it exists
if (file_exists($logFile)) {
    $logLines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $logLines = array_reverse($logLines); // Show newest entries first
}

// Clear logs if requested
if (isset($_POST['clear_logs']) && $_POST['clear_logs'] === 'true') {
    if (file_exists($logFile)) {
        file_put_contents($logFile, '');
        Session::setFlash('success', 'Email error logs have been cleared.');
        header('Location: ' . BASE_URL . '/admin/index.php?page=email-logs');
        exit;
    }
}

// Handle test email
$testResult = null;
if (isset($_POST['test_email']) && !empty($_POST['email'])) {
    $testEmail = Validation::sanitize($_POST['email']);
    $testResult = EmailHelper::testEmailConfiguration($testEmail);
}
?>

<!-- Email Logs Section -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Email Error Logs</h1>
        
        <?php if (!empty($logLines)): ?>
        <form method="post" onsubmit="return confirm('Are you sure you want to clear all logs?');">
            <input type="hidden" name="clear_logs" value="true">
            <button type="submit" class="btn btn-sm btn-danger">
                <i class="fas fa-trash me-1"></i> Clear Logs
            </button>
        </form>
        <?php endif; ?>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Email Sending Errors</h6>
        </div>
        <div class="card-body">
            <?php if (empty($logLines)): ?>
                <div class="alert alert-info">
                    No email errors have been logged.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logLines as $line): 
                                // Extract date and message
                                preg_match('/\[(.*?)\]\s(.*)/', $line, $matches);
                                if (count($matches) >= 3):
                                    $date = $matches[1];
                                    $message = $matches[2];
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($date); ?></td>
                                    <td><?php echo htmlspecialchars($message); ?></td>
                                </tr>
                            <?php 
                                endif;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Email Troubleshooting</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                
                    
                    <h5>Current Email Configuration:</h5>
                    <p>
                        <strong>From Address:</strong> <?php echo htmlspecialchars(EMAIL_FROM); ?><br>
                        <strong>Admin Email:</strong> <?php echo htmlspecialchars(ADMIN_EMAIL); ?><br>
                        <strong>Mail Method:</strong> <?php echo defined('USE_SMTP') && USE_SMTP ? 'SMTP' : 'PHP mail()'; ?>
                    </p>
                                    </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Test Email Configuration</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($testResult): ?>
                                <div class="alert alert-<?php echo $testResult['success'] ? 'success' : 'danger'; ?> mb-3">
                                    <?php echo htmlspecialchars($testResult['message']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="post">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address to Test</label>
                                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter email address">
                                    <div class="form-text">A test email will be sent to this address.</div>
                                </div>
                                <button type="submit" name="test_email" class="btn btn-primary">Send Test Email</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
