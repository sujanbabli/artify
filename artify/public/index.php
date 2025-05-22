<?php
// Initialize the application
require_once '../includes/init.php';

// Get current page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Define allowed pages
$allowedPages = [
    'home', 
    'shop', 
    'product', 
    'cart', 
    'checkout',
    'checkout-success',
    'order-confirmation', 
    'testimonials', 
    'submit-testimonial',
    'about',
    'login',
    'register',
    'account',
    'logout',
    'orders',
    'order-detail',
    'profile',
    'change-password'
];

// If page is not allowed, default to home
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

// Load page content
require_once VIEWS_DIR . '/templates/header.php';
require_once VIEWS_DIR . '/pages/' . $page . '.php';
require_once VIEWS_DIR . '/templates/footer.php';
