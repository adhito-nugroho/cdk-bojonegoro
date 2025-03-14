<?php
/**
 * Logout Admin untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menangani proses logout dari halaman admin
 */

// Mulai session
session_start();

// Define BASE_PATH
define('BASE_PATH', dirname(__DIR__) . '/');

// Include config dan fungsi-fungsi
require_once BASE_PATH . 'includes/config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/functions.php';

// Log aktivitas logout jika user sedang login
if (isset($_SESSION['user_id'])) {
    logActivity('Logout', 'users', $_SESSION['user_id']);
}

// Hapus semua data session
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: " . ADMIN_URL . "/login.php");
exit;