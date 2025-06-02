<?php
// Initialize the application
require_once '../includes/init.php';

// Directly include the cart page
echo "<h1>Testing Cart Page Loading</h1>";
require_once VIEWS_DIR . '/pages/cart.php';
