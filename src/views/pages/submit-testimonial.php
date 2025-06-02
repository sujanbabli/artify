<?php
// Initialize variables
$name = $email = $text = '';
$rating = 5;
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check CSRF token first
    if (!isset($_POST['csrf_token']) || !Session::validateCsrfToken($_POST['csrf_token'])) {
        $errors['general'] = 'Security validation failed. Please try again.';
    } else {
        $validation = new Validation();
        
        // Validate name
        if ($validation->required($_POST['name'], 'Name')) {
            $name = Validation::sanitize($_POST['name']);
        } else {
            $errors['name'] = $validation->getError('Name');
        }
        
        // Validate email
        if ($validation->required($_POST['email'], 'Email') && $validation->email($_POST['email'])) {
            $email = Validation::sanitize($_POST['email']);
        } else {
            $errors['email'] = $validation->getError('Email');
        }
        
        // Validate rating
        if (isset($_POST['rating']) && is_numeric($_POST['rating']) && $_POST['rating'] >= 1 && $_POST['rating'] <= 5) {
            $rating = (int)$_POST['rating'];
        } else {
            $errors['rating'] = 'Please select a rating';
        }
        
        // Validate testimonial text
        if ($validation->required($_POST['text'], 'Testimonial') && $validation->minLength($_POST['text'], 10, 'Testimonial')) {
            $text = Validation::sanitize($_POST['text']);
        } else {
            $errors['text'] = $validation->getError('Testimonial');
        }
        
        // If validation passed, save testimonial
        if (empty($errors)) {
            // Save testimonial
            $testimonialModel = new TestimonialModel();
            $result = $testimonialModel->addTestimonial([
                'name' => $name,
                'email' => $email,
                'rating' => $rating,
                'text' => $text
            ]);
            
            if ($result) {
                // Send confirmation emails
                EmailHelper::sendTestimonialConfirmation($name, $email, $rating, $text);
                EmailHelper::sendTestimonialNotificationToAdmin($name, $email, $rating, $text);
                
                // Set success message
                Session::setFlash('success', 'Thank you for your testimonial! It will be reviewed by our team and published soon.');
                
                // Redirect to testimonials page
                header('Location: ' . BASE_URL . '/public/index.php?page=testimonials');
                exit;
            } else {
                $errors['general'] = 'An error occurred while submitting your testimonial. Please try again.';
            }
        }
    }
}

// Generate new CSRF token
$csrfToken = Session::generateCsrfToken();
?>

<!-- Submit Testimonial Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Share Your Experience</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $errors['general']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <p>We value your feedback! Please share your experience with Artify and our products. Your testimonial will be reviewed by our team before being published.</p>
                        
                        <form action="<?php echo BASE_URL; ?>/public/index.php?page=submit-testimonial" method="post">
                            <!-- CSRF Token -->
                            <?php echo Session::csrfField(); ?>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['name']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email Address</label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Your Rating</label>
                                <div class="rating">
                                    <div class="star-rating">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo $rating == $i ? 'checked' : ''; ?>>
                                            <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <?php if (isset($errors['rating'])): ?>
                                    <div class="text-danger mt-1">
                                        <?php echo $errors['rating']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="text" class="form-label">Your Testimonial</label>
                                <textarea class="form-control <?php echo isset($errors['text']) ? 'is-invalid' : ''; ?>" id="text" name="text" rows="5" required><?php echo htmlspecialchars($text); ?></textarea>
                                <?php if (isset($errors['text'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['text']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit Testimonial</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?php echo BASE_URL; ?>/public/index.php?page=testimonials" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Testimonials
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
