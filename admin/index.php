<?php
/**
 * Admin Dashboard Home
 * CDK Wilayah Bojonegoro
 */

// Set page title
$page_title = 'Dashboard';

// Include header
require_once 'includes/header.php';

// Get stats for dashboard
$conn = getConnection();

// Count layanan
$layananQuery = "SELECT COUNT(*) as total FROM layanan WHERE status = 'aktif'";
$layananResult = $conn->query($layananQuery);
$layananTotal = 0;
if ($layananResult && $row = $layananResult->fetch_assoc()) {
    $layananTotal = $row['total'];
}

// Count program
$programQuery = "SELECT COUNT(*) as total FROM program WHERE status = 'aktif'";
$programResult = $conn->query($programQuery);
$programTotal = 0;
if ($programResult && $row = $programResult->fetch_assoc()) {
    $programTotal = $row['total'];
}

// Count publikasi
$publikasiQuery = "SELECT COUNT(*) as total FROM publikasi WHERE status = 'published'";
$publikasiResult = $conn->query($publikasiQuery);
$publikasiTotal = 0;
if ($publikasiResult && $row = $publikasiResult->fetch_assoc()) {
    $publikasiTotal = $row['total'];
}

// Count galeri
$galeriQuery = "SELECT COUNT(*) as total FROM galeri WHERE status = 'aktif'";
$galeriResult = $conn->query($galeriQuery);
$galeriTotal = 0;
if ($galeriResult && $row = $galeriResult->fetch_assoc()) {
    $galeriTotal = $row['total'];
}

// Count dokumen
$dokumenQuery = "SELECT COUNT(*) as total FROM dokumen WHERE status = 'aktif'";
$dokumenResult = $conn->query($dokumenQuery);
$dokumenTotal = 0;
if ($dokumenResult && $row = $dokumenResult->fetch_assoc()) {
    $dokumenTotal = $row['total'];
}

// Count unread messages
$pesanQuery = "SELECT COUNT(*) as total FROM pesan_kontak WHERE status = 'belum_dibaca'";
$pesanResult = $conn->query($pesanQuery);
$pesanTotal = 0;
if ($pesanResult && $row = $pesanResult->fetch_assoc()) {
    $pesanTotal = $row['total'];
}

// Get recent publikasi
$recentPublikasiQuery = "SELECT id, judul, kategori, tanggal, status FROM publikasi ORDER BY tanggal DESC LIMIT 5";
$recentPublikasiResult = $conn->query($recentPublikasiQuery);
$recentPublikasi = [];
if ($recentPublikasiResult) {
    while ($row = $recentPublikasiResult->fetch_assoc()) {
        $recentPublikasi[] = $row;
    }
}

// Get recent messages
$recentPesanQuery = "SELECT id, nama, email, kategori, created_at, status FROM pesan_kontak ORDER BY created_at DESC LIMIT 5";
$recentPesanResult = $conn->query($recentPesanQuery);
$recentPesan = [];
if ($recentPesanResult) {
    while ($row = $recentPesanResult->fetch_assoc()) {
        $recentPesan[] = $row;
    }
}

// Get recent activity logs
$recentActivityQuery = "SELECT al.*, u.nama_lengkap 
                        FROM activity_logs al 
                        JOIN users u ON al.user_id = u.id 
                        ORDER BY al.created_at DESC LIMIT 10";
$recentActivityResult = $conn->query($recentActivityQuery);
$recentActivity = [];
if ($recentActivityResult) {
    while ($row = $recentActivityResult->fetch_assoc()) {
        $recentActivity[] = $row;
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Selamat datang di Dashboard Admin CDK Wilayah Bojonegoro</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <!-- Layanan -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary">
                        <i class="ri-customer-service-line"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-number"><?php echo $layananTotal; ?></h4>
                        <p class="stat-label">Layanan</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="layanan.php" class="card-link">Kelola Layanan <i class="ri-arrow-right-line"></i></a>
            </div>
        </div>
    </div>

    <!-- Program -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-number"><?php echo $programTotal; ?></h4>
                        <p class="stat-label">Program</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="program.php" class="card-link">Kelola Program <i class="ri-arrow-right-line"></i></a>
            </div>
        </div>
    </div>

    <!-- Publikasi -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-info">
                        <i class="ri-newspaper-line"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-number"><?php echo $publikasiTotal; ?></h4>
                        <p class="stat-label">Publikasi</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: 100%"></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="publikasi.php" class="card-link">Kelola Publikasi <i class="ri-arrow-right-line"></i></a>
            </div>
        </div>
    </div>

    <!-- Galeri -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-warning">
                        <i class="ri-image-2-line"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-number"><?php echo $galeriTotal; ?></h4>
                        <p class="stat-label">Galeri</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-warning" style="width: 100%"></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="galeri.php" class="card-link">Kelola Galeri <i class="ri-arrow-right-line"></i></a>
            </div>
        </div>
    </div>

    <!-- Dokumen -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-danger">
                        <i class="ri-file-pdf-line"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-number"><?php echo $dokumenTotal; ?></h4>
                        <p class="stat-label">Dokumen</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-danger" style="width: 100%"></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="dokumen.php" class="card-link">Kelola Dokumen <i class="ri-arrow-right-line"></i></a>
            </div>
        </div>
    </div>

    <!-- Pesan -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-secondary">
                        <i class="ri-message-3-line"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-number"><?php echo $pesanTotal; ?></h4>
                        <p class="stat-label">Pesan Belum Dibaca</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-secondary" style="width: 100%"></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="pesan.php" class="card-link">Kelola Pesan <i class="ri-arrow-right-line"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Publikasi -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Publikasi Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentPublikasi)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada publikasi</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentPublikasi as $publikasi): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($publikasi['judul']); ?></td>
                                        <td><span
                                                class="badge bg-info"><?php echo htmlspecialchars($publikasi['kategori']); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($publikasi['tanggal'])); ?></td>
                                        <td>
                                            <?php if ($publikasi['status'] === 'published'): ?>
                                                <span class="badge bg-success">Dipublikasi</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="publikasi.php" class="btn btn-sm btn-primary">Lihat Semua Publikasi</a>
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Pesan Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentPesan)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada pesan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentPesan as $pesan): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pesan['nama']); ?></td>
                                        <td><span
                                                class="badge bg-info"><?php echo htmlspecialchars($pesan['kategori']); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($pesan['created_at'])); ?></td>
                                        <td>
                                            <?php if ($pesan['status'] === 'belum_dibaca'): ?>
                                                <span class="badge bg-danger">Belum Dibaca</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Sudah Dibaca</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="pesan.php" class="btn btn-sm btn-primary">Lihat Semua Pesan</a>
            </div>
        </div>
    </div>
