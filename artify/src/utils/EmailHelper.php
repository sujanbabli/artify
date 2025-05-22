<?php
/**
 * Email Helper Class
 * 
 * Handles sending emails to customers and admin
 */
class EmailHelper {
    /**
     * Send order confirmation email to customer
     * @param object $customer Customer details
     * @param object $purchase Purchase details
     * @param array $items Purchase items
     * @return boolean Success status
     */
    public static function sendOrderConfirmation($customer, $purchase, $items) {
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
        
        // Set email headers
        $headers = "From: " . EMAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Send email
        return mail($to, $subject, $message, $headers);
    }
    
    /**
     * Send order notification email to admin
     * @param object $customer Customer details
     * @param object $purchase Purchase details
     * @param array $items Purchase items
     * @return boolean Success status
     */
    public static function sendOrderNotificationToAdmin($customer, $purchase, $items) {
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
        
        // Set email headers
        $headers = "From: " . EMAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . $customer->CustEmail . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Send email
        return mail($to, $subject, $message, $headers);
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
        
        // Set email headers
        $headers = "From: " . EMAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Send email
        return mail($to, $subject, $message, $headers);
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
        
        // Set email headers
        $headers = "From: " . EMAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Send email
        return mail($to, $subject, $message, $headers);
    }
}
