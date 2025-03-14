<?php
/**
 * Functions untuk website CDK Wilayah Bojonegoro
 * 
 * File ini berisi fungsi-fungsi umum yang digunakan di website
 */

// Pastikan file config sudah diinclude
if (!defined('BASE_PATH')) {
    require_once 'config.php';
}

/**
 * Fungsi untuk memformat pesan error
 * 
 * @param string $message Pesan error
 * @return string HTML pesan error
 */
function showError($message)
{
    return '<div class="alert alert-danger">' . $message . '</div>';
}

/**
 * Fungsi untuk memformat pesan sukses
 * 
 * @param string $message Pesan sukses
 * @return string HTML pesan sukses
 */
function showSuccess($message)
{
    return '<div class="alert alert-success">' . $message . '</div>';
}

/**
 * Fungsi untuk memformat pesan info
 * 
 * @param string $message Pesan info
 * @return string HTML pesan info
 */
function showInfo($message)
{
    return '<div class="alert alert-info">' . $message . '</div>';
}

/**
 * Fungsi untuk redirect
 * 
 * @param string $url URL tujuan
 * @return void
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit();
}

/**
 * Fungsi untuk membersihkan input
 * 
 * @param string $data Data yang akan dibersihkan
 * @return string Data yang sudah dibersihkan
 */
function cleanInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Fungsi untuk mengecek apakah user sudah login
 * 
 * @return boolean True jika user sudah login, false jika belum
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Fungsi untuk mengecek apakah user adalah admin
 * 
 * @return boolean True jika user adalah admin, false jika bukan
 */
function isAdmin()
{
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

/**
 * Fungsi untuk mengambil data user yang sedang login
 * 
 * @return array|boolean Data user yang sedang login atau false jika belum login
 */
function getCurrentUser()
{
    if (!isLoggedIn()) {
        return false;
    }

    require_once 'db.php';
    return db_fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

/**
 * Fungsi untuk mengambil nilai setting
 * 
 * @param string $key Key setting
 * @param string $default Nilai default jika setting tidak ditemukan
 * @return string Nilai setting
 */
function getSetting($key, $default = '')
{
    require_once 'db.php';
    $setting = db_fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);

    if ($setting) {
        return $setting['setting_value'];
    }

    return $default;
}

/**
 * Fungsi untuk mengupdate setting
 * 
 * @param string $key Key setting
 * @param string $value Nilai setting
 * @return boolean True jika berhasil, false jika gagal
 */
function updateSetting($key, $value)
{
    require_once 'db.php';
    $setting = db_fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);

    if ($setting) {
        return db_update('settings', ['setting_value' => $value], 'id = ?', [$setting['id']]);
    } else {
        return db_insert('settings', [
            'setting_key' => $key,
            'setting_value' => $value
        ]);
    }
}

/**
 * Fungsi untuk mengupload file
 * 
 * @param array $file File yang diupload ($_FILES['field'])
 * @param string $destination Folder tujuan
 * @param array $allowed_ext Extension yang diperbolehkan
 * @param int $max_size Ukuran maksimal file (dalam bytes)
 * @return string|boolean Nama file baru atau false jika gagal
 */
function uploadFile($file, $destination, $allowed_ext = [], $max_size = 2097152)
{
    // Cek apakah ada error
    if ($file['error'] != UPLOAD_ERR_OK) {
        return false;
    }

    // Cek ukuran file
    if ($file['size'] > $max_size) {
        return false;
    }

    // Cek extension
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!empty($allowed_ext) && !in_array($file_ext, $allowed_ext)) {
        return false;
    }

    // Buat nama file baru (gunakan timestamp)
    $new_name = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file['name']);
    $target_file = $destination . '/' . $new_name;

    // Buat direktori jika belum ada
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $new_name;
    }

    return false;
}

/**
 * Fungsi untuk membuat slug
 * 
 * @param string $text Text yang akan dijadikan slug
 * @return string Slug dari text
 */
function createSlug($text)
{
    // Ganti spasi dengan dash
    $text = preg_replace('/ +/', '-', trim($text));

    // Transliterate ke ASCII
    if (function_exists('iconv')) {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    }

    // Hapus karakter yang tidak valid
    $text = preg_replace('/[^a-zA-Z0-9\-]/', '', $text);

    // Hapus dash berurutan
    $text = preg_replace('/-+/', '-', $text);

    // Trim dash di awal dan akhir
    $text = trim($text, '-');

    // Lower case
    $text = strtolower($text);

    return $text;
}

/**
 * Fungsi untuk memformat tanggal ke format Indonesia
 * 
 * @param string $date Tanggal dalam format database (YYYY-MM-DD)
 * @param boolean $with_time Tampilkan waktu juga
 * @return string Tanggal dalam format Indonesia
 */
