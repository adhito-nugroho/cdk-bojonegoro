<?php
/**
 * Manajemen Menu untuk CDK Wilayah Bojonegoro
 * 
 * File ini menangani manajemen menu website
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

// Set judul halaman
$page_title = 'Manajemen Menu - Admin Panel CDK Wilayah Bojonegoro';

// Ambil parameter posisi menu (default: main_menu)
$position = isset($_GET['position']) && in_array($_GET['position'], ['main_menu', 'footer_menu', 'quick_links'])
    ? $_GET['position'] : 'main_menu';

// Dapatkan daftar menu berdasarkan posisi
$menus = db_fetch_all("
    SELECT m1.*, m2.title as parent_title 
    FROM menu m1 
    LEFT JOIN menu m2 ON m1.parent_id = m2.id 
    WHERE m1.position = ? 
    ORDER BY m1.order_number ASC
", [$position]);

// Dapatkan daftar menu yang bisa dijadikan parent
$parent_menus = db_fetch_all("
    SELECT id, title 
    FROM menu 
    WHERE position = ? AND parent_id IS NULL 
    ORDER BY order_number ASC
", [$position]);

// Include header
include_once dirname(dirname(__DIR__)) . '/includes/header.php';
?>

<!-- Menu Management Content -->
<div class="page-header">
    <h4>Manajemen Menu</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manajemen Menu</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Daftar Menu
                        <?php if ($position === 'main_menu'): ?>
                            Utama
                        <?php elseif ($position === 'footer_menu'): ?>
                            Footer
                        <?php else: ?>
                            Link Cepat
                        <?php endif; ?>
                    </h5>
                    <div>
                        <div class="btn-group" role="group">
                            <a href="<?php echo ADMIN_URL; ?>/modules/menu/index.php?position=main_menu"
                                class="btn btn-sm <?php echo $position === 'main_menu' ? 'btn-primary' : 'btn-outline-primary'; ?>">Menu
                                Utama</a>
                            <a href="<?php echo ADMIN_URL; ?>/modules/menu/index.php?position=footer_menu"
                                class="btn btn-sm <?php echo $position === 'footer_menu' ? 'btn-primary' : 'btn-outline-primary'; ?>">Menu
                                Footer</a>
                            <a href="<?php echo ADMIN_URL; ?>/modules/menu/index.php?position=quick_links"
                                class="btn btn-sm <?php echo $position === 'quick_links' ? 'btn-primary' : 'btn-outline-primary'; ?>">Link
                                Cepat</a>
                        </div>
                    </div>
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

                <?php if (empty($menus)): ?>
                    <div class="alert alert-info">
                        Belum ada menu di posisi ini. Silakan tambahkan menu baru.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Judul</th>
                                    <th>URL</th>
                                    <th>Parent</th>
                                    <th>Urutan</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="menu-sortable">
                                <?php foreach ($menus as $index => $menu): ?>
                                    <tr data-id="<?php echo $menu['id']; ?>">
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <?php if (!empty($menu['icon'])): ?>
                                                <i class="<?php echo $menu['icon']; ?> me-1"></i>
                                            <?php endif; ?>
                                            <?php echo $menu['title']; ?>
                                        </td>
                                        <td><?php echo $menu['url']; ?></td>
                                        <td>
                                            <?php if (!empty($menu['parent_id'])): ?>
                                                <span class="badge bg-secondary"><?php echo $menu['parent_title']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $menu['order_number']; ?></td>
                                        <td><?php echo getStatusBadge($menu['is_active']); ?></td>
                                        <td>
                                            <a href="<?php echo ADMIN_URL; ?>/modules/menu/edit.php?id=<?php echo $menu['id']; ?>"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo ADMIN_URL; ?>/modules/menu/delete.php?id=<?php echo $menu['id']; ?>"
                                                class="btn btn-sm btn-danger btn-delete" title="Hapus"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i> Anda dapat mengurutkan menu dengan drag and drop. Jangan
                        lupa klik tombol "Simpan Urutan" setelah mengubah urutan.
                    </div>

                    <div class="text-end">
                        <button id="save-order" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Simpan Urutan
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Tambah Menu Baru</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo ADMIN_URL; ?>/modules/menu/add.php" method="post">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="position" value="<?php echo $position; ?>">

                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="url" name="url" required>
                        <div class="form-text">Contoh: #beranda, /pages/tentang.php, https://google.com</div>
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon (Opsional)</label>
                        <input type="text" class="form-control" id="icon" name="icon">
                        <div class="form-text">Masukkan class icon dari Font Awesome atau Remix Icon</div>
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Menu (Opsional)</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">-- Pilih Parent Menu --</option>
                            <?php foreach ($parent_menus as $parent): ?>
                                <option value="<?php echo $parent['id']; ?>"><?php echo $parent['title']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="order_number" class="form-label">Urutan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="order_number" name="order_number" min="1"
                            value="<?php echo count($menus) + 1; ?>" required>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                checked>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Menu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informasi Menu</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-bold">Menu Utama</h6>
                    <p class="mb-0 small">Menu yang ditampilkan di navbar website. Menu ini akan ditampilkan di bagian
                        atas website.</p>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold">Menu Footer</h6>
                    <p class="mb-0 small">Menu yang ditampilkan di bagian footer website, biasanya untuk navigasi cepat
                        ke halaman utama.</p>
                </div>

                <div class="mb-0">
                    <h6 class="fw-bold">Link Cepat</h6>
                    <p class="mb-0 small">Link-link khusus yang ditampilkan di bagian footer website, biasanya untuk
                        akses cepat ke layanan atau halaman penting.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function () {
        // Inisialisasi sortable
        $("#menu-sortable").sortable({
            update: function (event, ui) {
                // Update nomor urut pada tabel saat item di-drag
                $("#menu-sortable tr").each(function (index) {
                    $(this).find("td:eq(0)").text(index + 1);
                });
            }
        });

        // Simpan urutan menu
        $("#save-order").click(function () {
            var menuIds = [];

            // Ambil id menu sesuai urutan
            $("#menu-sortable tr").each(function () {
                menuIds.push($(this).data("id"));
            });

            // Kirim data ke server menggunakan AJAX
            $.ajax({
                url: "<?php echo ADMIN_URL; ?>/modules/menu/update_order.php",
                type: "POST",
                data: {
                    menu_ids: menuIds,
                    position: "<?php echo $position; ?>",
                    csrf_token: "<?php echo generateCsrfToken(); ?>"
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        alert("Urutan menu berhasil disimpan.");
                        location.reload();
                    } else {
                        alert("Gagal menyimpan urutan menu: " + response.message);
                    }
                },
                error: function () {
                    alert("Terjadi kesalahan. Silakan coba lagi.");
                }
            });
        });
    });
</script>

<?php
// Include footer
include_once dirname(dirname(__DIR__)) . '/includes/footer.php';
?>