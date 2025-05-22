<?php
/**
 * Validation Helper Class
 * 
 * Handles form validations
 */
class Validation {
    private $errors = [];
    
    /**
     * Check if field is empty
     * @param string $field Field value
     * @param string $fieldName Field name for error message
     * @return boolean
     */
    public function required($field, $fieldName) {
        if(empty(trim($field))) {
            $this->addError($fieldName, $fieldName . ' is required');
            return false;
        }
        return true;
    }
    
    /**
     * Validate email format
     * @param string $email Email to validate
     * @param string $fieldName Field name for error message
     * @return boolean
     */
    public function email($email, $fieldName = 'Email') {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError($fieldName, 'Please enter a valid email address');
            return false;
        }
        return true;
    }
    
    /**
     * Validate minimum length
     * @param string $field Field value
     * @param int $min Minimum length
     * @param string $fieldName Field name for error message
     * @return boolean
     */
    public function minLength($field, $min, $fieldName) {
        if(strlen(trim($field)) < $min) {
            $this->addError($fieldName, $fieldName . ' must be at least ' . $min . ' characters long');
            return false;
        }
        return true;
    }
    
    /**
     * Validate maximum length
     * @param string $field Field value
     * @param int $max Maximum length
     * @param string $fieldName Field name for error message
     * @return boolean
     */
    public function maxLength($field, $max, $fieldName) {
        if(strlen(trim($field)) > $max) {
            $this->addError($fieldName, $fieldName . ' must not exceed ' . $max . ' characters');
            return false;
        }
        return true;
    }
    
    /**
     * Validate numeric value
     * @param mixed $field Field value
     * @param string $fieldName Field name for error message
     * @return boolean
     */
    public function numeric($field, $fieldName) {
        if(!is_numeric($field)) {
            $this->addError($fieldName, $fieldName . ' must be a number');
            return false;
        }
        return true;
    }
    
    /**
     * Validate phone number format
     * @param string $phone Phone number
     * @param string $fieldName Field name for error message
     * @return boolean
     */
    public function phone($phone, $fieldName = 'Phone') {
        // Remove any non-digit characters
        $phone = preg_replace('/[^\d]/', '', $phone);
        
        if(strlen($phone) < 8 || strlen($phone) > 15) {
            $this->addError($fieldName, 'Please enter a valid phone number');
            return false;
        }
        return true;
    }
    
    /**
     * Validate date format (YYYY-MM-DD)
     * @param string $date Date to validate
     * @param string $fieldName Field name for error message
     * @return boolean
     */
    public function date($date, $fieldName = 'Date') {
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format, $date);
        if(!$d || $d->format($format) !== $date) {
            $this->addError($fieldName, 'Please enter a valid date (YYYY-MM-DD)');
            return false;
        }
        return true;
    }
    
    /**
     * Validate if value matches pattern
     * @param string $field Field value
     * @param string $pattern Regex pattern
     * @param string $fieldName Field name for error message
     * @param string $message Error message
     * @return boolean
     */
    public function matches($field, $pattern, $fieldName, $message = null) {
        if (!preg_match($pattern, $field)) {
            $this->addError($fieldName, $message ?: $fieldName . ' is not valid');
            return false;
        }
        return true;
    }
    
    /**
     * Validate password strength
     * @param string $password Password to validate
     * @param string $fieldName Field name for error message
     * @return boolean
     */
    public function password($password, $fieldName = 'Password') {
        // Minimum 8 characters, at least one uppercase letter, one lowercase letter, and one number
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
        
        if (!preg_match($pattern, $password)) {
            $this->addError($fieldName, 'Password must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, and one number');
            return false;
        }
        return true;
    }
    
    /**
     * Validate postal/zip code format
     * @param string $code Postal code to validate
     * @param string $fieldName Field name for error message
     * @return boolean
     */
    public function postalCode($code, $fieldName = 'Postal Code') {
        // Basic pattern for most postal codes worldwide
        if (!preg_match('/^[a-zA-Z0-9\s-]{3,10}$/', $code)) {
            $this->addError($fieldName, 'Please enter a valid postal code');
            return false;
        }
        return true;
    }
    
    /**
     * Check if two fields match (e.g., password confirmation)
     * @param string $field1 First field value
     * @param string $field2 Second field value
     * @param string $field1Name First field name
     * @param string $field2Name Second field name
     * @return boolean
     */
    public function confirmMatch($field1, $field2, $field1Name, $field2Name) {
        if ($field1 !== $field2) {
            $this->addError($field2Name, $field1Name . ' and ' . $field2Name . ' do not match');
            return false;
        }
        return true;
    }
    
    /**
     * Add error message
     * @param string $field Field name
     * @param string $message Error message
     */
    private function addError($field, $message) {
        $this->errors[$field] = $message;
    }
    
    /**
     * Check if validation passed
     * @return boolean
     */
    public function passed() {
        return empty($this->errors);
    }
    
    /**
     * Get all error messages
     * @return array Error messages
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get specific error message
     * @param string $field Field name
     * @return string Error message or empty string if no error
     */
    public function getError($field) {
        return isset($this->errors[$field]) ? $this->errors[$field] : '';
    }
    
    /**
     * Sanitize data to prevent XSS
     * @param string $data Data to sanitize
     * @return string Sanitized data
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            // If it's an array, sanitize each element
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }
        
        // For strings
        if (is_string($data)) {
            // Remove any HTML and PHP tags
            $data = strip_tags($data);
            
            // Convert special characters to HTML entities
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            // Trim whitespace
            $data = trim($data);
            
            return $data;
        }
        
        // Return unchanged if not a string or array
        return $data;
    }
}
