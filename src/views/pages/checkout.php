<?php
// Check if cart is empty
$cartController = new CartController();
$cartItems = $cartController->getCartItems();
$cartSummary = $cartController->getCartSummary();

if (empty($cartItems)) {
    Session::setFlash('info', 'Your cart is empty. Add some items before checkout.');
    header('Location: ' . BASE_URL . '/public/index.php?page=shop');
    exit;
}

// Check if user is logged in
if (!Session::get('customer_logged_in')) {
    // Store the current page as the redirect destination after login
    Session::set('redirect_after_login', BASE_URL . '/public/index.php?page=checkout');
    
    // Redirect to login page with a message
    Session::setFlash('info', 'Please login or create an account to complete your purchase.');
    header('Location: ' . BASE_URL . '/public/index.php?page=login');
    exit;
}

// Initialize variables
$customer = [
    'title' => '',
    'firstName' => '',
    'lastName' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'state' => '',
    'postCode' => '',
    'country' => ''
];

// If customer is logged in, get their details
if (Session::get('customer_logged_in')) {
    $customerModel = new CustomerModel();
    $customerData = $customerModel->getCustomerByEmail(Session::get('customer_email'));
    
    if ($customerData) {
        $customer['title'] = $customerData->Title;
        $customer['firstName'] = $customerData->CustFName;
        $customer['lastName'] = $customerData->CustLName;
        $customer['email'] = $customerData->CustEmail;
        $customer['phone'] = $customerData->Phone;
        $customer['address'] = $customerData->Address;
        $customer['city'] = $customerData->City;
        $customer['state'] = $customerData->State;
        $customer['postCode'] = $customerData->PostCode;
        $customer['country'] = $customerData->Country;
    }
}

// Process checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $errors = [];
    
    // Basic validation - Check required fields
    $requiredFields = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'postCode', 'country'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = ucfirst(str_replace('postCode', 'postal code', $field)) . ' is required.';
        }
    }
    
    // Email validation
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    
    // If no errors, process the order
    if (empty($errors)) {
        try {
            // Get customer email
            $customerEmail = Session::get('customer_email');
            
            // Create a purchase model
            $purchaseModel = new PurchaseModel();
            
            // Create shipping address string
            $shippingAddress = $customer['title'] . ' ' . $customer['firstName'] . ' ' . $customer['lastName'] . "\n" .
                              $customer['address'] . "\n" .
                              $customer['city'] . ', ' . $customer['state'] . ' ' . $customer['postCode'] . "\n" .
                              $customer['country'] . "\n" .
                              'Phone: ' . $customer['phone'];
            
            // Create purchase record
            $purchaseId = $purchaseModel->createPurchase($customerEmail);
            
            if ($purchaseId) {
                // Update purchase with shipping info and total
                $purchaseModel->updatePurchase([
                    'id' => $purchaseId,
                    'totalAmount' => $cartSummary['total_price'],
                    'shippingAddress' => $shippingAddress
                ]);
                
                // Add purchase items
                foreach ($cartItems as $item) {
                    $purchaseModel->addPurchaseItem(
                        $purchaseId, 
                        $item['product']->ProductNo, 
                        $item['quantity'],
                        $item['product']->Price
                    );
                }
                
                // Get purchase details for email
                $purchase = $purchaseModel->getPurchaseById($purchaseId);
                $purchaseItems = $purchaseModel->getPurchaseItems($purchaseId);
                $customerData = $customerModel->getCustomerByEmail($customerEmail);
                
                // Send confirmation email to customer
                require_once(ROOT_DIR . '/src/utils/EmailHelper.php');
                $customerEmailSent = EmailHelper::sendOrderConfirmation($customerData, $purchase, $purchaseItems);
                
                // Also notify admin about the new order
                $adminEmailSent = EmailHelper::sendOrderNotificationToAdmin($customerData, $purchase, $purchaseItems);
                
                // Store email status in session
                Session::set('customer_email_sent', $customerEmailSent);
                Session::set('admin_email_sent', $adminEmailSent);
                
                // Store the purchase ID in session for order confirmation
                Session::set('last_purchase_id', $purchaseId);
                
                // Clear cart after successful checkout
                $cartController->clearCart();
                
                // Redirect to confirmation page
                header('Location: ' . BASE_URL . '/public/index.php?page=order-confirmation');
                exit;
            } else {
                throw new Exception('Failed to create purchase record');
            }
        } catch (Exception $e) {
            Session::setFlash('error', 'There was a problem processing your order. Please try again. Error: ' . $e->getMessage());
        }
    } else {
        // Keep the submitted values
        foreach ($_POST as $key => $value) {
            if (isset($customer[$key])) {
                $customer[$key] = Validation::sanitize($value);
            }
        }
    }
}
?>

