<?php
/**
 * Header untuk website CDK Wilayah Bojonegoro
 */

// Pastikan file config sudah diinclude
if (!defined('BASE_PATH')) {
    require_once 'config.php';
}

// Include database dan functions
require_once 'db.php';
require_once 'functions.php';

// Ambil pengaturan website
$site_title = getSetting('site_title', 'CDK Wilayah Bojonegoro - Dinas Kehutanan Provinsi Jawa Timur');
$site_description = getSetting('site_description', 'Unit Pelaksana Teknis Dinas Kehutanan Provinsi Jawa Timur');
$site_logo = getSetting('site_logo', 'assets/images/logo.png');
$site_favicon = getSetting('site_favicon', 'assets/images/favicon.ico');

// Ambil menu utama
$main_menu = getMenuByPosition('main_menu');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#2e7d32" />
    <title><?php echo $site_title; ?></title>
    <meta name="description" content="<?php echo $site_description; ?>">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo SITE_URL . '/' . $site_favicon; ?>">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@accessibility-community/accessible-dark-mode@1.0.0/style.css" />

    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/styles.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="loading-animation"></div>
    </div>

    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <img src="<?php echo SITE_URL . '/' . $site_logo; ?>" alt="Logo CDK Bojonegoro" height="40" />
                CDK Wilayah Bojonegoro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php foreach ($main_menu as $menu): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $menu['url']; ?>"><?php echo $menu['title']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Start -->
    <div id="main-content"></div>