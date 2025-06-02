<?php
/**
 * Navbar for Artify test pages
 * This file provides a consistent navigation across all test pages
 */
$base_url = isset($base_url) ? $base_url : '../../';
?>
<nav style="background-color: #333; color: white; padding: 10px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="<?php echo $base_url; ?>" style="color: white; text-decoration: none; font-size: 20px; font-weight: bold;">
                Artify
            </a>
        </div>
        <div>
            <a href="<?php echo $base_url; ?>tests/application_test_guide.php" style="color: white; margin-right: 15px; text-decoration: none;">
                Test Guide
            </a>
            <a href="<?php echo $base_url; ?>tests/database/load_test_data.php" style="color: white; margin-right: 15px; text-decoration: none;">
                Load Test Data
            </a>
            <a href="<?php echo $base_url; ?>tests/database/test_database.php" style="color: white; margin-right: 15px; text-decoration: none;">
                Test Database
            </a>
            <a href="<?php echo $base_url; ?>tests/troubleshooting.php" style="color: white; text-decoration: none;">
                Troubleshooting
            </a>
        </div>
    </div>
</nav>
