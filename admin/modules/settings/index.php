<?php
/**
 * Pengaturan Website untuk CDK Wilayah Bojonegoro
 * 
 * File ini menangani pengaturan umum website
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

// Inisialisasi variabel
$error_msg = '';
$success_msg = '';

// Dapatkan semua pengaturan
$settings = getAllSettings();

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi token CSRF
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $error_msg = 'Token CSRF tidak valid. Silakan coba lagi.';
    } else {
        // Sanitasi dan update pengaturan
        $updated_settings = [];

        // General Settings
        $updated_settings['site_title'] = cleanInput($_POST['site_title'] ?? '');
        $updated_settings['site_description'] = cleanInput($_POST['site_description'] ?? '');

        // Contact Settings
        $updated_settings['contact_email'] = cleanInput($_POST['contact_email'] ?? '');
        $updated_settings['contact_phone'] = cleanInput($_POST['contact_phone'] ?? '');
        $updated_settings['contact_address'] = cleanInput($_POST['contact_address'] ?? '');
        $updated_settings['office_hours'] = cleanInput($_POST['office_hours'] ?? '');
        $updated_settings['contact_hotline'] = cleanInput($_POST['contact_hotline'] ?? '');

        // Hero Settings
        $updated_settings['hero_title'] = cleanInput($_POST['hero_title'] ?? '');
        $updated_settings['hero_subtitle'] = cleanInput($_POST['hero_subtitle'] ?? '');

        // Social Media
        $updated_settings['facebook_url'] = cleanInput($_POST['facebook_url'] ?? '#');
        $updated_settings['twitter_url'] = cleanInput($_POST['twitter_url'] ?? '#');
        $updated_settings['instagram_url'] = cleanInput($_POST['instagram_url'] ?? '#');
        $updated_settings['youtube_url'] = cleanInput($_POST['youtube_url'] ?? '#');

        // Upload logo jika ada
        if (!empty($_FILES['site_logo']['name'])) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            $max_size = 2 * 1024 * 1024; // 2MB

            $logo_file = uploadFile($_FILES['site_logo'], ASSETS_PATH . 'images', $allowed_ext, $max_size);

            if ($logo_file) {
                $updated_settings['site_logo'] = 'assets/images/' . $logo_file;
            } else {
                $error_msg = 'Gagal mengupload logo. Pastikan ukuran file tidak melebihi 2MB dan format file adalah jpg, jpeg, png, atau gif.';
            }
        }

        // Upload favicon jika ada
        if (!empty($_FILES['site_favicon']['name'])) {
            $allowed_ext = ['ico', 'png'];
            $max_size = 1 * 1024 * 1024; // 1MB

            $favicon_file = uploadFile($_FILES['site_favicon'], ASSETS_PATH . 'images', $allowed_ext, $max_size);

            if ($favicon_file) {
                $updated_settings['site_favicon'] = 'assets/images/' . $favicon_file;
            } else {
                $error_msg = 'Gagal mengupload favicon. Pastikan ukuran file tidak melebihi 1MB dan format file adalah ico atau png.';
            }
        }

        // Upload video hero jika ada
        if (!empty($_FILES['hero_video']['name'])) {
            $allowed_ext = ['mp4', 'webm'];
            $max_size = 20 * 1024 * 1024; // 20MB

            $video_file = uploadFile($_FILES['hero_video'], ASSETS_PATH . 'videos', $allowed_ext, $max_size);

            if ($video_file) {
                $updated_settings['hero_video'] = 'assets/videos/' . $video_file;
            } else {
                $error_msg = 'Gagal mengupload video hero. Pastikan ukuran file tidak melebihi 20MB dan format file adalah mp4 atau webm.';
            }
        }

        // Jika tidak ada error, update semua pengaturan
        if (empty($error_msg)) {
            $old_settings = $settings;
            $updateResult = updateSettings($updated_settings);

            if ($updateResult) {
                // Log aktivitas
                logActivity('Memperbarui pengaturan website', 'settings', null, json_encode($old_settings), json_encode($updated_settings));

                $success_msg = 'Pengaturan berhasil diperbarui.';

                // Perbarui nilai settings
                $settings = getAllSettings();
            } else {
                $error_msg = 'Gagal memperbarui beberapa pengaturan. Silakan coba lagi.';
            }
        }
    }
}

// Set judul halaman
$page_title = 'Pengaturan Website - Admin Panel CDK Wilayah Bojonegoro';

// Include header
include_once dirname(dirname(__DIR__)) . '/includes/header.php';
?>

<!-- Settings Content -->
<div class="page-header">
    <h4>Pengaturan Website</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-cogs me-2"></i> Form Pengaturan Website</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                        type="button" role="tab" aria-controls="general" aria-selected="true">
                        <i class="fas fa-globe me-2"></i> Umum
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                        type="button" role="tab" aria-controls="contact" aria-selected="false">
                        <i class="fas fa-address-card me-2"></i> Kontak
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button"
                        role="tab" aria-controls="hero" aria-selected="false">
                        <i class="fas fa-image me-2"></i> Beranda Hero
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button"
                        role="tab" aria-controls="social" aria-selected="false">
                        <i class="fas fa-share-alt me-2"></i> Media Sosial
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="settingsTabsContent">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="site_title" class="form-label">Judul Website <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="site_title" name="site_title"
                                value="<?php echo $settings['site_title'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="site_description" class="form-label">Deskripsi Website</label>
                            <input type="text" class="form-control" id="site_description" name="site_description"
                                value="<?php echo $settings['site_description'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="site_logo" class="form-label">Logo Website</label>
                            <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*"
                                onchange="previewImage(this, 'logo-preview')">
                            <div class="form-text">Ukuran maksimal 2MB. Format: JPG, JPEG, PNG, GIF.</div>

                            <div class="mt-2 <?php echo !empty($settings['site_logo']) ? '' : 'd-none'; ?>"
                                id="logo-preview-container">
                                <label class="form-label">Logo Saat Ini:</label>
                                <div class="border p-2 rounded">
                                    <img id="logo-preview"
                                        src="<?php echo !empty($settings['site_logo']) ? SITE_URL . '/' . $settings['site_logo'] : ''; ?>"
                                        class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="site_favicon" class="form-label">Favicon Website</label>
                            <input type="file" class="form-control" id="site_favicon" name="site_favicon"
                                accept="image/x-icon,image/png" onchange="previewImage(this, 'favicon-preview')">
                            <div class="form-text">Ukuran maksimal 1MB. Format: ICO, PNG.</div>

                            <div class="mt-2 <?php echo !empty($settings['site_favicon']) ? '' : 'd-none'; ?>"
                                id="favicon-preview-container">
                                <label class="form-label">Favicon Saat Ini:</label>
                                <div class="border p-2 rounded">
                                    <img id="favicon-preview"
                                        src="<?php echo !empty($settings['site_favicon']) ? SITE_URL . '/' . $settings['site_favicon'] : ''; ?>"
                                        class="img-thumbnail" style="max-height: 50px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Settings -->
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="contact_email" class="form-label">Email Kontak <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email"
                                value="<?php echo $settings['contact_email'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label">Nomor Telepon <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                                value="<?php echo $settings['contact_phone'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="contact_hotline" class="form-label">Nomor Hotline</label>
                            <input type="text" class="form-control" id="contact_hotline" name="contact_hotline"
                                value="<?php echo $settings['contact_hotline'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="office_hours" class="form-label">Jam Pelayanan</label>
                            <input type="text" class="form-control" id="office_hours" name="office_hours"
                                value="<?php echo $settings['office_hours'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contact_address" class="form-label">Alamat Kantor <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="contact_address" name="contact_address" rows="3"
                            required><?php echo $settings['contact_address'] ?? ''; ?></textarea>
                    </div>
                </div>

                <!-- Hero Settings -->
                <div class="tab-pane fade" id="hero" role="tabpanel" aria-labelledby="hero-tab">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="hero_title" class="form-label">Judul Hero <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="hero_title" name="hero_title"
                                value="<?php echo $settings['hero_title'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="hero_subtitle" class="form-label">Subjudul Hero</label>
                            <input type="text" class="form-control" id="hero_subtitle" name="hero_subtitle"
                                value="<?php echo $settings['hero_subtitle'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="hero_video" class="form-label">Video Background Hero</label>
                        <input type="file" class="form-control" id="hero_video" name="hero_video"
                            accept="video/mp4,video/webm">
                        <div class="form-text">Ukuran maksimal 20MB. Format: MP4, WEBM.</div>

                        <?php if (!empty($settings['hero_video'])): ?>
                            <div class="mt-2">
                                <label class="form-label">Video Saat Ini:</label>
                                <div class="border p-2 rounded">
                                    <video width="320" height="180" controls>
                                        <source src="<?php echo SITE_URL . '/' . $settings['hero_video']; ?>"
                                            type="video/mp4">
                                        Browser Anda tidak mendukung tag video.
                                    </video>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Social Media Settings -->
                <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="facebook_url" class="form-label">URL Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                <input type="text" class="form-control" id="facebook_url" name="facebook_url"
                                    value="<?php echo $settings['facebook_url'] ?? '#'; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="twitter_url" class="form-label">URL Twitter</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                <input type="text" class="form-control" id="twitter_url" name="twitter_url"
                                    value="<?php echo $settings['twitter_url'] ?? '#'; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="instagram_url" class="form-label">URL Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                <input type="text" class="form-control" id="instagram_url" name="instagram_url"
                                    value="<?php echo $settings['instagram_url'] ?? '#'; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="youtube_url" class="form-label">URL YouTube</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                                <input type="text" class="form-control" id="youtube_url" name="youtube_url"
                                    value="<?php echo $settings['youtube_url'] ?? '#'; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
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
        const previewContainer = document.getElementById(previewId + '-container');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php
// Include footer
include_once dirname(dirname(__DIR__)) . '/includes/footer.php';
?>