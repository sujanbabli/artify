<?php
// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product controller
$productController = new ProductController();
$product = $productController->getProductById($productId);

// If product not found, redirect to shop page
if (!$product) {
    Session::setFlash('error', 'Product not found');
    header('Location: ' . BASE_URL . '/public/index.php?page=shop');
    exit;
}
?>

<!-- Product Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow">
                    <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($product->ImagePath); ?>" 
                         class="card-img-top img-fluid" 
                         alt="<?php echo htmlspecialchars($product->Description); ?>">
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/public/index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/public/index.php?page=shop">Shop</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/public/index.php?page=shop&category=<?php echo urlencode($product->Category); ?>"><?php echo htmlspecialchars($product->Category); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product->Description); ?></li>
                    </ol>
                </nav>
                
                <h1 class="mb-3"><?php echo htmlspecialchars($product->Description); ?></h1>
                
                <p class="text-muted">Category: <?php echo htmlspecialchars($product->Category); ?></p>
                
                <h2 class="text-primary mb-4">$<?php echo number_format($product->Price, 2); ?></h2>
                
                <div class="mb-4">
                    <h5>Specifications:</h5>
                    <ul class="list-unstyled">
                        <?php if ($product->Colour): ?>
                            <li><strong>Color:</strong> <?php echo htmlspecialchars($product->Colour); ?></li>
                        <?php endif; ?>
                        <?php if ($product->Size): ?>
                            <li><strong>Size:</strong> <?php echo htmlspecialchars($product->Size); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <form class="add-to-cart-form mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="quantity" class="col-form-label">Quantity:</label>
                        </div>
                        <div class="col-auto">
                            <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="10">
                        </div>
                        <input type="hidden" name="product_id" value="<?php echo $product->ProductNo; ?>">
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <div class="mt-5">
            <h3 class="mb-4">You May Also Like</h3>
            
            <?php
            // Get products in the same category (up to 4)
            $relatedProducts = $productController->getProductsByCategory($product->Category);
            
            // Filter out current product and limit to 4
            $relatedProducts = array_filter($relatedProducts, function($p) use ($product) {
                return $p->ProductNo !== $product->ProductNo;
            });
            
            $relatedProducts = array_slice($relatedProducts, 0, 4);
            
            if (!empty($relatedProducts)):
            ?>
                <div class="row">
                    <?php foreach ($relatedProducts as $related): ?>
                        <div class="col-md-3 mb-4">
                            <div class="product-card card h-100">
                                <div class="product-img-container">
                                    <a href="<?php echo BASE_URL; ?>/public/index.php?page=product&id=<?php echo $related->ProductNo; ?>">
                                        <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($related->ImagePath); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($related->Description); ?>">
                                    </a>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <a href="<?php echo BASE_URL; ?>/public/index.php?page=product&id=<?php echo $related->ProductNo; ?>" class="text-decoration-none text-dark">
                                        <h5 class="product-title"><?php echo htmlspecialchars($related->Description); ?></h5>
                                    </a>
                                    <p class="product-category"><?php echo htmlspecialchars($related->Category); ?></p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="product-price">$<?php echo number_format($related->Price, 2); ?></span>
                                        <form class="add-to-cart-form">
                                            <input type="hidden" name="product_id" value="<?php echo $related->ProductNo; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-cart-plus"></i> Add
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No related products found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
