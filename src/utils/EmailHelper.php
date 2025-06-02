<?php
/**
 * Email Helper Class
 * 
 * Handles sending emails to customers and admin
 */

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {
    /**
     * Send email using PHPMailer or PHP mail() based on configuration
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email message (HTML)
     * @param string $fromEmail From email address
     * @param string $fromName From name
     * @param string $replyToEmail Reply-to email address
     * @return boolean Success status
     */
    private static function sendEmail($to, $subject, $message, $fromEmail = null, $fromName = null, $replyToEmail = null) {
        // Set defaults if not provided
        $fromEmail = $fromEmail ?? EMAIL_FROM;
        $fromName = $fromName ?? SITE_NAME;
        $replyToEmail = $replyToEmail ?? ADMIN_EMAIL;
        
        // If SMTP is enabled in config, use PHPMailer with SMTP
        if (defined('USE_SMTP') && USE_SMTP === true) {
            try {
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);
                
                // Server settings
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
                $mail->isSMTP();                         // Send using SMTP
                $mail->Host       = SMTP_HOST;           // SMTP server
                $mail->SMTPAuth   = true;                // Enable SMTP authentication
                $mail->Username   = SMTP_USERNAME;       // SMTP username
                $mail->Password   = SMTP_PASSWORD;       // SMTP password
                $mail->SMTPSecure = SMTP_SECURE;         // Enable TLS/SSL encryption
                $mail->Port       = SMTP_PORT;           // TCP port to connect to
                
                // Fix trailing space in username if present
                $mail->Username = trim($mail->Username);
                
                // Recipients
                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($to);                  // Add a recipient
                $mail->addReplyTo($replyToEmail);
                
                // Content
                $mail->isHTML(true);                     // Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->AltBody = strip_tags(str_replace('<br>', "\n", $message)); // Plain text alternative
                
                // Send email
                $mail->send();
                return true;
            } catch (Exception $e) {
                self::logEmailError($to, $subject, 'PHPMailer error: ' . $mail->ErrorInfo);
                return false;
            }
        } else {
            // Use PHP's mail() function
            $headers = "From: {$fromName} <{$fromEmail}>\r\n";
            $headers .= "Reply-To: {$replyToEmail}\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            // Send email
            $success = mail($to, $subject, $message, $headers);
            
            // Log error if mail fails
            if (!$success) {
                self::logEmailError($to, $subject, 'mail() function returned false');
            }
            
            return $success;
        }
    }
    
    /**
     * Log email errors
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $errorMsg Error message
     */
    private static function logEmailError($to, $subject, $errorMsg) {
        $logFile = ROOT_DIR . '/logs/email_errors.log';
        $dir = dirname($logFile);
        
        // Create logs directory if it doesn't exist
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Format log message with more details
        $logMessage = date('[Y-m-d H:i:s]') . " Failed to send email: \n";
        $logMessage .= "To: {$to}\n";
        $logMessage .= "Subject: {$subject}\n";
        $logMessage .= "Error: {$errorMsg}\n";
        $logMessage .= "SMTP Config: " . (USE_SMTP ? 'Enabled' : 'Disabled') . "\n";
        
        if (USE_SMTP) {
            $logMessage .= "SMTP Host: " . SMTP_HOST . "\n";
            $logMessage .= "SMTP Port: " . SMTP_PORT . "\n";
            $logMessage .= "SMTP Username: " . SMTP_USERNAME . "\n";
            $logMessage .= "SMTP Security: " . SMTP_SECURE . "\n";
        }
        
        $logMessage .= "----------------------------------------\n";
        
        // Append to log file
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        // If in debug mode, also log to PHP error log
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("Email Error: To: {$to}, Subject: {$subject}, Error: {$errorMsg}");
        }
    }
    
    /**
     * Validate email address
     * @param string $email Email to validate
     * @return boolean Valid or not
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Send order confirmation email to customer
     * @param object $customer Customer details
     * @param object $purchase Purchase details
     * @param array $items Purchase items
     * @return boolean Success status
     */
    public static function sendOrderConfirmation($customer, $purchase, $items) {
        // Check if email notifications are enabled
        if (!get_site_setting('email_notifications_enabled', true) || 
            !get_site_setting('email_notifications_to_customer', true)) {
            return false;
        }
        
        // Validate email
        if (empty($customer->CustEmail) || !self::validateEmail($customer->CustEmail)) {
            self::logEmailError('invalid email', 'Order Confirmation', 'Invalid email address: ' . ($customer->CustEmail ?? 'empty'));
            return false;
        }
        
        $to = $customer->CustEmail;
        $subject = "Order Confirmation - Artify #" . $purchase->PurchaseNo;
        
        // Start building the email content
        $message = "<html><body>";
        $message .= "<h2>Thank you for your order from Artify!</h2>";
        $message .= "<p>Hello {$customer->CustFName} {$customer->CustLName},</p>";
        $message .= "<p>Your order #{$purchase->PurchaseNo} has been received and is being processed.</p>";
        
        $message .= "<h3>Order Details:</h3>";
        $message .= "<table border='1' cellpadding='5' cellspacing='0' width='100%'>";
        $message .= "<tr><th>Item</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>";
        
        $total = 0;
        
        foreach($items as $item) {
            $subtotal = $item->Quantity * $item->Price;
            $total += $subtotal;
            
            $message .= "<tr>";
            $message .= "<td>{$item->Description}</td>";
            $message .= "<td>{$item->Quantity}</td>";
            $message .= "<td>$" . number_format($item->Price, 2) . "</td>";
            $message .= "<td>$" . number_format($subtotal, 2) . "</td>";
            $message .= "</tr>";
        }
        
        $message .= "<tr><td colspan='3' align='right'><strong>Total:</strong></td>";
        $message .= "<td>$" . number_format($total, 2) . "</td></tr>";
        $message .= "</table>";
        
        $message .= "<h3>Delivery Address:</h3>";
        $message .= "<p>{$customer->Address}<br>";
        $message .= "{$customer->City}, {$customer->State} {$customer->PostCode}<br>";
        $message .= "{$customer->Country}</p>";
        
        $message .= "<p>If you have any questions about your order, please contact us.</p>";
        $message .= "<p>Thank you for shopping with Artify!</p>";
        $message .= "</body></html>";
        
        // Send email using our helper method
        return self::sendEmail($to, $subject, $message, EMAIL_FROM, SITE_NAME, ADMIN_EMAIL);
    }
    
    /**
     * Send order notification email to admin
     * @param object $customer Customer details
     * @param object $purchase Purchase details
     * @param array $items Purchase items
     * @return boolean Success status
     */
    public static function sendOrderNotificationToAdmin($customer, $purchase, $items) {
        // Check if email notifications are enabled
        if (!get_site_setting('email_notifications_enabled', true) || 
            !get_site_setting('email_notifications_to_admin', true)) {
            return false;
        }
        
        // Validate admin email from config
        if (empty(ADMIN_EMAIL) || !self::validateEmail(ADMIN_EMAIL)) {
            self::logEmailError('invalid admin email', 'Order Notification', 'Invalid admin email address: ' . (ADMIN_EMAIL ?? 'empty'));
            return false;
        }
        
        $to = ADMIN_EMAIL;
        $subject = "New Order Received - Artify #" . $purchase->PurchaseNo;
        
        // Start building the email content
        $message = "<html><body>";
        $message .= "<h2>New Order Received</h2>";
        $message .= "<p>A new order has been placed on the Artify website.</p>";
        
        $message .= "<h3>Customer Information:</h3>";
        $message .= "<p>Name: {$customer->Title} {$customer->CustFName} {$customer->CustLName}<br>";
        $message .= "Email: {$customer->CustEmail}<br>";
        $message .= "Phone: {$customer->Phone}</p>";
        
        $message .= "<h3>Delivery Address:</h3>";
        $message .= "<p>{$customer->Address}<br>";
        $message .= "{$customer->City}, {$customer->State} {$customer->PostCode}<br>";
        $message .= "{$customer->Country}</p>";
        
        $message .= "<h3>Order Details (#{$purchase->PurchaseNo}):</h3>";
        $message .= "<table border='1' cellpadding='5' cellspacing='0' width='100%'>";
        $message .= "<tr><th>Product ID</th><th>Description</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>";
        
        $total = 0;
        
        foreach($items as $item) {
            $subtotal = $item->Quantity * $item->Price;
            $total += $subtotal;
            
            $message .= "<tr>";
            $message .= "<td>{$item->ProductNo}</td>";
            $message .= "<td>{$item->Description}</td>";
            $message .= "<td>{$item->Quantity}</td>";
            $message .= "<td>$" . number_format($item->Price, 2) . "</td>";
            $message .= "<td>$" . number_format($subtotal, 2) . "</td>";
            $message .= "</tr>";
        }
        
        $message .= "<tr><td colspan='4' align='right'><strong>Total:</strong></td>";
        $message .= "<td>$" . number_format($total, 2) . "</td></tr>";
        $message .= "</table>";
        
        $message .= "<p>Please process this order as soon as possible.</p>";
        $message .= "</body></html>";
        
        // Send email using our helper method
        return self::sendEmail($to, $subject, $message, EMAIL_FROM, SITE_NAME, $customer->CustEmail);
    }
    
    /**
     * Send testimonial submission confirmation email to customer
     * @param string $name Customer name
     * @param string $email Customer email
     * @param int $rating Rating given
     * @param string $text Testimonial text
     * @return boolean Success status
     */
    public static function sendTestimonialConfirmation($name, $email, $rating, $text) {
        // Check if email notifications are enabled
        if (!get_site_setting('email_notifications_enabled', true) || 
            !get_site_setting('email_notifications_to_customer', true)) {
            return false;
        }
        
        // Validate email
        if (empty($email) || !self::validateEmail($email)) {
            self::logEmailError('invalid email', 'Testimonial Confirmation', 'Invalid email address: ' . ($email ?? 'empty'));
            return false;
        }
        
        $to = $email;
        $subject = "Thank You for Your Testimonial - Artify";
        
        // Start building the email content
        $message = "<html><body>";
        $message .= "<h2>Thank You for Your Testimonial!</h2>";
        $message .= "<p>Hello {$name},</p>";
        $message .= "<p>Thank you for taking the time to share your experience with Artify. We appreciate your feedback!</p>";
        
        $message .= "<h3>Your Testimonial:</h3>";
        $message .= "<p><strong>Rating:</strong> ";
        for ($i = 1; $i <= 5; $i++) {
            $message .= $i <= $rating ? "★" : "☆";
        }
        $message .= "</p>";
        
        $message .= "<p><strong>Your Comments:</strong><br>";
        $message .= nl2br(htmlspecialchars($text)) . "</p>";
        
        $message .= "<p>Your testimonial will be reviewed and published on our website soon.</p>";
        $message .= "<p>Thank you for your support!</p>";
        $message .= "<p>The Artify Team</p>";
        $message .= "</body></html>";
        
        // Send email using our helper method
        return self::sendEmail($to, $subject, $message, EMAIL_FROM, SITE_NAME, ADMIN_EMAIL);
    }
    
    /**
     * Send notification about new testimonial to admin
     * @param string $name Customer name
     * @param string $email Customer email
     * @param int $rating Rating given
     * @param string $text Testimonial text
     * @return boolean Success status
     */
    public static function sendTestimonialNotificationToAdmin($name, $email, $rating, $text) {
        // Check if email notifications are enabled
        if (!get_site_setting('email_notifications_enabled', true) || 
            !get_site_setting('email_notifications_to_admin', true)) {
            return false;
        }
        
        // Validate admin email from config
        if (empty(ADMIN_EMAIL) || !self::validateEmail(ADMIN_EMAIL)) {
            self::logEmailError('invalid admin email', 'Testimonial Notification', 'Invalid admin email address: ' . (ADMIN_EMAIL ?? 'empty'));
            return false;
        }
        
        $to = ADMIN_EMAIL;
        $subject = "New Testimonial Submission - Artify";
        
        // Start building the email content
        $message = "<html><body>";
        $message .= "<h2>New Testimonial Submission</h2>";
        $message .= "<p>A new testimonial has been submitted on the Artify website.</p>";
        
        $message .= "<h3>Testimonial Details:</h3>";
        $message .= "<p><strong>Name:</strong> {$name}<br>";
        $message .= "<strong>Email:</strong> {$email}<br>";
        $message .= "<strong>Rating:</strong> {$rating}/5<br>";
        $message .= "<strong>Date:</strong> " . date('F j, Y') . "</p>";
        
        $message .= "<h3>Testimonial Text:</h3>";
        $message .= "<p>" . nl2br(htmlspecialchars($text)) . "</p>";
        
        $message .= "<p>Please review this testimonial and approve it in the admin panel.</p>";
        $message .= "<p><a href='" . BASE_URL . "/admin/index.php?page=testimonials'>Go to Testimonials Management</a></p>";
        $message .= "</body></html>";
        
        // Send email using our helper method
        return self::sendEmail($to, $subject, $message, EMAIL_FROM, SITE_NAME, $email);
    }
    
    /**
     * Test email configuration by sending a test email
     * @param string $email Email address to send test to
     * @return array Status and message
     */
    public static function testEmailConfiguration($email) {
        // Force enable for testing purposes
        $wasEnabled = get_site_setting('email_notifications_enabled', true);
        update_site_setting('email_notifications_enabled', true);
        
        if (empty($email) || !self::validateEmail($email)) {
            // Restore setting
            update_site_setting('email_notifications_enabled', $wasEnabled);
            return ['success' => false, 'message' => 'Invalid email address'];
        }
        
        $subject = "Test Email - Artify " . date('Y-m-d H:i:s');
        $message = "<html><body>";
        $message .= "<h2>Test Email from Artify</h2>";
        $message .= "<p>This is a test email to verify that your email configuration is working correctly.</p>";
        $message .= "<p>If you received this email, your system can send emails successfully.</p>";
        $message .= "<p>Sent at: " . date('Y-m-d H:i:s') . "</p>";
        $message .= "</body></html>";
        
        // Try to send email using our helper method
        $success = self::sendEmail($email, $subject, $message, EMAIL_FROM, SITE_NAME, $email);
        
        // Restore setting
        update_site_setting('email_notifications_enabled', $wasEnabled);
        
        if ($success) {
            return ['success' => true, 'message' => 'Test email sent successfully to ' . $email];
        } else {
            self::logEmailError($email, $subject, 'Test email failed');
            return ['success' => false, 'message' => 'Failed to send test email. Check server configuration.'];
        }
    }
}
