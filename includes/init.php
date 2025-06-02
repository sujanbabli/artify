<?php
// Load configuration
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/site_settings.php';

// Load Composer's autoloader
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
}

// Load helper classes
require_once INCLUDES_DIR . '/database.php';
require_once INCLUDES_DIR . '/session.php';

// Load utility classes
require_once UTILS_DIR . '/EmailHelper.php';
require_once UTILS_DIR . '/Cart.php';
require_once UTILS_DIR . '/Validation.php';

// Load base model
require_once MODELS_DIR . '/Model.php';

// Load models
require_once MODELS_DIR . '/ProductModel.php';
require_once MODELS_DIR . '/CustomerModel.php';
require_once MODELS_DIR . '/PurchaseModel.php';
require_once MODELS_DIR . '/NewsModel.php';
require_once MODELS_DIR . '/TestimonialModel.php';
require_once MODELS_DIR . '/AdminModel.php';

// Load controllers
require_once CONTROLLERS_DIR . '/CartController.php';
require_once CONTROLLERS_DIR . '/ProductController.php';

// Initialize the session
Session::init();

// Create logs directory if it doesn't exist
$logsDir = ROOT_DIR . '/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}
