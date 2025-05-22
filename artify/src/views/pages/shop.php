<?php
// Get product controller
$productController = new ProductController();

// Get all categories for filter
$categories = $productController->getCategories();

// Get price range
$priceRange = $productController->getPriceRange();
$minPrice = floor($priceRange['min_price']);
$maxPrice = ceil($priceRange['max_price']);

// Get filter values from URL
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$minPriceFilter = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$maxPriceFilter = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Prepare filters array
$filters = [
    'search' => $search,
    'category' => $category,
    'min_price' => $minPriceFilter,
    'max_price' => $maxPriceFilter,
    'sort' => $sort
];

// Get filtered products
$products = $productController->searchProducts($filters);

// Determine page title
if ($category) {
    $pageTitle = ucfirst($category) . ' Collection';
} else {
    $pageTitle = 'All Artworks';
}

// Add search term to page title if present
if ($search) {
    $pageTitle = 'Search Results for "' . htmlspecialchars($search) . '"';
}
?>

<!-- Shop Header -->
<div class="container mt-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1><?php echo $pageTitle; ?></h1>
            <p>Browse our collection of unique Aboriginal artworks</p>
        </div>
    </div>
    
    <!-- Filter and Search Section -->
    <div class="card rounded-custom mb-4">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/public/index.php" method="GET" class="row g-3">
                <input type="hidden" name="page" value="shop">
                
                <!-- Search -->
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Search artwork..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-aboriginal" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat->Category); ?>" <?php echo ($category == $cat->Category) ? 'selected' : ''; ?>>
                                <?php echo ucfirst(htmlspecialchars($cat->Category)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Price Range Filter -->
                <div class="col-md-5">
                    <label class="form-label">Price Range ($<?php echo $minPrice; ?> - $<?php echo $maxPrice; ?>)</label>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($minPriceFilter); ?>" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($maxPriceFilter); ?>" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sort Order Filter -->
                <div class="col-md-4">
                    <label for="sort" class="form-label">Sort Order</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Newest Arrivals</option>
                        <option value="price_low_high" <?php echo ($sort == 'price_low_high') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high_low" <?php echo ($sort == 'price_high_low') ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                </div>
                
                <!-- Filter Apply/Reset Buttons -->
                <div class="col-12 d-flex justify-content-end mt-3">
                    <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-outline-secondary me-2">Reset Filters</a>
                    <button type="submit" class="btn btn-aboriginal">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Filter Tags -->
    <?php if ($search || $category || $minPriceFilter || $maxPriceFilter): ?>
    <div class="mb-4">
        <div class="d-flex flex-wrap gap-2">
            <?php if ($search): ?>
            <div class="badge bg-aboriginal p-2">
                Search: <?php echo htmlspecialchars($search); ?>
                <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop<?php 
                    echo ($category ? '&category=' . urlencode($category) : '') . 
                    ($minPriceFilter ? '&min_price=' . urlencode($minPriceFilter) : '') . 
                    ($maxPriceFilter ? '&max_price=' . urlencode($maxPriceFilter) : ''); 
                ?>" class="text-white ms-1"><i class="fas fa-times"></i></a>
            </div>
            <?php endif; ?>
            
            <?php if ($category): ?>
            <div class="badge bg-aboriginal p-2">
                Category: <?php echo ucfirst(htmlspecialchars($category)); ?>
                <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop<?php 
                    echo ($search ? '&search=' . urlencode($search) : '') . 
                    ($minPriceFilter ? '&min_price=' . urlencode($minPriceFilter) : '') . 
                    ($maxPriceFilter ? '&max_price=' . urlencode($maxPriceFilter) : ''); 
                ?>" class="text-white ms-1"><i class="fas fa-times"></i></a>
            </div>
            <?php endif; ?>
            
            <?php if ($minPriceFilter || $maxPriceFilter): ?>
            <div class="badge bg-aboriginal p-2">
                Price: 
                <?php echo ($minPriceFilter ? '$' . htmlspecialchars($minPriceFilter) : '$' . $minPrice); ?> - 
                <?php echo ($maxPriceFilter ? '$' . htmlspecialchars($maxPriceFilter) : '$' . $maxPrice); ?>
                <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop<?php 
                    echo ($search ? '&search=' . urlencode($search) : '') . 
                    ($category ? '&category=' . urlencode($category) : ''); 
                ?>" class="text-white ms-1"><i class="fas fa-times"></i></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Products Grid -->
<section class="py-5">
    <div class="container">
        <?php if (empty($products)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <?php if ($search || $category || $minPriceFilter || $maxPriceFilter): ?>
                    No artworks found matching your search criteria. Try adjusting your filters or <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="alert-link">view all artworks</a>.
                <?php else: ?>
                    No artworks found in this category. Please check back later or explore other categories.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12 mb-4">
                    <p class="text-muted">
                        Showing <?php echo count($products); ?> artwork<?php echo count($products) > 1 ? 's' : ''; ?>
                    </p>
                </div>
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="product-card card h-100">
                            <div class="product-img-container">
                                <a href="<?php echo BASE_URL; ?>/public/index.php?page=product&id=<?php echo $product->ProductNo; ?>">
                                    <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($product->ImagePath); ?>" 
                                         class="card-img-top product-image" 
                                         alt="<?php echo htmlspecialchars($product->Description); ?>">
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <a href="<?php echo BASE_URL; ?>/public/index.php?page=product&id=<?php echo $product->ProductNo; ?>" class="text-decoration-none text-dark">
                                    <h5 class="product-title"><?php echo htmlspecialchars($product->Description); ?></h5>
                                </a>
                                <p class="product-category"><?php echo htmlspecialchars($product->Category); ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="product-price">$<?php echo number_format($product->Price, 2); ?></span>
                                    <form class="add-to-cart-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product->ProductNo; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-sm btn-aboriginal">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
