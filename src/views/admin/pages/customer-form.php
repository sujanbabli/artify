<?php
// Start output buffering to prevent "headers already sent" error
ob_start();

// Get customer model
$customerModel = new CustomerModel();

// Get customer email from URL if editing
$email = isset($_GET['email']) ? $_GET['email'] : '';
$isEdit = !empty($email);

// Initialize customer data
$customer = [
    'email' => '',
    'firstName' => '',
    'lastName' => '',
    'title' => '',
    'address' => '',
    'city' => '',
    'state' => '',
    'country' => '',
    'postCode' => '',
    'phone' => '',
    'password' => ''
];

// If editing, get customer data
if ($isEdit) {
    $customerObj = $customerModel->getCustomerByEmail($email);
    
    if (!$customerObj) {
        Session::setFlash('error', 'Customer not found');
        header('Location: ' . BASE_URL . '/admin/index.php?page=customers');
        exit;
    }
    
    $customer = [
        'email' => $customerObj->CustEmail,
        'firstName' => $customerObj->CustFName,
        'lastName' => $customerObj->CustLName,
        'title' => $customerObj->Title,
        'address' => $customerObj->Address,
        'city' => $customerObj->City,
        'state' => $customerObj->State,
        'country' => $customerObj->Country,
        'postCode' => $customerObj->PostCode,
        'phone' => $customerObj->Phone,
        'password' => '' // We don't show the password for security reasons
    ];
}

// Initialize errors array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate form data
    $validation = new Validation();
    
    // Required fields
    if ($validation->required($_POST['firstName'], 'First Name')) {
        $customer['firstName'] = Validation::sanitize($_POST['firstName']);
    } else {
        $errors['firstName'] = $validation->getError('First Name');
    }
    
    if ($validation->required($_POST['lastName'], 'Last Name')) {
        $customer['lastName'] = Validation::sanitize($_POST['lastName']);
    } else {
        $errors['lastName'] = $validation->getError('Last Name');
    }
    
    // Email validation
    if ($validation->required($_POST['email'], 'Email') && $validation->email($_POST['email'])) {
        $customer['email'] = Validation::sanitize($_POST['email']);
        
        // Check if email exists and is not the current customer being edited
        if (!$isEdit && $customerModel->getCustomerByEmail($customer['email'])) {
            $errors['email'] = 'Email already exists';
        } elseif ($isEdit && $customer['email'] !== $email && $customerModel->getCustomerByEmail($customer['email'])) {
            $errors['email'] = 'Email already exists';
        }
    } else {
        $errors['email'] = $validation->getError('Email');
    }
    
    // Optional fields
    $customer['title'] = Validation::sanitize($_POST['title'] ?? '');
    $customer['address'] = Validation::sanitize($_POST['address'] ?? '');
    $customer['city'] = Validation::sanitize($_POST['city'] ?? '');
    $customer['state'] = Validation::sanitize($_POST['state'] ?? '');
    $customer['country'] = Validation::sanitize($_POST['country'] ?? '');
    $customer['postCode'] = Validation::sanitize($_POST['postCode'] ?? '');
    $customer['phone'] = Validation::sanitize($_POST['phone'] ?? '');
    
    // Password handling
    if (!$isEdit || !empty($_POST['password'])) {
        if (!empty($_POST['password'])) {
            if (strlen($_POST['password']) < 6) {
                $errors['password'] = 'Password must be at least 6 characters long';
            } else {
                $customer['password'] = $_POST['password'];
            }
        } elseif (!$isEdit) {
            $errors['password'] = 'Password is required for new customers';
        }
    }
    
    // If validation passed, save customer
    if (empty($errors)) {
        if ($isEdit) {
            // Update customer
            $result = $customerModel->updateCustomer($email, $customer);
            
            if ($result) {
                Session::setFlash('success', 'Customer updated successfully');
                header('Location: ' . BASE_URL . '/admin/index.php?page=customers');
                exit;
            } else {
                Session::setFlash('error', 'Failed to update customer');
            }
        } else {
            // Add new customer
            $result = $customerModel->registerCustomer(
                $customer['email'],
                $customer['password'],
                $customer['firstName'],
                $customer['lastName']
            );
            
            if ($result) {
                Session::setFlash('success', 'Customer added successfully');
                header('Location: ' . BASE_URL . '/admin/index.php?page=customers');
                exit;
            } else {
                Session::setFlash('error', 'Failed to add customer');
            }
        }
    }
}
?>

