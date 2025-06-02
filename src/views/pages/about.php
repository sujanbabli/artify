<?php
// Get some featured artists if available
$productModel = new ProductModel();
$featuredArtists = $productModel->getFeaturedArtists();
?>

<!-- About Section Hero -->
<section class="py-5 bg-aboriginal-pattern">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="fw-bold display-4 mb-4 text-aboriginal">Celebrating Aboriginal Arts</h1>
                <p class="lead">Artify proudly showcases the rich cultural heritage and contemporary artistic expressions of the Aboriginal peoples of Darwin, Northern Territory.</p>
                <p>Each piece tells a story, connects with Country, and preserves traditional knowledge while embracing modern artistic innovation.</p>
                <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-aboriginal btn-lg mt-3">
                    <i class="fas fa-palette me-2"></i>Explore Our Collection
                </a>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="<?php echo BASE_URL; ?>/public/images/about-artify.jpg" alt="Aboriginal Art from Darwin" class="img-fluid rounded-custom shadow-lg">
                    <div class="about-overlay-box bg-aboriginal p-4 rounded shadow-lg">
                        <h4 class="text-white">Connecting to Country</h4>
                        <p class="text-white-50 mb-0">Aboriginal art is not just decoration; it's a visual language that tells the stories of the land, people, and Dreamtime.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Mission -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold text-aboriginal">Our Mission</h2>
                <p class="lead">To promote, celebrate and ethically distribute authentic Aboriginal art from Darwin and the Northern Territory.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 h-100 shadow-sm hover-card">
                    <div class="card-body p-4">
                        <div class="icon-box mb-3">
                            <i class="fas fa-handshake text-aboriginal"></i>
                        </div>
                        <h4>Fair Compensation</h4>
                        <p class="text-muted">We ensure artists receive fair compensation for their work, respecting the value of their cultural contributions.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 h-100 shadow-sm hover-card">
                    <div class="card-body p-4">
                        <div class="icon-box mb-3">
                            <i class="fas fa-landmark text-aboriginal"></i>
                        </div>
                        <h4>Cultural Respect</h4>
                        <p class="text-muted">We promote understanding of the cultural significance and stories behind each piece of art.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 h-100 shadow-sm hover-card">
                    <div class="card-body p-4">
                        <div class="icon-box mb-3">
                            <i class="fas fa-globe-asia text-aboriginal"></i>
                        </div>
                        <h4>Global Recognition</h4>
                        <p class="text-muted">We help bring Aboriginal art from Darwin to a worldwide audience while preserving its cultural integrity.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Cultural Significance -->
<section class="py-5 bg-aboriginal-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="position-relative">
                    <img src="<?php echo BASE_URL; ?>/public/images/patterns/aboriginal-pattern-2.png" alt="Aboriginal Art Cultural Significance" class="img-fluid rounded-custom shadow-lg">
                    <div class="dot-pattern-overlay"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold text-aboriginal mb-4">The Cultural Significance</h2>
                <p>Aboriginal art is one of the oldest continuous art traditions in the world, dating back at least 60,000 years. For the Aboriginal peoples of Darwin and the Northern Territory, art is intrinsically linked to cultural identity and connection to Country.</p>
                
                <div class="mt-4">
                    <h5><i class="fas fa-paint-brush text-aboriginal me-2"></i>Storytelling Through Art</h5>
                    <p>Traditional Aboriginal art uses symbols and icons to tell stories of the Dreamtime, creation narratives, and maintain cultural knowledge across generations.</p>
                    
                    <h5><i class="fas fa-map-marked-alt text-aboriginal me-2"></i>Connection to Country</h5>
                    <p>Each artwork represents a deep spiritual connection to the land, depicting traditional hunting grounds, water sources, and sacred sites.</p>
                    
                    <h5><i class="fas fa-users text-aboriginal me-2"></i>Community and Knowledge</h5>
                    <p>Art creation is often a communal activity where knowledge, stories, and techniques are passed down through generations, strengthening cultural bonds.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ethical Statement -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold text-aboriginal mb-4">Our Ethical Commitment</h2>
                <p class="lead mb-4">Artify is committed to the ethical sourcing and selling of Aboriginal art.</p>
                
                <div class="ethical-badge mb-4">
                    <i class="fas fa-certificate text-aboriginal"></i>
                    <span>Ethically Sourced</span>
                </div>
                
                <p>We work directly with artists and community art centers to ensure fair compensation. We provide clear provenance information for all artworks and respect cultural protocols regarding sacred imagery and knowledge.</p>
                <p>A percentage of all sales goes back to community development initiatives in the artists' communities, supporting cultural preservation and sustainable artistic practices.</p>
                
                <a href="<?php echo BASE_URL; ?>/public/index.php?page=shop" class="btn btn-aboriginal mt-3">
                    Explore Our Ethically Sourced Collection
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Visit Us -->
<section class="py-5 bg-aboriginal-dark text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-4">Visit Our Gallery</h2>
                <p class="lead">Experience the beauty and cultural richness of Aboriginal art in person.</p>
                
                <div class="mt-4">
                    <div class="d-flex mb-3">
                        <div class="contact-icon me-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Address</h5>
                            <p class="mb-0">123 Mitchell Street, Darwin NT 0800</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="contact-icon me-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Opening Hours</h5>
                            <p class="mb-0">Monday - Saturday: 9:00 AM - 5:00 PM<br>Sunday: 10:00 AM - 4:00 PM</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="contact-icon me-3">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Contact</h5>
                            <p class="mb-0">Phone: (08) 8941 XXXX<br>Email: info@artify.com.au</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="map-container rounded-custom overflow-hidden shadow-lg">
                    <!-- Replace with actual map embed -->
                    <img src="<?php echo BASE_URL; ?>/public/images/about/darwin-map.jpg" alt="Map to Artify Gallery" class="img-fluid w-100">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Acknowledgement of Country -->
<section class="py-4 bg-aboriginal-pattern-subtle">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <h4 class="fw-bold mb-3">Acknowledgement of Country</h4>
                <p class="mb-0">Artify acknowledges the Traditional Owners of the land on which we operate, the Larrakia people of the Darwin region. We pay our respects to Elders past, present and emerging, and celebrate the rich cultural heritage and vital connection to land, sea and community of all Aboriginal and Torres Strait Islander peoples across Australia.</p>
            </div>
        </div>
    </div>
</section>
