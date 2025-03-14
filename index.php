<?php
/**
 * Index file untuk website CDK Wilayah Bojonegoro
 * 
 * File ini adalah halaman utama/beranda website
 */

// Define BASE_PATH
define('BASE_PATH', __DIR__ . '/');

// Include config dan fungsi-fungsi
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Include header
include_once 'includes/header.php';

// Ambil pengaturan hero
$hero_title = getSetting('hero_title', 'Cabang Dinas Kehutanan Wilayah Bojonegoro');
$hero_subtitle = getSetting('hero_subtitle', 'Melayani masyarakat dalam pengelolaan dan pelestarian hutan');
$hero_video = getSetting('hero_video', 'assets/videos/forest-bg.mp4');

// Ambil data statistik
$statistics = db_fetch_all("SELECT * FROM statistik WHERE kategori = 'umum' AND tahun = ? AND is_active = 1 ORDER BY id ASC", [date('Y')]);

// Ambil data wilayah kerja
$wilayah_kerja = db_fetch_all("SELECT * FROM wilayah_kerja WHERE is_active = 1 ORDER BY nama_wilayah ASC");

// Ambil tugas dan fungsi
$tugas = db_fetch_all("SELECT * FROM tugas_fungsi WHERE jenis = 'tugas' AND is_active = 1 ORDER BY urutan ASC");
$fungsi = db_fetch_all("SELECT * FROM tugas_fungsi WHERE jenis = 'fungsi' AND is_active = 1 ORDER BY urutan ASC");

