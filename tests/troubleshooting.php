<?php
/**
 * Artify Troubleshooting Guide
 * 
 * This file provides detailed troubleshooting steps for common issues that might arise
 * during installation or operation of the Artify application.
 */
$base_url = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artify Troubleshooting Guide</title>
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
        .issue {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            margin-bottom: 20px;
            padding: 15px;
        }
        .solution {
            background-color: #e8f5e9;
            border-left: 4px solid #4CAF50;
            margin: 10px 0;
            padding: 15px;
        }
        code {
            background-color: #f5f5f5;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: 'Courier New', Courier, monospace;
        }
        .diagnostic {
            background-color: #e7f3fe;
            border-left: 4px solid #2196F3;
            padding: 10px;
            margin: 10px 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
    
    <h1>Artify Troubleshooting Guide</h1>
    <p>This comprehensive guide addresses common issues that may occur during installation or operation of the Artify application and provides detailed solutions.</p>
    
    <div style="margin: 20px 0; text-align: center;">
        <a href="application_test_guide.php" class="button button-blue">Return to Test Guide</a>
        <a href="database/load_test_data.php" class="button">Load Test Data</a>
        <a href="database/test_database.php" class="button">Run Database Tests</a>
    </div>
    
    <h2>Table of Contents</h2>
    <ol>
        <li><a href="#database">Database Issues</a></li>
        <li><a href="#server">Server Configuration Issues</a></li>
        <li><a href="#application">Application Functionality Issues</a></li>
        <li><a href="#images">Image and Media Issues</a></li>
        <li><a href="#performance">Performance Issues</a></li>
        <li><a href="#security">Security Issues</a></li>
        <li><a href="#diagnostics">Diagnostic Tools</a></li>
    </ol>
    
    <h2 id="database">1. Database Issues</h2>
    
    <div class="issue">
        <h3>Issue: Unable to connect to the database</h3>
        <p>The application fails to connect to the MySQL database, showing errors like "Connection refused" or "Access denied".</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Check database credentials:</strong>
                    <p>Verify the credentials in <code>/config/config.php</code> are correct:</p>
                    <pre><code>define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'artify');</code></pre>
                </li>
                <li>
                    <strong>Verify MySQL service is running:</strong>
                    <p>Check that the MySQL service is running in your XAMPP Control Panel.</p>
                </li>
                <li>
                    <strong>Test connection manually:</strong>
                    <p>Try connecting to MySQL using phpMyAdmin or the MySQL command line:</p>
                    <pre><code>mysql -u root -p</code></pre>
                </li>
                <li>
                    <strong>Check MySQL port:</strong>
                    <p>If MySQL is running on a non-standard port, update DB_HOST to include the port:</p>
                    <pre><code>define('DB_HOST', 'localhost:3307');</code></pre>
                </li>
            </ol>
        </div>
    </div>
    
    <div class="issue">
        <h3>Issue: Database tables not created</h3>
        <p>The application fails to create the necessary database tables during installation.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Manually run the database load script:</strong>
                    <p>Navigate to <code>/tests/database/load_test_data.php</code> in your browser to run the table creation script.</p>
                </li>
                <li>
                    <strong>Check MySQL user permissions:</strong>
                    <p>Ensure the MySQL user has CREATE, ALTER, and DROP privileges.</p>
                </li>
                <li>
                    <strong>Examine error logs:</strong>
                    <p>Check the PHP error logs in XAMPP/logs directory for specific SQL errors.</p>
                </li>
                <li>
                    <strong>Create tables manually:</strong>
                    <p>If needed, you can create the tables manually using phpMyAdmin by importing the SQL schema from the load_test_data.php file.</p>
                </li>
            </ol>
        </div>
    </div>
    
    <div class="issue">
        <h3>Issue: Data not being saved or retrieved</h3>
        <p>Forms submit without error but data isn't saved, or data isn't being retrieved correctly.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Check table structure:</strong>
                    <p>Verify the table structure matches the expected schema using phpMyAdmin.</p>
                </li>
                <li>
                    <strong>Debug SQL queries:</strong>
                    <p>Add temporary debugging code to print SQL queries before execution:</p>
                    <pre><code>// Debug SQL query
echo "Query: " . $sql;
// Then run your query</code></pre>
                </li>
                <li>
                    <strong>Check for SQL errors:</strong>
                    <p>Capture and display SQL errors:</p>
                    <pre><code>$result = $conn->query($sql);
