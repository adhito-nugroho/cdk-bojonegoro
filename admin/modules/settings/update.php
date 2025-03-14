<?php
/**
 * Update Pengaturan untuk CDK Wilayah Bojonegoro
 * 
 * File ini menangani proses update pengaturan website
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

// Cek jika permintaan adalah AJAX
if (!isAjax()) {
    // Jika bukan AJAX, redirect ke halaman settings
    redirect(ADMIN_URL . '/modules/settings/index.php');
    exit;
}

// Inisialisasi respons
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Validasi token CSRF
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    $response['message'] = 'Token CSRF tidak valid. Silakan coba lagi.';
    echo json_encode($response);
    exit;
}

// Proses update berdasarkan kategori
if (isset($_POST['category']) && isset($_POST['data'])) {
    $category = cleanInput($_POST['category']);
    $data = $_POST['data'];

    // Dapatkan pengaturan saat ini untuk pencatatan perubahan
    $current_settings = [];
    foreach ($data as $key => $value) {
        $current_settings[$key] = getSetting($key, '');
    }

    // Validasi dan sanitasi data
    $validated_data = [];
    $validation_error = false;

    foreach ($data as $key => $value) {
        // Validasi berdasarkan key
        switch ($key) {
            case 'contact_email':
                if (empty($value)) {
                    $response['message'] = 'Email kontak wajib diisi.';
                    $validation_error = true;
                } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $response['message'] = 'Format email tidak valid.';
                    $validation_error = true;
                } else {
                    $validated_data[$key] = $value;
                }
                break;

            case 'site_title':
            case 'hero_title':
                if (empty($value)) {
                    $response['message'] = 'Judul wajib diisi.';
                    $validation_error = true;
                } else {
                    $validated_data[$key] = cleanInput($value);
                }
                break;

            default:
                // Untuk key lainnya, cukup bersihkan input
                $validated_data[$key] = cleanInput($value);
                break;
        }

        // Jika terjadi error validasi, hentikan proses
        if ($validation_error) {
            echo json_encode($response);
            exit;
        }
    }

    // Update data jika validasi berhasil
    $update_success = true;
    foreach ($validated_data as $key => $value) {
        if (!updateSetting($key, $value)) {
            $update_success = false;
        }
    }

    if ($update_success) {
        // Log aktivitas
        logActivity(
            'Memperbarui pengaturan ' . $category,
            'settings',
            null,
            json_encode($current_settings),
            json_encode($validated_data)
        );

        $response['success'] = true;
        $response['message'] = 'Pengaturan ' . ucfirst($category) . ' berhasil diperbarui.';
        $response['data'] = $validated_data;
    } else {
        $response['message'] = 'Gagal memperbarui beberapa pengaturan. Silakan coba lagi.';
    }
} else {
    $response['message'] = 'Data yang diperlukan tidak lengkap.';
}

// Mengembalikan respons dalam format JSON
echo json_encode($response);
exit;