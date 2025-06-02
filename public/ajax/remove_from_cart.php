<?php
// Initialize application
require_once '../../includes/init.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get product ID from request
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// Validate inputs
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Remove from cart
$cartController = new CartController();
$result = $cartController->removeCartItem($productId);

// Return response
echo json_encode($result);
exit;