if (!$result) {
    echo "Error: " . $conn->error;
}</code></pre>
                </li>
                <li>
                    <strong>Run database tests:</strong>
                    <p>Navigate to <code>/tests/database/test_database.php</code> to run CRUD tests on all tables.</p>
                </li>
            </ol>
        </div>
    </div>
    
    <h2 id="server">2. Server Configuration Issues</h2>
    
    <div class="issue">
        <h3>Issue: 500 Internal Server Error</h3>
        <p>Browser shows a 500 Internal Server Error when accessing the application.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Check server error logs:</strong>
                    <p>Examine the Apache error logs in XAMPP/logs directory.</p>
                </li>
                <li>
                    <strong>Enable PHP error display:</strong>
                    <p>Temporarily modify <code>php.ini</code> to show errors:</p>
                    <pre><code>display_errors = On
error_reporting = E_ALL</code></pre>
                    <p>Or add this to the top of your PHP files:</p>
                    <pre><code>ini_set('display_errors', 1);
error_reporting(E_ALL);</code></pre>
                </li>
                <li>
                    <strong>Check file permissions:</strong>
                    <p>Ensure all application files have correct permissions:</p>
                    <ul>
                        <li>Directories: 755 (rwxr-xr-x)</li>
                        <li>Files: 644 (rw-r--r--)</li>
                    </ul>
                </li>
                <li>
                    <strong>Verify .htaccess file:</strong>
                    <p>Check for syntax errors in .htaccess files.</p>
                </li>
            </ol>
        </div>
    </div>
    
    <div class="issue">
        <h3>Issue: URL Routing Problems</h3>
        <p>The application's URLs are not routing correctly, resulting in 404 errors or incorrect page loads.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Check .htaccess configuration:</strong>
                    <p>Verify the .htaccess file in the root directory is correct:</p>
                    <pre><code># Disable directory listing
Options -Indexes

# Redirect root to public directory
RewriteEngine On
RewriteRule ^$ public/ [L]
RewriteRule ^(.*)$ public/$1 [L]</code></pre>
                </li>
                <li>
                    <strong>Enable mod_rewrite:</strong>
                    <p>Ensure Apache's mod_rewrite module is enabled in XAMPP.</p>
                    <p>In httpd.conf, uncomment the line:</p>
                    <pre><code>LoadModule rewrite_module modules/mod_rewrite.so</code></pre>
                </li>
                <li>
                    <strong>Check BASE_URL setting:</strong>
                    <p>Verify the BASE_URL in config.php matches your actual server setup:</p>
                    <pre><code>define('BASE_URL', 'http://localhost/artify');</code></pre>
                </li>
                <li>
                    <strong>Test with direct file paths:</strong>
                    <p>Temporarily test accessing files directly (e.g., /public/index.php) to isolate routing issues.</p>
                </li>
            </ol>
        </div>
    </div>
    
    <h2 id="application">3. Application Functionality Issues</h2>
    
    <div class="issue">
        <h3>Issue: User Registration/Login Failure</h3>
        <p>Users cannot register or login to the application.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Check session configuration:</strong>
                    <p>Verify PHP session settings in php.ini:</p>
                    <pre><code>session.save_path = "/tmp"
session.use_cookies = 1
session.use_only_cookies = 1</code></pre>
                </li>
                <li>
                    <strong>Debug authentication logic:</strong>
                    <p>Add temporary debugging code to trace authentication flow:</p>
                    <pre><code>// In login processing
echo "Password verification result: ";
var_dump(password_verify($password, $hashed_password));</code></pre>
                </li>
                <li>
                    <strong>Check users table:</strong>
                    <p>Verify the users table has correct data and structure.</p>
                </li>
                <li>
                    <strong>Clear browser cookies:</strong>
                    <p>Have users clear their browser cookies and cache.</p>
                </li>
                <li>
                    <strong>Test with default user:</strong>
                    <p>Try logging in with the default admin user created in the test data:</p>
                    <pre><code>Username: admin
Email: admin@artify.com
Password: password123</code></pre>
                </li>
            </ol>
        </div>
    </div>
    
    <div class="issue">
        <h3>Issue: Shopping Cart Problems</h3>
        <p>Items not adding to cart, cart items disappearing, or incorrect calculations.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Check session handling:</strong>
                    <p>Verify session storage for cart items is working:</p>
                    <pre><code>// Debug session
