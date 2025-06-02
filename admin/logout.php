<?php
// Initialize application
require_once '../includes/init.php';

// Clear admin session data
Session::remove('admin_id');
Session::remove('admin_username');
Session::remove('admin_email');
Session::remove('admin_name');
Session::remove('admin_role');
Session::remove('admin_logged_in');

// Set flash message
Session::setFlash('info', 'You have been logged out successfully');

// Redirect to admin login page
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
