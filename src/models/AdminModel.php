<?php
/**
 * Admin Model
 * 
 * Handles admin authentication and profile management
 */
class AdminModel extends Model {
    /**
     * Authenticate admin
     * @param string $username Username
     * @param string $password Password
     * @return object|boolean Admin data or false if authentication fails
     */
    public function login($username, $password) {
        $this->db->query("SELECT * FROM Admin WHERE Username = :username");
        $this->db->bind(':username', $username);
        
        $admin = $this->db->single();
        
        if($admin && password_verify($password, $admin->Password)) {
            // Update last login time
            $this->updateLastLogin($admin->AdminNo);
            
            return $admin;
        } else {
            return false;
        }
    }
    
    /**
     * Update admin last login time
     * @param int $id Admin ID
     * @return boolean Success status
     */
    private function updateLastLogin($id) {
        $this->db->query("UPDATE Admin SET LastLogin = NOW() WHERE AdminNo = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Get admin by ID
     * @param int $id Admin ID
     * @return object Admin or false if not found
     */
    public function getAdminById($id) {
        $this->db->query("SELECT * FROM Admin WHERE AdminNo = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Update admin password
     * @param int $id Admin ID
     * @param string $newPassword New password
     * @return boolean Success status
     */
    public function changePassword($id, $newPassword) {
        $this->db->query("UPDATE Admin SET Password = :password WHERE AdminNo = :id");
        $this->db->bind(':password', password_hash($newPassword, PASSWORD_DEFAULT));
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Verify current password
     * @param int $id Admin ID
     * @param string $password Current password to verify
     * @return boolean True if password is correct
     */
    public function verifyPassword($id, $password) {
        $this->db->query("SELECT Password FROM Admin WHERE AdminNo = :id");
        $this->db->bind(':id', $id);
        $admin = $this->db->single();
        
        if ($admin) {
            return password_verify($password, $admin->Password);
        }
        
        return false;
    }
    
    /**
     * Update admin profile
     * @param array $data Admin data
     * @return boolean Success status
     */
    public function updateAdmin($data) {
        $this->db->query("UPDATE Admin SET Name = :name, Email = :email, Username = :username WHERE AdminNo = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':id', $data['id']);
        return $this->db->execute();
    }
    
    /**
     * Get all admins
     * @return array Admin records
     */
    public function getAllAdmins() {
        $this->db->query("SELECT * FROM Admin ORDER BY AdminNo");
        return $this->db->resultSet();
    }
    
    /**
     * Register a new admin
     * @param array $data Admin data
     * @return boolean|int Admin ID on success, false on failure
     */
    public function registerAdmin($data) {
        try {
            // Check if username already exists
            $this->db->query("SELECT * FROM Admin WHERE Username = :username");
            $this->db->bind(':username', $data['username']);
            
            if ($this->db->single()) {
                return false; // Username already exists
            }
            
            // Check if email already exists
            $this->db->query("SELECT * FROM Admin WHERE Email = :email");
            $this->db->bind(':email', $data['email']);
            
            if ($this->db->single()) {
                return false; // Email already exists
            }
            
            // Insert new admin
            $this->db->query("INSERT INTO Admin 
                            (Username, Password, Email, Name, Role, LastLogin) 
                            VALUES 
                            (:username, :password, :email, :name, :role, NULL)");
            
            // Bind values
            $this->db->bind(':username', $data['username']);
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':role', $data['role'] ?? 'Editor'); // Default role is Editor if not specified
            
            // Execute
            $this->db->execute();
            
            // Return the last insert ID
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            // Log the error
            error_log('Error in registerAdmin: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get admin by username
     * @param string $username Admin username
     * @return object Admin or false if not found
     */
    public function getAdminByUsername($username) {
        $this->db->query("SELECT * FROM Admin WHERE Username = :username");
        $this->db->bind(':username', $username);
        return $this->db->single();
    }
}
