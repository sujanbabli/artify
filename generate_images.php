<?php
// Script to generate placeholder product images

// Define image paths from database
$images = [
    'abstract_sunset.jpg',
    'ocean_sculpture.jpg',
    'floral_canvas.jpg',
    'bronze_figurine.jpg',
    'city_skyline.jpg',
    'geometric_canvas.jpg',
    'wood_carving.jpg',
    'wildlife_photo.jpg'
];

// Directory to save images
$directory = __DIR__ . '/public/images/products/';

// Create sample images
foreach ($images as $image) {
    // Create image dimensions
    $width = 600;
    $height = 600;
    
    // Create image resource
    $img = imagecreatetruecolor($width, $height);
    
    // Define colors
    $background = imagecolorallocate($img, rand(200, 255), rand(200, 255), rand(200, 255));
    $text_color = imagecolorallocate($img, 0, 0, 0);
    $border = imagecolorallocate($img, rand(100, 150), rand(100, 150), rand(100, 150));
    
    // Fill background
    imagefill($img, 0, 0, $background);
    
    // Draw border
    imagerectangle($img, 0, 0, $width - 1, $height - 1, $border);
    
    // Draw product name
    $product_name = pathinfo($image, PATHINFO_FILENAME);
    $product_name = str_replace('_', ' ', $product_name);
    $product_name = ucwords($product_name);
    
    // Center text
    $font_size = 5;
    $text_width = imagefontwidth($font_size) * strlen($product_name);
    $text_height = imagefontheight($font_size);
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    // Add text
    imagestring($img, $font_size, $x, $y, $product_name, $text_color);
    
    // Add "ARTIFY" as a watermark
    $watermark = "ARTIFY";
    $watermark_width = imagefontwidth(3) * strlen($watermark);
    $watermark_x = ($width - $watermark_width) / 2;
    $watermark_y = $height - 30;
    imagestring($img, 3, $watermark_x, $watermark_y, $watermark, $text_color);
    
    // Save image
    imagejpeg($img, $directory . $image, 90);
    
    // Free memory
    imagedestroy($img);
    
    echo "Created image: {$image}\n";
}

echo "All sample product images have been created successfully!";
