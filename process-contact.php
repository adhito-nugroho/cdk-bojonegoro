<?php
/**
 * Contact Form Processing
 * CDK Wilayah Bojonegoro
 */

// Include configuration and functions
require_once 'admin/includes/config.php';
require_once 'admin/includes/functions.php';

// Set header to JSON
header('Content-Type: application/json');

// Default response
$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan dalam memproses permintaan Anda.'
];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    // Validate data
    $errors = [];

    if (empty($nama)) {
        $errors[] = 'Nama lengkap harus diisi';
    }

    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }

    if (!empty($telepon) && !preg_match('/^[0-9\+\-\(\) ]{8,15}$/', $telepon)) {
        $errors[] = 'Format nomor telepon tidak valid';
    }

    if (empty($kategori)) {
        $errors[] = 'Kategori harus dipilih';
    }

    if (empty($pesan)) {
        $errors[] = 'Pesan harus diisi';
    } elseif (strlen($pesan) < 10) {
        $errors[] = 'Pesan terlalu pendek (minimal 10 karakter)';
    }

    // If no errors, process the form
    if (empty($errors)) {
        // Get database connection
        $conn = getConnection();

        // Create messages table if not exists (since it's not in the main schema)
        $createTableQuery = "CREATE TABLE IF NOT EXISTS `pesan_kontak` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nama` varchar(100) NOT NULL,
            `email` varchar(100) NOT NULL,
            `telepon` varchar(20) DEFAULT NULL,
            `kategori` varchar(50) NOT NULL,
            `pesan` text NOT NULL,
            `status` enum('belum_dibaca','dibaca') NOT NULL DEFAULT 'belum_dibaca',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $conn->query($createTableQuery);

        // Prepare and execute the query
        $stmt = $conn->prepare("INSERT INTO pesan_kontak (nama, email, telepon, kategori, pesan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $email, $telepon, $kategori, $pesan);

        if ($stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => 'Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.'
            ];

            // Optionally send notification email to admin
            $adminEmail = getSetting('email');
            if ($adminEmail) {
                $subject = "Pesan Kontak Baru dari Website CDK Bojonegoro";
                $emailBody = "
                    <p>Ada pesan kontak baru dari website:</p>
                    <p><strong>Nama:</strong> $nama</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Telepon:</strong> $telepon</p>
                    <p><strong>Kategori:</strong> $kategori</p>
                    <p><strong>Pesan:</strong><br>$pesan</p>
                ";

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: $email" . "\r\n";

                // Uncomment below to enable email sending
                // mail($adminEmail, $subject, $emailBody, $headers);
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Gagal menyimpan pesan Anda. Silakan coba lagi nanti.'
            ];
        }

        $stmt->close();
        $conn->close();
    } else {
        // Return first error
        $response = [
            'status' => 'error',
            'message' => $errors[0]
        ];
    }
}

// Return JSON response
echo json_encode($response);