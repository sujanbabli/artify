<?php
/**
 * Database Test Script
 * 
 * This script tests all CRUD operations on all tables.
 * Run load_test_data.php before running this script.
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
    <title>Artify Database Tests</title>
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
        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 20px 0;
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
        .note {
            color: blue;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../includes/navbar.php'; ?>

    <h1>Artify Database Tests</h1>
    
    <div style="margin: 20px 0;">
        <a href="load_test_data.php" class="button">Reload Test Data</a>
        <a href="../application_test_guide.php" class="button button-blue">Return to Test Guide</a>
    </div>

<?php
// Initialize test results
$tests_run = 0;
$tests_passed = 0;

function run_test($name, $test_function) {
    global $tests_run, $tests_passed;
    $tests_run++;
    
    echo "<h3>Test: $name</h3>";
    try {
        $result = $test_function();
        if ($result === true) {
            echo "<p style='color:green'>✓ PASSED</p>";
            $tests_passed++;
        } else {
            echo "<p style='color:red'>✗ FAILED: $result</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ FAILED WITH EXCEPTION: " . $e->getMessage() . "</p>";
    }
    echo "<hr>";
}

try {
    // Connect to the database
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>Connected to database successfully.</p>";
    
    // Test Customer CRUD Operations
    run_test("Create Customer", function() use ($conn) {
        $password_hash = password_hash('newpassword', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Customer (CustEmail, CustFName, CustLName, Title, Address, City, State, Country, PostCode, Phone, CustPassword) 
            VALUES ('new@example.com', 'New', 'Customer', 'Mr', '123 New St', 'New City', 'New State', 'Country', '12345', '123-456-7890', :password)");
        $stmt->bindParam(':password', $password_hash);
        $result = $stmt->execute();
        
        if (!$result) return "Failed to insert customer";
        
        return true;
    });
    
    run_test("Read Customer", function() use ($conn) {
        $stmt = $conn->prepare("SELECT * FROM Customer WHERE CustEmail = 'new@example.com'");
        $stmt->execute();
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$customer) return "Failed to read customer";
        if ($customer['CustFName'] !== 'New') return "Customer first name mismatch";
        if ($customer['CustLName'] !== 'Customer') return "Customer last name mismatch";
        
        return true;
    });
    
    run_test("Update Customer", function() use ($conn) {
        $stmt = $conn->prepare("UPDATE Customer SET CustFName = 'Updated', City = 'Updated City' WHERE CustEmail = 'new@example.com'");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to update customer";
        
        $stmt = $conn->prepare("SELECT CustFName, City FROM Customer WHERE CustEmail = 'new@example.com'");
        $stmt->execute();
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($customer['CustFName'] !== 'Updated') return "Customer update failed";
        if ($customer['City'] !== 'Updated City') return "Customer city update failed";
        
        return true;
    });
    
    run_test("Delete Customer", function() use ($conn) {
        $stmt = $conn->prepare("DELETE FROM Customer WHERE CustEmail = 'new@example.com'");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to delete customer";
        
        if ($stmt->rowCount() === 0) return "No rows affected by delete operation";
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Customer WHERE CustEmail = 'new@example.com'");
        $stmt->execute();
        $count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count !== 0) return "Customer was not deleted (count = $count)";
        
        return true;
    });
    
    // Test Category CRUD Operations
    run_test("Create Category", function() use ($conn) {
        $stmt = $conn->prepare("INSERT INTO Category (Name, Description) VALUES ('Test Category', 'A test category')");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to insert category";
        
        return true;
    });
    
    run_test("Read Category", function() use ($conn) {
        $stmt = $conn->prepare("SELECT * FROM Category WHERE Name = 'Test Category'");
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$category) return "Failed to read category";
        if ($category['Description'] !== 'A test category') return "Category description mismatch";
        
        return true;
    });
    
    run_test("Update Category", function() use ($conn) {
        $stmt = $conn->prepare("UPDATE Category SET Description = 'Updated description' WHERE Name = 'Test Category'");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to update category";
        
        $stmt = $conn->prepare("SELECT Description FROM Category WHERE Name = 'Test Category'");
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($category['Description'] !== 'Updated description') return "Category update failed";
        
        return true;
    });
    
    run_test("Delete Category", function() use ($conn) {
        $stmt = $conn->prepare("SELECT CategoryId FROM Category WHERE Name = 'Test Category'");
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$category) return "Category not found for deletion";
        $category_id = $category['CategoryId'];
        
        // Delete the category
        $stmt = $conn->prepare("DELETE FROM Category WHERE CategoryId = :category_id");
        $stmt->bindParam(':category_id', $category_id);
        $result = $stmt->execute();
        
        if (!$result) return "Failed to delete category";
        
        if ($stmt->rowCount() === 0) return "No rows affected by delete operation";
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Category WHERE CategoryId = :category_id");
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        $count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count !== 0) return "Category was not deleted (count = $count)";
        
        return true;
    });
    
    // Test Product CRUD Operations
    run_test("Create Product", function() use ($conn) {
        $stmt = $conn->prepare("INSERT INTO Product (Description, Price, Category, Colour, Size, ImagePath, Stock) 
            VALUES ('Test Product', 99.99, 'Painting', 'Red', '10x12', 'images/products/test_product.jpg', 10)");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to insert product";
        
        return true;
    });
    
    run_test("Read Product", function() use ($conn) {
        $stmt = $conn->prepare("SELECT * FROM Product WHERE Description = 'Test Product'");
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) return "Failed to read product";
        if ($product['Price'] != 99.99) return "Product price mismatch";
        if ($product['Category'] !== 'Painting') return "Product category mismatch";
        
        return true;
    });
    
    run_test("Update Product", function() use ($conn) {
        $stmt = $conn->prepare("UPDATE Product SET Price = 129.99, Stock = 15 WHERE Description = 'Test Product'");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to update product";
        
        $stmt = $conn->prepare("SELECT Price, Stock FROM Product WHERE Description = 'Test Product'");
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product['Price'] != 129.99) return "Product price update failed";
        if ($product['Stock'] != 15) return "Product stock update failed";
        
        return true;
    });
    
    run_test("Delete Product", function() use ($conn) {
        $stmt = $conn->prepare("SELECT ProductNo FROM Product WHERE Description = 'Test Product'");
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) return "Product not found for deletion";
        $product_id = $product['ProductNo'];
        
        // Delete the product
        $stmt = $conn->prepare("DELETE FROM Product WHERE ProductNo = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $result = $stmt->execute();
        
        if (!$result) return "Failed to delete product";
        
        if ($stmt->rowCount() === 0) return "No rows affected by delete operation";
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Product WHERE ProductNo = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count !== 0) return "Product was not deleted (count = $count)";
        
        return true;
    });
    
    // Test Purchase and PurchaseItem CRUD Operations
    run_test("Create Purchase with Items", function() use ($conn) {
        // Begin transaction to ensure both purchase and items are added
        $conn->beginTransaction();
        
        try {
            // Check if we have a customer
            $stmt = $conn->prepare("SELECT CustEmail FROM Customer WHERE CustEmail = 'test@example.com'");
            $stmt->execute();
            if (!$stmt->fetch()) {
                echo "<p class='note'>Customer doesn't exist, creating one for the test...</p>";
                $password_hash = password_hash('password123', PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO Customer (CustEmail, CustFName, CustLName, Title, Address, City, State, Country, PostCode, Phone, CustPassword) 
                    VALUES ('test@example.com', 'Test', 'User', 'Ms', '456 Test Ave', 'Test City', 'Test State', 'Country', '54321', '987-654-3210', :password)");
                $stmt->bindParam(':password', $password_hash);
                $stmt->execute();
            }
            
            // Check if we have a product
            $stmt = $conn->prepare("SELECT ProductNo FROM Product LIMIT 1");
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                echo "<p class='note'>No products exist, creating one for the test...</p>";
                $stmt = $conn->prepare("INSERT INTO Product (Description, Price, Category, Colour, Size, ImagePath, Stock) 
                    VALUES ('Test Product for Purchase', 199.99, 'Painting', 'Blue', '20x30', 'images/products/test_purchase.jpg', 5)");
                $stmt->execute();
                $product_id = $conn->lastInsertId();
            } else {
                $product_id = $product['ProductNo'];
            }
            
            // Insert purchase
            $stmt = $conn->prepare("INSERT INTO Purchase (Date, CustEmail, Status, TotalAmount, ShippingAddress, BillingAddress, PaymentMethod) 
                VALUES (NOW(), 'test@example.com', 'Pending', 199.99, 'Test Address', 'Test Address', 'Credit Card')");
            $stmt->execute();
            $purchase_id = $conn->lastInsertId();
            
            // Insert purchase item
            $stmt = $conn->prepare("INSERT INTO PurchaseItem (Quantity, PurchaseNo, ProductNo, Price, Subtotal) 
                VALUES (1, :purchase_id, :product_id, 199.99, 199.99)");
            $stmt->bindParam(':purchase_id', $purchase_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            return "Transaction failed: " . $e->getMessage();
        }
    });
    
    run_test("Read Purchase with Items", function() use ($conn) {
        $stmt = $conn->prepare("
            SELECT p.*, pi.ProductNo, pi.Quantity, pi.Price as ItemPrice 
            FROM Purchase p
            JOIN PurchaseItem pi ON p.PurchaseNo = pi.PurchaseNo
            WHERE p.ShippingAddress = 'Test Address'
            LIMIT 1
        ");
        $stmt->execute();
        $purchase = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$purchase) return "Failed to read purchase with items";
        if ($purchase['TotalAmount'] != 199.99) return "Purchase total amount mismatch";
        if ($purchase['Quantity'] != 1) return "Purchase item quantity mismatch";
        
        return true;
    });
    
    run_test("Update Purchase", function() use ($conn) {
        $stmt = $conn->prepare("UPDATE Purchase SET Status = 'Processing' WHERE ShippingAddress = 'Test Address'");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to update purchase";
        
        $stmt = $conn->prepare("SELECT Status FROM Purchase WHERE ShippingAddress = 'Test Address'");
        $stmt->execute();
        $purchase = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($purchase['Status'] !== 'Processing') return "Purchase update failed";
        
        return true;
    });
    
    run_test("Delete Purchase and Items (Cascade)", function() use ($conn) {
        // Get the purchase ID
        $stmt = $conn->prepare("SELECT PurchaseNo FROM Purchase WHERE ShippingAddress = 'Test Address'");
        $stmt->execute();
        $purchase = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$purchase) return "Purchase not found for deletion";
        $purchase_id = $purchase['PurchaseNo'];
        
        // Count purchase items before deletion
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM PurchaseItem WHERE PurchaseNo = :purchase_id");
        $stmt->bindParam(':purchase_id', $purchase_id);
        $stmt->execute();
        $items_before = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "<p class='note'>Found {$items_before} purchase item(s) that will be deleted by cascade.</p>";
        
        // Delete purchase (should cascade to purchase items)
        $stmt = $conn->prepare("DELETE FROM Purchase WHERE PurchaseNo = :purchase_id");
        $stmt->bindParam(':purchase_id', $purchase_id);
        $result = $stmt->execute();
        
        if (!$result) return "Failed to delete purchase";
        
        if ($stmt->rowCount() === 0) return "No rows affected by delete operation";
        
        // Check if purchase was deleted
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Purchase WHERE PurchaseNo = :purchase_id");
        $stmt->bindParam(':purchase_id', $purchase_id);
        $stmt->execute();
        $purchase_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($purchase_count !== 0) return "Purchase was not deleted (count = $purchase_count)";
        
        // Check if purchase items were deleted (cascade)
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM PurchaseItem WHERE PurchaseNo = :purchase_id");
        $stmt->bindParam(':purchase_id', $purchase_id);
        $stmt->execute();
        $items_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($items_count !== 0) return "Purchase items were not deleted (cascade failed)";
        
        return true;
    });
    
    // Test News CRUD Operations
    run_test("Create News", function() use ($conn) {
        $stmt = $conn->prepare("INSERT INTO News (Title, Text, Content, Summary, Author, Date) 
            VALUES ('Test News', 'Test news text', 'Test news content', 'Test news summary', 'Test Author', CURRENT_DATE)");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to insert news";
        
        return true;
    });
    
    run_test("Read News", function() use ($conn) {
        $stmt = $conn->prepare("SELECT * FROM News WHERE Title = 'Test News'");
        $stmt->execute();
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$news) return "Failed to read news";
        if ($news['Author'] !== 'Test Author') return "News author mismatch";
        
        return true;
    });
    
    run_test("Update News", function() use ($conn) {
        $stmt = $conn->prepare("UPDATE News SET Text = 'Updated text', Author = 'Updated Author' WHERE Title = 'Test News'");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to update news";
        
        $stmt = $conn->prepare("SELECT Text, Author FROM News WHERE Title = 'Test News'");
        $stmt->execute();
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($news['Text'] !== 'Updated text') return "News text update failed";
        if ($news['Author'] !== 'Updated Author') return "News author update failed";
        
        return true;
    });
    
    run_test("Delete News", function() use ($conn) {
        $stmt = $conn->prepare("SELECT NewsNo FROM News WHERE Title = 'Test News'");
        $stmt->execute();
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$news) return "News not found for deletion";
        $news_id = $news['NewsNo'];
        
        // Delete the news
        $stmt = $conn->prepare("DELETE FROM News WHERE NewsNo = :news_id");
        $stmt->bindParam(':news_id', $news_id);
        $result = $stmt->execute();
        
        if (!$result) return "Failed to delete news";
        
        if ($stmt->rowCount() === 0) return "No rows affected by delete operation";
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM News WHERE NewsNo = :news_id");
        $stmt->bindParam(':news_id', $news_id);
        $stmt->execute();
        $count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count !== 0) return "News was not deleted (count = $count)";
        
        return true;
    });
    
    // Test Testimonial CRUD Operations
    run_test("Create Testimonial", function() use ($conn) {
        $stmt = $conn->prepare("INSERT INTO Testimonial (Name, Email, Rating, Text, Approved) 
            VALUES ('Test User', 'testuser@example.com', 5, 'This is a test testimonial', FALSE)");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to insert testimonial";
        
        return true;
    });
    
    run_test("Read Testimonial", function() use ($conn) {
        $stmt = $conn->prepare("SELECT * FROM Testimonial WHERE Email = 'testuser@example.com'");
        $stmt->execute();
        $testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$testimonial) return "Failed to read testimonial";
        if ($testimonial['Rating'] != 5) return "Testimonial rating mismatch";
        if ($testimonial['Text'] !== 'This is a test testimonial') return "Testimonial text mismatch";
        
        return true;
    });
    
    run_test("Update Testimonial", function() use ($conn) {
        $stmt = $conn->prepare("UPDATE Testimonial SET Rating = 4, Approved = TRUE WHERE Email = 'testuser@example.com'");
        $result = $stmt->execute();
        
        if (!$result) return "Failed to update testimonial";
        
        $stmt = $conn->prepare("SELECT Rating, Approved FROM Testimonial WHERE Email = 'testuser@example.com'");
        $stmt->execute();
        $testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($testimonial['Rating'] != 4) return "Testimonial rating update failed";
        if (!$testimonial['Approved']) return "Testimonial approved update failed";
        
        return true;
    });
    
    run_test("Delete Testimonial", function() use ($conn) {
        $stmt = $conn->prepare("SELECT TestimonialNo FROM Testimonial WHERE Email = 'testuser@example.com'");
        $stmt->execute();
        $testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$testimonial) return "Testimonial not found for deletion";
        $testimonial_id = $testimonial['TestimonialNo'];
        
        // Delete the testimonial
        $stmt = $conn->prepare("DELETE FROM Testimonial WHERE TestimonialNo = :testimonial_id");
        $stmt->bindParam(':testimonial_id', $testimonial_id);
        $result = $stmt->execute();
        
        if (!$result) return "Failed to delete testimonial";
        
        if ($stmt->rowCount() === 0) return "No rows affected by delete operation";
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Testimonial WHERE TestimonialNo = :testimonial_id");
        $stmt->bindParam(':testimonial_id', $testimonial_id);
        $stmt->execute();
        $count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count !== 0) return "Testimonial was not deleted (count = $count)";
        
        return true;
    });
    
    // Test Cart and CartItem CRUD Operations
    run_test("Create Cart with Items", function() use ($conn) {
        // Begin transaction
        $conn->beginTransaction();
        
        try {
            // Generate a cart ID
            $cart_id = 'test_cart_' . time();
            
            // Insert cart
            $stmt = $conn->prepare("INSERT INTO Cart (CartId, CustEmail, CreatedAt) 
                VALUES (:cart_id, 'test@example.com', NOW())");
            $stmt->bindParam(':cart_id', $cart_id);
            $stmt->execute();
            
            // Get a product ID
            $stmt = $conn->prepare("SELECT ProductNo FROM Product LIMIT 1");
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                echo "<p class='note'>No products exist, creating one for the test...</p>";
                $stmt = $conn->prepare("INSERT INTO Product (Description, Price, Category, Colour, Size, ImagePath, Stock) 
                    VALUES ('Test Product for Cart', 149.99, 'Print', 'Red', '15x20', 'images/products/test_cart.jpg', 5)");
                $stmt->execute();
                $product_id = $conn->lastInsertId();
            } else {
                $product_id = $product['ProductNo'];
            }
            
            // Insert cart item
            $stmt = $conn->prepare("INSERT INTO CartItem (CartId, ProductNo, Quantity) 
                VALUES (:cart_id, :product_id, 2)");
            $stmt->bindParam(':cart_id', $cart_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            
            $conn->commit();
            
            // Store the cart ID for other tests
            $GLOBALS['test_cart_id'] = $cart_id;
            $GLOBALS['test_product_id'] = $product_id;
            
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            return "Transaction failed: " . $e->getMessage();
        }
    });
    
    run_test("Read Cart with Items", function() use ($conn) {
        if (!isset($GLOBALS['test_cart_id'])) return "Test cart ID not found";
        $cart_id = $GLOBALS['test_cart_id'];
        
        $stmt = $conn->prepare("
            SELECT c.*, ci.ProductNo, ci.Quantity
            FROM Cart c
            JOIN CartItem ci ON c.CartId = ci.CartId
            WHERE c.CartId = :cart_id
        ");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cart) return "Failed to read cart with items";
        if ($cart['Quantity'] != 2) return "Cart item quantity mismatch";
        
        return true;
    });
    
    run_test("Update Cart Item", function() use ($conn) {
        if (!isset($GLOBALS['test_cart_id'])) return "Test cart ID not found";
        if (!isset($GLOBALS['test_product_id'])) return "Test product ID not found";
        
        $cart_id = $GLOBALS['test_cart_id'];
        $product_id = $GLOBALS['test_product_id'];
        
        $stmt = $conn->prepare("UPDATE CartItem SET Quantity = 3 WHERE CartId = :cart_id AND ProductNo = :product_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':product_id', $product_id);
        $result = $stmt->execute();
        
        if (!$result) return "Failed to update cart item";
        
        $stmt = $conn->prepare("SELECT Quantity FROM CartItem WHERE CartId = :cart_id AND ProductNo = :product_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cart_item['Quantity'] != 3) return "Cart item quantity update failed";
        
        return true;
    });
    
    run_test("Delete Cart and Items (Cascade)", function() use ($conn) {
        if (!isset($GLOBALS['test_cart_id'])) return "Test cart ID not found";
        $cart_id = $GLOBALS['test_cart_id'];
        
        // Count cart items before deletion
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM CartItem WHERE CartId = :cart_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
        $items_before = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "<p class='note'>Found {$items_before} cart item(s) that will be deleted by cascade.</p>";
        
        // Delete cart (should cascade to cart items)
        $stmt = $conn->prepare("DELETE FROM Cart WHERE CartId = :cart_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $result = $stmt->execute();
        
        if (!$result) return "Failed to delete cart";
        
        if ($stmt->rowCount() === 0) return "No rows affected by delete operation";
        
        // Check if cart was deleted
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Cart WHERE CartId = :cart_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
        $cart_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($cart_count !== 0) return "Cart was not deleted (count = $cart_count)";
        
        // Check if cart items were deleted (cascade)
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM CartItem WHERE CartId = :cart_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
        $items_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($items_count !== 0) return "Cart items were not deleted (cascade failed)";
        
        return true;
    });
    
    // Test Foreign Key Constraint
    run_test("Foreign Key Constraint: PurchaseItem to Product", function() use ($conn) {
        try {
            // Try to add a purchase item with a non-existent product_id
            $stmt = $conn->prepare("INSERT INTO PurchaseItem (Quantity, PurchaseNo, ProductNo, Price, Subtotal) 
                VALUES (1, 1, 9999, 99.99, 99.99)");
            $stmt->execute();
            
            return "Foreign key constraint failed - was able to add purchase item with invalid product ID";
        } catch (PDOException $e) {
            // This should fail with a constraint violation
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return true;
            } else {
                return "Unexpected error: " . $e->getMessage();
            }
        }
    });
    
    // Summary
    echo "<h2>Test Summary:</h2>";
    echo "<p>Tests Run: $tests_run</p>";
    echo "<p>Tests Passed: $tests_passed</p>";
    
    if ($tests_run === $tests_passed) {
        echo "<p style='color:green'>All tests passed! The database is working correctly.</p>";
    } else {
        echo "<p style='color:red'>Some tests failed. Please review the results above.</p>";
        echo "<a href='../troubleshooting.php' class='button' style='background-color: #ff9800;'>Go to Troubleshooting Guide</a>";
    }
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='load_test_data.php' class='button'>Reload Test Data</a>";
    echo "<a href='../application_test_guide.php' class='button button-blue'>Return to Test Guide</a>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<h2>Database Connection Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    
    // Add specific help for foreign key constraint errors
    if (strpos($e->getMessage(), 'Integrity constraint violation: 1451') !== false) {
        echo "<div style='background-color: #fff3cd; padding: 15px; margin: 20px 0; border-left: 4px solid #ffc107;'>";
        echo "<h3>Foreign Key Constraint Error Detected</h3>";
        echo "<p>This error occurs when trying to delete a record that has related records in another table.</p>";
        echo "<p>For example, trying to delete a category that has products assigned to it.</p>";
        echo "<p><strong>Solution:</strong> Use our Foreign Key Fix utility to update the constraint to CASCADE:</p>";
        echo "<a href='../includes/foreign_key_fix.php' class='button' style='background-color: #ff9800;'>Fix Foreign Key Issues</a>";
        echo "</div>";
    }
    
    echo "<a href='../troubleshooting.php' class='button' style='background-color: #ff9800;'>Go to Troubleshooting Guide</a>";
}
?>
</body>
</html>