<!-- Checkout Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="mb-0">Checkout</h1>
            <div class="checkout-steps">
                <span class="badge bg-primary py-2 px-3"><i class="fas fa-user me-2"></i>Account</span>
                <span class="mx-2"><i class="fas fa-chevron-right"></i></span>
                <span class="badge bg-secondary py-2 px-3"><i class="fas fa-truck me-2"></i>Shipping</span>
                <span class="mx-2"><i class="fas fa-chevron-right"></i></span>
                <span class="badge bg-secondary py-2 px-3"><i class="fas fa-check-circle me-2"></i>Confirmation</span>
            </div>
        </div>
        
        <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-danger">
                <?php echo Session::getFlash('error')['message']; ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <div class="checkout-form">
                    <h4 class="mb-4">Shipping Information</h4>
                    
                    <form action="<?php echo BASE_URL; ?>/public/index.php?page=checkout" method="post" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <!-- Title -->
                            <div class="col-md-2">
                                <label for="title" class="form-label">Title</label>
                                <select class="form-select" id="title" name="title">
                                    <option value="" <?php echo ($customer['title'] === '') ? 'selected' : ''; ?>>None</option>
                                    <option value="Mr" <?php echo ($customer['title'] === 'Mr') ? 'selected' : ''; ?>>Mr</option>
                                    <option value="Mrs" <?php echo ($customer['title'] === 'Mrs') ? 'selected' : ''; ?>>Mrs</option>
                                    <option value="Ms" <?php echo ($customer['title'] === 'Ms') ? 'selected' : ''; ?>>Ms</option>
                                    <option value="Dr" <?php echo ($customer['title'] === 'Dr') ? 'selected' : ''; ?>>Dr</option>
                                </select>
                            </div>
                            
                            <!-- First Name -->
                            <div class="col-md-5">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control <?php echo isset($errors['firstName']) ? 'is-invalid' : ''; ?>" id="firstName" name="firstName" value="<?php echo $customer['firstName']; ?>" required>
                                <?php if (isset($errors['firstName'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['firstName']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Last Name -->
                            <div class="col-md-5">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control <?php echo isset($errors['lastName']) ? 'is-invalid' : ''; ?>" id="lastName" name="lastName" value="<?php echo $customer['lastName']; ?>" required>
                                <?php if (isset($errors['lastName'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['lastName']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo $customer['email']; ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?php echo $customer['phone']; ?>" required>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['phone']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Address -->
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>" id="address" name="address" value="<?php echo $customer['address']; ?>" required>
                                <?php if (isset($errors['address'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['address']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- City -->
                            <div class="col-md-5">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control <?php echo isset($errors['city']) ? 'is-invalid' : ''; ?>" id="city" name="city" value="<?php echo $customer['city']; ?>" required>
                                <?php if (isset($errors['city'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['city']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- State -->
                            <div class="col-md-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control <?php echo isset($errors['state']) ? 'is-invalid' : ''; ?>" id="state" name="state" value="<?php echo $customer['state']; ?>" required>
                                <?php if (isset($errors['state'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['state']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Post Code -->
                            <div class="col-md-4">
                                <label for="postCode" class="form-label">Post Code</label>
                                <input type="text" class="form-control <?php echo isset($errors['postCode']) ? 'is-invalid' : ''; ?>" id="postCode" name="postCode" value="<?php echo $customer['postCode']; ?>" required>
                                <?php if (isset($errors['postCode'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['postCode']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Country -->
                            <div class="col-md-6">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control <?php echo isset($errors['country']) ? 'is-invalid' : ''; ?>" id="country" name="country" value="<?php echo !empty($customer['country']) ? $customer['country'] : 'Australia'; ?>" required>
                                <?php if (isset($errors['country'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['country']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="card mb-4 border-primary">
                            <div class="card-body bg-primary bg-opacity-10">
                                <h5>Order Total: $<?php echo number_format($cartSummary['total_price'], 2); ?></h5>
                                <p class="mb-0 small">By placing your order, you agree to Artify's terms and conditions.</p>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary btn-lg w-100" type="submit">
                            <i class="fas fa-check-circle me-2"></i>Complete Order
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <span><?php echo htmlspecialchars($item['product']->Description); ?></span>
                                        <span class="text-muted">x<?php echo $item['quantity']; ?></span>
                                    </div>
                                    <span>$<?php echo number_format($item['subtotal'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($cartSummary['total_price'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-0">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold text-primary">$<?php echo number_format($cartSummary['total_price'], 2); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="<?php echo BASE_URL; ?>/public/index.php?page=cart" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
