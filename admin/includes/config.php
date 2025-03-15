<?php
/**
 * Database Connection and Configuration
 * config.php
 */

// Error reporting
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');         // Change according to your setup
define('DB_PASS', '');             // Change according to your setup
define('DB_NAME', 'cdk_bojonegoro');

// Site Configuration
define('SITE_NAME', 'CDK Wilayah Bojonegoro');
define('SITE_URL', 'http://localhost/cdk-bojonegoro');
define('ADMIN_URL', SITE_URL . '/admin');

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../../uploads/');
define('PUBLIKASI_DIR', UPLOAD_DIR . 'publikasi/');
define('GALERI_DIR', UPLOAD_DIR . 'galeri/');
define('DOKUMEN_DIR', UPLOAD_DIR . 'dokumen/');

// Max File Size (in bytes) - 5MB
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);

// Date and Time Zone
date_default_timezone_set('Asia/Jakarta');

// Pagination defaults
define('DEFAULT_ITEMS_PER_PAGE', 10);

// Activity Logging
define('ENABLE_ACTIVITY_LOGS', true);

// Security settings
define('PASSWORD_RESET_TIMEOUT', 60 * 60); // 1 hour in seconds

// Database Connection
function getConnection()
{
    static $conn = null;

    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }

            $conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log($e->getMessage());
            die("Database connection error. Please try again later.");
        }
    }

    return $conn;
}

// Get Site Settings
function getSetting($key)
{
    $conn = getConnection();
    $key = $conn->real_escape_string($key);

    $query = "SELECT nilai FROM pengaturan WHERE kunci = '$key' LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['nilai'];
    }

    return null;
}

// Set Site Settings
function setSetting($key, $value)
{
    $conn = getConnection();
    $key = $conn->real_escape_string($key);
    $value = $conn->real_escape_string($value);

    $query = "INSERT INTO pengaturan (kunci, nilai) VALUES ('$key', '$value') 
              ON DUPLICATE KEY UPDATE nilai = '$value'";

    return $conn->query($query);
}

// Get multiple settings at once
function getSettings($keys)
{
    $conn = getConnection();
    $result = [];

    if (!is_array($keys) || empty($keys)) {
        return $result;
    }

    // Build placeholders for keys
    $placeholders = array();
    foreach ($keys as $key) {
        $placeholders[] = "'" . $conn->real_escape_string($key) . "'";
    }

    $keysList = implode(',', $placeholders);

    $query = "SELECT kunci, nilai FROM pengaturan WHERE kunci IN ($keysList)";
    $queryResult = $conn->query($query);

    if ($queryResult) {
        while ($row = $queryResult->fetch_assoc()) {
            $result[$row['kunci']] = $row['nilai'];
        }
    }

    return $result;
}

// Initialize settings if not exist
function initializeSettings()
{
    $conn = getConnection();

    $defaultSettings = [
        'nama_instansi' => 'Cabang Dinas Kehutanan Wilayah Bojonegoro',
        'alamat' => 'Jl. Hayam Wuruk No. 9, Bojonegoro, Jawa Timur',
        'telepon' => '(0353) 123456',
        'email' => 'info@cdk-bojonegoro.jatimprov.go.id',
        'jam_layanan' => 'Senin - Jumat: 08:00 - 16:00 WIB',
        'logo' => 'logo.png',
        'map_coordinates' => '-7.1507, 111.8871',
        'meta_description' => 'Cabang Dinas Kehutanan Wilayah Bojonegoro - Melayani masyarakat dalam pengelolaan dan pelestarian hutan',
        'tahun_berdiri' => '2018',
        'facebook' => 'https://facebook.com/',
        'twitter' => 'https://twitter.com/',
        'instagram' => 'https://instagram.com/',
        'youtube' => 'https://youtube.com/'
    ];

    foreach ($defaultSettings as $key => $value) {
        $existingValue = getSetting($key);

        if ($existingValue === null) {
            setSetting($key, $value);
        }
    }
}

// Call initialize settings
initializeSettings();