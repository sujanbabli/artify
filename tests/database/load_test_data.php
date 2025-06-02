<?php
/**
 * Database Test Load Script
 * 
 * This script populates the database with test data for testing purposes.
 * Run this script before running the test_database.php script.
 */

// Include configuration
require_once __DIR__ . '/../../config/config.php';

// Set up error handling for tests
ini_set('display_errors', 1);
error_reporting(E_ALL);

$base_url = '../../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artify Database Test Load</title>
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
        p {
            margin: 10px 0;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            margin: 20px 5px;
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
    <?php include_once __DIR__ . '/../includes/navbar.php'; ?>

    <h1>Artify Database Test Load</h1>
    <p>Populating database with test data based on the Artify database structure...</p>

<?php
try {
    // Connect to the database
    $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists, if not create it
    $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $conn->exec("USE " . DB_NAME);
    
    echo "<p class='success'>Connected to database successfully.</p>";
    
    // Create tables based on artify_db.sql structure
    
    // Drop tables in the correct order to respect foreign key constraints
    $conn->exec("DROP TABLE IF EXISTS CartItem");
    $conn->exec("DROP TABLE IF EXISTS Cart");
    $conn->exec("DROP TABLE IF EXISTS PurchaseItem");
    $conn->exec("DROP TABLE IF EXISTS Purchase");
    $conn->exec("DROP TABLE IF EXISTS Testimonial");
    $conn->exec("DROP TABLE IF EXISTS News");
    $conn->exec("DROP TABLE IF EXISTS Product");
    $conn->exec("DROP TABLE IF EXISTS Category");
    $conn->exec("DROP TABLE IF EXISTS Customer");
    $conn->exec("DROP TABLE IF EXISTS Admin");
    
    // Create Customer table
    $conn->exec("CREATE TABLE IF NOT EXISTS Customer (
        CustEmail VARCHAR(100) PRIMARY KEY,
        CustFName VARCHAR(50) NOT NULL,
        CustLName VARCHAR(50) NOT NULL,
        Title VARCHAR(10),
        Address VARCHAR(255),
        City VARCHAR(50),
        State VARCHAR(50),
        Country VARCHAR(50),
        PostCode VARCHAR(20),
        Phone VARCHAR(20),
        CustPassword VARCHAR(255)
    )");
    echo "<p class='success'>Customer table created successfully.</p>";
    
    // Create Category table
    $conn->exec("CREATE TABLE IF NOT EXISTS Category (
        CategoryId INT AUTO_INCREMENT PRIMARY KEY,
        Name VARCHAR(50) NOT NULL UNIQUE,
        Description TEXT,
        Parent INT,
        FOREIGN KEY (Parent) REFERENCES Category(CategoryId) ON DELETE SET NULL
    )");
    echo "<p class='success'>Category table created successfully.</p>";
    
    // Create Product table
    $conn->exec("CREATE TABLE IF NOT EXISTS Product (
        ProductNo INT AUTO_INCREMENT PRIMARY KEY,
        Description TEXT NOT NULL,
        Price DECIMAL(10, 2) NOT NULL,
        Category VARCHAR(50) NOT NULL,
        Colour VARCHAR(50),
        Size VARCHAR(50),
        ImagePath VARCHAR(255),
        Stock INT DEFAULT 10,
        DateAdded DATETIME DEFAULT CURRENT_TIMESTAMP,
        Featured BOOLEAN DEFAULT FALSE,
        Active BOOLEAN DEFAULT TRUE
    )");
    echo "<p class='success'>Product table created successfully.</p>";
    
    // Create Purchase table (Orders)
    $conn->exec("CREATE TABLE IF NOT EXISTS Purchase (
        PurchaseNo INT AUTO_INCREMENT PRIMARY KEY,
        Date DATETIME DEFAULT CURRENT_TIMESTAMP,
        CustEmail VARCHAR(100) NOT NULL,
        Status VARCHAR(20) DEFAULT 'Pending',
        TotalAmount DECIMAL(10, 2) DEFAULT 0,
        ShippingAddress VARCHAR(255),
        BillingAddress VARCHAR(255),
        PaymentMethod VARCHAR(50),
        TrackingNumber VARCHAR(50),
        Notes TEXT,
        FOREIGN KEY (CustEmail) REFERENCES Customer(CustEmail) ON DELETE CASCADE
    )");
    echo "<p class='success'>Purchase table created successfully.</p>";
    
    // Create PurchaseItem table (Order Items)
    $conn->exec("CREATE TABLE IF NOT EXISTS PurchaseItem (
        ItemNo INT AUTO_INCREMENT PRIMARY KEY,
        Quantity INT NOT NULL,
        PurchaseNo INT NOT NULL,
        ProductNo INT NOT NULL,
        Price DECIMAL(10, 2) NOT NULL,
        Subtotal DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (PurchaseNo) REFERENCES Purchase(PurchaseNo) ON DELETE CASCADE,
        FOREIGN KEY (ProductNo) REFERENCES Product(ProductNo) ON DELETE RESTRICT
    )");
    echo "<p class='success'>PurchaseItem table created successfully.</p>";
    
    // Create News table
    $conn->exec("CREATE TABLE IF NOT EXISTS News (
        NewsNo INT AUTO_INCREMENT PRIMARY KEY,
        Title VARCHAR(100) NOT NULL,
        Text TEXT NOT NULL,
        Content TEXT NOT NULL,
        Summary VARCHAR(255),
        Author VARCHAR(100),
        ImagePath VARCHAR(255),
        Date DATE DEFAULT (CURRENT_DATE),
        Active BOOLEAN DEFAULT TRUE
    )");
    echo "<p class='success'>News table created successfully.</p>";
    
    // Create Testimonial table
    $conn->exec("CREATE TABLE IF NOT EXISTS Testimonial (
        TestimonialNo INT AUTO_INCREMENT PRIMARY KEY,
        Name VARCHAR(100) NOT NULL,
        Email VARCHAR(100) NOT NULL,
        Rating INT NOT NULL DEFAULT 5,
        Text TEXT NOT NULL,
        Date DATETIME DEFAULT CURRENT_TIMESTAMP,
        Approved BOOLEAN DEFAULT FALSE
    )");
    echo "<p class='success'>Testimonial table created successfully.</p>";
    
    // Create Admin table
    $conn->exec("CREATE TABLE IF NOT EXISTS Admin (
        AdminNo INT AUTO_INCREMENT PRIMARY KEY,
        Username VARCHAR(50) NOT NULL UNIQUE,
        Password VARCHAR(255) NOT NULL,
        Email VARCHAR(100) NOT NULL,
        Name VARCHAR(100) NOT NULL,
        Role VARCHAR(50) DEFAULT 'Editor',
        LastLogin DATETIME
    )");
    echo "<p class='success'>Admin table created successfully.</p>";
    
    // Create Cart table
    $conn->exec("CREATE TABLE IF NOT EXISTS Cart (
        CartId VARCHAR(64) PRIMARY KEY,
        CustEmail VARCHAR(100),
        CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (CustEmail) REFERENCES Customer(CustEmail) ON DELETE CASCADE
    )");
    echo "<p class='success'>Cart table created successfully.</p>";
    
    // Create CartItem table
    $conn->exec("CREATE TABLE IF NOT EXISTS CartItem (
        CartItemId INT AUTO_INCREMENT PRIMARY KEY,
        CartId VARCHAR(64) NOT NULL,
        ProductNo INT NOT NULL,
        Quantity INT NOT NULL DEFAULT 1,
        DateAdded DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (CartId) REFERENCES Cart(CartId) ON DELETE CASCADE,
        FOREIGN KEY (ProductNo) REFERENCES Product(ProductNo) ON DELETE CASCADE,
        UNIQUE KEY (CartId, ProductNo)
    )");
    echo "<p class='success'>CartItem table created successfully.</p>";
    
    // Insert test data
    
    // Test Customers
    $password_hash = password_hash('password123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO Customer (CustEmail, CustFName, CustLName, Title, Address, City, State, Country, PostCode, Phone, CustPassword) VALUES 
        ('admin@artify.com', 'Admin', 'User', 'Mr', '123 Admin St', 'Admin City', 'Admin State', 'Country', '12345', '123-456-7890', :password),
        ('test@example.com', 'Test', 'User', 'Ms', '456 Test Ave', 'Test City', 'Test State', 'Country', '54321', '987-654-3210', :password),
        ('artist@example.com', 'Jane', 'Artist', 'Mrs', '789 Artist Blvd', 'Art City', 'Art State', 'Country', '67890', '555-123-4567', :password)");
    $stmt->bindParam(':password', $password_hash);
    $stmt->execute();
    echo "<p class='success'>Test customers added successfully.</p>";
    
    // Test Categories
    $conn->exec("INSERT INTO Category (Name, Description) VALUES 
        ('Painting', 'Original paintings and artwork'),
        ('Sculpture', 'Three-dimensional art pieces'),
        ('Print', 'High-quality art prints'),
        ('Photography', 'Fine art photography'),
        ('Mixed Media', 'Art combining multiple techniques')");
    echo "<p class='success'>Test categories added successfully.</p>";
    
    // Test Products
    $conn->exec("INSERT INTO Product (Description, Price, Category, Colour, Size, ImagePath, Featured, Stock) VALUES
        ('Abstract Sunset Painting', 299.99, 'Painting', 'Multi', '24x36', 'images/products/abstract_sunset.jpg', TRUE, 5),
        ('Blue Ocean Sculpture', 499.99, 'Sculpture', 'Blue', '12x12x20', 'images/products/ocean_sculpture.jpg', TRUE, 3),
        ('Floral Canvas Art', 199.99, 'Painting', 'Green', '18x24', 'images/products/floral_canvas.jpg', FALSE, 8),
        ('Bronze Figurine', 349.99, 'Sculpture', 'Bronze', '8x5x12', 'images/products/bronze_figurine.jpg', FALSE, 4),
        ('City Skyline Print', 149.99, 'Print', 'Gray', '24x36', 'images/products/city_skyline.jpg', TRUE, 15),
        ('Abstract Geometric Canvas', 279.99, 'Painting', 'Multi', '30x30', 'images/products/geometric_canvas.jpg', FALSE, 6),
        ('Handcrafted Wood Carving', 399.99, 'Sculpture', 'Brown', '12x8x5', 'images/products/wood_carving.jpg', FALSE, 2),
        ('Wildlife Photography Print', 129.99, 'Photography', 'Color', '20x30', 'images/products/wildlife_photo.jpg', TRUE, 10)");
    echo "<p class='success'>Test products added successfully.</p>";
    
    // Test Purchases (Orders)
    $conn->exec("INSERT INTO Purchase (Date, CustEmail, Status, TotalAmount, ShippingAddress, BillingAddress, PaymentMethod) VALUES 
        (NOW(), 'test@example.com', 'Delivered', 499.98, '456 Test Ave, Test City, Test State, 54321', '456 Test Ave, Test City, Test State, 54321', 'Credit Card'),
        (NOW(), 'test@example.com', 'Processing', 349.99, '456 Test Ave, Test City, Test State, 54321', '456 Test Ave, Test City, Test State, 54321', 'PayPal')");
    echo "<p class='success'>Test purchases added successfully.</p>";
    
    // Test Purchase Items
    $conn->exec("INSERT INTO PurchaseItem (Quantity, PurchaseNo, ProductNo, Price, Subtotal) VALUES 
        (1, 1, 1, 299.99, 299.99),
        (1, 1, 3, 199.99, 199.99),
        (1, 2, 4, 349.99, 349.99)");
    echo "<p class='success'>Test purchase items added successfully.</p>";
    
    // Test News
    $conn->exec("INSERT INTO News (Title, Text, Content, Summary, Author, Date) VALUES
        ('Grand Opening of Artify Online Store', 'We are excited to announce the launch of our new online store!', 'We are excited to announce the launch of our new online store! Now you can browse and purchase our artwork from the comfort of your home. Check out our latest collections and enjoy fast shipping to your doorstep.', 'Artify launches online art store with home delivery', 'Artify Team', CURRENT_DATE),
        ('Summer Art Collection Arriving Soon', 'Our new summer collection is arriving next month!', 'Our new summer collection is arriving next month! Featuring bright, vibrant pieces perfect for adding color to your home or office. Pre-orders will be available starting next week.', 'New summer art collection coming soon with pre-order options', 'Jane Smith', DATE_ADD(CURRENT_DATE, INTERVAL 5 DAY))");
    echo "<p class='success'>Test news added successfully.</p>";
    
    // Test Testimonials
    $conn->exec("INSERT INTO Testimonial (Name, Email, Rating, Text, Approved) VALUES
        ('John D.', 'john@example.com', 5, 'I absolutely love the abstract painting I purchased. The colors are even more vibrant in person!', TRUE),
        ('Sarah M.', 'sarah@example.com', 4, 'Great customer service and fast shipping. Will definitely shop here again.', TRUE),
        ('Michael R.', 'michael@example.com', 5, 'The sculpture I bought is now the centerpiece of my living room. Exquisite craftsmanship!', TRUE)");
    echo "<p class='success'>Test testimonials added successfully.</p>";
    
    // Test Admin
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO Admin (Username, Password, Email, Name, Role) VALUES 
        ('admin', :password, 'admin@artify.com', 'Admin User', 'Administrator')");
    $stmt->bindParam(':password', $admin_password);
    $stmt->execute();
    echo "<p class='success'>Test admin added successfully.</p>";
    
    // Test Cart
    $conn->exec("INSERT INTO Cart (CartId, CustEmail, CreatedAt) VALUES 
        ('cart123', 'test@example.com', NOW())");
    echo "<p class='success'>Test cart added successfully.</p>";
    
    // Test Cart Items
    $conn->exec("INSERT INTO CartItem (CartId, ProductNo, Quantity) VALUES 
        ('cart123', 1, 2),
        ('cart123', 5, 1)");
    echo "<p class='success'>Test cart items added successfully.</p>";
    
    echo "<h2 class='success'>All test data loaded successfully!</h2>";
    echo "<p>You can now run the test_database.php script to test CRUD operations.</p>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='test_database.php' class='button button-blue'>Run Database Tests</a>";
    echo "<a href='../application_test_guide.php' class='button'>Return to Test Guide</a>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<h2 class='error'>Error:</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='../troubleshooting.php' class='button' style='background-color: #ff9800;'>Go to Troubleshooting Guide</a>";
    echo "</div>";
}
?>
</body>
</html>
