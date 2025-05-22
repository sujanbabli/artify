<?php
// Get the current page for active navigation
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'home';

// Initialize cart controller to get cart items count
$cartController = new CartController();
$cartSummary = $cartController->getCartSummary();
$cartItemsCount = $cartSummary['total_items'];

// Get latest news for homepage
$newsModel = new NewsModel();
$latestNews = $newsModel->getLatestNews();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/public/index.php">
                <i class="fas fa-palette me-2"></i>Artify
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'home') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/public/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'shop') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/public/index.php?page=shop">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'about') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/public/index.php?page=about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'testimonials') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/public/index.php?page=testimonials">Testimonials</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="<?php echo BASE_URL; ?>/public/index.php?page=cart" class="btn btn-outline-light position-relative me-2">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <?php if ($cartItemsCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $cartItemsCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <?php if (Session::get('customer_logged_in')): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars(Session::get('customer_name')); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/public/index.php?page=orders"><i class="fas fa-box me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/public/index.php?page=profile"><i class="fas fa-id-card me-2"></i>My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/public/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php elseif (Session::get('admin_logged_in')): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-shield me-1"></i> <?php echo htmlspecialchars(Session::get('admin_name') ?: Session::get('admin_username')); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php?page=products"><i class="fas fa-box me-2"></i>Manage Products</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php?page=orders"><i class="fas fa-shopping-cart me-2"></i>Manage Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Admin Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?page=login" class="btn btn-outline-light">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                        <a href="<?php echo BASE_URL; ?>/admin/login.php" class="btn btn-outline-light ms-2">
                            <i class="fas fa-user-shield me-1"></i> Admin
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (Session::hasFlash('success')): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo Session::getFlash('success')['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo Session::getFlash('error')['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('info')): ?>
        <div class="container mt-3">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo Session::getFlash('info')['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <main class="pb-5">
        <!-- Content will be inserted here -->