// Ambil data struktur organisasi
$struktur_organisasi = db_fetch_all("
    SELECT s1.*, s2.nama as parent_nama 
    FROM struktur_organisasi s1 
    LEFT JOIN struktur_organisasi s2 ON s1.parent_id = s2.id 
    WHERE s1.is_active = 1 
    ORDER BY s1.level ASC, s1.urutan ASC
");

// Ambil data layanan
$layanan = db_fetch_all("SELECT * FROM layanan WHERE is_active = 1 ORDER BY urutan ASC");

// Ambil data program
$program = db_fetch_all("SELECT * FROM program WHERE is_active = 1 ORDER BY urutan ASC");

// Ambil data capaian program
$capaian = db_fetch_all("
    SELECT c.*, p.nama_program, p.icon 
    FROM capaian_program c 
    JOIN program p ON c.program_id = p.id 
    WHERE c.tahun = ? AND c.is_active = 1 
    ORDER BY c.id ASC
", [date('Y')]);

// Ambil data berita/publikasi terbaru
$berita_terbaru = db_fetch_all("
    SELECT p.*, k.nama_kategori, k.slug as kategori_slug, u.name as penulis_nama 
    FROM publikasi p 
    JOIN kategori_publikasi k ON p.kategori_id = k.id 
    JOIN users u ON p.penulis_id = u.id 
    WHERE p.is_active = 1 
    ORDER BY p.tanggal_publikasi DESC, p.id DESC 
    LIMIT 4
");

// Ambil data dokumen terbaru
$dokumen_terbaru = db_fetch_all("
    SELECT * FROM dokumen 
    WHERE is_active = 1 AND is_public = 1 
    ORDER BY tanggal_upload DESC, id DESC 
    LIMIT 4
");

// Ambil data galeri terbaru
$galeri_terbaru = db_fetch_all("
    SELECT g.*, k.nama_kategori, k.slug as kategori_slug 
    FROM galeri g 
    JOIN galeri_kategori k ON g.kategori_id = k.id 
    WHERE g.is_active = 1 
    ORDER BY g.created_at DESC, g.id DESC 
    LIMIT 6
");

// Ambil data kawasan hutan untuk chart
$kawasan_data = db_fetch_all("
    SELECT kh.kategori, SUM(kh.luas) as total_luas 
    FROM kawasan_hutan kh 
    WHERE kh.tahun = ? 
    GROUP BY kh.kategori
", [date('Y')]);

// Ambil data hasil hutan untuk chart
$hasil_hutan_data = db_fetch_all("
    SELECT hh.jenis, SUM(hh.volume) as total_volume, hh.satuan 
    FROM hasil_hutan hh 
    WHERE hh.tahun = ? 
    GROUP BY hh.jenis, hh.satuan
", [date('Y')]);
?>

<!-- Hero Section -->
<section id="beranda" class="hero-section">
  <div id="particles-js"></div>
  <div class="hero-video-bg">
    <video autoplay muted loop playsinline>
      <source src="<?php echo SITE_URL . '/' . $hero_video; ?>" type="video/mp4">
    </video>
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content-wrapper">
    <div class="hero-content">
      <h1 data-aos="fade-up"><?php echo $hero_title; ?></h1>
      <p data-aos="fade-up" data-aos-delay="100">
        <?php echo $hero_subtitle; ?>
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
    <?php foreach ($statistics as $index => $stat): ?>
        <div class="stat-card" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
          <div class="stat-icon">
            <i class="<?php echo $stat['icon']; ?>"></i>
          </div>
          <h3 class="stat-number" data-counter="<?php echo $stat['nilai']; ?>"><?php echo number_format($stat['nilai'], 0, ',', '.'); ?>+</h3>
          <p class="stat-label"><?php echo $stat['judul']; ?><?php echo !empty($stat['satuan']) ? ' ' . $stat['satuan'] : ''; ?></p>
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
              <?php
              $total_wilayah = count($wilayah_kerja);
              $half = ceil($total_wilayah / 2);
              $first_half = array_slice($wilayah_kerja, 0, $half);
              $second_half = array_slice($wilayah_kerja, $half);
              ?>
              
              <div class="col-md-6">
                <?php foreach ($first_half as $wilayah): ?>
                    <div class="wilayah-item">
                      <i class="fas fa-circle-check"></i>
                      <span><?php echo $wilayah['nama_wilayah']; ?></span>
                    </div>
                <?php endforeach; ?>
              </div>
              
              <div class="col-md-6">
                <?php foreach ($second_half as $wilayah): ?>
                    <div class="wilayah-item">
                      <i class="fas fa-circle-check"></i>
                      <span><?php echo $wilayah['nama_wilayah']; ?></span>
                    </div>
                <?php endforeach; ?>
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
            <?php if (!empty($tugas)): ?>
                <p><?php echo $tugas[0]['deskripsi']; ?></p>
            <?php else: ?>
                <p>Membantu Kepala Dinas Kehutanan melaksanakan sebagian urusan pemerintahan yang menjadi kewenangan Provinsi di wilayah kerja.</p>
            <?php endif; ?>

            <h5 class="mt-4">Fungsi Utama</h5>
            <div class="fungsi-list">
              <?php foreach ($fungsi as $item): ?>
                  <div class="fungsi-item">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $item['deskripsi']; ?></span>
                  </div>
              <?php endforeach; ?>
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
              <?php
              // Kepala CDK
              $kepala = array_filter($struktur_organisasi, function ($item) {
                  return $item['level'] == 1;
              });

              // Sub Bagian
              $sub_bagian = array_filter($struktur_organisasi, function ($item) {
                  return $item['level'] == 2;
              });

              // Seksi-seksi
              $seksi = array_filter($struktur_organisasi, function ($item) {
                  return $item['level'] == 3;
              });

              if (!empty($kepala)):
                  $kepala = reset($kepala);
                  ?>
                  <!-- Kepala CDK -->
                  <div class="org-level">
                    <div class="org-item">
                      <div class="org-content primary">
                        <div class="org-box">
                          <div class="org-icon">
                            <i class="fas fa-user-tie mb-2"></i>
                          </div>
                          <h5 class="fw-bold mb-2">
                            <?php echo $kepala['jabatan']; ?>
                          </h5>
                          <p class="mb-0">
                            <?php echo $kepala['nama']; ?>
                          </p>
                        </div>
                        <div class="org-connector"></div>
                      </div>
                    </div>
                  </div>
              <?php endif; ?>

              <?php if (!empty($sub_bagian)):
                  $sub_bagian = reset($sub_bagian);
                  ?>
                  <!-- Sub Bagian -->
                  <div class="org-level">
                    <div class="org-item">
                      <div class="org-content secondary">
                        <div class="org-box">
                          <div class="org-icon">
                            <i class="fas fa-tasks mb-2"></i>
                          </div>
                          <h5 class="fw-bold mb-2"><?php echo $sub_bagian['jabatan']; ?></h5>
                          <p class="mb-0">
                            <?php echo $sub_bagian['nama']; ?>
                          </p>
                        </div>
                        <div class="org-connector"></div>
                      </div>
                    </div>
                  </div>
              <?php endif; ?>

              <?php if (!empty($seksi)): ?>
                  <!-- Seksi-seksi -->
                  <div class="org-level">
                    <div class="org-item-wrapper">
                      <?php foreach ($seksi as $item): ?>
                          <div class="org-item">
                            <div class="org-content tertiary">
                              <div class="org-box">
                                <div class="org-icon">
                                  <i class="fas fa-seedling mb-2"></i>
                                </div>
                                <h5 class="fw-bold mb-2">
                                  <?php echo $item['jabatan']; ?>
                                </h5>
                                <p class="mb-0">
                                  <?php echo $item['nama']; ?>
                                </p>
                              </div>
                            </div>
                          </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
              <?php endif; ?>
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
      <?php
      foreach ($layanan as $item):
          // Ambil detail layanan
          $detail_layanan = db_fetch_all("SELECT * FROM layanan_detail WHERE layanan_id = ? AND is_active = 1 ORDER BY urutan ASC", [$item['id']]);
          ?>
          <div class="col-lg-4" data-aos="fade-up">
            <div class="service-card glass-card">
              <div class="service-icon-wrapper">
                <i class="<?php echo $item['icon']; ?> service-icon"></i>
              </div>
              <div class="service-content">
                <h4><?php echo $item['nama_layanan']; ?></h4>
                <ul class="service-list">
                  <?php foreach ($detail_layanan as $detail): ?>
                      <li>
                        <i class="fas fa-check-circle"></i> <?php echo $detail['item_deskripsi']; ?>
                      </li>
                  <?php endforeach; ?>
                </ul>
                <div class="service-action">
                  <a href="<?php echo !empty($item['link_form']) ? $item['link_form'] : '#'; ?>" class="btn btn-success mt-3">Ajukan Permohonan</a>
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
      <p class="section-subheading">Program dan kegiatan teknis bidang kehutanan sesuai Pergub No 48 Tahun 2018</p>
    </div>
    
    <div class="program-grid">
      <?php
      foreach ($program as $index => $item):
          // Ambil detail program
          $detail_program = db_fetch_all("SELECT * FROM program_detail WHERE program_id = ? AND is_active = 1 ORDER BY urutan ASC", [$item['id']]);
          ?>
          <div class="program-card" data-aos="fade-up<div class="program-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
            <div class="program-header">
              <div class="program-icon">
                <i class="<?php echo $item['icon']; ?>"></i>
              </div>
              <div class="program-title">
                <h4><?php echo $item['nama_program']; ?></h4>
                <p><?php echo $item['deskripsi']; ?></p>
              </div>
            </div>
            <div class="program-content">
              <ul class="program-list">
                <?php foreach ($detail_program as $detail): ?>
                    <li>
                      <i class="ri-check-line"></i>
                      <span><?php echo $detail['item_deskripsi']; ?></span>
                    </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Setelah section program dan sebelum section monitoring -->
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
          <select class="year-select" id="yearSelectForest">
            <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
            <option value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
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
          <select class="year-select" id="yearSelectProduction">
            <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
            <option value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
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
              <?php
              $detail_stats = db_fetch_all("SELECT * FROM statistik WHERE kategori = 'detail' AND tahun = ? AND is_active = 1 ORDER BY id ASC LIMIT 4", [date('Y')]);
              foreach ($detail_stats as $stat):
                  ?>
                  <div class="stat-item">
                    <div class="stat-icon">
                      <i class="<?php echo $stat['icon']; ?>"></i>
                    </div>
                    <div class="stat-info">
                      <span class="stat-label"><?php echo $stat['judul']; ?></span>
                      <span class="stat-value"><?php echo number_format($stat['nilai'], 0, ',', '.'); ?>     <?php echo $stat['satuan']; ?></span>
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
            <?php foreach ($capaian as $item):
                $percentage = ($item['realisasi'] / $item['target']) * 100;
                ?>
                <div class="progress-item">
                  <div class="progress-header">
                    <div class="progress-title">
                      <i class="<?php echo $item['icon']; ?> progress-icon"></i>
                      <span><?php echo $item['nama_program']; ?></span>
                    </div>
                    <span class="progress-percentage"><?php echo number_format($percentage, 0); ?>%</span>
                  </div>
                  <div class="progress">
                    <div class="progress-bar" style="width: <?php echo number_format($percentage, 0); ?>%">
                      <div class="progress-glow"></div>
                    </div>
                  </div>
                </div>
            <?php endforeach; ?>
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
          <?php foreach ($berita_terbaru as $berita): ?>
              <div class="col-md-6">
                <div class="news-card animate-hover">
                  <img src="<?php echo UPLOADS_URL; ?>/berita/<?php echo $berita['gambar']; ?>" alt="<?php echo $berita['judul']; ?>" class="news-image">
                  <div class="news-content">
                    <span class="news-tag"><?php echo $berita['nama_kategori']; ?></span>
                    <h5><?php echo $berita['judul']; ?></h5>
                    <p><?php echo trimText($berita['ringkasan'], 120); ?></p>
                    <div class="news-meta">
                      <span><i class="ri-calendar-line"></i> <?php echo formatTanggal($berita['tanggal_publikasi']); ?></span>
                      <a href="<?php echo SITE_URL; ?>/modules/publikasi/detail.php?slug=<?php echo $berita['slug']; ?>" class="read-more">Baca Selengkapnya <i class="ri-arrow-right-line"></i></a>
                    </div>
                  </div>
                </div>
              </div>
          <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
          <a href="<?php echo SITE_URL; ?>/modules/publikasi/index.php" class="btn btn-outline-success">
            Lihat Semua Berita <i class="ri-arrow-right-line"></i>
          </a>
        </div>
      </div>

      <!-- Sidebar Dokumen -->
      <div class="col-lg-4" data-aos="fade-up">
        <div class="sidebar-box">
          <h4 class="sidebar-title">Dokumen Penting</h4>
          <div class="doc-list">
            <?php foreach ($dokumen_terbaru as $dokumen): ?>
                <a href="<?php echo SITE_URL; ?>/modules/dokumen/download.php?id=<?php echo $dokumen['id']; ?>" class="doc-item">
                  <i class="<?php echo getFileIcon($dokumen['file_path']); ?> doc-icon"></i>
                  <div class="doc-info">
                    <h6><?php echo $dokumen['judul']; ?></h6>
                    <span class="doc-meta"><?php echo formatTanggal($dokumen['tanggal_upload']); ?> • <?php echo strtoupper($dokumen['file_type']); ?> • <?php echo formatFileSize($dokumen['file_size']); ?></span>
                  </div>
                </a>
            <?php endforeach; ?>
          </div>
          <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/modules/dokumen/index.php" class="btn btn-outline-success btn-sm">
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
      <button class="btn btn-outline-success active" data-filter="all">Semua</button>
      <?php
      $galeri_kategori = db_fetch_all("SELECT * FROM galeri_kategori WHERE is_active = 1");
      foreach ($galeri_kategori as $kategori):
          ?>
          <button class="btn btn-outline-success" data-filter="<?php echo $kategori['slug']; ?>"><?php echo $kategori['nama_kategori']; ?></button>
      <?php endforeach; ?>
    </div>

    <!-- Gallery Items -->
    <div class="row g-4 gallery-container">
      <?php foreach ($galeri_terbaru as $galeri): ?>
          <div class="col-lg-4 col-md-6 gallery-item <?php echo $galeri['kategori_slug']; ?>" data-aos="fade-up">
            <div class="gallery-card">
              <div class="gallery-image">
                <img src="<?php echo UPLOADS_URL; ?>/galeri/<?php echo $galeri['gambar']; ?>" alt="<?php echo $galeri['judul']; ?>">
                <div class="gallery-overlay">
                  <a href="<?php echo UPLOADS_URL; ?>/galeri/<?php echo $galeri['gambar']; ?>" class="gallery-popup">
                    <i class="ri-zoom-in-line"></i>
                  </a>
                </div>
              </div>
              <div class="gallery-content">
                <h5><?php echo $galeri['judul']; ?></h5>
                <p><?php echo trimText($galeri['deskripsi'], 50); ?></p>
                <div class="gallery-meta">
                  <span><i class="ri-calendar-line"></i> <?php echo formatTanggal($galeri['tanggal_kegiatan']); ?></span>
                </div>
              </div>
            </div>
          </div>
      <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-4">
      <a href="<?php echo SITE_URL; ?>/modules/galeri/index.php" class="btn btn-outline-success">
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
              <p><?php echo getSetting('contact_address', 'Jl. Hayam Wuruk No. 9, Bojonegoro, Jawa Timur'); ?></p>
            </div>
          </div>
          <div class="contact-info-item">
            <i class="fas fa-phone contact-icon"></i>
            <div>
              <h5>Telepon</h5>
              <p><?php echo getSetting('contact_phone', '(0353) 123456'); ?></p>
              <p>Hotline: <?php echo getSetting('contact_hotline', '0800-1234-5678'); ?></p>
            </div>
          </div>
          <div class="contact-info-item">
            <i class="fas fa-envelope contact-icon"></i>
            <div>
              <h5>Email</h5>
              <p><?php echo getSetting('contact_email', 'info@cdk-bojonegoro.jatimprov.go.id'); ?></p>
            </div>
          </div>
          <div class="contact-info-item">
            <i class="fas fa-clock contact-icon"></i>
            <div>
              <h5>Jam Pelayanan</h5>
              <p><?php echo getSetting('office_hours', 'Senin - Jumat: 08:00 - 16:00 WIB'); ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Kontak -->
      <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="contact-form-container">
          <form class="contact-form" action="<?php echo SITE_URL; ?>/modules/kontak/submit.php" method="post">
            <?php echo csrfField(); ?>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="nama">Nama Lengkap</label>
                  <input
                    type="text"
                    class="form-control"
                    id="nama"
                    name="nama"
                    required
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    required
                  />
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
                  <textarea
                    class="form-control"
                    id="pesan"
                    name="pesan"
                    rows="5"
                    required
                  ></textarea>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-success">
                  Kirim Pesan
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
// Chart untuk kawasan hutan
document.addEventListener('DOMContentLoaded', function() {
  const forestCtx = document.getElementById('forestAreaChart').getContext('2d');
  const forestData = <?php echo json_encode($kawasan_data); ?>;
  
  const forestLabels = forestData.map(item => item.kategori);
  const forestValues = forestData.map(item => item.total_luas);
  
  const forestChart = new Chart(forestCtx, {
    type: 'doughnut',
    data: {
      labels: forestLabels,
      datasets: [{
        data: forestValues,
        backgroundColor: [
          'rgba(45, 106, 79, 0.8)',
          'rgba(76, 175, 80, 0.8)',
          'rgba(139, 195, 74, 0.8)',
          'rgba(205, 220, 57, 0.8)'
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
        },
        tooltip: {
          callbacks: {
            label: function(tooltipItem) {
              return tooltipItem.label + ': ' + Number(tooltipItem.raw).toLocaleString() + ' Ha';
            }
          }
        }
      }
    }
  });
  
  // Chart untuk hasil hutan
  const productionCtx = document.getElementById('forestProductionChart').getContext('2d');
  const productionData = <?php echo json_encode($hasil_hutan_data); ?>;
  
  const productionLabels = productionData.map(item => item.jenis);
  const productionValues = productionData.map(item => item.total_volume);
  const productionUnits = productionData.map(item => item.satuan);
  
  const productionChart = new Chart(productionCtx, {
    type: 'bar',
    data: {
      labels: productionLabels,
      datasets: [{
        label: 'Volume',
        data: productionValues,
        backgroundColor: 'rgba(45, 106, 79, 0.7)',
        borderColor: 'rgba(45, 106, 79, 1)',
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
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(tooltipItem) {
              const dataIndex = tooltipItem.dataIndex;
              return 'Volume: ' + Number(tooltipItem.raw).toLocaleString() + ' ' + productionUnits[dataIndex];
            }
          }
        }
      }
    }
  });
  
  // Year selectors event handlers
  document.getElementById('yearSelectForest').addEventListener('change', function() {
    // Tambahkan AJAX request untuk mendapatkan data berdasarkan tahun yang dipilih
    // dan update chart forestChart
  });
  
  document.getElementById('yearSelectProduction').addEventListener('change', function() {
    // Tambahkan AJAX request untuk mendapatkan data berdasarkan tahun yang dipilih
    // dan update chart productionChart
  });
});

// Galeri filter
document.addEventListener('DOMContentLoaded', function() {
  const filterButtons = document.querySelectorAll('.gallery-filter button');
  const galleryItems = document.querySelectorAll('.gallery-item');
  
  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Remove active class from all buttons
      filterButtons.forEach(btn => btn.classList.remove('active'));
      
      // Add active class to clicked button
      this.classList.add('active');
      
      const filter = this.getAttribute('data-filter');
      
      // Show/hide gallery items based on filter
      galleryItems.forEach(item => {
        if (filter === 'all' || item.classList.contains(filter)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });
  
  // Gallery popup
  const galleryPopupLinks = document.querySelectorAll('.gallery-popup');
  const imageModal = document.getElementById('imageModal');
  const modalImage = document.getElementById('modalImage');
  const modalClose = document.querySelector('.modal-close');
  
  galleryPopupLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      modalImage.src = this.getAttribute('href');
      imageModal.style.display = 'flex';
    });
  });
  
  modalClose.addEventListener('click', function() {
    imageModal.style.display = 'none';
  });
  
  window.addEventListener('click', function(e) {
    if (e.target === imageModal) {
      imageModal.style.display = 'none';
    }
  });
});
</script>

<?php
// Include footer
include_once 'includes/footer.php';
?>