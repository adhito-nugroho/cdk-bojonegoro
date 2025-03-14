<?php
/**
 * Config Admin untuk website CDK Wilayah Bojonegoro
 * 
 * File ini berisi konfigurasi khusus untuk bagian admin
 */

// Define BASE_PATH jika belum didefinisikan
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__) . '/');

    // Include konfigurasi utama
    require_once BASE_PATH . 'includes/config.php';
}

// Definisi konstanta admin
define('ADMIN_ASSETS', ADMIN_URL . '/assets');
define('ADMIN_UPLOADS', UPLOADS_URL);

// Judul admin panel
define('ADMIN_TITLE', 'Admin Panel - CDK Wilayah Bojonegoro');

// Pengaturan pagination
define('ITEMS_PER_PAGE', 10);

// Periksa jika sudah login (untuk halaman yang memerlukan autentikasi)
function requireLogin()
{
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

// Periksa role admin
function requireAdmin()
{
    requireLogin();

    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        $_SESSION['error_message'] = 'Anda tidak memiliki akses ke halaman ini.';
        header('Location: ' . ADMIN_URL . '/index.php');
        exit;
    }
}

// Periksa role editor atau admin
function requireEditorOrAdmin()
{
    requireLogin();

    if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'editor')) {
        $_SESSION['error_message'] = 'Anda tidak memiliki akses ke halaman ini.';
        header('Location: ' . ADMIN_URL . '/index.php');
        exit;
    }
}

// Fungsi untuk menampilkan pesan sukses
function adminAlert($message, $type = 'success')
{
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

// Fungsi untuk menampilkan status aktif/nonaktif
function getStatusBadge($isActive)
{
    if ($isActive) {
        return '<span class="badge bg-success">Aktif</span>';
    } else {
        return '<span class="badge bg-danger">Nonaktif</span>';
    }
}

// Fungsi untuk menentukan menu sidebar yang aktif
function isActiveMenu($menuName)
{
    $currentPage = basename($_SERVER['SCRIPT_NAME']);
    $currentFolder = basename(dirname($_SERVER['SCRIPT_NAME']));

    // Kasus khusus untuk index.php di folder admin
    if ($currentPage === 'index.php' && $currentFolder === 'admin' && $menuName === 'dashboard') {
        return true;
    }

    // Kasus umum untuk file di subfolder modules admin
    if ($currentFolder === $menuName) {
        return true;
    }

    return false;
}

// Fungsi untuk mendapatkan icon menu
function getMenuIcon($menuName)
{
    $icons = [
        'dashboard' => 'fas fa-tachometer-alt',
        'users' => 'fas fa-users',
        'profile' => 'fas fa-user-circle',
        'wilayah' => 'fas fa-map-marked-alt',
        'struktur' => 'fas fa-sitemap',
        'layanan' => 'fas fa-hands-helping',
        'program' => 'fas fa-clipboard-list',
        'statistik' => 'fas fa-chart-bar',
        'capaian' => 'fas fa-tasks',
        'publikasi' => 'fas fa-newspaper',
        'dokumen' => 'fas fa-file-alt',
        'galeri' => 'fas fa-images',
        'pesan' => 'fas fa-envelope',
        'settings' => 'fas fa-cogs',
        'menu' => 'fas fa-list'
    ];

    return $icons[$menuName] ?? 'fas fa-circle';
}

// Fungsi untuk membuat link pagination
function getPaginationLinks($current_page, $total_pages, $url_pattern)
{
    $links = '';

    // Previous button
    if ($current_page > 1) {
        $links .= '<li class="page-item">
                      <a class="page-link" href="' . sprintf($url_pattern, $current_page - 1) . '" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                   </li>';
    } else {
        $links .= '<li class="page-item disabled">
                      <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                   </li>';
    }

    // Page numbers
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);

    // Always show first page
    if ($start_page > 1) {
        $links .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, 1) . '">1</a></li>';
        if ($start_page > 2) {
            $links .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
    }

    // Page numbers
    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $current_page) {
            $links .= '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
        } else {
            $links .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, $i) . '">' . $i . '</a></li>';
        }
    }

    // Always show last page
    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            $links .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        $links .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, $total_pages) . '">' . $total_pages . '</a></li>';
    }

    // Next button
    if ($current_page < $total_pages) {
        $links .= '<li class="page-item">
                      <a class="page-link" href="' . sprintf($url_pattern, $current_page + 1) . '" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                   </li>';
    } else {
        $links .= '<li class="page-item disabled">
                      <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                   </li>';
    }

    return $links;
}