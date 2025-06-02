<?php
// Load configuration
require_once '../config/config.php';

// Create database connection
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Artify Database Update</h2>";
    
    // Updates to Customer table
    try {
        // Check if CustPassword column exists
        $stmt = $conn->query("SHOW COLUMNS FROM Customer LIKE 'CustPassword'");
        if ($stmt->rowCount() == 0) {
            $conn->exec("ALTER TABLE Customer ADD COLUMN CustPassword VARCHAR(255)");
            echo "<p>Added CustPassword column to Customer table</p>";
        }
        
        // Make address fields optional
        $conn->exec("ALTER TABLE Customer MODIFY Address VARCHAR(255) NULL");
        $conn->exec("ALTER TABLE Customer MODIFY City VARCHAR(50) NULL");
        $conn->exec("ALTER TABLE Customer MODIFY State VARCHAR(50) NULL");
        $conn->exec("ALTER TABLE Customer MODIFY Country VARCHAR(50) NULL");
        $conn->exec("ALTER TABLE Customer MODIFY PostCode VARCHAR(20) NULL");
        $conn->exec("ALTER TABLE Customer MODIFY Phone VARCHAR(20) NULL");
        echo "<p>Made address fields optional in Customer table</p>";
    } catch (PDOException $e) {
        echo "<p>Error updating Customer table: " . $e->getMessage() . "</p>";
    }
    
    // Updates to Product table
    try {
        // Add new columns to Product table
        $productColumns = [
            "Stock" => "INT DEFAULT 10",
            "DateAdded" => "DATETIME DEFAULT CURRENT_TIMESTAMP",
            "Featured" => "BOOLEAN DEFAULT FALSE"
        ];
        
        foreach ($productColumns as $colName => $colDef) {
            $stmt = $conn->query("SHOW COLUMNS FROM Product LIKE '$colName'");
            if ($stmt->rowCount() == 0) {
                $conn->exec("ALTER TABLE Product ADD COLUMN $colName $colDef");
                echo "<p>Added $colName column to Product table</p>";
            }
        }
    } catch (PDOException $e) {
        echo "<p>Error updating Product table: " . $e->getMessage() . "</p>";
    }
    
    // Updates to Purchase table
    try {
        // Add new columns to Purchase table
        $purchaseColumns = [
            "TotalAmount" => "DECIMAL(10, 2) DEFAULT 0",
            "ShippingAddress" => "VARCHAR(255)",
            "BillingAddress" => "VARCHAR(255)",
            "PaymentMethod" => "VARCHAR(50)",
            "TrackingNumber" => "VARCHAR(50)",
            "Notes" => "TEXT"
        ];
        
        foreach ($purchaseColumns as $colName => $colDef) {
            $stmt = $conn->query("SHOW COLUMNS FROM Purchase LIKE '$colName'");
            if ($stmt->rowCount() == 0) {
                $conn->exec("ALTER TABLE Purchase ADD COLUMN $colName $colDef");
                echo "<p>Added $colName column to Purchase table</p>";
            }
        }
        
        // Update foreign key to use CASCADE
        try {
            $conn->exec("ALTER TABLE Purchase DROP FOREIGN KEY Purchase_ibfk_1");
            $conn->exec("ALTER TABLE Purchase ADD CONSTRAINT Purchase_ibfk_1 
                         FOREIGN KEY (CustEmail) REFERENCES Customer(CustEmail) ON DELETE CASCADE");
            echo "<p>Updated foreign key in Purchase table</p>";
        } catch (PDOException $e) {
            // Foreign key might already be correct
        }
    } catch (PDOException $e) {
        echo "<p>Error updating Purchase table: " . $e->getMessage() . "</p>";
    }
    
    // Updates to PurchaseItem table
    try {
        // Add new columns to PurchaseItem table
        $purchaseItemColumns = [
            "Price" => "DECIMAL(10, 2) NOT NULL DEFAULT 0",
            "Subtotal" => "DECIMAL(10, 2) NOT NULL DEFAULT 0"
        ];
        
        foreach ($purchaseItemColumns as $colName => $colDef) {
            $stmt = $conn->query("SHOW COLUMNS FROM PurchaseItem LIKE '$colName'");
            if ($stmt->rowCount() == 0) {
                $conn->exec("ALTER TABLE PurchaseItem ADD COLUMN $colName $colDef");
                echo "<p>Added $colName column to PurchaseItem table</p>";
            }
        }
        
        // Update foreign keys to use CASCADE and RESTRICT
        try {
            $conn->exec("ALTER TABLE PurchaseItem DROP FOREIGN KEY PurchaseItem_ibfk_1");
            $conn->exec("ALTER TABLE PurchaseItem DROP FOREIGN KEY PurchaseItem_ibfk_2");
            $conn->exec("ALTER TABLE PurchaseItem ADD CONSTRAINT PurchaseItem_ibfk_1 
                         FOREIGN KEY (PurchaseNo) REFERENCES Purchase(PurchaseNo) ON DELETE CASCADE");
            $conn->exec("ALTER TABLE PurchaseItem ADD CONSTRAINT PurchaseItem_ibfk_2 
                         FOREIGN KEY (ProductNo) REFERENCES Product(ProductNo) ON DELETE RESTRICT");
            echo "<p>Updated foreign keys in PurchaseItem table</p>";
        } catch (PDOException $e) {
            // Foreign keys might already be correct
        }
    } catch (PDOException $e) {
        echo "<p>Error updating PurchaseItem table: " . $e->getMessage() . "</p>";
    }
    
    // Updates to News table
    try {
        // Add new columns to News table
        $newsColumns = [
            "Content" => "TEXT",
            "Summary" => "VARCHAR(255)",
            "Author" => "VARCHAR(100)",
            "ImagePath" => "VARCHAR(255)"
        ];
        
        foreach ($newsColumns as $colName => $colDef) {
            $stmt = $conn->query("SHOW COLUMNS FROM News LIKE '$colName'");
            if ($stmt->rowCount() == 0) {
                $conn->exec("ALTER TABLE News ADD COLUMN $colName $colDef");
                echo "<p>Added $colName column to News table</p>";
            }
        }
    } catch (PDOException $e) {
        echo "<p>Error updating News table: " . $e->getMessage() . "</p>";
    }
    
    // Updates to Admin table
    try {
        // Add new columns to Admin table
        $adminColumns = [
            "Role" => "VARCHAR(50) DEFAULT 'Editor'",
            "LastLogin" => "DATETIME"
        ];
        
        foreach ($adminColumns as $colName => $colDef) {
            $stmt = $conn->query("SHOW COLUMNS FROM Admin LIKE '$colName'");
            if ($stmt->rowCount() == 0) {
                $conn->exec("ALTER TABLE Admin ADD COLUMN $colName $colDef");
                echo "<p>Added $colName column to Admin table</p>";
            }
        }
    } catch (PDOException $e) {
        echo "<p>Error updating Admin table: " . $e->getMessage() . "</p>";
    }
    
    // Create new tables if they don't exist
    
    // Category table
    try {
        $stmt = $conn->query("SHOW TABLES LIKE 'Category'");
        if ($stmt->rowCount() == 0) {
            $conn->exec("CREATE TABLE IF NOT EXISTS Category (
                CategoryId INT AUTO_INCREMENT PRIMARY KEY,
                Name VARCHAR(50) NOT NULL UNIQUE,
                Description TEXT,
                Parent INT,
                FOREIGN KEY (Parent) REFERENCES Category(CategoryId) ON DELETE SET NULL
            )");
            
            // Insert default categories
            $conn->exec("INSERT INTO Category (Name, Description) VALUES
                ('Painting', 'Original paintings and artwork'),
                ('Sculpture', 'Three-dimensional art pieces'),
                ('Print', 'High-quality art prints'),
                ('Photography', 'Fine art photography'),
                ('Mixed Media', 'Art combining multiple techniques')");
                
            echo "<p>Created Category table and added default categories</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error creating Category table: " . $e->getMessage() . "</p>";
    }
    
    // Cart table
    try {
        $stmt = $conn->query("SHOW TABLES LIKE 'Cart'");
        if ($stmt->rowCount() == 0) {
            $conn->exec("CREATE TABLE IF NOT EXISTS Cart (
                CartId VARCHAR(64) PRIMARY KEY,
                CustEmail VARCHAR(100),
                CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (CustEmail) REFERENCES Customer(CustEmail) ON DELETE CASCADE
            )");
            echo "<p>Created Cart table</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error creating Cart table: " . $e->getMessage() . "</p>";
    }
    
    // CartItem table
    try {
        $stmt = $conn->query("SHOW TABLES LIKE 'CartItem'");
        if ($stmt->rowCount() == 0) {
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
            echo "<p>Created CartItem table</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error creating CartItem table: " . $e->getMessage() . "</p>";
    }
    
    echo "<p>Database update completed successfully!</p>";
    
} catch(PDOException $e) {
    echo "<p>Connection error: " . $e->getMessage() . "</p>";
}

// Close connection
$conn = null;
?>
