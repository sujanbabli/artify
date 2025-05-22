<?php
// Get latest news
$newsModel = new NewsModel();
$latestNews = $newsModel->getLatestNews();

// Get product controller
$productController = new ProductController();
$featuredProducts = $productController->getAllProducts();

// Only show 8 featured products on homepage
$featuredProducts = array_slice($featuredProducts, 0, 8);
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1>Welcome to Artify</h1>
            <p class="lead">Discover unique artworks that bring life to your space</p>
            <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-primary btn-lg">Shop Now</a>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<?php if ($latestNews): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="news-card card border-0">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4"><?php echo htmlspecialchars($latestNews->Title); ?></h2>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($latestNews->Text)); ?></p>
                        <p class="text-muted text-end">Posted on <?php echo date('F j, Y', strtotime($latestNews->Date)); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Featured Artworks</h2>
        
        <div class="row">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="product-card card h-100">
                        <div class="product-img-container">
                            <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($product->ImagePath); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product->Description); ?>">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="product-title"><?php echo htmlspecialchars($product->Description); ?></h5>
                            <p class="product-category"><?php echo htmlspecialchars($product->Category); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="product-price">$<?php echo number_format($product->Price, 2); ?></span>
                                <form class="add-to-cart-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product->ProductNo; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-primary">View All Artworks</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2>About Artify</h2>
                <p>Artify is a Darwin-based art company dedicated to bringing beautiful, unique artworks to art enthusiasts around Australia. Our collection features works from local artists who draw inspiration from the stunning landscapes and vibrant culture of the Northern Territory.</p>
                <p>Every piece in our collection is carefully selected to ensure quality, uniqueness, and artistic value. Whether you're looking for a statement piece for your living room or a thoughtful gift for a loved one, Artify has something to suit every taste and space.</p>
            </div>
            <div class="col-md-6">
                <img src="<?php echo BASE_URL; ?>/public/images/about-artify.jpg" alt="About Artify" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Preview -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">What Our Customers Say</h2>
        
        <?php
        $testimonialModel = new TestimonialModel();
        $testimonials = $testimonialModel->getApprovedTestimonials();
        
        // Only show up to 3 testimonials on homepage
        $testimonials = array_slice($testimonials, 0, 3);
        
        if (!empty($testimonials)):
        ?>
            <div class="row">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <p class="testimonial-content">"<?php echo htmlspecialchars($testimonial->Text); ?>"</p>
                            <p class="testimonial-author">- <?php echo htmlspecialchars($testimonial->Name); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?php echo BASE_URL; ?>/public/index.php?page=testimonials" class="btn btn-outline-primary">View All Testimonials</a>
            </div>
        <?php else: ?>
            <p class="text-center">No testimonials yet. Be the first to <a href="<?php echo BASE_URL; ?>/public/index.php?page=submit-testimonial">share your experience</a>!</p>
        <?php endif; ?>
    </div>
</section>
