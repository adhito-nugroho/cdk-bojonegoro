<?php
/**
 * Header Admin untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menampilkan header halaman admin
 */

// Periksa apakah file diakses langsung
if (!defined('BASE_PATH')) {
    exit('Akses langsung ke file ini tidak diperbolehkan');
}

// Include auth check
include_once dirname(__FILE__) . '/auth-check.php';

// Dapatkan data user yang sedang login
$currentUser = getCurrentUser();

// Dapatkan jumlah pesan yang belum dibaca
$unreadMessages = getUnreadPesan();

// Dapatkan title halaman
$page_title = $page_title ?? ADMIN_TITLE;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon"
        href="<?php echo SITE_URL; ?>/<?php echo getSetting('site_favicon', 'assets/images/favicon.ico'); ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <!-- Summernote -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    <!-- Custom styles -->
    <link rel="stylesheet" href="<?php echo ADMIN_ASSETS; ?>/css/admin-style.css">

    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #1b4332;
            --accent-color: #52b788;
            --text-color: #333;
            --light-color: #fff;
            --sidebar-width: 250px;
            --header-height: 60px;
        }

        /* Base Layout */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--light-color);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header img {
            height: 40px;
        }

        .sidebar-header h5 {
            margin: 0;
            font-size: 1.1rem;
            color: var(--light-color);
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-header {
            padding: 0.5rem 1.5rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.6);
        }

        .menu-item {
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--light-color);
        }

        .menu-item.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--light-color);
            border-left: 4px solid var(--accent-color);
        }

        .menu-item i {
            margin-right: 0.75rem;
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-header {
            height: var(--header-height);
            background-color: var(--light-color);
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            margin-right: 1rem;
        }

        .header-right {
            display: flex;
            align-items: center;
        }

        .header-item {
            margin-left: 1rem;
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .user-dropdown img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.5rem;
        }

        .user-dropdown .user-name {
            font-weight: 600;
        }

        .user-dropdown .user-role {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Main Content */
        .main-content {
            padding: 1.5rem;
            flex: 1;
        }

        .page-header {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-header h4 {
            margin: 0;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .page-header .breadcrumb {
            margin: 0;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #f0f0f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
        }

        /* Dashboard Stats */
        .stats-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.25rem;
            display: flex;
            align-items: center;
            height: 100%;
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .stats-icon.users {
            background-color: #4e73df;
        }

        .stats-icon.posts {
            background-color: #1cc88a;
        }

        .stats-icon.documents {
            background-color: #f6c23e;
        }

        .stats-icon.messages {
            background-color: #e74a3b;
        }

        .stats-info h5 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .stats-info span {
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* Tables */
        .data-table {
            width: 100% !important;
        }

        /* Forms */
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .custom-file-upload {
            display: block;
            padding: 0.75rem 1rem;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            cursor: pointer;
            text-align: center;
        }

        .custom-file-preview {
            margin-top: 1rem;
            border: 1px dashed #ced4da;
            padding: 0.5rem;
            border-radius: 0.25rem;
            text-align: center;
        }

        .custom-file-preview img {
            max-width: 100%;
            max-height: 200px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content-wrapper {
                margin-left: 0;
            }

            .content-wrapper.sidebar-open {
                margin-left: var(--sidebar-width);
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="<?php echo SITE_URL; ?>/<?php echo getSetting('site_logo', 'assets/images/logo.png'); ?>"
                alt="Logo">
            <h5>CDK Bojonegoro</h5>
        </div>

        <div class="sidebar-menu">
            <div class="menu-header">Dashboard</div>
            <a href="<?php echo ADMIN_URL; ?>/index.php"
                class="menu-item <?php echo isActiveMenu('dashboard') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('dashboard'); ?>"></i> Dashboard
            </a>

            <div class="menu-header">Konten</div>
            <a href="<?php echo ADMIN_URL; ?>/modules/publikasi/index.php"
                class="menu-item <?php echo isActiveMenu('publikasi') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('publikasi'); ?>"></i> Publikasi
            </a>
            <a href="<?php echo ADMIN_URL; ?>/modules/dokumen/index.php"
                class="menu-item <?php echo isActiveMenu('dokumen') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('dokumen'); ?>"></i> Dokumen
            </a>
            <a href="<?php echo ADMIN_URL; ?>/modules/galeri/index.php"
                class="menu-item <?php echo isActiveMenu('galeri') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('galeri'); ?>"></i> Galeri
            </a>

            <div class="menu-header">Profil Lembaga</div>
            <a href="<?php echo ADMIN_URL; ?>/modules/wilayah/index.php"
                class="menu-item <?php echo isActiveMenu('wilayah') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('wilayah'); ?>"></i> Wilayah Kerja
            </a>
            <a href="<?php echo ADMIN_URL; ?>/modules/struktur/index.php"
                class="menu-item <?php echo isActiveMenu('struktur') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('struktur'); ?>"></i> Struktur Organisasi
            </a>

            <div class="menu-header">Layanan</div>
            <a href="<?php echo ADMIN_URL; ?>/modules/layanan/index.php"
                class="menu-item <?php echo isActiveMenu('layanan') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('layanan'); ?>"></i> Layanan
            </a>
            <a href="<?php echo ADMIN_URL; ?>/modules/program/index.php"
                class="menu-item <?php echo isActiveMenu('program') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('program'); ?>"></i> Program
            </a>

            <div class="menu-header">Data & Statistik</div>
            <a href="<?php echo ADMIN_URL; ?>/modules/statistik/index.php"
                class="menu-item <?php echo isActiveMenu('statistik') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('statistik'); ?>"></i> Statistik
            </a>
            <a href="<?php echo ADMIN_URL; ?>/modules/capaian/index.php"
                class="menu-item <?php echo isActiveMenu('capaian') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('capaian'); ?>"></i> Capaian Program
            </a>

            <div class="menu-header">Komunikasi</div>
            <a href="<?php echo ADMIN_URL; ?>/modules/pesan/index.php"
                class="menu-item <?php echo isActiveMenu('pesan') ? 'active' : ''; ?>">
                <i class="<?php echo getMenuIcon('pesan'); ?>"></i> Pesan Kontak
                <?php if ($unreadMessages > 0): ?>
                    <span class="badge bg-danger ms-auto"><?php echo $unreadMessages; ?></span>
                <?php endif; ?>
            </a>

            <?php if ($currentUser['role'] === 'admin'): ?>
                <div class="menu-header">Admin</div>
                <a href="<?php echo ADMIN_URL; ?>/modules/users/index.php"
                    class="menu-item <?php echo isActiveMenu('users') ? 'active' : ''; ?>">
                    <i class="<?php echo getMenuIcon('users'); ?>"></i> Pengguna
                </a>
                <a href="<?php echo ADMIN_URL; ?>/modules/menu/index.php"
                    class="menu-item <?php echo isActiveMenu('menu') ? 'active' : ''; ?>">
                    <i class="<?php echo getMenuIcon('menu'); ?>"></i> Menu
                </a>
                <a href="<?php echo ADMIN_URL; ?>/modules/settings/index.php"
                    class="menu-item <?php echo isActiveMenu('settings') ? 'active' : ''; ?>">
                    <i class="<?php echo getMenuIcon('settings'); ?>"></i> Pengaturan
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="content-wrapper" id="content-wrapper">
        <!-- Header -->
        <header class="main-header">
            <div class="header-left">
                <button id="toggle-sidebar" class="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <span><?php echo $page_title; ?></span>
            </div>

            <div class="header-right">
                <div class="header-item">
                    <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Lihat Website
                    </a>
                </div>

                <?php if ($unreadMessages > 0): ?>
                    <div class="header-item">
                        <a href="<?php echo ADMIN_URL; ?>/modules/pesan/index.php"
                            class="btn btn-sm btn-outline-secondary position-relative">
                            <i class="fas fa-envelope"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $unreadMessages; ?>
                            </span>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="header-item dropdown">
                    <a class="dropdown-toggle user-dropdown" href="#" role="button" id="userDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($currentUser['profile_image'])): ?>
                            <img src="<?php echo UPLOADS_URL; ?>/profile/<?php echo $currentUser['profile_image']; ?>"
                                alt="User">
                        <?php else: ?>
                            <img src="<?php echo ADMIN_ASSETS; ?>/images/default-user.png" alt="User">
                        <?php endif; ?>
                        <div class="d-none d-md-block ms-2">
                            <div class="user-name"><?php echo $currentUser['name']; ?></div>
                            <div class="user-role"><?php echo ucfirst($currentUser['role']); ?></div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?php echo ADMIN_URL; ?>/modules/profile/index.php"><i
                                    class="fas fa-user me-2"></i> Profil</a></li>
                        <li><a class="dropdown-item" href="<?php echo ADMIN_URL; ?>/modules/settings/index.php"><i
                                    class="fas fa-cog me-2"></i> Pengaturan</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?php echo ADMIN_URL; ?>/logout.php"><i
                                    class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>