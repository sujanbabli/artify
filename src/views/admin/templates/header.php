<?php
// Get current page from URL
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Get admin details from session
$adminId = Session::get('admin_id');
$adminUsername = Session::get('admin_username');
$adminName = Session::get('admin_name');
$adminRole = Session::get('admin_role');

// Check if admin is logged in
if (!Session::get('admin_logged_in')) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/admin.css">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-palette me-2"></i>Artify</h3>
            <p class="text-muted mb-0">Admin Panel</p>
        </div>

        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=dashboard" class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=products" class="nav-link <?php echo ($currentPage == 'products' || $currentPage == 'product-form') ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Products
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=orders" class="nav-link <?php echo ($currentPage == 'orders') ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials" class="nav-link <?php echo ($currentPage == 'testimonials') ? 'active' : ''; ?>">
                    <i class="fas fa-comment"></i> Testimonials
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=news" class="nav-link <?php echo ($currentPage == 'news') ? 'active' : ''; ?>">
                    <i class="fas fa-newspaper"></i> News
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($page === 'customers' || $page === 'customer-form' || $page === 'customer-detail') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/admin/index.php?page=customers">
                    <i class="fas fa-users fa-fw me-2"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=settings" class="nav-link <?php echo ($currentPage == 'settings') ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=email-settings" class="nav-link <?php echo ($currentPage == 'email-settings') ? 'active' : ''; ?>">
                    <i class="fas fa-envelope-open"></i> Email Settings
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=resend-emails" class="nav-link <?php echo ($currentPage == 'resend-emails') ? 'active' : ''; ?>">
                    <i class="fas fa-paper-plane"></i> Resend Emails
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=logout" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <div class="admin-navbar d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-sm btn-outline-secondary d-md-none me-2" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand">
                    <?php
                    switch ($currentPage) {
                        case 'dashboard':
                            echo '<i class="fas fa-tachometer-alt me-2"></i>Dashboard';
                            break;
                        case 'products':
                            echo '<i class="fas fa-box me-2"></i>Products';
                            break;
                        case 'product-form':
                            echo isset($_GET['id']) ? '<i class="fas fa-edit me-2"></i>Edit Product' : '<i class="fas fa-plus me-2"></i>Add Product';
                            break;
                        case 'orders':
                            echo '<i class="fas fa-shopping-cart me-2"></i>Orders';
                            break;
                        case 'testimonials':
                            echo '<i class="fas fa-comment me-2"></i>Testimonials';
                            break;
                        case 'news':
                            echo '<i class="fas fa-newspaper me-2"></i>News';
                            break;
                        case 'settings':
                            echo '<i class="fas fa-cog me-2"></i>Settings';
                            break;
                        default:
                            echo 'Admin Panel';
                    }
                    ?>
                </span>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-shield me-2"></i><?php echo htmlspecialchars($adminName ?: $adminUsername); ?>
                    <?php if ($adminRole): ?>
                        <span class="badge bg-secondary ms-1"><?php echo htmlspecialchars($adminRole); ?></span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php?page=settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php?page=logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (Session::hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo Session::getFlash('success')['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo Session::getFlash('error')['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (Session::hasFlash('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo Session::getFlash('info')['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Content will be inserted here -->