<?php
/**
 * Product Model
 * 
 * Handles product related database operations
 */
class ProductModel extends Model {
    /**
     * Get all active products
     * @return array Products
     */
    public function getAllProducts() {
        $this->db->query("SELECT * FROM Product WHERE Active = 1 ORDER BY ProductNo DESC");
        return $this->db->resultSet();
    }
    
    /**
     * Get products by category
     * @param string $category Category name
     * @return array Products
     */
    public function getProductsByCategory($category) {
        $this->db->query("SELECT * FROM Product WHERE Category = :category AND Active = 1 ORDER BY ProductNo DESC");
        $this->db->bind(':category', $category);
        return $this->db->resultSet();
    }
    
    /**
     * Get product by ID
     * @param int $id Product ID
     * @return object Product or false if not found
     */
    public function getProductById($id) {
        $this->db->query("SELECT * FROM Product WHERE ProductNo = :id AND Active = 1");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Get all product categories
     * @return array Categories
     */
    public function getCategories() {
        $this->db->query("SELECT DISTINCT Category FROM Product WHERE Active = 1 ORDER BY Category");
        return $this->db->resultSet();
    }
    
    /**
     * Add new product
     * @param array $data Product data
     * @return boolean Success status
     */
    public function addProduct($data) {
        $this->db->query("INSERT INTO Product (Description, Price, Category, Colour, Size, ImagePath, Active) 
                          VALUES (:description, :price, :category, :colour, :size, :imagePath, :active)");
        
        // Bind values
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':colour', $data['colour'] ?? null);
        $this->db->bind(':size', $data['size'] ?? null);
        $this->db->bind(':imagePath', $data['imagePath'] ?? null);
        $this->db->bind(':active', $data['active'] ? 1 : 0);
        
        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Update product
     * @param array $data Product data
     * @return boolean Success status
     */
    public function updateProduct($data) {
        $this->db->query("UPDATE Product SET 
                          Description = :description, 
                          Price = :price, 
                          Category = :category, 
                          Colour = :colour, 
                          Size = :size, 
                          ImagePath = :imagePath,
                          Active = :active 
                          WHERE ProductNo = :id");
        
        // Bind values
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':colour', $data['colour'] ?? null);
        $this->db->bind(':size', $data['size'] ?? null);
        $this->db->bind(':imagePath', $data['imagePath'] ?? null);
        $this->db->bind(':active', $data['active'] ? 1 : 0);
        $this->db->bind(':id', $data['id']);
        
        // Execute
        return $this->db->execute();
    }
    
    /**
     * Delete product (set as inactive)
     * @param int $id Product ID
     * @return boolean Success status
     */
    public function deleteProduct($id) {
        $this->db->query("UPDATE Product SET Active = 0 WHERE ProductNo = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Get featured artists
     * @param int $limit Optional limit of artists to return
     * @return array Artists
     */
    public function getFeaturedArtists($limit = 3) {
        // In a real application, this would query an Artists table
        // For now, we'll return an empty array since the template has fallback content
        return [];
        
        /* Example of how this might look with a real Artists table:
        $this->db->query("SELECT * FROM Artists WHERE Featured = 1 AND Active = 1 ORDER BY RAND() LIMIT :limit");
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
        */
    }
    
    /**
     * Search products with filters
     * 
     * @param array $filters Associative array of filters (search, category, min_price, max_price, sort)
     * @return array Products matching the search criteria
     */
    public function searchProducts($filters = []) {
        $sql = "SELECT * FROM Product WHERE Active = 1";
        $params = [];
        
        // Apply search term filter
        if (!empty($filters['search'])) {
            $sql .= " AND (Description LIKE :search OR Category LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Apply category filter
        if (!empty($filters['category'])) {
            $sql .= " AND Category = :category";
            $params[':category'] = $filters['category'];
        }
        
        // Apply minimum price filter
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $sql .= " AND Price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        
        // Apply maximum price filter
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $sql .= " AND Price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        
        // Apply sorting
        $sort = isset($filters['sort']) ? $filters['sort'] : 'newest';
        switch ($sort) {
            case 'price_low_high':
                $sql .= " ORDER BY Price ASC";
                break;
            case 'price_high_low':
                $sql .= " ORDER BY Price DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY Description ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY Description DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY ProductNo DESC";
                break;
        }
        
        $this->db->query($sql);
        
        // Bind all parameters
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Get min and max prices of products
     * 
     * @return array Associative array with min_price and max_price keys
     */
    public function getPriceRange() {
        $this->db->query("SELECT MIN(Price) as min_price, MAX(Price) as max_price FROM Product WHERE Active = 1");
        $result = $this->db->single();
        
        // Convert stdClass to array to ensure consistent return type
        return [
            'min_price' => $result->min_price,
            'max_price' => $result->max_price
        ];
    }
}
