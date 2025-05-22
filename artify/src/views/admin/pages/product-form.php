<?php
// Start output buffering to prevent "headers already sent" error
ob_start();

// Get product controller
$productController = new ProductController();

// Get product ID from URL if editing
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $productId > 0;

// Initialize product data
$product = [
    'id' => 0,
    'description' => '',
    'price' => '',
    'category' => '',
    'colour' => '',
    'size' => '',
    'imagePath' => '',
    'active' => true
];

// If editing, get product data
if ($isEdit) {
    $productObj = $productController->getProductById($productId);
    
    if (!$productObj) {
        Session::setFlash('error', 'Product not found');
        header('Location: ' . BASE_URL . '/admin/index.php?page=products');
        exit;
    }
    
    $product = [
        'id' => $productObj->ProductNo,
        'description' => $productObj->Description,
        'price' => $productObj->Price,
        'category' => $productObj->Category,
        'colour' => $productObj->Colour,
        'size' => $productObj->Size,
        'imagePath' => $productObj->ImagePath,
        'active' => $productObj->Active
    ];
}

// Get categories for dropdown
$categories = $productController->getCategories();
$categoryOptions = array_map(function($cat) {
    return $cat->Category;
}, $categories);

// Ensure unique categories and sort alphabetically
$categoryOptions = array_unique($categoryOptions);
sort($categoryOptions);

// Initialize errors array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate form data
    $validation = new Validation();
    
    // Validate required fields
    if ($validation->required($_POST['description'], 'Description')) {
        $product['description'] = Validation::sanitize($_POST['description']);
    } else {
        $errors['description'] = $validation->getError('Description');
    }
    
    if ($validation->required($_POST['price'], 'Price')) {
        if ($validation->numeric($_POST['price'], 'Price')) {
            $product['price'] = (float)$_POST['price'];
        } else {
            $errors['price'] = $validation->getError('Price');
        }
    } else {
        $errors['price'] = $validation->getError('Price');
    }
    
    if ($validation->required($_POST['category'], 'Category')) {
        $product['category'] = Validation::sanitize($_POST['category']);
    } else {
        $errors['category'] = $validation->getError('Category');
    }
    
    // Optional fields
    $product['colour'] = Validation::sanitize($_POST['colour'] ?? '');
    $product['size'] = Validation::sanitize($_POST['size'] ?? '');
    $product['active'] = isset($_POST['active']) ? true : false;
    
    // If editing, keep the existing image path unless a new image is uploaded
    if ($isEdit) {
        $product['imagePath'] = $productObj->ImagePath;
    }
    
    // Handle image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['name'] && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $targetDir = PUBLIC_DIR . '/uploads/products/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Ensure directory exists
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                $errors['image'] = 'Failed to create upload directory';
            }
        }
        
        if (empty($errors['image'])) {
            // Check if file is an actual image
            $check = @getimagesize($_FILES['image']['tmp_name']);
            if ($check !== false) {
                // Check file size (limit to 5MB)
                if ($_FILES['image']['size'] <= 5000000) {
                    // Allow certain file formats
                    if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                        // Upload file
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                            // Store the new image path
                            $product['imagePath'] = 'uploads/products/' . $fileName;
                            
                            // If editing and old image exists, delete it
                            if ($isEdit && !empty($productObj->ImagePath) && $productObj->ImagePath !== $product['imagePath']) {
                                $oldImagePath = PUBLIC_DIR . '/' . $productObj->ImagePath;
                                if (file_exists($oldImagePath)) {
                                    @unlink($oldImagePath);
                                }
                            }
                        } else {
                            $errors['image'] = 'Failed to upload image. Please check file permissions.';
                        }
                    } else {
                        $errors['image'] = 'Only JPG, JPEG, PNG & GIF files are allowed';
                    }
                } else {
                    $errors['image'] = 'Image file is too large (max 5MB)';
                }
            } else {
                $errors['image'] = 'File is not an image or could not be processed';
            }
        }
    }
    
    // If validation passed, save product
    if (empty($errors)) {
        if ($isEdit) {
            // Update existing product
            $result = $productController->updateProduct($product);
            
            if ($result) {
                Session::setFlash('success', 'Product updated successfully');
                header('Location: ' . BASE_URL . '/admin/index.php?page=products');
                exit;
            } else {
                Session::setFlash('error', 'Failed to update product');
            }
        } else {
            // Add new product
            $result = $productController->addProduct($product);
            
            if ($result) {
                Session::setFlash('success', 'Product added successfully');
                header('Location: ' . BASE_URL . '/admin/index.php?page=products');
                exit;
            } else {
                Session::setFlash('error', 'Failed to add product');
            }
        }
    }
}
?>

