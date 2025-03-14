<?php
/**
 * Profil Admin untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menangani tampilan dan pembaruan profil pengguna
 */

// Define BASE_PATH
define('BASE_PATH', dirname(dirname(dirname(__DIR__))) . '/');

// Include konfigurasi dan fungsi
require_once BASE_PATH . 'includes/config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/functions.php';
require_once dirname(dirname(__DIR__)) . '/config.php';
require_once dirname(dirname(__DIR__)) . '/functions.php';

// Cek login
if (!isLoggedIn()) {
    redirect(ADMIN_URL . '/login.php');
}

// Dapatkan data user yang sedang login
$user = getCurrentUser();

// Inisialisasi variabel
$success_msg = '';
$error_msg = '';
$old_password = '';
$new_password = '';
$confirm_password = '';

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi token CSRF
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $error_msg = 'Token CSRF tidak valid. Silakan coba lagi.';
    } else {
        // Pembaruan profil
        if (isset($_POST['update_profile'])) {
            $name = cleanInput($_POST['name']);
            $email = cleanInput($_POST['email']);

            // Validasi input
            if (empty($name)) {
                $error_msg = 'Nama lengkap wajib diisi.';
            } elseif (empty($email)) {
                $error_msg = 'Email wajib diisi.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_msg = 'Format email tidak valid.';
            } else {
                // Cek apakah email sudah digunakan oleh user lain
                $check_email = db_fetch("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $user['id']]);

                if ($check_email) {
                    $error_msg = 'Email sudah digunakan oleh pengguna lain.';
                } else {
                    // Update data profil
                    $data = [
                        'name' => $name,
                        'email' => $email
                    ];

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
                        $result = updateUser($user['id'], $data);

                        if ($result) {
                            $success_msg = 'Profil berhasil diperbarui.';

                            // Log aktivitas
                            logActivity('Memperbarui profil', 'users', $user['id']);

                            // Refresh data user
                            $user = getUserById($user['id']);
                        } else {
                            $error_msg = 'Gagal memperbarui profil. Silakan coba lagi.';
                        }
                    }
                }
            }
        }

        // Pembaruan password
        if (isset($_POST['update_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Validasi input
            if (empty($old_password)) {
                $error_msg = 'Password lama wajib diisi.';
            } elseif (empty($new_password)) {
                $error_msg = 'Password baru wajib diisi.';
            } elseif (strlen($new_password) < 6) {
                $error_msg = 'Password baru minimal 6 karakter.';
            } elseif ($new_password !== $confirm_password) {
                $error_msg = 'Konfirmasi password tidak sesuai dengan password baru.';
            } elseif (!password_verify($old_password, $user['password'])) {
                $error_msg = 'Password lama tidak sesuai.';
            } else {
                // Update password
                $data = [
                    'password' => password_hash($new_password, PASSWORD_DEFAULT)
                ];

                $result = updateUser($user['id'], $data);

                if ($result) {
                    $success_msg = 'Password berhasil diperbarui.';

                    // Log aktivitas
                    logActivity('Memperbarui password', 'users', $user['id']);

                    // Reset field password
                    $old_password = '';
                    $new_password = '';
                    $confirm_password = '';
                } else {
                    $error_msg = 'Gagal memperbarui password. Silakan coba lagi.';
                }
            }
        }
    }
}

// Set judul halaman
$page_title = 'Profil Pengguna - Admin Panel CDK Wilayah Bojonegoro';

// Include header
include_once dirname(dirname(__DIR__)) . '/includes/header.php';
?>

<!-- Profile Content -->
<div class="page-header">
    <h4>Profil Pengguna</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-circle me-2"></i> Informasi Pengguna</h5>
            </div>
            <div class="card-body text-center">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="<?php echo UPLOADS_URL; ?>/profile/<?php echo $user['profile_image']; ?>" alt="Profile Image"
                        class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <img src="<?php echo ADMIN_ASSETS; ?>/images/default-user.png" alt="Default Profile"
                        class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <?php endif; ?>

                <h5 class="mb-1"><?php echo $user['name']; ?></h5>
                <p class="text-muted"><?php echo ucfirst($user['role']); ?></p>

                <div class="profile-details mt-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Username:</label>
                        <p><?php echo $user['username']; ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email:</label>
                        <p><?php echo $user['email']; ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Login Terakhir:</label>
                        <p><?php echo $user['last_login'] ? formatTanggal($user['last_login'], true) : 'Belum pernah login'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row">
            <!-- Form Update Profil -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-user-edit me-2"></i> Edit Profil</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success_msg)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $success_msg; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_msg)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error_msg; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" enctype="multipart/form-data">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="update_profile" value="1">

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo $user['name']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo $user['email']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="profile_image" class="form-label">Foto Profil</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image"
                                    accept="image/*" onchange="previewImage(this, 'profile-preview')">
                                <div class="form-text">Ukuran maksimal 2MB. Format: JPG, JPEG, PNG, GIF.</div>

                                <div class="mt-2 <?php echo !empty($user['profile_image']) ? '' : 'd-none'; ?>"
                                    id="preview-container">
                                    <label class="form-label">Preview:</label>
                                    <div class="border p-2 rounded">
                                        <img id="profile-preview"
                                            src="<?php echo !empty($user['profile_image']) ? UPLOADS_URL . '/profile/' . $user['profile_image'] : ''; ?>"
                                            class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Form Update Password -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-key me-2"></i> Ubah Password</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="update_password" value="1">

                            <div class="mb-3">
                                <label for="old_password" class="form-label">Password Lama <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="old_password" name="old_password"
                                        value="<?php echo $old_password; ?>" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button"
                                        data-target="old_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                        value="<?php echo $new_password; ?>" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button"
                                        data-target="new_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Password minimal 6 karakter.</div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password Baru <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password" value="<?php echo $confirm_password; ?>" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button"
                                        data-target="confirm_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-key me-2"></i> Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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