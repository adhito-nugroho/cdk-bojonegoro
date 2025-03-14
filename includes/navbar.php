<?php
/**
 * Navbar untuk website CDK Wilayah Bojonegoro
 */

// Pastikan file config sudah diinclude
if (!defined('BASE_PATH')) {
    require_once 'config.php';
}

// Include functions
if (!function_exists('getMenuByPosition')) {
    require_once 'functions.php';
}

// Ambil pengaturan website
$site_logo = getSetting('site_logo', 'assets/images/logo.png');

// Ambil semua menu utama
$main_menu = getMenuByPosition('main_menu');

// Deteksi halaman aktif
$current_url = $_SERVER['REQUEST_URI'];
$current_page = basename($current_url);
?>

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
                <?php foreach ($main_menu as $menu):
                    // Periksa apakah menu ini adalah halaman aktif
                    $is_active = false;
                    if ($menu['url'] == '#beranda' && $current_page == 'index.php') {
                        $is_active = true;
                    } elseif (strpos($current_url, $menu['url']) !== false) {
                        $is_active = true;
                    }
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $is_active ? 'active' : ''; ?>" href="<?php echo $menu['url']; ?>">
                            <?php if (!empty($menu['icon'])): ?>
                                <i class="<?php echo $menu['icon']; ?> me-1"></i>
                            <?php endif; ?>
                            <?php echo $menu['title']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>