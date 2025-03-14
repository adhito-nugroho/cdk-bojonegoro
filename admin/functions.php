<?php
/**
 * Functions Admin untuk website CDK Wilayah Bojonegoro
 * 
 * File ini berisi fungsi-fungsi khusus untuk bagian admin
 */

// Define BASE_PATH jika belum didefinisikan
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__) . '/');

    // Include konfigurasi utama
    require_once BASE_PATH . 'includes/config.php';
}

// Include file database dan functions
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/functions.php';

// Include konfigurasi admin
require_once 'config.php';

/**
 * Fungsi untuk mendapatkan jumlah total user
 * 
 * @return int Jumlah total user
 */
function getTotalUsers()
{
    return db_fetch("SELECT COUNT(*) as total FROM users")['total'];
}

/**
 * Fungsi untuk mendapatkan jumlah total publikasi
 * 
 * @return int Jumlah total publikasi
 */
function getTotalPublikasi()
{
    return db_fetch("SELECT COUNT(*) as total FROM publikasi WHERE is_active = 1")['total'];
}

/**
 * Fungsi untuk mendapatkan jumlah total dokumen
 * 
 * @return int Jumlah total dokumen
 */
function getTotalDokumen()
{
    return db_fetch("SELECT COUNT(*) as total FROM dokumen WHERE is_active = 1")['total'];
}

/**
 * Fungsi untuk mendapatkan jumlah total galeri
 * 
 * @return int Jumlah total galeri
 */
function getTotalGaleri()
{
    return db_fetch("SELECT COUNT(*) as total FROM galeri WHERE is_active = 1")['total'];
}

/**
 * Fungsi untuk mendapatkan jumlah total pesan
 * 
 * @return int Jumlah total pesan
 */
function getTotalPesan()
{
    return db_fetch("SELECT COUNT(*) as total FROM kontak_pesan")['total'];
}

/**
 * Fungsi untuk mendapatkan jumlah pesan yang belum dibaca
 * 
 * @return int Jumlah pesan yang belum dibaca
 */
function getUnreadPesan()
{
    return db_fetch("SELECT COUNT(*) as total FROM kontak_pesan WHERE is_read = 0")['total'];
}

/**
 * Fungsi untuk mendapatkan data statistik dashboard
 * 
 * @return array Data statistik dashboard
 */
function getDashboardStats()
{
    return [
        'total_users' => getTotalUsers(),
        'total_publikasi' => getTotalPublikasi(),
        'total_dokumen' => getTotalDokumen(),
        'total_galeri' => getTotalGaleri(),
        'total_pesan' => getTotalPesan(),
        'unread_pesan' => getUnreadPesan()
    ];
}

/**
 * Fungsi untuk mendapatkan 5 pesan terbaru
 * 
 * @return array 5 pesan terbaru
 */
