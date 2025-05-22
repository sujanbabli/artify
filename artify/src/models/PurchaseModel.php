<?php
/**
 * Purchase Model
 * 
 * Handles purchase related database operations
 */
class PurchaseModel extends Model {
    /**
     * Create new purchase
     * @param string $email Customer email
     * @return int|boolean Purchase ID or false on failure
     */
    public function createPurchase($email) {
        $this->db->query("INSERT INTO Purchase (CustEmail) VALUES (:email)");
        $this->db->bind(':email', $email);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Add purchase item
     * @param int $purchaseId Purchase ID
     * @param int $productId Product ID
     * @param int $quantity Quantity
     * @return boolean Success status
     */
    public function addPurchaseItem($purchaseId, $productId, $quantity) {
        $this->db->query("INSERT INTO PurchaseItem (PurchaseNo, ProductNo, Quantity) 
                          VALUES (:purchaseId, :productId, :quantity)");
        
        $this->db->bind(':purchaseId', $purchaseId);
        $this->db->bind(':productId', $productId);
        $this->db->bind(':quantity', $quantity);
        
        return $this->db->execute();
    }
    
    /**
     * Get purchase by ID
     * @param int $purchaseId Purchase ID
     * @return object Purchase or false if not found
     */
    public function getPurchaseById($purchaseId) {
        $this->db->query("SELECT * FROM Purchase WHERE PurchaseNo = :purchaseId");
        $this->db->bind(':purchaseId', $purchaseId);
        return $this->db->single();
    }
    
    /**
     * Get purchase items with product details
     * @param int $purchaseId Purchase ID
     * @return array Purchase items
     */
    public function getPurchaseItems($purchaseId) {
        $this->db->query("SELECT pi.*, p.Description, p.Price, p.Category, p.Colour, p.Size, p.ImagePath 
                          FROM PurchaseItem pi
                          JOIN Product p ON pi.ProductNo = p.ProductNo
                          WHERE pi.PurchaseNo = :purchaseId");
        
        $this->db->bind(':purchaseId', $purchaseId);
        return $this->db->resultSet();
    }
    
    /**
     * Get all purchases
     * @return array Purchases
     */
    public function getAllPurchases() {
        $this->db->query("SELECT p.*, c.CustFName, c.CustLName 
                          FROM Purchase p
                          JOIN Customer c ON p.CustEmail = c.CustEmail
                          ORDER BY p.Date DESC");
        
        return $this->db->resultSet();
    }
    
    /**
     * Get purchases by customer email
     * @param string $email Customer email
     * @return array Purchases
     */
    public function getPurchasesByCustomer($email) {
        $this->db->query("SELECT * FROM Purchase WHERE CustEmail = :email ORDER BY Date DESC");
        $this->db->bind(':email', $email);
        return $this->db->resultSet();
    }
    
    /**
     * Update purchase status
     * @param int $purchaseId Purchase ID
     * @param string $status New status
     * @return boolean Success status
     */
    public function updatePurchaseStatus($purchaseId, $status) {
        $this->db->query("UPDATE Purchase SET Status = :status WHERE PurchaseNo = :purchaseId");
        $this->db->bind(':status', $status);
        $this->db->bind(':purchaseId', $purchaseId);
        return $this->db->execute();
    }

    /**
     * Update order status (alias for updatePurchaseStatus)
     * @param int $orderId Order ID
     * @param string $status New status
     * @return boolean Success status
     */
    public function updateOrderStatus($orderId, $status) {
        return $this->updatePurchaseStatus($orderId, $status);
    }
    
    /**
     * Get purchase by ID with items
     * @param int $purchaseId Purchase ID
     * @return object|boolean Purchase with items or false if not found
     */
    public function getPurchaseByIdWithItems($purchaseId) {
        // Get the purchase
        $purchase = $this->getPurchaseById($purchaseId);
        
        if (!$purchase) {
            return false;
        }
        
        // Get the purchase items
        $purchase->items = $this->getPurchaseItems($purchaseId);
        
        // Get customer details
        $this->db->query("SELECT * FROM Customer WHERE CustEmail = :email");
        $this->db->bind(':email', $purchase->CustEmail);
        $purchase->customer = $this->db->single();
        
        return $purchase;
    }
    
    /**
     * Create new order
     * @param int $customerId Customer ID
     * @param float $totalAmount Total order amount
     * @param array $shippingDetails Shipping details
     * @return int|boolean Order ID or false on failure
     */
    public function createOrder($customerId, $totalAmount, $shippingDetails) {
        // Create order
        $this->db->query("INSERT INTO Orders (CustID, OrderDate, TotalAmount, Status) 
                         VALUES (:customerId, NOW(), :totalAmount, 'Pending')");
        
        $this->db->bind(':customerId', $customerId);
        $this->db->bind(':totalAmount', $totalAmount);
        
        if (!$this->db->execute()) {
            return false;
        }
        
        // Get order ID
        $orderId = $this->db->lastInsertId();
        
        // Create shipping record
        $this->db->query("INSERT INTO Shipping (OrderID, FirstName, LastName, Email, Phone, Address, City, State, PostCode, Country) 
                         VALUES (:orderId, :firstName, :lastName, :email, :phone, :address, :city, :state, :postCode, :country)");
        
        $this->db->bind(':orderId', $orderId);
        $this->db->bind(':firstName', $shippingDetails['firstName']);
        $this->db->bind(':lastName', $shippingDetails['lastName']);
        $this->db->bind(':email', $shippingDetails['email']);
        $this->db->bind(':phone', $shippingDetails['phone']);
        $this->db->bind(':address', $shippingDetails['address']);
        $this->db->bind(':city', $shippingDetails['city']);
        $this->db->bind(':state', $shippingDetails['state'] ?? '');
        $this->db->bind(':postCode', $shippingDetails['postCode']);
        $this->db->bind(':country', $shippingDetails['country']);
        
        if (!$this->db->execute()) {
            return false;
        }
        
        return $orderId;
    }
    
    /**
     * Add order item
     * @param int $orderId Order ID
     * @param int $productId Product ID
     * @param int $quantity Quantity
     * @param float $price Price per item
     * @return boolean Success status
     */
    public function addOrderItem($orderId, $productId, $quantity, $price) {
        $this->db->query("INSERT INTO OrderItems (OrderID, ProductID, Quantity, Price) 
                         VALUES (:orderId, :productId, :quantity, :price)");
        
        $this->db->bind(':orderId', $orderId);
        $this->db->bind(':productId', $productId);
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':price', $price);
        
        return $this->db->execute();
    }
    
    /**
     * Get order by ID
     * @param int $orderId Order ID
     * @return object Order or false if not found
     */
    public function getOrderById($orderId) {
        $this->db->query("SELECT o.*, c.CustEmail, c.CustFName, c.CustLName
                         FROM Orders o 
                         JOIN Customer c ON o.CustID = c.CustID
                         WHERE o.OrderID = :orderId");
        $this->db->bind(':orderId', $orderId);
        return $this->db->single();
    }
    
    /**
     * Get order items by order ID
     * @param int $orderId Order ID
     * @return array Order items
     */
    public function getOrderItems($orderId) {
        $this->db->query("SELECT oi.*, p.ProductName, p.Image
                         FROM OrderItems oi
                         JOIN Product p ON oi.ProductID = p.ProductID
                         WHERE oi.OrderID = :orderId");
        $this->db->bind(':orderId', $orderId);
        return $this->db->resultSet();
    }
    
    /**
     * Update purchase with additional information
     * @param array $data Purchase data including id, totalAmount, shippingAddress, etc.
     * @return boolean Success status
     */
    public function updatePurchase($data) {
        $sql = "UPDATE Purchase SET ";
        $params = [];
        
        // Build the SET part of the query based on provided data
        $setParts = [];
        
        if (isset($data['totalAmount'])) {
            $setParts[] = "TotalAmount = :totalAmount";
            $params[':totalAmount'] = $data['totalAmount'];
        }
        
        if (isset($data['shippingAddress'])) {
            $setParts[] = "ShippingAddress = :shippingAddress";
            $params[':shippingAddress'] = $data['shippingAddress'];
        }
        
        if (isset($data['billingAddress'])) {
            $setParts[] = "BillingAddress = :billingAddress";
            $params[':billingAddress'] = $data['billingAddress'];
        }
        
        if (isset($data['paymentMethod'])) {
            $setParts[] = "PaymentMethod = :paymentMethod";
            $params[':paymentMethod'] = $data['paymentMethod'];
        }
        
        if (isset($data['status'])) {
            $setParts[] = "Status = :status";
            $params[':status'] = $data['status'];
        }
        
        if (isset($data['notes'])) {
            $setParts[] = "Notes = :notes";
            $params[':notes'] = $data['notes'];
        }
        
        // If no data to update, return false
        if (empty($setParts)) {
            return false;
        }
        
        // Complete the SQL query
        $sql .= implode(', ', $setParts) . " WHERE PurchaseNo = :id";
        $params[':id'] = $data['id'];
        
        $this->db->query($sql);
        
        // Bind all parameters
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        // Execute the query
        return $this->db->execute();
    }
}