echo "Current session contents: ";
print_r($_SESSION);</code></pre>
                </li>
                <li>
                    <strong>Debug JavaScript:</strong>
                    <p>If cart uses AJAX, check browser console for JavaScript errors.</p>
                </li>
                <li>
                    <strong>Verify price calculations:</strong>
                    <p>Check for mathematical or type conversion issues in price calculations.</p>
                </li>
                <li>
                    <strong>Test in different browsers:</strong>
                    <p>Try the cart functionality in different browsers to isolate browser-specific issues.</p>
                </li>
            </ol>
        </div>
    </div>
    
    <h2 id="images">4. Image and Media Issues</h2>
    
    <div class="issue">
        <h3>Issue: Product Images Not Displaying</h3>
        <p>Product images are broken or not displaying.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Generate test images:</strong>
                    <p>Run the image generation script to create placeholder images:</p>
                    <p>Navigate to <code>/generate_images.php</code> in your browser.</p>
                </li>
                <li>
                    <strong>Check image directories:</strong>
                    <p>Verify the images directory exists and has correct permissions:</p>
                    <pre><code>/public/images/products/</code></pre>
                </li>
                <li>
                    <strong>Verify image paths in database:</strong>
                    <p>Check the image_path values in the products table match the actual file paths.</p>
                </li>
                <li>
                    <strong>Inspect browser network requests:</strong>
                    <p>Use browser developer tools to see if image requests are returning 404 or other errors.</p>
                </li>
            </ol>
        </div>
    </div>
    
    <div class="issue">
        <h3>Issue: Image Upload Failures</h3>
        <p>Admin users cannot upload product images.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Check PHP file upload settings:</strong>
                    <p>Verify these settings in php.ini:</p>
                    <pre><code>file_uploads = On
upload_max_filesize = 8M
post_max_size = 8M
max_file_size = 8M</code></pre>
                </li>
                <li>
                    <strong>Verify upload directory permissions:</strong>
                    <p>Make sure the images directory is writable:</p>
                    <pre><code>chmod 755 public/images/products</code></pre>
                </li>
                <li>
                    <strong>Debug file upload process:</strong>
                    <p>Add temporary code to show file upload details:</p>
                    <pre><code>echo "Upload info: ";
print_r($_FILES);</code></pre>
                </li>
                <li>
                    <strong>Check form configuration:</strong>
                    <p>Ensure the form has proper enctype:</p>
                    <pre><code>&lt;form enctype="multipart/form-data" method="post"&gt;</code></pre>
                </li>
            </ol>
        </div>
    </div>
    
    <h2 id="performance">5. Performance Issues</h2>
    
    <div class="issue">
        <h3>Issue: Slow Page Loading</h3>
        <p>The application loads slowly, especially on product pages.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Optimize database queries:</strong>
                    <p>Add indexes to commonly queried fields:</p>
                    <pre><code>ALTER TABLE products ADD INDEX (category_id);
ALTER TABLE order_items ADD INDEX (order_id);
ALTER TABLE order_items ADD INDEX (product_id);</code></pre>
                </li>
                <li>
                    <strong>Check for excessive queries:</strong>
                    <p>Use temporary debugging to count database queries per page:</p>
                    <pre><code>// At the top of your script
$query_count = 0;

// Modify your database query function to count queries
function db_query($sql) {
    global $query_count;
    $query_count++;
    // ... run query ...
}

// At the bottom of the page
echo "Total queries: " . $query_count;</code></pre>
                </li>
                <li>
                    <strong>Optimize image sizes:</strong>
                    <p>Ensure product images are appropriately sized and compressed.</p>
                </li>
                <li>
                    <strong>Enable PHP caching:</strong>
                    <p>Consider enabling OPcache in php.ini for better performance.</p>
                </li>
            </ol>
        </div>
    </div>
    
    <h2 id="security">6. Security Issues</h2>
    
    <div class="issue">
        <h3>Issue: Unauthorized Access to Admin Areas</h3>
        <p>Non-admin users can access administrative functions.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Verify role checking:</strong>
                    <p>Ensure all admin pages check for admin role:</p>
                    <pre><code>// At the top of all admin pages
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}</code></pre>
                </li>
                <li>
                    <strong>Check session security:</strong>
                    <p>Verify session configuration is secure:</p>
                    <pre><code>// In session initialization
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}</code></pre>
                </li>
                <li>
                    <strong>Test with non-admin account:</strong>
                    <p>Try accessing admin URLs with a regular user account to verify restrictions.</p>
                </li>
            </ol>
        </div>
    </div>
    
    <div class="issue">
        <h3>Issue: SQL Injection Vulnerabilities</h3>
        <p>Concerns about SQL injection in form inputs.</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Use prepared statements:</strong>
                    <p>Ensure all database queries use prepared statements:</p>
                    <pre><code>// UNSAFE:
