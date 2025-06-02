<?php
/**
 * Artify Application Test Guide
 * 
 * This file provides a structured approach to testing all features of the Artify application
 * after installation. Use this guide to ensure all functionality is working as expected.
 */
$base_url = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artify Application Test Guide</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px 20px;
        }
        h1, h2, h3 {
            color: #333;
        }
        .test-section {
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
            margin-bottom: 20px;
            padding: 10px 20px;
        }
        .test-steps {
            background-color: #f0f0f0;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .test-result {
            margin-top: 10px;
            padding: 10px;
            background-color: #e7f3fe;
            border-left: 4px solid #2196F3;
        }
        .troubleshooting {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 20px;
            margin-top: 10px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .success {
            color: green;
        }
        .failure {
            color: red;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            margin: 10px 5px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #45a049;
        }
        .button-blue {
            background-color: #2196F3;
        }
        .button-blue:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>
    
    <h1>Artify Application Test Guide</h1>
    <p>This guide provides comprehensive testing procedures to verify all features of the Artify application after installation.</p>
    
    <div style="margin: 20px 0; text-align: center;">
        <a href="database/load_test_data.php" class="button">Load Test Data</a>
        <a href="database/test_database.php" class="button button-blue">Run Database Tests</a>
        <a href="troubleshooting.php" class="button" style="background-color: #ff9800;">Troubleshooting Guide</a>
    </div>
    
    <div class="test-section">
        <h2>1. Pre-Installation Database Tests</h2>
        <p>Before testing the application, run the database tests to ensure the database structure and operations are working correctly.</p>
        
        <div class="test-steps">
            <h3>Test Steps:</h3>
            <ol>
                <li>Navigate to <a href="database/load_test_data.php">Load Test Data</a> in your browser</li>
                <li>Verify all tables were created successfully</li>
                <li>Navigate to <a href="database/test_database.php">Test Database</a> in your browser</li>
                <li>Verify all CRUD tests pass successfully</li>
            </ol>
        </div>
        
        <div class="test-result">
            <h3>Expected Result:</h3>
            <p>All database tests should pass, showing success messages for table creation and all CRUD operations.</p>
        </div>
        
        <div class="troubleshooting">
            <h3>Troubleshooting:</h3>
            <ul>
                <li>If database connection fails, check database credentials in <code>/config/config.php</code></li>
                <li>If tables fail to create, ensure MySQL user has CREATE TABLE privileges</li>
                <li>If CRUD tests fail, examine the specific error message for guidance</li>
            </ul>
        </div>
    </div>
    
    <div class="test-section">
        <h2>2. User Authentication Tests</h2>
        <p>Test the user registration, login, and account management features.</p>
        
        <div class="test-steps">
            <h3>Test Steps:</h3>
            <h4>2.1 Registration</h4>
            <ol>
                <li>Navigate to the registration page</li>
                <li>Create a new account with test credentials</li>
                <li>Verify you receive a success message</li>
            </ol>
            
            <h4>2.2 Login</h4>
            <ol>
                <li>Navigate to the login page</li>
                <li>Login with test credentials</li>
                <li>Verify you are redirected to the user dashboard</li>
            </ol>
            
            <h4>2.3 Account Management</h4>
            <ol>
                <li>Navigate to account settings</li>
                <li>Update profile information</li>
                <li>Change password</li>
                <li>Verify changes were saved</li>
            </ol>
            
            <h4>2.4 Logout</h4>
            <ol>
                <li>Click the logout button</li>
                <li>Verify you are redirected to the home page</li>
                <li>Verify you cannot access protected pages</li>
            </ol>
        </div>
        
        <div class="test-result">
            <h3>Expected Result:</h3>
            <p>All user authentication features should work smoothly, with appropriate redirects and access controls.</p>
        </div>
        
        <div class="troubleshooting">
            <h3>Troubleshooting:</h3>
            <ul>
                <li>If registration fails, check for database connectivity issues or validation errors</li>
                <li>If login fails, verify the user exists in the database and password hashing is working</li>
                <li>If session persistence fails, check PHP session configuration</li>
            </ul>
        </div>
    </div>
    
    <div class="test-section">
        <h2>3. Product Browsing and Searching Tests</h2>
        <p>Test the product catalog, category filtering, and search functionality.</p>
        
        <div class="test-steps">
            <h3>Test Steps:</h3>
            <h4>3.1 Product Catalog</h4>
            <ol>
                <li>Navigate to the products page</li>
                <li>Verify products are displayed with images, names, and prices</li>
                <li>Verify pagination works if there are multiple pages</li>
            </ol>
            
            <h4>3.2 Category Filtering</h4>
            <ol>
                <li>Click on different category filters</li>
                <li>Verify only products from the selected category are displayed</li>
            </ol>
            
            <h4>3.3 Product Search</h4>
            <ol>
                <li>Use the search bar to search for specific products</li>
                <li>Try searching by name, description, and price range</li>
                <li>Verify search results are accurate</li>
            </ol>
            
            <h4>3.4 Product Details</h4>
            <ol>
                <li>Click on a product to view its details</li>
                <li>Verify all product information is displayed correctly</li>
                <li>Verify related products are shown if applicable</li>
            </ol>
        </div>
        
        <div class="test-result">
            <h3>Expected Result:</h3>
            <p>All product browsing features should work correctly, showing appropriate products and details.</p>
        </div>
        
        <div class="troubleshooting">
            <h3>Troubleshooting:</h3>
            <ul>
                <li>If products don't display, check database connectivity and product table data</li>
                <li>If images don't load, verify image paths and directory permissions</li>
                <li>If search doesn't work, check SQL query construction in the search functionality</li>
            </ul>
        </div>
    </div>
    
    <div class="test-section">
        <h2>4. Shopping Cart Tests</h2>
        <p>Test the shopping cart functionality.</p>
        
        <div class="test-steps">
            <h3>Test Steps:</h3>
            <h4>4.1 Adding Products to Cart</h4>
            <ol>
                <li>View a product and click "Add to Cart"</li>
                <li>Verify the product is added to the cart</li>
                <li>Verify the cart count updates in the navigation</li>
            </ol>
            
            <h4>4.2 Cart Management</h4>
            <ol>
                <li>Navigate to the cart page</li>
                <li>Update quantities of products</li>
                <li>Remove products from the cart</li>
                <li>Verify subtotal and total amounts update correctly</li>
            </ol>
            
            <h4>4.3 Cart Persistence</h4>
            <ol>
                <li>Add items to cart while logged in</li>
                <li>Log out and log back in</li>
                <li>Verify cart items are still present</li>
            </ol>
        </div>
        
        <div class="test-result">
            <h3>Expected Result:</h3>
            <p>Shopping cart should maintain state, calculate totals correctly, and allow product management.</p>
        </div>
        
        <div class="troubleshooting">
            <h3>Troubleshooting:</h3>
            <ul>
                <li>If cart doesn't update, check JavaScript functionality and session storage</li>
                <li>If calculations are incorrect, check price formatting and math operations</li>
                <li>If cart doesn't persist, check session configuration or database cart storage</li>
            </ul>
        </div>
    </div>
    
    <div class="test-section">
        <h2>5. Checkout Process Tests</h2>
        <p>Test the checkout and order placement process.</p>
        
        <div class="test-steps">
            <h3>Test Steps:</h3>
            <h4>5.1 Checkout Process</h4>
            <ol>
                <li>Add products to cart</li>
                <li>Proceed to checkout</li>
                <li>Enter shipping information</li>
                <li>Select payment method</li>
                <li>Complete order</li>
            </ol>
            
            <h4>5.2 Order Confirmation</h4>
            <ol>
                <li>Verify order confirmation page shows correct information</li>
                <li>Verify you receive an order confirmation email (if configured)</li>
            </ol>
            
            <h4>5.3 Order History</h4>
            <ol>
                <li>Navigate to order history in user account</li>
                <li>Verify the new order appears in the history</li>
                <li>Check order details for accuracy</li>
            </ol>
        </div>
        
        <div class="test-result">
            <h3>Expected Result:</h3>
            <p>The checkout process should be smooth, orders should be recorded correctly, and confirmation should be sent.</p>
        </div>
        
        <div class="troubleshooting">
            <h3>Troubleshooting:</h3>
            <ul>
                <li>If checkout fails, check form validation and submission processes</li>
                <li>If orders don't save, check database connectivity and table structure</li>
                <li>If emails don't send, verify email configuration in config.php</li>
            </ul>
        </div>
    </div>
    
    <div class="test-section">
        <h2>6. Admin Panel Tests</h2>
        <p>Test the administrative features of the application.</p>
        
        <div class="test-steps">
            <h3>Test Steps:</h3>
            <h4>6.1 Admin Login</h4>
            <ol>
                <li>Login with admin credentials (admin@artify.com / password123)</li>
                <li>Verify you can access the admin dashboard</li>
            </ol>
            
            <h4>6.2 Product Management</h4>
            <ol>
                <li>View product list in admin panel</li>
                <li>Add a new product</li>
                <li>Edit an existing product</li>
                <li>Delete a product</li>
                <li>Verify changes reflect in the storefront</li>
            </ol>
            
            <h4>6.3 Category Management</h4>
            <ol>
                <li>View category list</li>
                <li>Add a new category</li>
                <li>Edit an existing category</li>
                <li>Delete a category</li>
                <li>Verify changes reflect in the storefront</li>
            </ol>
            
            <h4>6.4 Order Management</h4>
            <ol>
                <li>View order list</li>
                <li>View order details</li>
                <li>Update order status</li>
                <li>Verify status updates in customer's order history</li>
            </ol>
            
            <h4>6.5 User Management</h4>
            <ol>
                <li>View user list</li>
                <li>Edit user details</li>
                <li>Change user role</li>
                <li>Verify changes take effect</li>
            </ol>
        </div>
        
        <div class="test-result">
            <h3>Expected Result:</h3>
            <p>Admin should have full control over products, categories, orders, and users with all CRUD operations working.</p>
        </div>
        
        <div class="troubleshooting">
            <h3>Troubleshooting:</h3>
            <ul>
                <li>If admin access fails, check role permissions and authentication logic</li>
                <li>If product/category management fails, check form submissions and database operations</li>
                <li>If image uploads fail, check directory permissions and PHP file upload settings</li>
            </ul>
        </div>
    </div>
    
    <div class="test-section">
        <h2>7. Test Completion Checklist</h2>
        <p>Use this checklist to ensure all critical features have been tested:</p>
        
        <form>
            <h3>Database Tests</h3>
            <p><input type="checkbox"> Database structure and tables created successfully</p>
            <p><input type="checkbox"> All CRUD operations tested and working</p>
            
            <h3>User Authentication</h3>
            <p><input type="checkbox"> Registration working</p>
            <p><input type="checkbox"> Login working</p>
            <p><input type="checkbox"> Profile management working</p>
            <p><input type="checkbox"> Logout working</p>
            
            <h3>Product Features</h3>
            <p><input type="checkbox"> Product listing displays correctly</p>
            <p><input type="checkbox"> Category filtering works</p>
            <p><input type="checkbox"> Search functionality works</p>
            <p><input type="checkbox"> Product details display correctly</p>
            
            <h3>Shopping Cart</h3>
            <p><input type="checkbox"> Add to cart works</p>
            <p><input type="checkbox"> Update quantities works</p>
            <p><input type="checkbox"> Remove items works</p>
            <p><input type="checkbox"> Cart persists between sessions</p>
            
            <h3>Checkout Process</h3>
            <p><input type="checkbox"> Checkout process completes</p>
            <p><input type="checkbox"> Order confirmation displays</p>
            <p><input type="checkbox"> Order history shows placed orders</p>
            
            <h3>Admin Features</h3>
            <p><input type="checkbox"> Admin login and access control works</p>
            <p><input type="checkbox"> Product management works</p>
            <p><input type="checkbox"> Category management works</p>
            <p><input type="checkbox"> Order management works</p>
            <p><input type="checkbox"> User management works</p>
        </form>
    </div>
    
    <div style="margin-top: 30px; padding: 20px; background-color: #e8f5e9; border-radius: 5px;">
        <h2 style="color: #2e7d32;">Conclusion</h2>
        <p>After completing all tests successfully, our Artify application should be fully functional and ready for use. If you encounter any issues that cannot be resolved using the troubleshooting guide, please refer to the separate troubleshooting.php file for more detailed assistance.</p>
    </div>
</body>
</html>
