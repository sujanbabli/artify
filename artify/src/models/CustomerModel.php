<?php
/**
 * Customer Model
 * 
 * Handles customer related database operations
 */
class CustomerModel extends Model {
    /**
     * Add new customer or update if exists
     * @param array $data Customer data
     * @return boolean Success status
     */
    public function addOrUpdateCustomer($data) {
        // Check if customer exists
        $this->db->query("SELECT * FROM Customer WHERE CustEmail = :email");
        $this->db->bind(':email', $data['email']);
        $existingCustomer = $this->db->single();
        
        if($existingCustomer) {
            // Update customer
            $this->db->query("UPDATE Customer SET 
                              CustFName = :firstName, 
                              CustLName = :lastName, 
                              Title = :title, 
                              Address = :address, 
                              City = :city, 
                              State = :state, 
                              Country = :country, 
                              PostCode = :postCode, 
                              Phone = :phone 
                              WHERE CustEmail = :email");
        } else {
            // Add new customer
            $this->db->query("INSERT INTO Customer 
                             (CustEmail, CustFName, CustLName, Title, Address, City, State, Country, PostCode, Phone) 
                             VALUES 
                             (:email, :firstName, :lastName, :title, :address, :city, :state, :country, :postCode, :phone)");
        }
        
        // Bind values
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':firstName', $data['firstName']);
        $this->db->bind(':lastName', $data['lastName']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':city', $data['city']);
        $this->db->bind(':state', $data['state']);
        $this->db->bind(':country', $data['country']);
        $this->db->bind(':postCode', $data['postCode']);
        $this->db->bind(':phone', $data['phone']);
        
        // Execute
        return $this->db->execute();
    }
    
    /**
     * Register a new customer
     * @param string $firstName Customer first name
     * @param string $lastName Customer last name
     * @param string $email Customer email
     * @param string $hashedPassword Customer hashed password
     * @return boolean Success status
     */
    public function registerCustomer($firstName, $lastName, $email, $hashedPassword) {
        try {
            // Add new customer with password, set other fields to empty string
            $this->db->query("INSERT INTO Customer 
                            (CustEmail, CustFName, CustLName, CustPassword, Title, Address, City, State, Country, PostCode, Phone) 
                            VALUES 
                            (:email, :firstName, :lastName, :password, '', '', '', '', '', '', '')");
            
            // Bind values
            $this->db->bind(':email', $email);
            $this->db->bind(':firstName', $firstName);
            $this->db->bind(':lastName', $lastName);
            $this->db->bind(':password', $hashedPassword);
            
            // Execute
            return $this->db->execute();
        } catch (Exception $e) {
            // Log the error
            error_log('Error in registerCustomer: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all customers
     * @return array List of all customers
     */
    public function getAllCustomers() {
        $this->db->query("SELECT * FROM Customer ORDER BY CustLName, CustFName");
        return $this->db->resultSet();
    }
    
    /**
     * Get customer by email
     * @param string $email Customer email
     * @return object|boolean Customer object or false if not found
     */
    public function getCustomerByEmail($email) {
        $this->db->query("SELECT * FROM Customer WHERE CustEmail = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }
    
    /**
     * Update customer information
     * @param string $email Customer email (original)
     * @param array $data Customer data
     * @return boolean Success status
     */
    public function updateCustomer($email, $data) {
        $this->db->query("UPDATE Customer SET 
                          CustFName = :firstName, 
                          CustLName = :lastName, 
                          Title = :title,
                          Address = :address,
                          City = :city,
                          State = :state,
                          Country = :country,
                          PostCode = :postCode,
                          Phone = :phone
                          " . 
                          (!empty($data['password']) ? ", CustPassword = :password" : "") . 
                          " WHERE CustEmail = :email");
        
        $this->db->bind(':firstName', $data['firstName']);
        $this->db->bind(':lastName', $data['lastName']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':city', $data['city']);
        $this->db->bind(':state', $data['state']);
        $this->db->bind(':country', $data['country']);
        $this->db->bind(':postCode', $data['postCode']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $email);
        
        // Only bind password if it's set
        if (!empty($data['password'])) {
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        }
        
        return $this->db->execute();
    }
    
    /**
     * Delete customer
     * @param string $email Customer email
     * @return boolean Success status
     */
    public function deleteCustomer($email) {
        $this->db->query("DELETE FROM Customer WHERE CustEmail = :email");
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }
}
