<?php
/**
 * Hapus Pengguna Admin untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menangani proses hapus pengguna
 */

// Define BASE_PATH
define('BASE_PATH', dirname(dirname(dirname(__DIR__))) . '/');

// Include konfigurasi dan fungsi
require_once BASE_PATH . 'includes/config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/functions.php';
require_once dirname(dirname(__DIR__)) . '/config.php';
require_once dirname(dirname(__DIR__)) . '/functions.php';

// Cek login dan hak akses
requireAdmin();

// Periksa parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = 'ID pengguna tidak valid.';
    redirect(ADMIN_URL . '/modules/users/index.php');
    exit;
}

$user_id = (int) $_GET['id'];

// Cek jika pengguna mencoba menghapus dirinya sendiri
if ($user_id === (int) $_SESSION['user_id']) {
    $_SESSION['error_message'] = 'Anda tidak dapat menghapus akun yang sedang digunakan.';
    redirect(ADMIN_URL . '/modules/users/index.php');
    exit;
}

// Dapatkan data user
$user = getUserById($user_id);

// Jika user tidak ditemukan
if (!$user) {
    $_SESSION['error_message'] = 'Pengguna tidak ditemukan.';
    redirect(ADMIN_URL . '/modules/users/index.php');
    exit;
}

// Cek jumlah admin jika akan menghapus admin
if ($user['role'] === 'admin') {
    $adminCount = db_fetch("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")['total'];

    if ($adminCount <= 1) {
        $_SESSION['error_message'] = 'Tidak dapat menghapus admin terakhir.';
        redirect(ADMIN_URL . '/modules/users/index.php');
        exit;
    }
}

// Proses hapus user
$result = deleteUser($user_id);

if ($result) {
    // Hapus foto profil jika ada
    if (!empty($user['profile_image'])) {
        $profile_image_path = UPLOADS_PATH . 'profile/' . $user['profile_image'];
        if (file_exists($profile_image_path)) {
            unlink($profile_image_path);
        }
    }

    // Log aktivitas
    logActivity('Menghapus pengguna', 'users', $user_id, json_encode($user), null);

    // Set pesan sukses
    $_SESSION['success_message'] = 'Pengguna berhasil dihapus.';
} else {
    // Set pesan error
    $_SESSION['error_message'] = 'Gagal menghapus pengguna. Silakan coba lagi.';
}

// Redirect ke halaman daftar pengguna
redirect(ADMIN_URL . '/modules/users/index.php');
exit;