<div class="admin-form">
    <h4 class="form-title"><?php echo $isEdit ? 'Edit Customer' : 'Add New Customer'; ?></h4>
    
    <form action="<?php echo BASE_URL; ?>/admin/index.php?page=customer-form<?php echo $isEdit ? '&email=' . urlencode($email) : ''; ?>" method="post" class="needs-validation" novalidate>
        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Title -->
                    <div class="col-md-2 mb-3">
                        <label for="title" class="form-label">Title</label>
                        <select class="form-select" id="title" name="title">
                            <option value="">Select...</option>
                            <option value="Mr" <?php echo ($customer['title'] === 'Mr') ? 'selected' : ''; ?>>Mr</option>
                            <option value="Mrs" <?php echo ($customer['title'] === 'Mrs') ? 'selected' : ''; ?>>Mrs</option>
                            <option value="Ms" <?php echo ($customer['title'] === 'Ms') ? 'selected' : ''; ?>>Ms</option>
                            <option value="Dr" <?php echo ($customer['title'] === 'Dr') ? 'selected' : ''; ?>>Dr</option>
                        </select>
                    </div>
                    
                    <!-- First Name -->
                    <div class="col-md-5 mb-3">
                        <label for="firstName" class="form-label">First Name*</label>
                        <input type="text" class="form-control <?php echo isset($errors['firstName']) ? 'is-invalid' : ''; ?>" 
                               id="firstName" name="firstName" value="<?php echo htmlspecialchars($customer['firstName']); ?>" required>
                        <?php if (isset($errors['firstName'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['firstName']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Last Name -->
                    <div class="col-md-5 mb-3">
                        <label for="lastName" class="form-label">Last Name*</label>
                        <input type="text" class="form-control <?php echo isset($errors['lastName']) ? 'is-invalid' : ''; ?>" 
                               id="lastName" name="lastName" value="<?php echo htmlspecialchars($customer['lastName']); ?>" required>
                        <?php if (isset($errors['lastName'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['lastName']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email*</label>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" <?php echo $isEdit ? 'readonly' : ''; ?> required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['email']; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($isEdit): ?>
                            <div class="form-text">Email cannot be changed as it is used as the primary identifier.</div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Phone -->
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($customer['phone']); ?>">
                    </div>
                    
                    <!-- Password -->
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label"><?php echo $isEdit ? 'New Password' : 'Password*'; ?></label>
                        <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                               id="password" name="password" <?php echo $isEdit ? '' : 'required'; ?>>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['password']; ?>
                            </div>
                        <?php else: ?>
                            <div class="form-text"><?php echo $isEdit ? 'Leave blank to keep current password' : 'Minimum 6 characters'; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Address Information -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Address Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Address -->
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?php echo htmlspecialchars($customer['address']); ?>">
                    </div>
                    
                    <!-- City -->
                    <div class="col-md-4 mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" 
                               value="<?php echo htmlspecialchars($customer['city']); ?>">
                    </div>
                    
                    <!-- State/Province -->
                    <div class="col-md-4 mb-3">
                        <label for="state" class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="state" name="state" 
                               value="<?php echo htmlspecialchars($customer['state']); ?>">
                    </div>
                    
                    <!-- Post Code -->
                    <div class="col-md-4 mb-3">
                        <label for="postCode" class="form-label">Post/Zip Code</label>
                        <input type="text" class="form-control" id="postCode" name="postCode" 
                               value="<?php echo htmlspecialchars($customer['postCode']); ?>">
                    </div>
                    
                    <!-- Country -->
                    <div class="col-md-12 mb-3">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" class="form-control" id="country" name="country" 
                               value="<?php echo htmlspecialchars($customer['country']); ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=customers" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Customers
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i><?php echo $isEdit ? 'Update Customer' : 'Add Customer'; ?>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bootstrap form validation
        const forms = document.querySelectorAll('.needs-validation');
        
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>

<?php
// End output buffering
ob_end_flush();
?>
