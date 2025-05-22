<?php
/**
 * Session Helper Class
 * 
 * Handles session operations
 */
class Session {
    /**
     * Start session
     */
    public static function init() {
        // If session is not already active
        if(session_status() == PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
            session_regenerate_id();
        }
    }
    
    /**
     * Set session data
     * @param string $key Session key
     * @param mixed $value Session value
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session data
     * @param string $key Session key
     * @return mixed Session value or null if not found
     */
    public static function get($key) {
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }
    
    /**
     * Remove session data
     * @param string $key Session key
     */
    public static function remove($key) {
        if(isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Destroy session
     */
    public static function destroy() {
        session_unset();
        session_destroy();
    }
    
    /**
     * Set flash message
     * @param string $key Message key
     * @param string $message Message content
     * @param string $type Message type (success, danger, info, warning)
     */
    public static function setFlash($key, $message, $type = 'info') {
        self::set('flash_' . $key, [
            'message' => $message,
            'type' => $type
        ]);
    }
    
    /**
     * Get flash message and remove it
     * @param string $key Message key
     * @return mixed Message or null if not found
     */
    public static function getFlash($key) {
        $flash = self::get('flash_' . $key);
        self::remove('flash_' . $key);
        return $flash;
    }
    
    /**
     * Check if flash message exists
     * @param string $key Message key
     * @return boolean
     */
    public static function hasFlash($key) {
        return self::get('flash_' . $key) !== null;
    }
    
    /**
     * Generate CSRF token
     * @return string CSRF token
     */
    public static function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        self::set('csrf_token', $token);
        return $token;
    }
    
    /**
     * Check if CSRF token is valid
     * @param string $token Token to validate
     * @return boolean
     */
    public static function validateCsrfToken($token) {
        if(empty($token) || $token !== self::get('csrf_token')) {
            return false;
        }
        return true;
    }
    
    /**
     * Get CSRF token input field
     * @return string HTML input field with CSRF token
     */
    public static function csrfField() {
        $token = self::get('csrf_token') ?: self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
