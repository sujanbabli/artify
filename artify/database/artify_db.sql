-- Create Artify Database
CREATE DATABASE IF NOT EXISTS artify;
USE artify;

-- Create Customer table
CREATE TABLE IF NOT EXISTS Customer (
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
);

-- Create Product table
CREATE TABLE IF NOT EXISTS Product (
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
);

-- Create Purchase table (Orders)
CREATE TABLE IF NOT EXISTS Purchase (
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
);

-- Create PurchaseItem table (Order Items)
CREATE TABLE IF NOT EXISTS PurchaseItem (
    ItemNo INT AUTO_INCREMENT PRIMARY KEY,
    Quantity INT NOT NULL,
    PurchaseNo INT NOT NULL,
    ProductNo INT NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    Subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (PurchaseNo) REFERENCES Purchase(PurchaseNo) ON DELETE CASCADE,
    FOREIGN KEY (ProductNo) REFERENCES Product(ProductNo) ON DELETE RESTRICT
);

-- Create News table for company news/blog
CREATE TABLE IF NOT EXISTS News (
    NewsNo INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(100) NOT NULL,
    Text TEXT NOT NULL,
    Content TEXT NOT NULL,
    Summary VARCHAR(255),
    Author VARCHAR(100),
    ImagePath VARCHAR(255),
    Date DATE DEFAULT (CURRENT_DATE),
    Active BOOLEAN DEFAULT TRUE
);

-- Create Testimonial table
CREATE TABLE IF NOT EXISTS Testimonial (
    TestimonialNo INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    Rating INT NOT NULL DEFAULT 5,
    Text TEXT NOT NULL,
    Date DATETIME DEFAULT CURRENT_TIMESTAMP,
    Approved BOOLEAN DEFAULT FALSE
);

-- Create Admin table for admin login
CREATE TABLE IF NOT EXISTS Admin (
    AdminNo INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    Name VARCHAR(100) NOT NULL,
    Role VARCHAR(50) DEFAULT 'Editor',
    LastLogin DATETIME
);

-- Create Category table for product categories
CREATE TABLE IF NOT EXISTS Category (
    CategoryId INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(50) NOT NULL UNIQUE,
    Description TEXT,
    Parent INT,
    FOREIGN KEY (Parent) REFERENCES Category(CategoryId) ON DELETE SET NULL
);

-- Create Cart table for persistent shopping carts
CREATE TABLE IF NOT EXISTS Cart (
    CartId VARCHAR(64) PRIMARY KEY,
    CustEmail VARCHAR(100),
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (CustEmail) REFERENCES Customer(CustEmail) ON DELETE CASCADE
);

-- Create CartItem table for cart items
CREATE TABLE IF NOT EXISTS CartItem (
    CartItemId INT AUTO_INCREMENT PRIMARY KEY,
    CartId VARCHAR(64) NOT NULL,
    ProductNo INT NOT NULL,
    Quantity INT NOT NULL DEFAULT 1,
    DateAdded DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CartId) REFERENCES Cart(CartId) ON DELETE CASCADE,
    FOREIGN KEY (ProductNo) REFERENCES Product(ProductNo) ON DELETE CASCADE,
    UNIQUE KEY (CartId, ProductNo)
);

-- Insert sample admin user (password: admin123)
INSERT INTO Admin (Username, Password, Email, Name, Role) VALUES 
('admin', '$2y$10$8MYVGoVGfX/Zs1x.lmvk/OfKcLOKA7P.CGHtCPbAk3B3ZcHOAQMUm', 'admin@artify.com', 'Admin User', 'Administrator');

-- Insert product categories
INSERT INTO Category (Name, Description) VALUES
('Painting', 'Original paintings and artwork'),
('Sculpture', 'Three-dimensional art pieces'),
('Print', 'High-quality art prints'),
('Photography', 'Fine art photography'),
('Mixed Media', 'Art combining multiple techniques');

-- Insert sample product data
INSERT INTO Product (Description, Price, Category, Colour, Size, ImagePath, Featured, Stock) VALUES
('Abstract Sunset Painting', 299.99, 'Painting', 'Multi', '24x36', 'images/products/abstract_sunset.jpg', TRUE, 5),
('Blue Ocean Sculpture', 499.99, 'Sculpture', 'Blue', '12x12x20', 'images/products/ocean_sculpture.jpg', TRUE, 3),
('Floral Canvas Art', 199.99, 'Painting', 'Green', '18x24', 'images/products/floral_canvas.jpg', FALSE, 8),
('Bronze Figurine', 349.99, 'Sculpture', 'Bronze', '8x5x12', 'images/products/bronze_figurine.jpg', FALSE, 4),
('City Skyline Print', 149.99, 'Print', 'Gray', '24x36', 'images/products/city_skyline.jpg', TRUE, 15),
('Abstract Geometric Canvas', 279.99, 'Painting', 'Multi', '30x30', 'images/products/geometric_canvas.jpg', FALSE, 6),
('Handcrafted Wood Carving', 399.99, 'Sculpture', 'Brown', '12x8x5', 'images/products/wood_carving.jpg', FALSE, 2),
('Wildlife Photography Print', 129.99, 'Photography', 'Color', '20x30', 'images/products/wildlife_photo.jpg', TRUE, 10);

-- Insert sample news items
INSERT INTO News (Title, Text, Content, Summary, Author, Date) VALUES
('Grand Opening of Artify Online Store', 'We are excited to announce the launch of our new online store!', 'We are excited to announce the launch of our new online store! Now you can browse and purchase our artwork from the comfort of your home. Check out our latest collections and enjoy fast shipping to your doorstep.', 'Artify launches online art store with home delivery', 'Artify Team', CURRENT_DATE),
('Summer Art Collection Arriving Soon', 'Our new summer collection is arriving next month!', 'Our new summer collection is arriving next month! Featuring bright, vibrant pieces perfect for adding color to your home or office. Pre-orders will be available starting next week.', 'New summer art collection coming soon with pre-order options', 'Jane Smith', DATE_ADD(CURRENT_DATE, INTERVAL 5 DAY));

-- Insert sample testimonials
INSERT INTO Testimonial (Name, Email, Rating, Text, Approved) VALUES
('John D.', 'john@example.com', 5, 'I absolutely love the abstract painting I purchased. The colors are even more vibrant in person!', TRUE),
('Sarah M.', 'sarah@example.com', 4, 'Great customer service and fast shipping. Will definitely shop here again.', TRUE),
('Michael R.', 'michael@example.com', 5, 'The sculpture I bought is now the centerpiece of my living room. Exquisite craftsmanship!', TRUE);
