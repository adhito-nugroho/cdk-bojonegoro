<?php
/**
 * Konfigurasi Database untuk website CDK Wilayah Bojonegoro
 * 
 * File ini berisi konfigurasi database dan pengaturan dasar
 */

// Konfigurasi database
define('DB_HOST', 'localhost');      // Host database, biasanya localhost
define('DB_USER', 'root');           // Username database
define('DB_PASS', '');               // Password database, biasanya kosong untuk XAMPP
define('DB_NAME', 'cdk_bojonegoro'); // Nama database

// Konfigurasi website
define('SITE_NAME', 'CDK Wilayah Bojonegoro');
define('SITE_URL', 'http://localhost/cdk-bojonegoro'); // Ganti dengan URL yang sesuai

// Konfigurasi timezone
date_default_timezone_set('Asia/Jakarta');

// Pengaturan directory
define('BASE_PATH', dirname(__DIR__) . '/');
define('ADMIN_PATH', BASE_PATH . 'admin/');
define('ASSETS_PATH', BASE_PATH . 'assets/');
define('UPLOADS_PATH', ASSETS_PATH . 'uploads/');

// Pengaturan URL
define('ADMIN_URL', SITE_URL . '/admin');
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOADS_URL', ASSETS_URL . '/uploads');

// Pengaturan debug (set ke false untuk produksi)
define('DEBUG', true);

// Pengaturan session
if (!isset($_SESSION)) {
    session_start();
}

// Pengaturan error reporting
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}