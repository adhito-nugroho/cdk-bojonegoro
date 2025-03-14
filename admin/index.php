<?php
/**
 * Index Admin (Dashboard) untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menampilkan halaman dashboard admin
 */

// Define BASE_PATH
define('BASE_PATH', dirname(__DIR__) . '/');

// Include konfigurasi dan fungsi
require_once 'config.php';
require_once 'functions.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/functions.php';

// Set judul halaman
$page_title = 'Dashboard - Admin Panel CDK Wilayah Bojonegoro';

// Dapatkan statistik dashboard
$stats = getDashboardStats();

// Dapatkan pesan terbaru
$latestMessages = getLatestMessages();

// Dapatkan publikasi terbaru
$latestPublikasi = getLatestPublikasi();

// Dapatkan aktivitas terbaru
$latestActivities = getLatestActivities();

// Dapatkan data statistik kunjungan (contoh data saja)
$visitorStats = [
    'labels' => ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    'data' => [5, 10, 15, 12, 18, 22, 25, 30, 28, 25, 22, 20]
];

// Dapatkan data tren publikasi (contoh data saja)
$publicationTrends = [
    'labels' => ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    'data' => [3, 5, 2, 4, 6, 8, 5, 7, 9, 6, 8, 7]
];

// Include header
include_once 'includes/header.php';
?>

<!-- Dashboard Content -->
<div class="page-header">
    <h4>Dashboard</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Stats Overview -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-info">
                <h5><?php echo $stats['total_users']; ?></h5>
                <span>Total Pengguna</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon posts">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stats-info">
                <h5><?php echo $stats['total_publikasi']; ?></h5>
                <span>Total Publikasi</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon documents">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-info">
                <h5><?php echo $stats['total_dokumen']; ?></h5>
                <span>Total Dokumen</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon messages">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stats-info">
                <h5><?php echo $stats['total_pesan']; ?></h5>
                <span>Total Pesan <span class="text-danger">(<?php echo $stats['unread_pesan']; ?> belum
                        dibaca)</span></span>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Recent Data -->
<div class="row">
    <!-- Visitor Stats Chart -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i> Statistik Kunjungan</h5>
                <div>
                    <select class="form-select form-select-sm" id="yearSelector">
                        <option value="2025" selected>2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="visitorChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Publication Trends Chart -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar me-2"></i> Tren Publikasi</h5>
                <div>
                    <select class="form-select form-select-sm" id="publicationYearSelector">
                        <option value="2025" selected>2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="publicationChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Posts -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-newspaper me-2"></i> Publikasi Terbaru</h5>
                <a href="<?php echo ADMIN_URL; ?>/modules/publikasi/index.php" class="btn btn-sm btn-primary">Lihat
                    Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($latestPublikasi)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada publikasi</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($latestPublikasi as $publikasi): ?>
                                    <tr>
                                        <td><?php echo $publikasi['judul']; ?></td>
                                        <td><span class="badge bg-info"><?php echo $publikasi['nama_kategori']; ?></span></td>
                                        <td><?php echo formatTanggal($publikasi['tanggal_publikasi']); ?></td>
                                        <td>
                                            <a href="<?php echo ADMIN_URL; ?>/modules/publikasi/edit.php?id=<?php echo $publikasi['id']; ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo SITE_URL; ?>/modules/publikasi/detail.php?slug=<?php echo $publikasi['slug']; ?>"
                                                target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-envelope me-2"></i> Pesan Terbaru</h5>
                <a href="<?php echo ADMIN_URL; ?>/modules/pesan/index.php" class="btn btn-sm btn-primary">Lihat
                    Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($latestMessages)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada pesan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($latestMessages as $message): ?>
                                    <tr>
                                        <td><?php echo $message['nama']; ?></td>
                                        <td><span class="badge bg-secondary"><?php echo ucfirst($message['kategori']); ?></span>
                                        </td>
                                        <td><?php echo formatTanggal($message['created_at'], true); ?></td>
                                        <td>
                                            <?php if ($message['is_read']): ?>
                                                <span class="badge bg-success">Sudah dibaca</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Belum dibaca</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo ADMIN_URL; ?>/modules/pesan/view.php?id=<?php echo $message['id']; ?>"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history me-2"></i> Aktivitas Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>Tindakan</th>
                                <th>IP Address</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($latestActivities)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada aktivitas</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($latestActivities as $activity): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($activity['user_name'])): ?>
                                                <span class="fw-bold"><?php echo $activity['user_name']; ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Unknown</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($activity['table_name'])): ?>
                                                <?php echo formatActionSummary($activity['action'], $activity['table_name']); ?>
                                            <?php else: ?>
                                                <?php echo $activity['action']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $activity['ip_address']; ?></td>
                                        <td><?php echo formatTanggal($activity['created_at'], true); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Initialization Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Visitor Stats Chart
        const visitorCtx = document.getElementById('visitorChart').getContext('2d');
        const visitorChart = new Chart(visitorCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($visitorStats['labels']); ?>,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: <?php echo json_encode($visitorStats['data']); ?>,
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46, 125, 50, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Publication Trends Chart
        const publicationCtx = document.getElementById('publicationChart').getContext('2d');
        const publicationChart = new Chart(publicationCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($publicationTrends['labels']); ?>,
                datasets: [{
                    label: 'Jumlah Publikasi',
                    data: <?php echo json_encode($publicationTrends['data']); ?>,
                    backgroundColor: 'rgba(46, 125, 50, 0.7)',
                    borderColor: 'rgba(46, 125, 50, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Year selector for visitor chart
        document.getElementById('yearSelector').addEventListener('change', function () {
            // This would normally fetch new data from the server via AJAX
            // For now, we'll just simulate a data change
            const newData = [Math.random() * 10, Math.random() * 15, Math.random() * 20,
            Math.random() * 25, Math.random() * 30, Math.random() * 35,
            Math.random() * 40, Math.random() * 35, Math.random() * 30,
            Math.random() * 25, Math.random() * 20, Math.random() * 15];

            visitorChart.data.datasets[0].data = newData;
            visitorChart.update();
        });

        // Year selector for publication chart
        document.getElementById('publicationYearSelector').addEventListener('change', function () {
            // This would normally fetch new data from the server via AJAX
            // For now, we'll just simulate a data change
            const newData = [Math.random() * 5, Math.random() * 8, Math.random() * 6,
            Math.random() * 9, Math.random() * 7, Math.random() * 10,
            Math.random() * 8, Math.random() * 9, Math.random() * 7,
            Math.random() * 6, Math.random() * 8, Math.random() * 7];

            publicationChart.data.datasets[0].data = newData;
            publicationChart.update();
        });
    });
</script>

<?php
// Include footer
include_once 'includes/footer.php';
?>