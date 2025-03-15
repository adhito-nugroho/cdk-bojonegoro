<?php
/**
 * CDK Wilayah Bojonegoro - Main Website
 * 
 * This is the main entry point for the website that displays content from the database.
 */

// Include configuration and functions
require_once 'admin/includes/config.php';
require_once 'admin/includes/functions.php';

// Get database connection
$conn = getConnection();

// Get site settings
$siteTitle = getSetting('nama_instansi') ?: 'CDK Wilayah Bojonegoro';
$siteAddress = getSetting('alamat') ?: 'Jl. Hayam Wuruk No. 9, Bojonegoro, Jawa Timur';
$sitePhone = getSetting('telepon') ?: '(0353) 123456';
$siteEmail = getSetting('email') ?: 'info@cdk-bojonegoro.jatimprov.go.id';
$siteServiceHours = getSetting('jam_layanan') ?: 'Senin - Jumat: 08:00 - 16:00 WIB';
$mapCoordinates = getSetting('map_coordinates') ?: '-7.1507, 111.8871';
$metaDescription = getSetting('meta_description') ?: 'Cabang Dinas Kehutanan Wilayah Bojonegoro - Melayani masyarakat dalam pengelolaan dan pelestarian hutan';

// Get Layanan data
$layananQuery = "SELECT * FROM layanan WHERE status = 'aktif' ORDER BY urutan ASC";
$layananResult = $conn->query($layananQuery);
$layananItems = [];

if ($layananResult && $layananResult->num_rows > 0) {
    while ($layanan = $layananResult->fetch_assoc()) {
        // Get layanan detail items
        $layananId = $layanan['id'];
        $layananDetailQuery = "SELECT * FROM layanan_detail WHERE layanan_id = $layananId ORDER BY urutan ASC";
        $layananDetailResult = $conn->query($layananDetailQuery);
        $detailItems = [];

        if ($layananDetailResult && $layananDetailResult->num_rows > 0) {
            while ($detail = $layananDetailResult->fetch_assoc()) {
                $detailItems[] = $detail;
            }
        }

        $layanan['detail_items'] = $detailItems;
        $layananItems[] = $layanan;
    }
}

// Get Program data
$programQuery = "SELECT * FROM program WHERE status = 'aktif' ORDER BY urutan ASC";
$programResult = $conn->query($programQuery);
$programItems = [];

if ($programResult && $programResult->num_rows > 0) {
    while ($program = $programResult->fetch_assoc()) {
        // Get program detail items
        $programId = $program['id'];
        $programDetailQuery = "SELECT * FROM program_detail WHERE program_id = $programId ORDER BY urutan ASC";
        $programDetailResult = $conn->query($programDetailQuery);
        $detailItems = [];

        if ($programDetailResult && $programDetailResult->num_rows > 0) {
            while ($detail = $programDetailResult->fetch_assoc()) {
                $detailItems[] = $detail;
            }
        }

        $program['detail_items'] = $detailItems;
        $programItems[] = $program;
    }
}

// Get Publikasi data (recent news)
$publikasiQuery = "SELECT * FROM publikasi WHERE status = 'published' ORDER BY tanggal DESC LIMIT 4";
$publikasiResult = $conn->query($publikasiQuery);
$publikasiItems = [];

if ($publikasiResult && $publikasiResult->num_rows > 0) {
    while ($publikasi = $publikasiResult->fetch_assoc()) {
        $publikasiItems[] = $publikasi;
    }
}

// Get Dokumen data (recent documents)
$dokumenQuery = "SELECT * FROM dokumen WHERE status = 'aktif' ORDER BY tanggal DESC LIMIT 4";
$dokumenResult = $conn->query($dokumenQuery);
$dokumenItems = [];

if ($dokumenResult && $dokumenResult->num_rows > 0) {
    while ($dokumen = $dokumenResult->fetch_assoc()) {
        $dokumenItems[] = $dokumen;
    }
}

// Get Galeri data
$galeriKategoriQuery = "SELECT * FROM galeri_kategori WHERE status = 'aktif' ORDER BY urutan ASC";
$galeriKategoriResult = $conn->query($galeriKategoriQuery);
$galeriKategoriItems = [];

if ($galeriKategoriResult && $galeriKategoriResult->num_rows > 0) {
    while ($kategori = $galeriKategoriResult->fetch_assoc()) {
        $galeriKategoriItems[] = $kategori;
    }
}

$galeriQuery = "SELECT * FROM galeri WHERE status = 'aktif' ORDER BY tanggal DESC LIMIT 6";
$galeriResult = $conn->query($galeriQuery);
$galeriItems = [];

if ($galeriResult && $galeriResult->num_rows > 0) {
    while ($galeri = $galeriResult->fetch_assoc()) {
        $galeriItems[] = $galeri;
    }
}

// Get Statistik data
$statistikQuery = "SELECT * FROM statistik WHERE status = 'aktif' ORDER BY id ASC LIMIT 4";
$statistikResult = $conn->query($statistikQuery);
$statistikItems = [];

