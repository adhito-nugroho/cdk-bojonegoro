<?php
/**
 * Auth Check untuk admin CDK Wilayah Bojonegoro
 * 
 * File ini mengecek status login user saat mengakses halaman admin
 */

// Mulai session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define BASE_PATH jika belum didefinisikan
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)) . '/');
}

// Include config dan fungsi-fungsi
require_once BASE_PATH . 'includes/config.php';
require_once BASE_PATH . 'includes/functions.php';
require_once dirname(__DIR__) . '/config.php';

// Periksa jika sudah login
if (!isLoggedIn()) {
    // Simpan URL saat ini untuk redirect setelah login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

    // Redirect ke halaman login
    header('Location: ' . ADMIN_URL . '/login.php');
    exit;
}

// Ambil data user saat ini
$currentUser = getCurrentUser();

// Jika data user tidak ditemukan (mungkin telah dihapus), logout
if (!$currentUser) {
    // Hapus session
    session_unset();
    session_destroy();

    // Redirect ke halaman login
    header('Location: ' . ADMIN_URL . '/login.php');
    exit;
}

// Cek jika mencoba mengakses halaman yang memerlukan hak akses admin
$admin_only_pages = [
    'users',          // Manajemen user
    'settings',       // Pengaturan website
    'menu'            // Manajemen menu
];

// Dapatkan folder saat ini
$current_folder = basename(dirname($_SERVER['SCRIPT_NAME']));

// Jika halaman memerlukan hak akses admin dan user bukan admin, tolak akses
if (in_array($current_folder, $admin_only_pages) && $currentUser['role'] !== 'admin') {
    $_SESSION['error_message'] = 'Anda tidak memiliki akses ke halaman ini.';
    header('Location: ' . ADMIN_URL . '/index.php');
    exit;
}

// Cek jika mencoba mengakses halaman yang memerlukan hak akses editor atau admin
$editor_only_pages = [
    'publikasi',      // Manajemen publikasi
    'dokumen',        // Manajemen dokumen
    'galeri',         // Manajemen galeri
    'statistik',      // Manajemen statistik
    'capaian',        // Manajemen capaian
    'program',        // Manajemen program
    'layanan',        // Manajemen layanan
    'struktur',       // Manajemen struktur organisasi
    'wilayah'         // Manajemen wilayah kerja
];

// Jika halaman memerlukan hak akses editor dan user bukan editor atau admin, tolak akses
if (in_array($current_folder, $editor_only_pages) && $currentUser['role'] !== 'admin' && $currentUser['role'] !== 'editor') {
    $_SESSION['error_message'] = 'Anda tidak memiliki akses ke halaman ini.';
    header('Location: ' . ADMIN_URL . '/index.php');
    exit;
}

// Update last activity time untuk session timeout (opsional)
$_SESSION['last_activity'] = time();

// Setelah melewati semua pengecekan, user diizinkan mengakses halaman