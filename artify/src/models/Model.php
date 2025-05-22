<?php
/**
 * Base Model Class
 * 
 * This is the parent class for all models
 */
class Model {
    protected $db;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct() {
        $this->db = new Database();
    }
}
