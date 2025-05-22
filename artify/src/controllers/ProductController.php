<?php
/**
 * Product Controller
 * 
 * Handles product related operations
 */
class ProductController {
    private $productModel;
    
    /**
     * Constructor - Initialize model
     */
    public function __construct() {
        $this->productModel = new ProductModel();
    }
    
    /**
     * Get all products
     * @return array Products
     */
    public function getAllProducts() {
        return $this->productModel->getAllProducts();
    }
    
    /**
     * Get products by category
     * @param string $category Category name
     * @return array Products
     */
    public function getProductsByCategory($category) {
        return $this->productModel->getProductsByCategory($category);
    }
    
    /**
     * Get product by ID
     * @param int $id Product ID
     * @return object Product or false if not found
     */
    public function getProductById($id) {
        return $this->productModel->getProductById($id);
    }
    
    /**
     * Get all product categories
     * @return array Categories
     */
    public function getCategories() {
        return $this->productModel->getCategories();
    }
    
    /**
     * Add new product
     * @param array $data Product data
     * @return boolean Success status
     */
    public function addProduct($data) {
        // Make sure we have all required fields
        if (empty($data['description']) || empty($data['price']) || empty($data['category'])) {
            return false;
        }

        // Add product
        return $this->productModel->addProduct($data);
    }
    
    /**
     * Update product
     * @param array $data Product data
     * @return boolean Success status
     */
    public function updateProduct($data) {
        // Make sure we have all required fields
        if (empty($data['id']) || empty($data['description']) || empty($data['price']) || empty($data['category'])) {
            return false;
        }

        // Update product
        return $this->productModel->updateProduct($data);
    }
    
    /**
     * Delete product
     * @param int $id Product ID
     * @return boolean Success status
     */
    public function deleteProduct($id) {
        return $this->productModel->deleteProduct($id);
    }
    
    /**
     * Search products with filters
     * 
     * @param array $filters Associative array of filters (search, category, min_price, max_price)
     * @return array Products matching the search criteria
     */
    public function searchProducts($filters = []) {
        return $this->productModel->searchProducts($filters);
    }
    
    /**
     * Get min and max prices of products
     * 
     * @return array Associative array with min_price and max_price keys
     */
    public function getPriceRange() {
        return $this->productModel->getPriceRange();
    }
    
    /**
     * Handle image upload
     * @param array $file File data from $_FILES
     * @return string|boolean Image path or false on failure
     */
    private function handleImageUpload($file) {
        $targetDir = PUBLIC_DIR . '/images/products/';
        $fileName = time() . '_' . basename($file['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if file exists and is valid before attempting to get image size
        if (!isset($file['tmp_name']) || empty($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            return false;
        }
        
        // Check if image file is a actual image
        $check = @getimagesize($file['tmp_name']);
        if ($check === false) {
            return false;
        }
        
        // Check file size (limit to 5MB)
        if ($file['size'] > 5000000) {
            return false;
        }
        
        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            return false;
        }
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return 'images/products/' . $fileName;
        } else {
            return false;
        }
    }
}
