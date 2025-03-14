<?php
/**
 * Manajemen Pengguna Admin untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menampilkan daftar pengguna admin
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

// Parameter pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$perPage = ITEMS_PER_PAGE;

// Dapatkan daftar user
$users = getAllUsers($page, $perPage);

// Set judul halaman
$page_title = 'Manajemen Pengguna - Admin Panel CDK Wilayah Bojonegoro';

// Include header
include_once dirname(dirname(__DIR__)) . '/includes/header.php';
?>

<!-- Users Content -->
<div class="page-header">
    <h4>Manajemen Pengguna</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i> Daftar Pengguna</h5>
            <a href="<?php echo ADMIN_URL; ?>/modules/users/add.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Tambah Pengguna
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover table-striped data-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Login Terakhir</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users['data'])): ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data pengguna.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $no = ($page - 1) * $perPage + 1;
                        foreach ($users['data'] as $user):
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($user['profile_image'])): ?>
                                            <img src="<?php echo UPLOADS_URL; ?>/profile/<?php echo $user['profile_image']; ?>"
                                                alt="<?php echo $user['name']; ?>" class="rounded-circle me-2" width="40"
                                                height="40" style="object-fit: cover;">
                                        <?php else: ?>
                                            <img src="<?php echo ADMIN_ASSETS; ?>/images/default-user.png" alt="Default User"
                                                class="rounded-circle me-2" width="40" height="40">
                                        <?php endif; ?>
                                        <?php echo $user['name']; ?>
                                    </div>
                                </td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php elseif ($user['role'] === 'editor'): ?>
                                        <span class="badge bg-warning">Editor</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Staff</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $user['last_login'] ? formatTanggal($user['last_login'], true) : 'Belum pernah login'; ?>
                                </td>
                                <td>
                                    <a href="<?php echo ADMIN_URL; ?>/modules/users/edit.php?id=<?php echo $user['id']; ?>"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <?php if ($user['id'] != $_SESSION['user_id'] && ($adminCount ?? 0) > 1): ?>
                                        <a href="<?php echo ADMIN_URL; ?>/modules/users/delete.php?id=<?php echo $user['id']; ?>"
                                            class="btn btn-sm btn-danger btn-delete" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($users['pagination']['total_pages'] > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php echo getPaginationLinks($users['pagination']['current_page'], $users['pagination']['total_pages'], '?page=%d'); ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informasi Role Pengguna</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <span class="badge bg-danger me-2">Admin</span>
                        </h5>
                        <p class="card-text">Memiliki akses penuh ke seluruh fitur admin panel, termasuk manajemen
                            pengguna, pengaturan website, dan semua konten.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <span class="badge bg-warning me-2">Editor</span>
                        </h5>
                        <p class="card-text">Dapat mengelola konten website seperti publikasi, dokumen, galeri, dan data
                            statistik, tetapi tidak dapat mengakses pengaturan sistem.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <span class="badge bg-info me-2">Staff</span>
                        </h5>
                        <p class="card-text">Hanya dapat melihat dashboard, mengelola pesan kontak, dan mengakses
                            beberapa laporan dasar. Tidak dapat mengubah konten website.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once dirname(dirname(__DIR__)) . '/includes/footer.php';
?>