if ($statistikResult && $statistikResult->num_rows > 0) {
    while ($statistik = $statistikResult->fetch_assoc()) {
        $statistikItems[] = $statistik;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#2e7d32" />
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>" />
    <title><?php echo htmlspecialchars($siteTitle); ?> - Dinas Kehutanan Provinsi Jawa Timur</title>

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@accessibility-community/accessible-dark-mode@1.0.0/style.css" />

    <link rel="stylesheet" href="assets/css/styles.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Dashboard Styles -->
    <style>
        /* Global Spacing */
        :root {
            --section-spacing: 6rem;
            --section-spacing-sm: 4rem;
            --content-spacing: 2rem;
            --navbar-height: 80px;
        }

        /* Base Layout */
        html {
            scroll-padding-top: var(--navbar-height);
            scroll-behavior: smooth;
        }

        body {
            padding-top: var(--navbar-height);
        }

        /* Navbar */
        .navbar {
            height: var(--navbar-height);
            background: rgba(27, 67, 50, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            z-index: 1030;
        }

        .navbar.scrolled {
            background: rgba(27, 67, 50, 0.98);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #ffffff !important;
        }

        /* Section Base */
        section {
            padding: var(--section-spacing) 0;
            position: relative;
            overflow: visible;
        }

        /* Hero Section */
        .hero-section {
            padding: 0;
            height: 100vh;
            min-height: 600px;
            display: flex;
            align-items: center;
            margin-top: calc(-1 * var(--navbar-height));
        }

        .hero-video-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .hero-video-bg video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(45, 106, 79, 0.8), rgba(27, 67, 50, 0.85));
            z-index: 2;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 3;
            opacity: 0.3;
        }

        .hero-content-wrapper {
            position: relative;
            z-index: 4;
            width: 100%;
            padding: 0 1.5rem;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 3.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }

        /* Section Headers */
        .section-header {
            margin-bottom: var(--section-spacing-sm);
            text-align: center;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            z-index: 10;
            padding: 1rem 0;
            background: linear-gradient(to bottom, var(--bg-white) 50%, transparent);
        }

        .section-header h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
            font-weight: 700;
        }

        .section-header .section-subheading {
            font-size: 1.1rem;
            color: var(--text-medium);
            line-height: 1.6;
        }

        /* Content z-index */
        .section-content {
            position: relative;
            z-index: 1;
            margin-top: -50px;
            padding-top: 50px;
        }

        /* Dashboard Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: var(--content-spacing);
            position: relative;
            z-index: 1;
            padding-top: 2rem;
        }

        .dashboard-card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            transform: translateY(0);
            opacity: 1;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-header h4 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
            font-size: 1.2rem;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        /* Responsive */
        @media (max-width: 991px) {
            :root {
                --section-spacing: 5rem;
                --section-spacing-sm: 3rem;
                --navbar-height: 70px;
            }

            .section-header h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            :root {
                --section-spacing: 4rem;
                --section-spacing-sm: 2.5rem;
                --content-spacing: 1.5rem;
                --navbar-height: 60px;
            }

            .hero-content {
                padding: 2rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .section-header h2 {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 576px) {
            .hero-content {
                padding: 1.5rem;
                margin: 0 1rem;
            }

            .container {
                padding: 0 1rem;
            }
        }

        /* Update ScrollReveal animations */
        [data-aos] {
            pointer-events: all !important;
        }

        /* Update specific sections background */
        .profile-section {
            background: var(--bg-light);
        }

        .stats-dashboard {
            background: var(--bg-white);
            position: relative;
        }

        .monitoring-section {
            background: var(--bg-light);
        }

        /* Remove background from section header */
        .section-header {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }

        /* Update dashboard cards */
        .dashboard-grid {
            position: relative;
            z-index: 1;
            margin-top: 2rem;
        }

        .dashboard-card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }
    </style>

    <!-- Di bagian head, setelah CSS libraries yang ada -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="loading-animation"></div>
    </div>

    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/images/logo.png" alt="Logo CDK Bojonegoro" height="40" />
                <?php echo htmlspecialchars($siteTitle); ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#profil">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#program">Program</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#monitoring">Monitoring</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#publikasi">Publikasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section id="beranda" class="hero-section">
        <div id="particles-js"></div>
        <div class="hero-video-bg">
            <video autoplay muted loop playsinline>
                <source src="assets/videos/forest-bg.mp4" type="video/mp4">
            </video>
        </div>
        <div class="hero-content-wrapper">
            <div class="hero-content">
                <h1 data-aos="fade-up"><?php echo htmlspecialchars($siteTitle); ?></h1>
                <p data-aos="fade-up" data-aos-delay="100">
                    Melayani masyarakat dalam pengelolaan dan pelestarian hutan
                </p>
                <div class="hero-buttons" data-aos="fade-up" data-aos-delay="200">
                    <a href="#layanan" class="btn btn-success">
                        Layanan Kami <i class="ri-arrow-right-line"></i>
                    </a>
                    <a href="#kontak" class="btn btn-outline-light">
                        Hubungi Kami <i class="ri-customer-service-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Stats Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <?php foreach ($statistikItems as $index => $stat): ?>
                <div class="stat-card" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                    <div class="stat-icon">
                        <i class="<?php echo htmlspecialchars($stat['icon']); ?>"></i>
                    </div>
                    <h3 class="stat-number" data-counter="<?php echo htmlspecialchars($stat['nilai']); ?>">
                        <?php echo number_format($stat['nilai']); ?>     <?php echo $stat['satuan'] ? '+' : ''; ?>
                    </h3>
                    <p class="stat-label"><?php echo htmlspecialchars($stat['judul']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- Profil Section -->
    <section id="profil" class="profile-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Profil CDK Wilayah Bojonegoro</h2>
                <p class="section-subheading">
                    Unit Pelaksana Teknis Dinas Kehutanan Provinsi Jawa Timur
                </p>
            </div>

            <div class="row g-4">
                <!-- Wilayah Kerja -->
                <div class="col-lg-6" data-aos="fade-up">
                    <div class="profile-card glass-card">
                        <div class="card-header-custom">
                            <div class="header-icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <h4>Wilayah Kerja</h4>
                        </div>
                        <div class="wilayah-list">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="wilayah-item">
                                        <i class="fas fa-circle-check"></i>
                                        <span>Kabupaten Bojonegoro</span>
                                    </div>
                                    <div class="wilayah-item">
                                        <i class="fas fa-circle-check"></i>
                                        <span>Kabupaten Tuban</span>
                                    </div>
                                    <div class="wilayah-item">
                                        <i class="fas fa-circle-check"></i>
                                        <span>Kabupaten Lamongan</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="wilayah-item">
                                        <i class="fas fa-circle-check"></i>
                                        <span>Kabupaten Gresik</span>
                                    </div>
                                    <div class="wilayah-item">
                                        <i class="fas fa-circle-check"></i>
                                        <span>Kabupaten Sidoarjo</span>
                                    </div>
                                    <div class="wilayah-item">
                                        <i class="fas fa-circle-check"></i>
                                        <span>Kota Surabaya</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="map" class="wilayah-map mt-4"></div>
                    </div>
                </div>

                <!-- Tugas dan Fungsi -->
                <div class="col-lg-6" data-aos="fade-up">
                    <div class="profile-card glass-card">
                        <div class="card-header-custom">
                            <div class="header-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h4>Tugas dan Fungsi</h4>
                        </div>
                        <div class="tugas-content">
                            <h5>Tugas Pokok</h5>
                            <p>
                                Membantu Kepala Dinas Kehutanan melaksanakan sebagian urusan
                                pemerintahan yang menjadi kewenangan Provinsi di wilayah
                                kerja.
                            </p>

                            <h5 class="mt-4">Fungsi Utama</h5>
                            <div class="fungsi-list">
                                <div class="fungsi-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Penyusunan perencanaan program dan anggaran</span>
                                </div>
                                <div class="fungsi-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Pelaksanaan pelayanan, pemantauan, dan pengawasan
                                        kehutanan</span>
                                </div>
                                <div class="fungsi-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Pembinaan dan pengembangan hutan hak</span>
                                </div>
                                <div class="fungsi-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Rehabilitasi dan konservasi hutan</span>
                                </div>
                                <div class="fungsi-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Penyuluhan dan pemberdayaan masyarakat</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Struktur Organisasi -->
                <div class="col-12" data-aos="fade-up">
                    <div class="profile-card">
                        <div class="card-header-custom">
                            <div class="header-icon">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <h4>Struktur Organisasi</h4>
                        </div>
                        <div class="struktur-organisasi py-5">
                            <div class="org-tree">
                                <!-- Kepala CDK -->
                                <div class="org-level">
                                    <div class="org-item">
                                        <div class="org-content primary">
                                            <div class="org-box">
                                                <div class="org-icon">
                                                    <i class="fas fa-user-tie mb-2"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">
                                                    Kepala Cabang Dinas Kehutanan
                                                </h5>
                                                <p class="mb-0">
                                                    Pimpinan dan penanggung jawab CDK Wilayah Bojonegoro
                                                </p>
                                            </div>
                                            <div class="org-connector"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub Bagian -->
                                <div class="org-level">
                                    <div class="org-item">
                                        <div class="org-content secondary">
                                            <div class="org-box">
                                                <div class="org-icon">
                                                    <i class="fas fa-tasks mb-2"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">Sub Bagian Tata Usaha</h5>
                                                <p class="mb-0">
                                                    Administrasi, kepegawaian, keuangan, dan umum
                                                </p>
                                            </div>
                                            <div class="org-connector"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Seksi-seksi -->
                                <div class="org-level">
                                    <div class="org-item-wrapper">
                                        <div class="org-item">
                                            <div class="org-content tertiary">
                                                <div class="org-box">
                                                    <div class="org-icon">
                                                        <i class="fas fa-seedling mb-2"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">
                                                        Seksi Rehabilitasi Lahan dan Pemberdayaan
                                                        Masyarakat
                                                    </h5>
                                                    <p class="mb-0">
                                                        Rehabilitasi hutan dan pemberdayaan masyarakat
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="org-item">
                                            <div class="org-content tertiary">
                                                <div class="org-box">
                                                    <div class="org-icon">
                                                        <i class="fas fa-chart-line mb-2"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">
                                                        Seksi Tata Kelola dan Usaha Kehutanan
                                                    </h5>
                                                    <p class="mb-0">
                                                        Perizinan dan pengembangan usaha kehutanan
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section id="layanan" class="services-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Layanan Kehutanan</h2>
                <p class="section-subheading">
                    Pelayanan teknis bidang kehutanan sesuai wilayah kerja
                </p>
            </div>

            <div class="row g-4">
                <?php foreach ($layananItems as $layanan): ?>
                    <div class="col-lg-4" data-aos="fade-up">
                        <div class="service-card glass-card">
                            <div class="service-icon-wrapper">
                                <i class="<?php echo htmlspecialchars($layanan['icon']); ?> service-icon"></i>
                            </div>
                            <div class="service-content">
                                <h4><?php echo htmlspecialchars($layanan['judul']); ?></h4>
                                <ul class="service-list">
                                    <?php foreach ($layanan['detail_items'] as $detail): ?>
                                        <li>
                                            <i class="fas fa-check-circle"></i>
                                            <?php echo htmlspecialchars($detail['judul']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="service-action">
                                    <a href="#" class="btn btn-success mt-3">Ajukan Permohonan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="program" class="program-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Program & Kegiatan</h2>
                <p class="section-subheading">Program dan kegiatan teknis bidang kehutanan sesuai Pergub No 48 Tahun
                    2018</p>
            </div>

            <div class="program-grid">
                <?php foreach ($programItems as $program): ?>
                    <div class="program-card" data-aos="fade-up">
                        <div class="program-header">
                            <div class="program-icon">
                                <i class="<?php echo htmlspecialchars($program['icon']); ?>"></i>
                            </div>
                            <div class="program-title">
                                <h4><?php echo htmlspecialchars($program['judul']); ?></h4>
                                <p><?php echo htmlspecialchars($program['deskripsi']); ?></p>
                            </div>
                        </div>
                        <div class="program-content">
                            <ul class="program-list">
                                <?php foreach ($program['detail_items'] as $detail): ?>
                                    <li>
                                        <i class="ri-check-line"></i>
                                        <span><?php echo htmlspecialchars($detail['kegiatan']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="statistik" class="stats-dashboard">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Data & Statistik Kehutanan</h2>
                <p class="section-subheading">Informasi dan visualisasi data kehutanan wilayah Bojonegoro</p>
            </div>

            <div class="dashboard-grid">
                <!-- Luas Kawasan Hutan -->
                <div class="dashboard-card" data-aos="fade-up">
                    <div class="card-header">
                        <h4><i class="ri-landscape-line"></i> Luas Kawasan Hutan</h4>
                        <select class="year-select">
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="forestAreaChart"></canvas>
                    </div>
                </div>

                <!-- Produksi Hasil Hutan -->
                <div class="dashboard-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header">
                        <h4><i class="ri-plant-line"></i> Produksi Hasil Hutan</h4>
                        <select class="year-select">
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="forestProductionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Monitoring Section -->
    <section id="monitoring" class="monitoring-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Monitoring & Evaluasi</h2>
                <p class="section-subheading">
                    Pemantauan dan evaluasi kegiatan kehutanan
                </p>
            </div>

            <div class="row g-4">
                <!-- Statistik Kehutanan -->
                <div class="col-lg-6" data-aos="fade-up">
                    <div class="monitoring-card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar icon-header"></i>
                            <h4>Statistik Kehutanan</h4>
                        </div>
                        <div class="stats-container">
                            <canvas id="forestStats"></canvas>
                            <div class="stats-summary">
                                <?php foreach ($statistikItems as $statistik): ?>
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="<?php echo htmlspecialchars($statistik['icon']); ?>"></i>
                                        </div>
                                        <div class="stat-info">
                                            <span
                                                class="stat-label"><?php echo htmlspecialchars($statistik['judul']); ?></span>
                                            <span class="stat-value"><?php echo number_format($statistik['nilai']); ?>
                                                <?php echo htmlspecialchars($statistik['satuan']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Capaian Program -->
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="monitoring-card">
                        <div class="card-header">
                            <i class="fas fa-tasks icon-header"></i>
                            <h4>Capaian Program</h4>
                        </div>
                        <div class="progress-list">
                            <div class="progress-item">
                                <div class="progress-header">
                                    <div class="progress-title">
                                        <i class="fas fa-seedling progress-icon"></i>
                                        <span>Rehabilitasi Lahan</span>
                                    </div>
                                    <span class="progress-percentage">75%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 75%">
                                        <div class="progress-glow"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress-item">
                                <div class="progress-header">
                                    <div class="progress-title">
                                        <i class="fas fa-hands-helping progress-icon"></i>
                                        <span>Perhutanan Sosial</span>
                                    </div>
                                    <span class="progress-percentage">80%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 80%">
                                        <div class="progress-glow"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress-item">
                                <div class="progress-header">
                                    <div class="progress-title">
                                        <i class="fas fa-leaf progress-icon"></i>
                                        <span>Pembibitan</span>
                                    </div>
                                    <span class="progress-percentage">90%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 90%">
                                        <div class="progress-glow"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress-item">
                                <div class="progress-header">
                                    <div class="progress-title">
                                        <i class="fas fa-chalkboard-teacher progress-icon"></i>
                                        <span>Penyuluhan</span>
                                    </div>
                                    <span class="progress-percentage">85%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 85%">
                                        <div class="progress-glow"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Publikasi Section -->
    <section id="publikasi" class="publication-section">
        <div class="container py-5">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2>Publikasi & Informasi</h2>
                <p class="section-subheading">Berita terkini dan dokumen penting terkait kehutanan</p>
            </div>

            <div class="row g-4">
                <!-- Berita Terkini -->
                <div class="col-lg-8" data-aos="fade-up">
                    <div class="row g-4">
                        <?php foreach ($publikasiItems as $publikasi): ?>
                            <div class="col-md-6">
                                <div class="news-card animate-hover">
                                    <img src="admin/uploads/publikasi/<?php echo htmlspecialchars($publikasi['thumbnail']); ?>"
                                        alt="<?php echo htmlspecialchars($publikasi['judul']); ?>" class="news-image">
                                    <div class="news-content">
                                        <span
                                            class="news-tag"><?php echo htmlspecialchars($publikasi['kategori']); ?></span>
                                        <h5><?php echo htmlspecialchars($publikasi['judul']); ?></h5>
                                        <p><?php echo substr(strip_tags($publikasi['konten']), 0, 120); ?>...</p>
                                        <div class="news-meta">
                                            <span><i class="ri-calendar-line"></i>
                                                <?php echo date('d M Y', strtotime($publikasi['tanggal'])); ?></span>
                                            <a href="publikasi.php?slug=<?php echo htmlspecialchars($publikasi['slug']); ?>"
                                                class="read-more">Baca Selengkapnya <i class="ri-arrow-right-line"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-4">
                        <a href="publikasi.php" class="btn btn-outline-success">
                            Lihat Semua Berita <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>

                <!-- Sidebar Dokumen -->
                <div class="col-lg-4" data-aos="fade-up">
                    <div class="sidebar-box">
                        <h4 class="sidebar-title">Dokumen Penting</h4>
                        <div class="doc-list">
                            <?php foreach ($dokumenItems as $dokumen):
                                // Set icon based on file type
                                $iconClass = 'ri-file-text-line';
                                if ($dokumen['tipe_file'] == 'PDF')
                                    $iconClass = 'ri-file-pdf-line';
                                if ($dokumen['tipe_file'] == 'DOC' || $dokumen['tipe_file'] == 'DOCX')
                                    $iconClass = 'ri-file-word-line';
                                if ($dokumen['tipe_file'] == 'XLS' || $dokumen['tipe_file'] == 'XLSX')
                                    $iconClass = 'ri-file-excel-line';

                                // Format file size
                                $fileSize = round($dokumen['ukuran'] / (1024 * 1024), 1);
                                ?>
                                <a href="admin/uploads/dokumen/<?php echo htmlspecialchars($dokumen['filename']); ?>"
                                    class="doc-item" target="_blank">
                                    <i class="<?php echo $iconClass; ?> doc-icon"></i>
                                    <div class="doc-info">
                                        <h6><?php echo htmlspecialchars($dokumen['judul']); ?></h6>
                                        <span class="doc-meta"><?php echo date('d M Y', strtotime($dokumen['tanggal'])); ?>
                                            • <?php echo $dokumen['tipe_file']; ?> • <?php echo $fileSize; ?> MB</span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-4">
                            <a href="dokumen.php" class="btn btn-outline-success btn-sm">
                                Lihat Semua Dokumen <i class="ri-arrow-right-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Gallery Section -->
    <section id="galeri" class="gallery-section glass-card">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Galeri Kegiatan</h2>
                <p class="section-subheading">Dokumentasi program dan kegiatan kehutanan</p>
            </div>

            <!-- Gallery Filter -->
            <div class="gallery-filter d-flex justify-content-center" data-aos="fade-up">
                <?php foreach ($galeriKategoriItems as $kategori): ?>
                    <button class="btn btn-outline-success <?php echo ($kategori['slug'] == 'all') ? 'active' : ''; ?>"
                        data-filter="<?php echo htmlspecialchars($kategori['slug']); ?>">
                        <?php echo htmlspecialchars($kategori['nama']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Gallery Items -->
            <div class="row g-4 gallery-container">
                <?php foreach ($galeriItems as $index => $galeri): ?>
                    <div class="col-lg-4 col-md-6 gallery-item <?php echo htmlspecialchars($galeri['kategori']); ?>"
                        data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="gallery-card">
                            <div class="gallery-image">
                                <img src="admin/uploads/galeri/<?php echo htmlspecialchars($galeri['filename']); ?>"
                                    alt="<?php echo htmlspecialchars($galeri['judul']); ?>">
                                <div class="gallery-overlay">
                                    <a href="admin/uploads/galeri/<?php echo htmlspecialchars($galeri['filename']); ?>"
                                        class="gallery-popup">
                                        <i class="ri-zoom-in-line"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="gallery-content">
                                <h5><?php echo htmlspecialchars($galeri['judul']); ?></h5>
                                <p><?php echo htmlspecialchars($galeri['deskripsi']); ?></p>
                                <div class="gallery-meta">
                                    <span><i class="ri-calendar-line"></i>
                                        <?php echo date('d F Y', strtotime($galeri['tanggal'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4">
                <a href="galeri.php" class="btn btn-outline-success">
                    Lihat Semua Galeri <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Kontak Section -->
    <section id="kontak" class="contact-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Hubungi Kami</h2>
                <p class="section-subheading">Informasi kontak dan pelayanan</p>
            </div>

            <div class="row g-4">
                <!-- Informasi Kontak -->
                <div class="col-lg-4" data-aos="fade-up">
                    <div class="contact-info">
                        <div class="contact-info-item">
                            <i class="fas fa-map-marker-alt contact-icon"></i>
                            <div>
                                <h5>Alamat Kantor</h5>
                                <p><?php echo htmlspecialchars($siteAddress); ?></p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-phone contact-icon"></i>
                            <div>
                                <h5>Telepon</h5>
                                <p><?php echo htmlspecialchars($sitePhone); ?></p>
                                <p>Hotline: 0800-1234-5678</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-envelope contact-icon"></i>
                            <div>
                                <h5>Email</h5>
                                <p><?php echo htmlspecialchars($siteEmail); ?></p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-clock contact-icon"></i>
                            <div>
                                <h5>Jam Pelayanan</h5>
                                <p><?php echo htmlspecialchars($siteServiceHours); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Kontak -->
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-form-container">
                        <form class="contact-form" id="contactForm" method="post" action="process-contact.php">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama" name="nama" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telepon">Nomor Telepon</label>
                                        <input type="tel" class="form-control" id="telepon" name="telepon" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kategori">Kategori</label>
                                        <select class="form-select" id="kategori" name="kategori" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="perizinan">Perizinan</option>
                                            <option value="pengaduan">Pengaduan</option>
                                            <option value="informasi">Informasi</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="pesan">Pesan</label>
                                        <textarea class="form-control" id="pesan" name="pesan" rows="5"
                                            required></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-2"></i> Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div id="formResponse" class="mt-3" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="footer bg-dark text-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-info">
                        <img src="assets/images/logo-white.png" alt="Logo" height="60" class="mb-3" />
                        <p><?php echo htmlspecialchars($siteTitle); ?></p>
                        <div class="social-links mt-3">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h5>Link Cepat</h5>
                    <ul class="footer-links">
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#profil">Profil</a></li>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#program">Program</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Layanan Utama</h5>
                    <ul class="footer-links">
                        <?php
                        // Get the first 5 layanan items for footer links
                        $footerLayananQuery = "SELECT id, judul FROM layanan WHERE status = 'aktif' ORDER BY urutan ASC LIMIT 5";
                        $footerLayananResult = $conn->query($footerLayananQuery);
                        if ($footerLayananResult && $footerLayananResult->num_rows > 0) {
                            while ($layanan = $footerLayananResult->fetch_assoc()) {
                                echo '<li><a href="#layanan">' . htmlspecialchars($layanan['judul']) . '</a></li>';
                            }
                        } else {
                            // Fallback if no layanan found
                            echo '<li><a href="#layanan">Perizinan Kehutanan</a></li>';
                            echo '<li><a href="#layanan">Perhutanan Sosial</a></li>';
                            echo '<li><a href="#layanan">Rehabilitasi Hutan</a></li>';
                            echo '<li><a href="#layanan">Penyuluhan Kehutanan</a></li>';
                            echo '<li><a href="#kontak">Pengaduan</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Informasi Penting</h5>
                    <ul class="footer-links">
                        <?php
                        // Get recent documents for footer links
                        $footerDokumenQuery = "SELECT id, judul, filename FROM dokumen WHERE status = 'aktif' ORDER BY tanggal DESC LIMIT 4";
                        $footerDokumenResult = $conn->query($footerDokumenQuery);
                        if ($footerDokumenResult && $footerDokumenResult->num_rows > 0) {
                            while ($dokumen = $footerDokumenResult->fetch_assoc()) {
                                echo '<li><a href="admin/uploads/dokumen/' . htmlspecialchars($dokumen['filename']) . '" target="_blank">' . htmlspecialchars($dokumen['judul']) . '</a></li>';
                            }
                        } else {
                            // Fallback if no documents found
                            echo '<li><a href="#publikasi">Prosedur Pelayanan</a></li>';
                            echo '<li><a href="#publikasi">Standar Pelayanan</a></li>';
                            echo '<li><a href="#publikasi">Maklumat Pelayanan</a></li>';
                            echo '<li><a href="#publikasi">Peraturan Terkait</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom mt-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-0">
                            &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteTitle); ?>. Hak Cipta
                            Dilindungi.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="text-white me-3">Kebijakan Privasi</a>
                        <a href="#" class="text-white">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Modal Preview Image -->
    <div class="modal-preview" id="imageModal" style="display: none;">
        <span class="modal-close">&times;</span>
        <img id="modalImage" src="" alt="Preview Image">
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Particles Config
        particlesJS("particles-js", {
            particles: {
                number: {
                    value: 40,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: "#ffffff"
                },
                opacity: {
                    value: 0.3,
                    random: false
                },
                size: {
                    value: 2,
                    random: true
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#ffffff",
                    opacity: 0.2,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 3,
                    direction: "none",
                    random: false,
                    straight: false,
                    out_mode: "out",
                    bounce: false
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: {
                        enable: true,
                        mode: "repulse"
                    },
                    resize: true
                }
            },
            retina_detect: true
        });

        // Initialize Map with coordinates from database
        document.addEventListener("DOMContentLoaded", function () {
            try {
                const coordinates = [<?php echo $mapCoordinates; ?>];
                var map = L.map("map").setView(coordinates, 8);

                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: "© OpenStreetMap contributors",
                }).addTo(map);

                const locations = [
                    { name: "Bojonegoro", coords: [-7.1507, 111.8871] },
                    { name: "Tuban", coords: [-6.8989, 112.0531] },
                    { name: "Lamongan", coords: [-7.1089, 112.4168] },
                    { name: "Gresik", coords: [-7.1666, 112.655] },
                    { name: "Sidoarjo", coords: [-7.4558, 112.7183] },
                    { name: "Surabaya", coords: [-7.2575, 112.7521] },
                ];

                locations.forEach(function (loc) {
                    L.marker(loc.coords).addTo(map).bindPopup(loc.name);
                });

                // Add marker for CDK office with custom icon
                const cdkIcon = L.icon({
                    iconUrl: 'assets/images/map-marker.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });

                L.marker(coordinates, { icon: cdkIcon }).addTo(map)
                    .bindPopup(`<strong><?php echo htmlspecialchars($siteTitle); ?></strong><br><?php echo htmlspecialchars($siteAddress); ?>`);
            } catch (error) {
                console.error("Map initialization error:", error);
            }
        });

        // Initialize charts with data from database
        document.addEventListener("DOMContentLoaded", function () {
            // Forest Area Chart
            const forestAreaCtx = document.getElementById('forestAreaChart');
            if (forestAreaCtx) {
                new Chart(forestAreaCtx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            <?php
                            $forestAreaQuery = "SELECT judul FROM statistik WHERE kategori LIKE '%Hutan%' AND status = 'aktif' ORDER BY nilai DESC";
                            $forestAreaResult = $conn->query($forestAreaQuery);
                            if ($forestAreaResult && $forestAreaResult->num_rows > 0) {
                                $labels = [];
                                while ($row = $forestAreaResult->fetch_assoc()) {
                                    $labels[] = "'" . addslashes($row['judul']) . "'";
                                }
                                echo implode(', ', $labels);
                            } else {
                                echo "'Hutan Produksi', 'Hutan Lindung', 'Hutan Rakyat', 'Hutan Kota'";
                            }
                            ?>
                        ],
                        datasets: [{
                            data: [
                                <?php
                                $forestAreaQuery = "SELECT nilai FROM statistik WHERE kategori LIKE '%Hutan%' AND status = 'aktif' ORDER BY nilai DESC";
                                $forestAreaResult = $conn->query($forestAreaQuery);
                                if ($forestAreaResult && $forestAreaResult->num_rows > 0) {
                                    $values = [];
                                    while ($row = $forestAreaResult->fetch_assoc()) {
                                        $values[] = $row['nilai'];
                                    }
                                    echo implode(', ', $values);
                                } else {
                                    echo "45000, 25000, 15000, 5000";
                                }
                                ?>
                                <?php
                                $forestAreaQuery = "SELECT nilai FROM statistik WHERE kategori LIKE '%Hutan%' AND status = 'aktif' ORDER BY nilai DESC";
                                $forestAreaResult = $conn->query($forestAreaQuery);
                                if ($forestAreaResult && $forestAreaResult->num_rows > 0) {
                                    $values = [];
                                    while ($row = $forestAreaResult->fetch_assoc()) {
                                        $values[] = $row['nilai'];
                                    }
                                    echo implode(', ', $values);
                                } else {
                                    echo "45000, 25000, 15000, 5000";
                                }
                                ?>
                            ],
                            backgroundColor: [
                                'rgba(46, 125, 50, 0.8)',
                                'rgba(56, 142, 60, 0.8)',
                                'rgba(76, 175, 80, 0.8)',
                                'rgba(129, 199, 132, 0.8)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Forest Production Chart
            const forestProductionCtx = document.getElementById('forestProductionChart');
            if (forestProductionCtx) {
                new Chart(forestProductionCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                        datasets: [{
                            label: 'Produksi (Ton)',
                            data: [1200, 1900, 1500, 1800, 2200, 1600],
                            backgroundColor: 'rgba(46, 125, 50, 0.8)',
                            borderColor: 'rgba(46, 125, 50, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Forest Stats Chart
            const forestStatsCtx = document.getElementById('forestStats');
            if (forestStatsCtx) {
                new Chart(forestStatsCtx, {
                    type: 'bar',
                    data: {
                        labels: [
                            <?php
                            $statsQuery = "SELECT judul FROM statistik WHERE status = 'aktif' ORDER BY id ASC LIMIT 4";
                            $statsResult = $conn->query($statsQuery);
                            if ($statsResult && $statsResult->num_rows > 0) {
                                $labels = [];
                                while ($row = $statsResult->fetch_assoc()) {
                                    $labels[] = "'" . addslashes($row['judul']) . "'";
                                }
                                echo implode(', ', $labels);
                            } else {
                                echo "'Hutan Produksi', 'Hutan Rakyat', 'Area Rehabilitasi', 'Perhutanan Sosial'";
                            }
                            ?>
                        ],
                        datasets: [{
                            label: 'Luas Area (Ha)',
                            data: [
                                <?php
                                $statsQuery = "SELECT nilai FROM statistik WHERE status = 'aktif' ORDER BY id ASC LIMIT 4";
                                $statsResult = $conn->query($statsQuery);
                                if ($statsResult && $statsResult->num_rows > 0) {
                                    $values = [];
                                    while ($row = $statsResult->fetch_assoc()) {
                                        $values[] = $row['nilai'];
                                    }
                                    echo implode(', ', $values);
                                } else {
                                    echo "45000, 15000, 5000, 10000";
                                }
                                ?>
                            ],
                            backgroundColor: [
                                'rgba(46, 125, 50, 0.8)',
                                'rgba(76, 175, 80, 0.8)',
                                'rgba(129, 199, 132, 0.8)',
                                'rgba(27, 94, 32, 0.8)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });

        // Contact form AJAX submission
        document.addEventListener('DOMContentLoaded', function () {
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const formResponse = document.getElementById('formResponse');
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;

                    // Show loading state
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
                    submitButton.disabled = true;

                    fetch('process-contact.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            formResponse.style.display = 'block';

                            if (data.status === 'success') {
                                formResponse.className = 'alert alert-success mt-3';
                                formResponse.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + data.message;
                                contactForm.reset();
                            } else {
                                formResponse.className = 'alert alert-danger mt-3';
                                formResponse.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + data.message;
                            }

                            // Reset button state
                            submitButton.innerHTML = originalButtonText;
                            submitButton.disabled = false;

                            // Hide alert after 5 seconds
                            setTimeout(() => {
                                formResponse.style.display = 'none';
                            }, 5000);
                        })
                        .catch(error => {
                            formResponse.style.display = 'block';
                            formResponse.className = 'alert alert-danger mt-3';
                            formResponse.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan. Silakan coba lagi nanti.';

                            // Reset button state
                            submitButton.innerHTML = originalButtonText;
                            submitButton.disabled = false;
                        });
                });
            }
        });

        // Update ScrollReveal configuration
        const scrollReveal = ScrollReveal({
            distance: '20px',
            duration: 800,
            delay: 0,
            easing: 'ease-out',
            reset: false,
            useDelay: 'once',
            viewFactor: 0.1
        });

        // Remove animation from section headers
        scrollReveal.reveal('.section-header', {
            distance: '0px',
            opacity: 1,
            scale: 1,
            viewFactor: 0,
            beforeReveal: function (domEl) {
                domEl.style.opacity = '1';
                domEl.style.transform = 'none';
            }
        });

        // Animation only for content
        scrollReveal.reveal('.section-content, .dashboard-card', {
            delay: 200,
            distance: '30px',
            origin: 'bottom',
            interval: 100,
            viewFactor: 0.2,
            beforeReveal: function (domEl) {
                // Ensure section header stays on top
                const header = domEl.closest('section').querySelector('.section-header');
                if (header) {
                    header.style.zIndex = '10';
                }
            }
        });

        // Initialize AOS animations
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Gallery filter functionality
        document.addEventListener('DOMContentLoaded', function () {
            const filterButtons = document.querySelectorAll('.gallery-filter button');
            const galleryItems = document.querySelectorAll('.gallery-item');

            filterButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));

                    // Add active class to clicked button
                    this.classList.add('active');

                    // Get filter value
                    const filterValue = this.getAttribute('data-filter');

                    // Filter gallery items
                    galleryItems.forEach(item => {
                        if (filterValue === 'all' || item.classList.contains(filterValue)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Gallery popup functionality
            const galleryPopups = document.querySelectorAll('.gallery-popup');
            const imageModal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalClose = document.querySelector('.modal-close');

            galleryPopups.forEach(popup => {
                popup.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Get image source from href attribute
                    const imageSrc = this.getAttribute('href');

                    // Set modal image source
                    modalImage.src = imageSrc;

                    // Show modal
                    imageModal.style.display = 'flex';
                });
            });

            // Close modal on click
            if (modalClose) {
                modalClose.addEventListener('click', function () {
                    imageModal.style.display = 'none';
                });
            }

            // Close modal on outside click
            if (imageModal) {
                imageModal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        imageModal.style.display = 'none';
                    }
                });
            }

            // Close modal on ESC key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && imageModal.style.display === 'flex') {
                    imageModal.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
<?php
// Close database connection
$conn->close();
?>