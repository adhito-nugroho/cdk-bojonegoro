<?php
/**
 * Edit Pengguna Admin untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menangani proses edit pengguna
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

// Dapatkan data user
$user = getUserById($user_id);

// Jika user tidak ditemukan
if (!$user) {
    $_SESSION['error_message'] = 'Pengguna tidak ditemukan.';
    redirect(ADMIN_URL . '/modules/users/index.php');
    exit;
}

// Cek jumlah admin jika akan mengedit role admin
$adminCount = db_fetch("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")['total'];

// Inisialisasi variabel
$name = $user['name'];
$username = $user['username'];
$email = $user['email'];
$role = $user['role'];
$password = '';
$confirm_password = '';
$error_msg = '';

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi token CSRF
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $error_msg = 'Token CSRF tidak valid. Silakan coba lagi.';
    } else {
        // Ambil data dari form
        $name = cleanInput($_POST['name']);
        $email = cleanInput($_POST['email']);
        $role = cleanInput($_POST['role']);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validasi input
        if (empty($name)) {
            $error_msg = 'Nama lengkap wajib diisi.';
        } elseif (empty($email)) {
            $error_msg = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = 'Format email tidak valid.';
        } elseif (empty($role)) {
            $error_msg = 'Role wajib dipilih.';
        } elseif ($user['role'] === 'admin' && $role !== 'admin' && $adminCount <= 1) {
            $error_msg = 'Tidak dapat mengubah role admin terakhir.';
        } elseif (!empty($password) && strlen($password) < 6) {
            $error_msg = 'Password minimal 6 karakter.';
        } elseif (!empty($password) && $password !== $confirm_password) {
            $error_msg = 'Konfirmasi password tidak sesuai dengan password.';
        } else {
            // Cek apakah email sudah digunakan oleh user lain
            $check_email = db_fetch("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $user_id]);

            if ($check_email) {
                $error_msg = 'Email sudah digunakan oleh pengguna lain.';
            } else {
                // Data untuk update
                $data = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ];

                // Tambahkan password jika diisi
                if (!empty($password)) {
                    $data['password'] = password_hash($password, PASSWORD_DEFAULT);
                }

                // Upload foto profil jika ada
                if (!empty($_FILES['profile_image']['name'])) {
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
                    $max_size = 2 * 1024 * 1024; // 2MB

                    $profile_image = uploadImage($_FILES['profile_image'], UPLOADS_PATH . 'profile', 300, 300);

                    if ($profile_image) {
                        $data['profile_image'] = $profile_image;

                        // Hapus foto lama jika ada
                        if (!empty($user['profile_image'])) {
                            $old_image_path = UPLOADS_PATH . 'profile/' . $user['profile_image'];
                            if (file_exists($old_image_path)) {
                                unlink($old_image_path);
                            }
                        }
                    } else {
                        $error_msg = 'Gagal mengupload foto profil. Pastikan ukuran file tidak melebihi 2MB dan format file adalah jpg, jpeg, png, atau gif.';
                    }
                }

                if (empty($error_msg)) {
                    // Update user
                    $result = updateUser($user_id, $data);

                    if ($result) {
                        // Log aktivitas
                        logActivity('Memperbarui pengguna', 'users', $user_id, json_encode($user), json_encode($data));

                        // Set pesan sukses
                        $_SESSION['success_message'] = 'Pengguna berhasil diperbarui.';

                        // Jika yang diupdate adalah diri sendiri, update session
                        if ($user_id == $_SESSION['user_id']) {
                            $_SESSION['user_name'] = $name;
                            $_SESSION['user_role'] = $role;
                        }

                        // Redirect ke halaman daftar pengguna
                        redirect(ADMIN_URL . '/modules/users/index.php');
                        exit;
                    } else {
                        $error_msg = 'Gagal memperbarui pengguna. Silakan coba lagi.';
                    }
                }
            }
        }
    }
}

// Set judul halaman
$page_title = 'Edit Pengguna - Admin Panel CDK Wilayah Bojonegoro';

// Include header
include_once dirname(dirname(__DIR__)) . '/includes/header.php';
?>

<!-- Edit User Content -->
<div class="page-header">
    <h4>Edit Pengguna</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/modules/users/index.php">Pengguna</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i> Form Edit Pengguna</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username"
                        value="<?php echo htmlspecialchars($username); ?>" readonly disabled>
                    <div class="form-text">Username tidak dapat diubah.</div>
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="role" name="role" required <?php echo ($user['role'] === 'admin' && $adminCount <= 1) ? 'disabled' : ''; ?>>
                        <option value="">Pilih Role</option>
                        <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="editor" <?php echo ($role === 'editor') ? 'selected' : ''; ?>>Editor</option>
                        <option value="staff" <?php echo ($role === 'staff') ? 'selected' : ''; ?>>Staff</option>
                    </select>
                    <?php if ($user['role'] === 'admin' && $adminCount <= 1): ?>
                        <div class="form-text text-danger">Role admin terakhir tidak dapat diubah.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password <small class="text-muted">(Kosongkan jika tidak
                            ingin mengubah)</small></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password">
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">Password minimal 6 karakter.</div>
                </div>
                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        <button class="btn btn-outline-secondary toggle-password" type="button"
                            data-target="confirm_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="profile_image" class="form-label">Foto Profil</label>
                <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*"
                    onchange="previewImage(this, 'profile-preview')">
                <div class="form-text">Ukuran maksimal 2MB. Format: JPG, JPEG, PNG, GIF.</div>

                <div class="mt-2 <?php echo !empty($user['profile_image']) ? '' : 'd-none'; ?>" id="preview-container">
                    <label class="form-label">Preview:</label>
                    <div class="border p-2 rounded">
                        <img id="profile-preview"
                            src="<?php echo !empty($user['profile_image']) ? UPLOADS_URL . '/profile/' . $user['profile_image'] : ''; ?>"
                            class="img-thumbnail" style="max-height: 200px;">
                    </div>
                </div>
            </div>

            <div class="card p-3 mb-3 border-info">
                <div class="card-body p-0">
                    <h6 class="fw-bold">Informasi Role:</h6>
                    <div class="mb-2">
                        <span class="badge bg-danger me-2">Admin</span>
                        <small>Memiliki akses penuh ke seluruh fitur admin panel.</small>
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-warning me-2">Editor</span>
                        <small>Dapat mengelola konten website tetapi tidak dapat mengakses pengaturan sistem.</small>
                    </div>
                    <div>
                        <span class="badge bg-info me-2">Staff</span>
                        <small>Hanya dapat melihat dashboard, mengelola pesan kontak, dan mengakses beberapa laporan
                            dasar.</small>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="<?php echo ADMIN_URL; ?>/modules/users/index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview gambar
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const previewContainer = document.getElementById('preview-container');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>

<?php
// Include footer
include_once dirname(dirname(__DIR__)) . '/includes/footer.php';
?>