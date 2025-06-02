<?php
/**
 * Main application navbar
 * This file provides the navigation bar for the Artify application
 */
$base_url = isset($base_url) ? $base_url : '../';
?>
<nav style="background-color: #333; color: white; padding: 15px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div>
            <a href="index.php" style="color: white; text-decoration: none; font-size: 24px; font-weight: bold;">
                Artify
            </a>
        </div>
        <div>
            <a href="shop.php" style="color: white; margin-right: 20px; text-decoration: none;">Shop</a>
            <a href="categories.php" style="color: white; margin-right: 20px; text-decoration: none;">Categories</a>
            <a href="about.php" style="color: white; margin-right: 20px; text-decoration: none;">About</a>
            <a href="contact.php" style="color: white; margin-right: 20px; text-decoration: none;">Contact</a>
            <a href="<?php echo $base_url; ?>tests/application_test_guide.php" style="color: white; background-color: #4CAF50; padding: 8px 15px; border-radius: 4px; text-decoration: none;">
                Test App
            </a>
        </div>
    </div>
</nav>