function getLatestMessages()
{
    return db_fetch_all("
        SELECT * FROM kontak_pesan 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
}

/**
 * Fungsi untuk mendapatkan 5 publikasi terbaru
 * 
 * @return array 5 publikasi terbaru
 */
function getLatestPublikasi()
{
    return db_fetch_all("
        SELECT p.*, k.nama_kategori
        FROM publikasi p
        JOIN kategori_publikasi k ON p.kategori_id = k.id
        WHERE p.is_active = 1
        ORDER BY p.tanggal_publikasi DESC, p.id DESC
        LIMIT 5
    ");
}

/**
 * Fungsi untuk mendapatkan 5 aktivitas terbaru
 * 
 * @return array 5 aktivitas terbaru
 */
function getLatestActivities()
{
    return db_fetch_all("
        SELECT a.*, u.name as user_name
        FROM activity_log a
        LEFT JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC
        LIMIT 5
    ");
}

/**
 * Fungsi untuk memformat aktivitas log
 * 
 * @param array $log Data log aktivitas
 * @return string Teks aktivitas yang diformat
 */
function formatActivityLog($log)
{
    $text = '';

    // User
    if (!empty($log['user_name'])) {
        $text .= '<strong>' . $log['user_name'] . '</strong> ';
    } else {
        $text .= '<strong>Unknown User</strong> ';
    }

    // Action
    $text .= $log['action'];

    // Table and record
    if (!empty($log['table_name'])) {
        $text .= ' pada ' . formatTableName($log['table_name']);

        if (!empty($log['record_id'])) {
            $text .= ' #' . $log['record_id'];
        }
    }

    return $text;
}

/**
 * Fungsi untuk memformat nama tabel ke bentuk yang lebih mudah dibaca
 * 
 * @param string $tableName Nama tabel
 * @return string Nama tabel yang diformat
 */
function formatTableName($tableName)
{
    $tableLabels = [
        'users' => 'Pengguna',
        'settings' => 'Pengaturan',
        'wilayah_kerja' => 'Wilayah Kerja',
        'tugas_fungsi' => 'Tugas & Fungsi',
        'struktur_organisasi' => 'Struktur Organisasi',
        'layanan' => 'Layanan',
        'layanan_detail' => 'Detail Layanan',
        'program' => 'Program',
        'program_detail' => 'Detail Program',
        'statistik' => 'Statistik',
        'kawasan_hutan' => 'Kawasan Hutan',
        'hasil_hutan' => 'Hasil Hutan',
        'capaian_program' => 'Capaian Program',
        'kategori_publikasi' => 'Kategori Publikasi',
        'publikasi' => 'Publikasi',
        'tags' => 'Tag',
        'publikasi_tags' => 'Tag Publikasi',
        'dokumen' => 'Dokumen',
        'galeri_kategori' => 'Kategori Galeri',
        'galeri' => 'Galeri',
        'kontak_pesan' => 'Pesan Kontak',
        'menu' => 'Menu'
    ];

    return $tableLabels[$tableName] ?? $tableName;
}

/**
 * Fungsi untuk mendapatkan data user berdasarkan ID
 * 
 * @param int $userId ID user
 * @return array|bool Data user atau false jika tidak ditemukan
 */
function getUserById($userId)
{
    return db_fetch("SELECT * FROM users WHERE id = ?", [$userId]);
}

/**
 * Fungsi untuk mendapatkan semua daftar user
 * 
 * @param int $page Halaman saat ini
 * @param int $perPage Jumlah item per halaman
 * @return array Data hasil query dan informasi pagination
 */
function getAllUsers($page = 1, $perPage = ITEMS_PER_PAGE)
{
    $offset = ($page - 1) * $perPage;

    $total = db_fetch("SELECT COUNT(*) as total FROM users")['total'];
    $totalPages = ceil($total / $perPage);

    $users = db_fetch_all("
        SELECT * FROM users 
        ORDER BY name ASC 
        LIMIT $offset, $perPage
    ");

    return [
        'data' => $users,
        'pagination' => [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages
        ]
    ];
}

/**
 * Fungsi untuk menambahkan user baru
 * 
 * @param array $data Data user baru
 * @return int|bool ID user baru atau false jika gagal
 */
function addUser($data)
{
    // Hash password
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    return db_insert('users', $data);
}

/**
 * Fungsi untuk memperbarui data user
 * 
 * @param int $userId ID user
 * @param array $data Data user yang diperbarui
 * @return bool True jika berhasil, false jika gagal
 */
function updateUser($userId, $data)
{
    // Jika password diisi, hash password baru
    if (!empty($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    } else {
        // Jika password kosong, hapus key password dari data
        unset($data['password']);
    }

    return db_update('users', $data, 'id = ?', [$userId]);
}

/**
 * Fungsi untuk menghapus user
 * 
 * @param int $userId ID user
 * @return bool True jika berhasil, false jika gagal
 */
function deleteUser($userId)
{
    // Cek apakah user yang akan dihapus adalah user terakhir dengan role admin
    $adminCount = db_fetch("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")['total'];
    $userToDelete = getUserById($userId);

    if ($adminCount <= 1 && $userToDelete['role'] === 'admin') {
        return false; // Jangan hapus admin terakhir
    }

    return db_delete('users', 'id = ?', [$userId]);
}

/**
 * Fungsi untuk mendapatkan pengaturan website
 * 
 * @return array Daftar pengaturan website
 */
function getAllSettings()
{
    $settings = db_fetch_all("SELECT * FROM settings ORDER BY setting_key ASC");
    $result = [];

    // Format hasil query menjadi key-value
    foreach ($settings as $setting) {
        $result[$setting['setting_key']] = $setting['setting_value'];
    }

    return $result;
}

/**
 * Fungsi untuk memperbarui pengaturan website
 * 
 * @param array $data Data pengaturan yang diperbarui
 * @return bool True jika semua berhasil, false jika ada yang gagal
 */
function updateSettings($data)
{
    $success = true;

    foreach ($data as $key => $value) {
        if (!updateSetting($key, $value)) {
            $success = false;
        }
    }

    return $success;
}

/**
 * Fungsi untuk upload gambar dengan resize (opsional)
 * 
 * @param array $file File yang diupload ($_FILES['field'])
 * @param string $destination Folder tujuan
 * @param int $maxWidth Lebar maksimal gambar (0 untuk tidak resize)
 * @param int $maxHeight Tinggi maksimal gambar (0 untuk tidak resize)
 * @param int $quality Kualitas gambar (1-100, hanya untuk JPEG)
 * @return string|boolean Nama file baru atau false jika gagal
 */
function uploadImage($file, $destination, $maxWidth = 0, $maxHeight = 0, $quality = 80)
{
    // Cek apakah file adalah gambar
    if (!isImage($file['name'])) {
        return false;
    }

    // Upload file
    $fileName = uploadFile($file, $destination, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

    if (!$fileName) {
        return false;
    }

    // Jika tidak perlu resize, langsung return
    if ($maxWidth <= 0 && $maxHeight <= 0) {
        return $fileName;
    }

    // Path file
    $filePath = $destination . '/' . $fileName;

    // Resize gambar
    if (function_exists('imagecreatefromjpeg')) {
        // Get image info
        list($width, $height, $type) = getimagesize($filePath);

        // Hitung dimensi baru
        $newWidth = $width;
        $newHeight = $height;

        if ($maxWidth > 0 && $width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = ($maxWidth / $width) * $height;
        }

        if ($maxHeight > 0 && $newHeight > $maxHeight) {
            $newHeight = $maxHeight;
            $newWidth = ($maxHeight / $newHeight) * $newWidth;
        }

        // Jika dimensi tidak berubah, langsung return
        if ($newWidth == $width && $newHeight == $height) {
            return $fileName;
        }

        // Create image
        $sourceImage = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($filePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = imagecreatefromwebp($filePath);
                }
                break;
        }

        if (!$sourceImage) {
            return $fileName;
        }

        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Handle transparency for PNG dan GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagecolortransparent($newImage, imagecolorallocate($newImage, 0, 0, 0));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        // Resize
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $filePath, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $filePath);
                break;
            case IMAGETYPE_GIF:
                imagegif($newImage, $filePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagewebp')) {
                    imagewebp($newImage, $filePath, $quality);
                }
                break;
        }

        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);
    }

    return $fileName;
}

/**
 * Fungsi untuk mendapatkan nama bulan dalam bahasa Indonesia
 * 
 * @param int $monthNumber Nomor bulan (1-12)
 * @return string Nama bulan dalam bahasa Indonesia
 */
function getNamaBulan($monthNumber)
{
    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    return $months[$monthNumber] ?? '';
}

/**
 * Fungsi untuk mencari publikasi
 * 
 * @param string $keyword Kata kunci pencarian
 * @param int $page Halaman saat ini
 * @param int $perPage Jumlah item per halaman
 * @return array Data hasil query dan informasi pagination
 */
function searchPublikasi($keyword, $page = 1, $perPage = ITEMS_PER_PAGE)
{
    $offset = ($page - 1) * $perPage;
    $search = '%' . $keyword . '%';

    $total = db_fetch("
        SELECT COUNT(*) as total 
        FROM publikasi p
        WHERE (p.judul LIKE ? OR p.ringkasan LIKE ? OR p.isi LIKE ?)
    ", [$search, $search, $search])['total'];

    $totalPages = ceil($total / $perPage);

    $publikasi = db_fetch_all("
        SELECT p.*, k.nama_kategori, u.name as penulis_nama
        FROM publikasi p
        JOIN kategori_publikasi k ON p.kategori_id = k.id
        JOIN users u ON p.penulis_id = u.id
        WHERE (p.judul LIKE ? OR p.ringkasan LIKE ? OR p.isi LIKE ?)
        ORDER BY p.tanggal_publikasi DESC, p.id DESC
        LIMIT $offset, $perPage
    ", [$search, $search, $search]);

    return [
        'data' => $publikasi,
        'pagination' => [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages
        ]
    ];
}

/**
 * Fungsi untuk mencari dokumen
 * 
 * @param string $keyword Kata kunci pencarian
 * @param int $page Halaman saat ini
 * @param int $perPage Jumlah item per halaman
 * @return array Data hasil query dan informasi pagination
 */
function searchDokumen($keyword, $page = 1, $perPage = ITEMS_PER_PAGE)
{
    $offset = ($page - 1) * $perPage;
    $search = '%' . $keyword . '%';

    $total = db_fetch("
        SELECT COUNT(*) as total 
        FROM dokumen d
        WHERE (d.judul LIKE ? OR d.deskripsi LIKE ? OR d.kategori LIKE ?)
    ", [$search, $search, $search])['total'];

    $totalPages = ceil($total / $perPage);

    $dokumen = db_fetch_all("
        SELECT d.*, u.name as uploader_name
        FROM dokumen d
        LEFT JOIN users u ON d.user_id = u.id
        WHERE (d.judul LIKE ? OR d.deskripsi LIKE ? OR d.kategori LIKE ?)
        ORDER BY d.tanggal_upload DESC, d.id DESC
        LIMIT $offset, $perPage
    ", [$search, $search, $search]);

    return [
        'data' => $dokumen,
        'pagination' => [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages
        ]
    ];
}

/**
 * Fungsi untuk memformat ringkasan tindakan pada log aktivitas
 * 
 * @param string $action Tindakan yang dilakukan
 * @param string $table Nama tabel
 * @return string Teks ringkasan tindakan
 */
function formatActionSummary($action, $table)
{
    $tableName = formatTableName($table);

    if (strpos($action, 'tambah') !== false || strpos($action, 'tambahkan') !== false || strpos($action, 'buat') !== false) {
        return '<span class="badge bg-success">Tambah</span> ' . $tableName;
    } elseif (strpos($action, 'ubah') !== false || strpos($action, 'update') !== false || strpos($action, 'edit') !== false) {
        return '<span class="badge bg-warning">Ubah</span> ' . $tableName;
    } elseif (strpos($action, 'hapus') !== false || strpos($action, 'delete') !== false) {
        return '<span class="badge bg-danger">Hapus</span> ' . $tableName;
    } elseif (strpos($action, 'login') !== false) {
        return '<span class="badge bg-info">Login</span> ke sistem';
    } elseif (strpos($action, 'logout') !== false) {
        return '<span class="badge bg-secondary">Logout</span> dari sistem';
    }

    return '<span class="badge bg-primary">' . ucfirst($action) . '</span> ' . $tableName;
}