<div class="admin-form">
    <h4 class="form-title"><?php echo $isEdit ? 'Edit Product' : 'Add New Product'; ?></h4>
    
    <form action="<?php echo BASE_URL; ?>/admin/index.php?page=product-form<?php echo $isEdit ? '&id=' . $productId : ''; ?>" method="post" enctype="multipart/form-data">
        <!-- Product Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Product Name/Description*</label>
            <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <div class="invalid-feedback">
                    <?php echo $errors['description']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row">
            <!-- Price -->
            <div class="col-md-4 mb-3">
                <label for="price" class="form-label">Price*</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control <?php echo isset($errors['price']) ? 'is-invalid' : ''; ?>" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    <?php if (isset($errors['price'])): ?>
                        <div class="invalid-feedback">
                            <?php echo $errors['price']; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Category -->
            <div class="col-md-4 mb-3">
                <label for="category" class="form-label">Category*</label>
                <select class="form-select <?php echo isset($errors['category']) ? 'is-invalid' : ''; ?>" id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categoryOptions as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($product['category'] === $cat) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(ucfirst($cat)); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="new">+ Add New Category</option>
                </select>
                <?php if (isset($errors['category'])): ?>
                    <div class="invalid-feedback">
                        <?php echo $errors['category']; ?>
                    </div>
                <?php endif; ?>
                
                <!-- New Category (displayed when "Add New Category" is selected) -->
                <div id="newCategoryContainer" class="mt-2 d-none">
                    <input type="text" class="form-control" id="newCategory" placeholder="Enter new category">
                </div>
            </div>
            
            <!-- Status -->
            <div class="col-md-4 mb-3">
                <label class="form-label d-block">Status</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="active" name="active" <?php echo $product['active'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="active">Active</label>
                </div>
                <small class="text-muted">Inactive products won't be displayed in the shop</small>
            </div>
        </div>
        
        <div class="row">
            <!-- Colour -->
            <div class="col-md-6 mb-3">
                <label for="colour" class="form-label">Colour</label>
                <input type="text" class="form-control" id="colour" name="colour" value="<?php echo htmlspecialchars($product['colour']); ?>">
                <div class="form-text">Optional: Specify the colour of the product</div>
            </div>
            
            <!-- Size -->
            <div class="col-md-6 mb-3">
                <label for="size" class="form-label">Size</label>
                <input type="text" class="form-control" id="size" name="size" value="<?php echo htmlspecialchars($product['size']); ?>">
                <div class="form-text">Optional: Specify the size of the product (e.g., "30x40cm")</div>
            </div>
        </div>
        
        <!-- Product Image -->
        <div class="mb-4">
            <label for="image" class="form-label">Product Image</label>
            
            <?php if (!empty($product['imagePath'])): ?>
                <div class="mb-3">
                    <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($product['imagePath']); ?>" class="img-thumbnail" alt="Current product image" width="150">
                    <p class="text-muted small">Current image. Upload a new one to replace it.</p>
                </div>
            <?php endif; ?>
            
            <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image" accept="image/*">
            <?php if (isset($errors['image'])): ?>
                <div class="invalid-feedback">
                    <?php echo $errors['image']; ?>
                </div>
            <?php else: ?>
                <div class="form-text">
                    Accepted formats: JPG, JPEG, PNG, GIF. Max size: 5MB.<br>
                    <?php if (!$isEdit): ?>
                    <?php if (empty($product['imagePath'])): ?>
                    <span class="text-warning">Please upload a product image.</span>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=products" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i><?php echo $isEdit ? 'Update Product' : 'Add Product'; ?>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        const newCategoryContainer = document.getElementById('newCategoryContainer');
        const newCategoryInput = document.getElementById('newCategory');
        
        // Check if "Add New Category" is selected initially
        if (categorySelect.value === 'new') {
            newCategoryContainer.classList.remove('d-none');
        }
        
        // Show/hide new category input when "Add New Category" is selected
        categorySelect.addEventListener('change', function() {
            if (this.value === 'new') {
                newCategoryContainer.classList.remove('d-none');
                newCategoryInput.focus();
            } else {
                newCategoryContainer.classList.add('d-none');
            }
        });
        
        // Update category select with new category value before form submission
        document.querySelector('form').addEventListener('submit', function(event) {
            if (categorySelect.value === 'new') {
                const newCategoryValue = newCategoryInput.value.trim();
                
                if (newCategoryValue !== '') {
                    // Create a new option with the new category value
                    const newOption = document.createElement('option');
                    newOption.value = newCategoryValue;
                    newOption.textContent = newCategoryValue;
                    newOption.selected = true;
                    
                    // Insert the new option before the "Add New Category" option
                    categorySelect.insertBefore(newOption, categorySelect.options[categorySelect.options.length - 1]);
                } else {
                    // If new category is selected but no value is entered, prevent form submission
                    event.preventDefault();
                    alert('Please enter a category name or select an existing category');
                    categorySelect.classList.add('is-invalid');
                    newCategoryInput.focus();
                }
            }
        });
    });
</script>
<?php
// End output buffering
ob_end_flush();
?>