function formatTanggal($date, $with_time = false)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    if ($with_time) {
        $datetime = new DateTime($date);
        return $datetime->format('d') . ' ' . $bulan[(int) $datetime->format('m')] . ' ' . $datetime->format('Y H:i');
    } else {
        $datetime = new DateTime($date);
        return $datetime->format('d') . ' ' . $bulan[(int) $datetime->format('m')] . ' ' . $datetime->format('Y');
    }
}

/**
 * Fungsi untuk memotong teks
 * 
 * @param string $text Teks yang akan dipotong
 * @param int $length Panjang maksimal teks
 * @param string $append Teks yang ditambahkan di akhir jika teks terpotong
 * @return string Teks yang sudah dipotong
 */
function trimText($text, $length = 100, $append = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }

    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));

    return $text . $append;
}

/**
 * Fungsi untuk logging activity
 * 
 * @param string $action Aksi yang dilakukan
 * @param string $table_name Nama tabel yang diakses
 * @param int $record_id ID record yang diakses
 * @param string $old_values Nilai lama dalam format JSON
 * @param string $new_values Nilai baru dalam format JSON
 * @return boolean True jika berhasil, false jika gagal
 */
function logActivity($action, $table_name = null, $record_id = null, $old_values = null, $new_values = null)
{
    require_once 'db.php';

    $data = [
        'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
        'action' => $action,
        'table_name' => $table_name,
        'record_id' => $record_id,
        'old_values' => $old_values,
        'new_values' => $new_values,
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    ];

    return db_insert('activity_log', $data);
}

/**
 * Fungsi untuk mengambil URL saat ini
 * 
 * @return string URL saat ini
 */
function getCurrentUrl()
{
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $url;
}

/**
 * Fungsi untuk mengambil file extension dari nama file
 * 
 * @param string $filename Nama file
 * @return string Extension file
 */
function getFileExtension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Fungsi untuk mengecek apakah file adalah gambar
 * 
 * @param string $filename Nama file
 * @return boolean True jika file adalah gambar, false jika bukan
 */
function isImage($filename)
{
    $image_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    return in_array(getFileExtension($filename), $image_ext);
}

/**
 * Fungsi untuk mendapatkan icon berdasarkan extension file
 * 
 * @param string $filename Nama file
 * @return string Icon file
 */
function getFileIcon($filename)
{
    $ext = getFileExtension($filename);

    switch ($ext) {
        case 'pdf':
            return 'ri-file-pdf-line';
        case 'doc':
        case 'docx':
            return 'ri-file-word-line';
        case 'xls':
        case 'xlsx':
            return 'ri-file-excel-line';
        case 'ppt':
        case 'pptx':
            return 'ri-file-ppt-line';
        case 'zip':
        case 'rar':
        case '7z':
            return 'ri-file-zip-line';
        case 'txt':
            return 'ri-file-text-line';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'webp':
            return 'ri-image-line';
        case 'mp4':
        case 'avi':
        case 'mov':
            return 'ri-video-line';
        case 'mp3':
        case 'wav':
            return 'ri-music-line';
        default:
            return 'ri-file-line';
    }
}

/**
 * Fungsi untuk memformat ukuran file
 * 
 * @param int $size Ukuran file dalam bytes
 * @return string Ukuran file dalam format yang mudah dibaca
 */
function formatFileSize($size)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;

    while ($size >= 1024 && $i < 4) {
        $size /= 1024;
        $i++;
    }

    return round($size, 2) . ' ' . $units[$i];
}

/**
 * Fungsi untuk menghasilkan token CSRF
 * 
 * @return string Token CSRF
 */
function generateCsrfToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Fungsi untuk memvalidasi token CSRF
 * 
 * @param string $token Token CSRF dari form
 * @return boolean True jika token valid, false jika tidak
 */
function validateCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}

/**
 * Fungsi untuk membuat token CSRF hidden input
 * 
 * @return string HTML hidden input dengan token CSRF
 */
function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' . generateCsrfToken() . '">';
}

/**
 * Fungsi untuk mengecek apakah request adalah AJAX
 * 
 * @return boolean True jika request adalah AJAX, false jika bukan
 */
function isAjax()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Fungsi untuk mengambil menu berdasarkan posisi
 * 
 * @param string $position Posisi menu (main_menu, footer_menu, quick_links)
 * @return array Menu
 */
function getMenuByPosition($position)
{
    require_once 'db.php';
    return db_fetch_all(
        "SELECT * FROM menu WHERE position = ? AND is_active = 1 ORDER BY order_number ASC",
        [$position]
    );
}