</div>

<!-- Activity Log -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="timeline">
                    <?php if (empty($recentActivity)): ?>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <p class="text-center py-3">Belum ada aktivitas tercatat.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentActivity as $activity): ?>
                            <div class="timeline-item">
                                <div class="timeline-icon 
                                    <?php
                                    $iconClass = 'bg-primary';
                                    $icon = 'ri-edit-line';

                                    if (strpos($activity['action'], 'tambah') !== false) {
                                        $iconClass = 'bg-success';
                                        $icon = 'ri-add-line';
                                    } elseif (strpos($activity['action'], 'hapus') !== false) {
                                        $iconClass = 'bg-danger';
                                        $icon = 'ri-delete-bin-line';
                                    } elseif (strpos($activity['action'], 'login') !== false) {
                                        $iconClass = 'bg-info';
                                        $icon = 'ri-login-circle-line';
                                    }

                                    echo $iconClass;
                                    ?>">
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-time">
                                        <?php echo date('d M Y - H:i', strtotime($activity['created_at'])); ?></div>
                                    <h5 class="timeline-title"><?php echo htmlspecialchars($activity['nama_lengkap']); ?></h5>
                                    <p><?php echo htmlspecialchars($activity['action']); ?> di modul
                                        <?php echo htmlspecialchars($activity['module']); ?>
                                        <?php if ($activity['item_id']): ?>
                                            (ID: <?php echo $activity['item_id']; ?>)
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Akses Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="publikasi-form.php"
                            class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-3">
                            <i class="ri-add-line"></i> Tambah Publikasi
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="galeri-form.php"
                            class="btn btn-warning w-100 d-flex align-items-center justify-content-center gap-2 py-3">
                            <i class="ri-add-line"></i> Tambah Galeri
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="dokumen-form.php"
                            class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 py-3">
                            <i class="ri-add-line"></i> Tambah Dokumen
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="statistik-form.php"
                            class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2 py-3">
                            <i class="ri-add-line"></i> Tambah Statistik
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Stats Cards */
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .stat-icon i {
        font-size: 1.8rem;
        color: #fff;
    }

    .stat-content {
        flex: 1;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 600;
        margin: 0;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.9rem;
        color: var(--text-light);
        margin: 0;
    }

    .card-footer {
        background-color: transparent;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 0.75rem 1.5rem;
    }

    .card-link {
        color: var(--primary-color);
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .card-link:hover {
        color: var(--primary-dark);
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding: 1.5rem;
    }

    .timeline-item {
        position: relative;
        padding-left: 3rem;
        margin-bottom: 1.5rem;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 14px;
        height: 100%;
        width: 2px;
        background-color: #e9ecef;
    }

    .timeline-item:last-child::before {
        height: 15px;
    }

    .timeline-icon {
        position: absolute;
        top: 0;
        left: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .timeline-icon i {
        font-size: 14px;
        color: #fff;
    }

    .timeline-content {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        position: relative;
    }

    .timeline-content::before {
        content: '';
        position: absolute;
        top: 10px;
        left: -8px;
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid #f8f9fa;
    }

    .timeline-time {
        font-size: 0.75rem;
        color: var(--text-light);
        margin-bottom: 0.5rem;
    }

    .timeline-title {
        font-size: 1rem;
        margin: 0 0 0.5rem;
    }

    .timeline-content p {
        margin: 0;
        font-size: 0.9rem;
    }
</style>

<?php
// Include footer
require_once 'includes/footer.php';
?>