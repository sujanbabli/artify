<?php
// Get testimonials
$testimonialModel = new TestimonialModel();
$testimonials = $testimonialModel->getApprovedTestimonials();
?>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <h1 class="text-center mb-5">Customer Testimonials</h1>
        
        <?php if (empty($testimonials)): ?>
            <div class="alert alert-info text-center">
                <p>No testimonials yet. Be the first to share your experience!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $testimonial->Rating): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <div class="testimonial-text">
                                "<?php echo nl2br(htmlspecialchars($testimonial->Text)); ?>"
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="testimonial-author">
                                    <?php echo htmlspecialchars($testimonial->Name); ?>
                                </div>
                                <small class="text-muted">
                                    <?php echo date('M j, Y', strtotime($testimonial->Date)); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="<?php echo BASE_URL; ?>/public/index.php?page=submit-testimonial" class="btn btn-primary">
                <i class="fas fa-comment me-2"></i>Share Your Experience
            </a>
        </div>
    </div>
</section>
        