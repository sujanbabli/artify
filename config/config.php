<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'artify');

// Site Configuration
define('SITE_NAME', 'Artify - Online Art Shopping');
define('EMAIL_FROM', 'noreply@artify.com');
define('ADMIN_EMAIL', 'admin@artify.com');
define('BASE_URL', 'http://localhost/artify');

// Email Configuration
define('USE_SMTP', true); // Set to true to use SMTP instead of mail()
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'regmisushant94@gmail.com');
define('SMTP_PASSWORD', 'xrmmfanesbekrvrz');
define('SMTP_SECURE', 'tls'); // tls or ssl

// Session Configuration
define('SESSION_NAME', 'artify_session');
define('SESSION_LIFETIME', 3600); // 1 hour

// Directory Configuration
define('ROOT_DIR', dirname(__DIR__));
define('INCLUDES_DIR', ROOT_DIR . '/includes');
define('MODELS_DIR', ROOT_DIR . '/src/models');
define('VIEWS_DIR', ROOT_DIR . '/src/views');
define('CONTROLLERS_DIR', ROOT_DIR . '/src/controllers');
define('UTILS_DIR', ROOT_DIR . '/src/utils');
define('PUBLIC_DIR', ROOT_DIR . '/public');

// Debug Mode (Turn off in production)
define('DEBUG_MODE', true);

