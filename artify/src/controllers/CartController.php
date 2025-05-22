<?php
/**
 * Cart Controller
 * 
 * Handles shopping cart operations
 */
class CartController {
    /**
     * Add item to cart
     * @param int $productId Product ID
     * @param int $quantity Quantity
     * @return array Response data
     */
    public function addToCart($productId, $quantity = 1) {
        // Validate inputs
        $productId = (int)$productId;
        $quantity = (int)$quantity;
        
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product'];
        }
        
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Quantity must be greater than zero'];
        }
        
        // Check if product exists and is active
        $productModel = new ProductModel();
        $product = $productModel->getProductById($productId);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found or unavailable'];
        }
        
        // Add to cart
        Cart::addItem($productId, $quantity);
        
        return [
            'success' => true, 
            'message' => 'Product added to cart',
            'cart_count' => Cart::getTotalItems()
        ];
    }
    
    /**
     * Update cart item quantity
     * @param int $productId Product ID
     * @param int $quantity New quantity
     * @return array Response data
     */
    public function updateCartItem($productId, $quantity) {
        // Validate inputs
        $productId = (int)$productId;
        $quantity = (int)$quantity;
        
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product'];
        }
        
        // Update cart
        if ($quantity <= 0) {
            Cart::removeItem($productId);
            return [
                'success' => true, 
                'message' => 'Item removed from cart',
                'cart_count' => Cart::getTotalItems()
            ];
        } else {
            Cart::updateItem($productId, $quantity);
            return [
                'success' => true, 
                'message' => 'Cart updated',
                'cart_count' => Cart::getTotalItems()
            ];
        }
    }
    
    /**
     * Remove item from cart
     * @param int $productId Product ID
     * @return array Response data
     */
    public function removeCartItem($productId) {
        // Validate input
        $productId = (int)$productId;
        
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product'];
        }
        
        // Remove from cart
        Cart::removeItem($productId);
        
        return [
            'success' => true, 
            'message' => 'Item removed from cart',
            'cart_count' => Cart::getTotalItems()
        ];
    }
    
    /**
     * Get cart items with product details
     * @return array Cart items
     */
    public function getCartItems() {
        $db = new Database();
        return Cart::getItems($db);
    }
    
    /**
     * Get cart summary
     * @return array Cart summary
     */
    public function getCartSummary() {
        $db = new Database();
        return [
            'total_items' => Cart::getTotalItems(),
            'total_price' => Cart::getTotalPrice($db)
        ];
    }
    
    /**
     * Clear the cart
     * @return array Response data
     */
    public function clearCart() {
        Cart::clear();
        return ['success' => true, 'message' => 'Cart cleared'];
    }
}
