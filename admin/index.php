<?php
// Initialize the application
require_once '../includes/init.php';

// Check if admin is logged in
if (!Session::get('admin_logged_in')) {
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Get current page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Define allowed pages
$allowedPages = [
    'dashboard', 'products', 'product-form', 'orders', 'testimonials', 
    'news', 'settings', 'logout', 'customers', 'customer-form', 'customer-detail', 
    'email-logs', 'email-settings', 'resend-emails'
];

// If page is not allowed, default to dashboard
if (!in_array($page, $allowedPages)) {
    $page = 'dashboard';
}

// Handle view parameter for order details
$view = isset($_GET['view']) ? $_GET['view'] : null;

// Load page content
require_once VIEWS_DIR . '/admin/templates/header.php';

// If viewing a specific order
if ($page === 'orders' && !empty($view)) {
    require_once VIEWS_DIR . '/admin/pages/order-detail.php';
} else {
    // Load regular page
    require_once VIEWS_DIR . '/admin/pages/' . $page . '.php';
}

require_once VIEWS_DIR . '/admin/templates/footer.php';
