<?php
// Load configuration
require_once dirname(__DIR__) . '/config/config.php';

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