$query = "SELECT * FROM users WHERE username = '$username'";

// SAFE:
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();</code></pre>
                </li>
                <li>
                    <strong>Validate all inputs:</strong>
                    <p>Sanitize and validate all user inputs:</p>
                    <pre><code>$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    // Invalid username format
}</code></pre>
                </li>
                <li>
                    <strong>Check all form processing:</strong>
                    <p>Review all code that processes form submissions for proper sanitization.</p>
                </li>
            </ol>
        </div>
    </div>
    
    <h2 id="diagnostics">7. Diagnostic Tools</h2>
    
    <div class="diagnostic">
        <h3>PHP Configuration Check</h3>
        <p>Create a file called <code>phpinfo.php</code> in the root directory with this content:</p>
        <pre><code>&lt;?php phpinfo(); ?&gt;</code></pre>
        <p>Access this file in your browser to view PHP configuration details.</p>
    </div>
    
    <div class="diagnostic">
        <h3>Database Connection Test</h3>
        <p>Create a file called <code>db_test.php</code> with this content:</p>
        <pre><code>&lt;?php
require_once 'config/config.php';
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?&gt;</code></pre>
    </div>
    
    <div class="diagnostic">
        <h3>File Permission Check</h3>
        <p>Create a file called <code>check_permissions.php</code> with this content:</p>
        <pre><code>&lt;?php
$dirs = [
    'public/images/products',
    'logs',
    'tmp'
];

foreach ($dirs as $dir) {
    echo "Directory: $dir - ";
    if (file_exists($dir)) {
        echo "Exists. ";
        echo "Readable: " . (is_readable($dir) ? "Yes" : "No") . ". ";
        echo "Writable: " . (is_writable($dir) ? "Yes" : "No") . ".&lt;br&gt;";
    } else {
        echo "Does not exist.&lt;br&gt;";
    }
}
?&gt;</code></pre>
    </div>
    
    <div style="margin-top: 30px; padding: 20px; background-color: #e8f5e9; border-radius: 5px;">
        <h2 style="color: #2e7d32;">Emergency Recovery</h2>
        <p>If the application is completely non-functional and you need to restore it to a working state:</p>
        <ol>
            <li>Restore the database by running <code>/tests/database/load_test_data.php</code></li>
            <li>Verify all file permissions are correct</li>
            <li>Restart Apache and MySQL services in XAMPP</li>
            <li>Clear your browser cache and cookies</li>
            <li>If the application still doesn't work, consider reinstalling XAMPP or contacting technical support</li>
        </ol>
    </div>

    <div class="issue">
        <h3>Issue: Foreign Key Constraint Errors</h3>
        <p>Errors like "Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails"</p>
        
        <div class="solution">
            <h4>Solution:</h4>
            <ol>
                <li>
                    <strong>Understand the error:</strong>
                    <p>This error occurs when you try to delete a record that has related records in another table. For example, deleting a category that has products assigned to it.</p>
                </li>
                <li>
                    <strong>Options to fix:</strong>
                    <ul>
                        <li><strong>Delete related records first:</strong> Before deleting a parent record (like a category), delete or reassign all related child records (like products).</li>
                        <li><strong>Use ON DELETE CASCADE:</strong> Modify the foreign key constraint to automatically delete related records when a parent is deleted.</li>
                        <li><strong>Use ON DELETE SET NULL:</strong> Set the foreign key to NULL in related records when a parent is deleted (only works if the foreign key column allows NULL values).</li>
                    </ul>
                </li>
                <li>
                    <strong>Example SQL to modify constraint:</strong>
                    <pre><code>-- Drop existing foreign key
ALTER TABLE products DROP FOREIGN KEY products_ibfk_1;

-- Add it back with CASCADE
ALTER TABLE products 
ADD CONSTRAINT products_ibfk_1 
FOREIGN KEY (category_id) 
REFERENCES categories(id) 
ON DELETE CASCADE;</code></pre>
                </li>
                <li>
                    <strong>Rerun the database load script:</strong>
                    <p>The easiest solution is to rerun the <a href="../tests/database/load_test_data.php">load_test_data.php</a> script, which has been updated to use ON DELETE CASCADE.</p>
                </li>
            </ol>
        </div>
    </div>
</body>
</html>
