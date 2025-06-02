<?php
// Clear admin session data
Session::remove('admin_id');
Session::remove('admin_username');
Session::remove('admin_logged_in');

// Set flash message
Session::setFlash('info', 'You have been logged out successfully');

// Redirect to login page
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
