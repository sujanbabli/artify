<?php
include 'connect.php'; // Make sure this file connects to your DB properly

if (isset($_POST['add_product'])) {
  // Get form data
  $product_name = $_POST['product_name'];
  $product_price = $_POST['product_price'];

  // Handle image upload
  $product_image = $_FILES['product_image']['name'];
  $product_image_temp_name = $_FILES['product_image']['tmp_name']; // Fixed key
  $product_image_folder = 'images/' . $product_image;

  // Optional: Create images folder if it doesn't exist
  if (!is_dir('images')) {
    mkdir('images');
  }

  // Insert data into database
  $insert_query = mysqli_query($conn, "INSERT INTO `products` (name, price, image) VALUES ('$product_name', '$product_price', '$product_image')");

  if ($insert_query) {
    // Move uploaded file to target folder
    move_uploaded_file($product_image_temp_name, $product_image_folder);
    $display_message = "✅ Product inserted successfully.";
  } else {
    $display_message = "❌ Failed to insert product: " . mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ArtNest | Home</title>
  <!-- css file -->
  <link rel="stylesheet" href="css/styles.css"/>
  <!-- font awesome -->
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<!-- include header -->
<?php include('header.php') ?>
<?php include('connect.php') ?>

  <section class="hero">
    <div class="hero-content">
      <h1>Welcome to Artify</h1>
      <p>Buy directly from Darwin's finest local artists. Discover, collect, and cherish unique art pieces crafted with passion.</p>
      <a href="#" class="btn">Explore Gallery</a>
    </div>
  </section>
 <!-- container -->
<div class="container">

<?php if (isset($display_message)): ?>
  <div class="display_message">
    <span><?= $display_message ?></span>
    <i class="fas fa-times" onclick="this.parentElement.style.display='none';"></i>
  </div>
<?php endif; ?>


  <section>
    <h3 class="heading">Add Products</h3>
    <form action="" class="add_products" method="post" enctype="multipart/form-data">
      <input type="text" name="product_name" placeholder="Enter Product name" class="input fields" required>
      <input type="number" name="product_price" min="0" placeholder="Enter Product price" class="input fields" required>
      <input type="file" name="product_image"  class="input fields" required accept ="image/png, image/jpg, image/jpeg">
      <input type="submit" name="add_product" class="submit_btn" value="Add Product">
    </form>
  </section>
</div>

    
  
 </div>
  <main>
    <section class="gallery container">
      <h2>🖼️ Featured Artworks</h2>
      <div class="gallery-grid">
        <div class="art-item">
          <img src="assets/art1.jpg" alt="Artwork 1">
          <div class="info">
            <h3>MudCrab</h3>
            <p>$120</p>
          </div>
        </div>
        <div class="art-item">
          <img src="assets/art2.jpg" alt="Artwork 2">
          <div class="info">
            <h3>2 Baru</h3>
            <p>$95</p>
          </div>
        </div>
        <div class="art-item">
          <img src="assets/art3.jpg" alt="Artwork 3">
          <div class="info">
            <h3>Large Clapstick</h3>
            <p>$150</p>
          </div>
        </div>
      </div>
    </section>

    <section class="news container">
      <h2>📰 Latest News</h2>
      <div class="news-card">
        <h3>We're going online!</h3>
        <p>ArtNest will soon be Darwin’s first online-native art store. Browse, connect, and collect!</p>
      </div>
    </section>

    <section class="testimonials-link container">
      <a href="#" class="btn-outline">🌟 View Customer Testimonials</a>
    </section>
  </main>

  <footer class="footer">
    <p>&copy; 2025 ArtNest. Handcrafted with heart in Darwin.</p>
  </footer>

  <script src="js/main.js"></script>
</body>
</html>
