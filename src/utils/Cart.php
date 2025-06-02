<?php
/**
 * Cart Helper Class
 * 
 * Handles shopping cart operations
 */
class Cart {
    /**
     * Initialize cart in session if not exists
     */
    public static function init() {
        if(!Session::get('cart')) {
            Session::set('cart', []);
        }
    }
    
    /**
     * Add item to cart
     * @param int $productId Product ID
     * @param int $quantity Quantity (default: 1)
     * @return boolean Success status
     */
    public static function addItem($productId, $quantity = 1) {
        self::init();
        $cart = Session::get('cart');
        
        // If product already in cart, update quantity
        if(isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        
        Session::set('cart', $cart);
        return true;
    }
    
    /**
     * Update item quantity in cart
     * @param int $productId Product ID
     * @param int $quantity New quantity
     * @return boolean Success status
     */
    public static function updateItem($productId, $quantity) {
        self::init();
        $cart = Session::get('cart');
        
        if($quantity <= 0) {
            return self::removeItem($productId);
        }
        
        if(isset($cart[$productId])) {
            $cart[$productId] = $quantity;
            Session::set('cart', $cart);
            return true;
        }
        
        return false;
    }
    
    /**
     * Remove item from cart
     * @param int $productId Product ID
     * @return boolean Success status
     */
    public static function removeItem($productId) {
        self::init();
        $cart = Session::get('cart');
        
        if(isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::set('cart', $cart);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get cart items with product details
     * @param object $db Database connection
     * @return array Cart items with product details
     */
    public static function getItems($db) {
        self::init();
        $cart = Session::get('cart');
        $items = [];
        
        if(!empty($cart)) {
            // Get product details for each item in cart
            foreach($cart as $productId => $quantity) {
                $db->query("SELECT * FROM Product WHERE ProductNo = :productId AND Active = 1");
                $db->bind(':productId', $productId);
                $product = $db->single();
                
                if($product) {
                    $items[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'subtotal' => $product->Price * $quantity
                    ];
                } else {
                    // If product not found or inactive, remove from cart
                    self::removeItem($productId);
                }
            }
        }
        
        return $items;
    }
    
    /**
     * Get total number of items in cart
     * @return int Total items
     */
    public static function getTotalItems() {
        self::init();
        $cart = Session::get('cart');
        $total = 0;
        
        foreach($cart as $quantity) {
            $total += $quantity;
        }
        
        return $total;
    }
    
    /**
     * Get total price of items in cart
     * @param object $db Database connection
     * @return float Total price
     */
    public static function getTotalPrice($db) {
        $items = self::getItems($db);
        $total = 0;
        
        foreach($items as $item) {
            $total += $item['subtotal'];
        }
        
        return $total;
    }
    
    /**
     * Check if cart is empty
     * @return boolean
     */
    public static function isEmpty() {
        self::init();
        return empty(Session::get('cart'));
    }
    
    /**
     * Clear the cart
     */
    public static function clear() {
        Session::set('cart', []);
    }
}
