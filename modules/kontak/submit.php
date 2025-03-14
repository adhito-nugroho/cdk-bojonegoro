<?php
/**
 * Submit Kontak untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menangani pengiriman pesan kontak
 */

// Define BASE_PATH
define('BASE_PATH', dirname(dirname(__DIR__)) . '/');

// Include config dan fungsi-fungsi
require_once BASE_PATH . 'includes/config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/functions.php';

// Inisialisasi variabel
$errors = [];
$success = false;

// Periksa apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi token CSRF
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $errors[] = 'Token CSRF tidak valid. Silakan coba lagi.';
    } else {
        // Validasi input
        $nama = isset($_POST['nama']) ? cleanInput($_POST['nama']) : '';
        $email = isset($_POST['email']) ? cleanInput($_POST['email']) : '';
        $telepon = isset($_POST['telepon']) ? cleanInput($_POST['telepon']) : '';
        $kategori = isset($_POST['kategori']) ? cleanInput($_POST['kategori']) : '';
        $pesan = isset($_POST['pesan']) ? cleanInput($_POST['pesan']) : '';

        // Validasi field yang wajib diisi
        if (empty($nama)) {
            $errors[] = 'Nama wajib diisi.';
        }

        if (empty($email)) {
            $errors[] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        if (empty($kategori)) {
            $errors[] = 'Kategori wajib dipilih.';
        }

        if (empty($pesan)) {
            $errors[] = 'Pesan wajib diisi.';
        }

        // Jika tidak ada error, simpan pesan ke database
        if (empty($errors)) {
            $data = [
                'nama' => $nama,
                'email' => $email,
                'telepon' => $telepon,
                'kategori' => $kategori,
                'pesan' => $pesan,
                'status' => 'baru',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'is_read' => 0
            ];

            $result = db_insert('kontak_pesan', $data);

            if ($result) {
                $success = true;

                // Log aktivitas
                logActivity('Pesan kontak baru dikirim', 'kontak_pesan', $result);

                // Kirim notifikasi email ke admin (opsional)
                // sendNotificationEmail($data);

                // Redirect dengan pesan sukses
                $_SESSION['contact_success'] = 'Pesan Anda berhasil dikirim. Kami akan menghubungi Anda segera.';
                redirect(SITE_URL . '/#kontak');
            } else {
                $errors[] = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.';
            }
        }
    }
}

// Jika ada error, simpan ke session dan redirect kembali ke form
if (!empty($errors)) {
    $_SESSION['contact_errors'] = $errors;

    // Simpan data input untuk diisi kembali
    $_SESSION['contact_data'] = [
        'nama' => $nama ?? '',
        'email' => $email ?? '',
        'telepon' => $telepon ?? '',
        'kategori' => $kategori ?? '',
        'pesan' => $pesan ?? ''
    ];

    redirect(SITE_URL . '/#kontak');
}

/**
 * Fungsi untuk mengirim email notifikasi ke admin (opsional)
 * 
 * @param array $data Data pesan kontak
 * @return boolean True jika email berhasil dikirim, false jika gagal
 */
function sendNotificationEmail($data)
{
    $to = getSetting('contact_notification_email', 'admin@cdk-bojonegoro.jatimprov.go.id');
    $subject = 'Pesan Kontak Baru - ' . $data['kategori'];

    $message = "
    <html>
    <head>
        <title>Pesan Kontak Baru</title>
    </head>
    <body>
        <h2>Pesan Kontak Baru</h2>
        <p>Ada pesan kontak baru dari website CDK Wilayah Bojonegoro.</p>
        <table>
            <tr>
                <td><strong>Nama</strong></td>
                <td>: {$data['nama']}</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>: {$data['email']}</td>
            </tr>
            <tr>
                <td><strong>Telepon</strong></td>
                <td>: {$data['telepon']}</td>
            </tr>
            <tr>
                <td><strong>Kategori</strong></td>
                <td>: {$data['kategori']}</td>
            </tr>
            <tr>
                <td><strong>Pesan</strong></td>
                <td>: {$data['pesan']}</td>
            </tr>
        </table>
        <p>Silakan login ke panel admin untuk menindaklanjuti pesan ini.</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: CDK Wilayah Bojonegoro <noreply@cdk-bojonegoro.jatimprov.go.id>" . "\r\n";

    return mail($to, $subject, $message, $headers);
}