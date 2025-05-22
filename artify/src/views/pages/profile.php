<?php
// Check if user is logged in
if (!Session::get('customer_logged_in')) {
    // Store the current page URL to redirect back after login
    Session::set('redirect_after_login', BASE_URL . '/public/index.php?page=profile');
    
    // Redirect to login page
    header('Location: ' . BASE_URL . '/public/index.php?page=login');
    exit;
}

// Get customer email from session
$customerEmail = Session::get('customer_email');

// Get customer model
$customerModel = new CustomerModel();

// Get customer data
$customer = $customerModel->getCustomerByEmail($customerEmail);

// Process form submission
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = isset($_POST['firstName']) ? Validation::sanitize($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? Validation::sanitize($_POST['lastName']) : '';
    $title = isset($_POST['title']) ? Validation::sanitize($_POST['title']) : '';
    $address = isset($_POST['address']) ? Validation::sanitize($_POST['address']) : '';
    $city = isset($_POST['city']) ? Validation::sanitize($_POST['city']) : '';
    $state = isset($_POST['state']) ? Validation::sanitize($_POST['state']) : '';
    $country = isset($_POST['country']) ? Validation::sanitize($_POST['country']) : '';
    $postCode = isset($_POST['postCode']) ? Validation::sanitize($_POST['postCode']) : '';
    $phone = isset($_POST['phone']) ? Validation::sanitize($_POST['phone']) : '';
    
    // Validate form data
    if (empty($firstName)) {
        $errors[] = 'First name is required';
    }
    
    if (empty($lastName)) {
        $errors[] = 'Last name is required';
    }
    
    // If no validation errors, update profile
    if (empty($errors)) {
        $data = [
            'email' => $customerEmail,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'title' => $title,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'postCode' => $postCode,
            'phone' => $phone
        ];
        
        if ($customerModel->addOrUpdateCustomer($data)) {
            $success = true;
            
            // Update session name
            Session::set('customer_name', $firstName . ' ' . $lastName);
            
            // Refresh customer data
            $customer = $customerModel->getCustomerByEmail($customerEmail);
        } else {
            $errors[] = 'Failed to update profile. Please try again.';
        }
    }
}
?>

<!-- Profile Section -->
<section class="py-5">
    <div class="container">
        <h1 class="mb-4">My Profile</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success mb-4">
                Your profile has been updated successfully.
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Edit Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo BASE_URL; ?>/public/index.php?page=profile">
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="title" class="form-label">Title</label>
                                    <select class="form-select" id="title" name="title">
                                        <option value="" <?php echo empty($customer->Title) ? 'selected' : ''; ?>>--</option>
                                        <option value="Mr." <?php echo ($customer->Title === 'Mr.') ? 'selected' : ''; ?>>Mr.</option>
                                        <option value="Mrs." <?php echo ($customer->Title === 'Mrs.') ? 'selected' : ''; ?>>Mrs.</option>
                                        <option value="Ms." <?php echo ($customer->Title === 'Ms.') ? 'selected' : ''; ?>>Ms.</option>
                                        <option value="Dr." <?php echo ($customer->Title === 'Dr.') ? 'selected' : ''; ?>>Dr.</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label for="firstName" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required value="<?php echo htmlspecialchars($customer->CustFName); ?>">
                                </div>
                                <div class="col-md-5">
                                    <label for="lastName" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required value="<?php echo htmlspecialchars($customer->CustLName); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($customer->CustEmail); ?>" disabled>
                                <div class="form-text">Email address cannot be changed.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer->Phone); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($customer->Address); ?>">
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($customer->City); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="state" class="form-label">State/Province</label>
                                    <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($customer->State); ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($customer->Country); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="postCode" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postCode" name="postCode" value="<?php echo htmlspecialchars($customer->PostCode); ?>">
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer->CustEmail); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer->CustFName . ' ' . $customer->CustLName); ?></p>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="<?php echo BASE_URL; ?>/public/index.php?page=change-password" class="btn btn-outline-primary">
                                Change Password
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order History</h5>
                    </div>
                    <div class="card-body">
                        <p>View your past orders and track current orders.</p>
                        <div class="d-grid gap-2">
                            <a href="<?php echo BASE_URL; ?>/public/index.php?page=orders" class="btn btn-outline-primary">
                                View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
