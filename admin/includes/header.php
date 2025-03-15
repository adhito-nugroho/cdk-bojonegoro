<?php
/**
 * Admin Header Template
 * CDK Wilayah Bojonegoro
 */

// Include required files
require_once 'config.php';
require_once 'functions.php';
require_once 'auth.php';

// Require login for all admin pages (except login.php)
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php') {
    requireLogin();
}

// Get site settings
$siteTitle = getSetting('nama_instansi') ?: 'CDK Wilayah Bojonegoro';

// Set page title
$page_title = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin Dashboard <?php echo $siteTitle; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Remixicon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    <!-- Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <style>
        :root {
            --primary-color: #2d6a4f;
            --primary-dark: #1b4332;
            --primary-light: #40916c;
            --secondary-color: #74c69d;
            --secondary-light: #95d5b2;
            --secondary-dark: #52b788;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --topbar-height: 60px;
            --content-padding: 1.5rem;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--primary-dark);
            color: #fff;
            z-index: 999;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            padding: 1rem;
            height: var(--topbar-height);
            background-color: var(--primary-color);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 0.75rem;
        }

        .sidebar-brand h5 {
            font-size: 1.1rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar.collapsed .sidebar-brand h5 {
            display: none;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-heading {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
            white-space: nowrap;
        }

        .sidebar.collapsed .sidebar-heading {
            text-align: center;
            font-size: 0.6rem;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: #fff;
            background-color: var(--primary-color);
            box-shadow: inset 4px 0 0 var(--secondary-color);
        }

        .nav-icon {
            font-size: 1.2rem;
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            padding: 0.75rem;
            justify-content: center;
        }

        .sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        /* Topbar styles */
        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            height: var(--topbar-height);
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            z-index: 998;
            transition: all 0.3s ease;
        }

        .sidebar-toggle {
            font-size: 1.2rem;
            color: var(--primary-dark);
            cursor: pointer;
            margin-right: 1.5rem;
        }

        .topbar-title {
            flex-grow: 1;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--primary-dark);
        }

        .topbar-menu {
            display: flex;
            align-items: center;
        }

        .topbar-menu .dropdown-toggle {
            display: flex;
            align-items: center;
            color: var(--primary-dark);
            text-decoration: none;
        }

        .topbar-menu .dropdown-toggle::after {
            display: none;
        }

        .topbar-menu .dropdown-menu {
            margin-top: 0.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 0.5rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-right: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-light);
            color: #fff;
            font-weight: 500;
        }

        .user-info {
            line-height: 1.2;
        }

        .user-name {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        /* Main content styles */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: var(--content-padding);
            transition: all 0.3s ease;
            min-height: calc(100vh - var(--topbar-height));
        }

        .sidebar.collapsed~.main-content,
        .sidebar.collapsed~.topbar {
            margin-left: var(--sidebar-collapsed-width);
            left: var(--sidebar-collapsed-width);
        }

        /* Page header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .page-subtitle {
            color: var(--text-light);
            margin-top: 0.25rem;
        }

        /* Breadcrumbs */
        .breadcrumb {
            margin-bottom: 0;
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--text-medium);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 1.25rem 1.5rem;
        }

        .card-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Form styles */
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            height: auto;
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(64, 145, 108, 0.15);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Button styles */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-success:hover,
        .btn-success:focus {
            background-color: var(--secondary-dark);
            border-color: var(--secondary-dark);
        }

        /* Table styles */
        .table {
            margin-bottom: 0;
        }

        .table th {
            font-weight: 600;
            color: var(--primary-dark);
            background-color: rgba(64, 145, 108, 0.05);
            border-bottom-width: 1px;
        }

        /* DataTables */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 1rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }

            .sidebar.mobile-show {
                width: var(--sidebar-width);
                transform: translateX(0);
            }

            .topbar,
            .main-content {
                margin-left: 0 !important;
                left: 0 !important;
            }

            .topbar {
                padding-left: 1rem;
            }

            .sidebar-brand h5 {
                display: block !important;
            }

            .nav-link span {
                display: inline !important;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="../assets/images/logo.png" alt="CDK Bojonegoro">
            <h5><?php echo $siteTitle; ?></h5>
        </div>

        <?php include 'sidebar.php'; ?>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <div class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </div>

        <div class="topbar-title">
            <?php echo $page_title; ?>
        </div>

        <div class="topbar-menu">
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['nama_lengkap'] ?? 'User', 0, 1)); ?>
                    </div>
                    <div class="user-info d-none d-sm-block">
                        <div class="user-name"><?php echo $_SESSION['nama_lengkap'] ?? 'User'; ?></div>
                        <div class="user-role"><?php echo ucfirst($_SESSION['role'] ?? 'user'); ?></div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profil</a></li>
                    <li><a class="dropdown-item" href="change-password.php"><i class="fas fa-key me-2"></i> Ubah
                            Password</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Display flash messages
        echo flashMessage('success');
        echo flashMessage('error');
        echo flashMessage('warning');
        echo flashMessage('message');
        ?>