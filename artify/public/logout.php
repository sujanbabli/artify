<?php
// Initialize application
require_once '../includes/init.php';

// Clear customer session data
Session::remove('customer_id');
Session::remove('customer_email');
Session::remove('customer_name');
Session::remove('customer_logged_in');

// Clear cart
Cart::clear();

// Set flash message
Session::setFlash('info', 'You have been logged out successfully');

// Redirect to home page
header('Location: ' . BASE_URL);
exit;
