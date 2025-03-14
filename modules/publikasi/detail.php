<?php
/**
 * Detail Publikasi untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menampilkan detail publikasi/berita
 */

// Define BASE_PATH
define('BASE_PATH', dirname(dirname(__DIR__)) . '/');

// Include config dan fungsi-fungsi
require_once BASE_PATH . 'includes/config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/functions.php';

// Periksa parameter slug
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    redirect(SITE_URL . '/modules/publikasi/index.php');
}

$slug = cleanInput($_GET['slug']);

// Ambil data publikasi berdasarkan slug
$publikasi = db_fetch("
    SELECT p.*, k.nama_kategori, k.slug as kategori_slug, u.name as penulis_nama 
    FROM publikasi p 
    JOIN kategori_publikasi k ON p.kategori_id = k.id 
    JOIN users u ON p.penulis_id = u.id 
    WHERE p.slug = ? AND p.is_active = 1
", [$slug]);

// Jika publikasi tidak ditemukan, redirect ke halaman daftar publikasi
if (!$publikasi) {
    redirect(SITE_URL . '/modules/publikasi/index.php');
}

// Update view count
db_query("UPDATE publikasi SET view_count = view_count + 1 WHERE id = ?", [$publikasi['id']]);

// Ambil tags publikasi jika ada
$tags = db_fetch_all("
    SELECT t.* 
    FROM tags t 
    JOIN publikasi_tags pt ON t.id = pt.tag_id 
    WHERE pt.publikasi_id = ?
", [$publikasi['id']]);

// Ambil publikasi terkait (berdasarkan kategori yang sama)
$related_posts = db_fetch_all("
    SELECT p.id, p.judul, p.slug, p.gambar, p.tanggal_publikasi 
    FROM publikasi p 
    WHERE p.kategori_id = ? AND p.id != ? AND p.is_active = 1 
    ORDER BY p.tanggal_publikasi DESC 
    LIMIT 3
", [$publikasi['kategori_id'], $publikasi['id']]);

// Meta tags untuk SEO
$meta_title = $publikasi['judul'] . ' - CDK Wilayah Bojonegoro';
$meta_description = $publikasi['ringkasan'] ?? trimText($publikasi['isi'], 160, '');
$meta_image = UPLOADS_URL . '/berita/' . $publikasi['gambar'];

// Include header
$page_title = $meta_title;
include_once BASE_PATH . 'includes/header.php';
?>

<!-- Detail Publikasi Section -->
<section class="publikasi-detail-section">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Beranda</a></li>
                        <li class="breadcrumb-item"><a
                                href="<?php echo SITE_URL; ?>/modules/publikasi/index.php">Publikasi</a></li>
                        <li class="breadcrumb-item"><a
                                href="<?php echo SITE_URL; ?>/modules/publikasi/kategori.php?slug=<?php echo $publikasi['kategori_slug']; ?>"><?php echo $publikasi['nama_kategori']; ?></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $publikasi['judul']; ?></li>
                    </ol>
                </nav>

                <!-- Artikel Detail -->
                <article class="publikasi-detail">
                    <header class="publikasi-header">
                        <h1 class="publikasi-title"><?php echo $publikasi['judul']; ?></h1>
                        <div class="publikasi-meta">
                            <span class="publikasi-date"><i class="ri-calendar-line"></i>
                                <?php echo formatTanggal($publikasi['tanggal_publikasi']); ?></span>
                            <span class="publikasi-author"><i class="ri-user-line"></i>
                                <?php echo $publikasi['penulis_nama']; ?></span>
                            <span class="publikasi-category"><i class="ri-price-tag-3-line"></i>
                                <?php echo $publikasi['nama_kategori']; ?></span>
                            <span class="publikasi-views"><i class="ri-eye-line"></i>
                                <?php echo number_format($publikasi['view_count'] + 1, 0, ',', '.'); ?> kali
                                dibaca</span>
                        </div>
                    </header>

                    <?php if (!empty($publikasi['gambar'])): ?>
                        <div class="publikasi-featured-image">
                            <img src="<?php echo UPLOADS_URL . '/berita/' . $publikasi['gambar']; ?>"
                                alt="<?php echo $publikasi['judul']; ?>" class="img-fluid rounded">
                        </div>
                    <?php endif; ?>

                    <div class="publikasi-content mt-4">
                        <?php echo $publikasi['isi']; ?>
                    </div>

                    <?php if (!empty($tags)): ?>
                        <div class="publikasi-tags mt-4">
                            <h5>Tags:</h5>
                            <div class="tags-list">
                                <?php foreach ($tags as $tag): ?>
                                    <a href="<?php echo SITE_URL; ?>/modules/publikasi/tag.php?slug=<?php echo $tag['slug']; ?>"
                                        class="tag-item"><?php echo $tag['nama_tag']; ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Social Share -->
                    <div class="publikasi-share mt-4">
                        <h5>Bagikan:</h5>
                        <div class="share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(getCurrentUrl()); ?>"
                                target="_blank" class="share-btn facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(getCurrentUrl()); ?>&text=<?php echo urlencode($publikasi['judul']); ?>"
                                target="_blank" class="share-btn twitter">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($publikasi['judul'] . ' ' . getCurrentUrl()); ?>"
                                target="_blank" class="share-btn whatsapp">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </article>

                <?php if (!empty($related_posts)): ?>
                    <!-- Artikel Terkait -->
                    <div class="related-posts mt-5">
                        <h3>Publikasi Terkait</h3>
                        <div class="row g-4 mt-2">
                            <?php foreach ($related_posts as $related): ?>
                                <div class="col-md-4">
                                    <div class="related-post-card">
                                        <a href="<?php echo SITE_URL; ?>/modules/publikasi/detail.php?slug=<?php echo $related['slug']; ?>"
                                            class="related-post-image">
                                            <img src="<?php echo UPLOADS_URL . '/berita/' . $related['gambar']; ?>"
                                                alt="<?php echo $related['judul']; ?>" class="img-fluid">
                                        </a>
                                        <div class="related-post-content">
                                            <a
                                                href="<?php echo SITE_URL; ?>/modules/publikasi/detail.php?slug=<?php echo $related['slug']; ?>">
                                                <h5><?php echo $related['judul']; ?></h5>
                                            </a>
                                            <span class="related-post-date"><i class="ri-calendar-line"></i>
                                                <?php echo formatTanggal($related['tanggal_publikasi']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <!-- Sidebar -->
                <div class="publikasi-sidebar">
                    <!-- Pencarian -->
                    <div class="sidebar-widget">
                        <h4 class="widget-title">Pencarian</h4>
                        <div class="search-widget">
                            <form action="<?php echo SITE_URL; ?>/modules/publikasi/index.php" method="get">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Cari publikasi..."
                                        name="search">
                                    <button class="btn btn-success" type="submit">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <?php
                    $categories = db_fetch_all("
                        SELECT k.*, COUNT(p.id) as total_publikasi 
                        FROM kategori_publikasi k 
                        LEFT JOIN publikasi p ON k.id = p.kategori_id AND p.is_active = 1
                        WHERE k.is_active = 1 
                        GROUP BY k.id 
                        ORDER BY k.nama_kategori ASC
                    ");
                    ?>
                    <div class="sidebar-widget">
                        <h4 class="widget-title">Kategori</h4>
                        <ul class="category-list">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a
                                        href="<?php echo SITE_URL; ?>/modules/publikasi/kategori.php?slug=<?php echo $category['slug']; ?>">
                                        <?php echo $category['nama_kategori']; ?>
                                        <span
                                            class="badge bg-success rounded-pill"><?php echo $category['total_publikasi']; ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Publikasi Terbaru -->
                    <?php
                    $latest_posts = db_fetch_all("
                        SELECT p.judul, p.slug, p.gambar, p.tanggal_publikasi 
                        FROM publikasi p 
                        WHERE p.is_active = 1 
                        ORDER BY p.tanggal_publikasi DESC, p.id DESC 
                        LIMIT 5
                    ");
                    ?>
                    <div class="sidebar-widget">
                        <h4 class="widget-title">Publikasi Terbaru</h4>
                        <div class="latest-posts">
                            <?php foreach ($latest_posts as $post): ?>
                                <div class="latest-post-item">
                                    <div class="latest-post-image">
                                        <a
                                            href="<?php echo SITE_URL; ?>/modules/publikasi/detail.php?slug=<?php echo $post['slug']; ?>">
                                            <img src="<?php echo UPLOADS_URL . '/berita/' . $post['gambar']; ?>"
                                                alt="<?php echo $post['judul']; ?>" class="img-fluid">
                                        </a>
                                    </div>
                                    <div class="latest-post-content">
                                        <a
                                            href="<?php echo SITE_URL; ?>/modules/publikasi/detail.php?slug=<?php echo $post['slug']; ?>">
                                            <h6><?php echo $post['judul']; ?></h6>
                                        </a>
                                        <span class="latest-post-date"><i class="ri-calendar-line"></i>
                                            <?php echo formatTanggal($post['tanggal_publikasi']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Tags -->
                    <?php
                    $all_tags = db_fetch_all("
                        SELECT t.*, COUNT(pt.publikasi_id) as total_publikasi 
                        FROM tags t 
                        JOIN publikasi_tags pt ON t.id = pt.tag_id 
                        JOIN publikasi p ON pt.publikasi_id = p.id AND p.is_active = 1
                        GROUP BY t.id 
                        ORDER BY total_publikasi DESC 
                        LIMIT 15
                    ");
                    ?>
                    <div class="sidebar-widget">
                        <h4 class="widget-title">Tags</h4>
                        <div class="tags-cloud">
                            <?php foreach ($all_tags as $tag): ?>
                                <a href="<?php echo SITE_URL; ?>/modules/publikasi/tag.php?slug=<?php echo $tag['slug']; ?>"
                                    class="tag-item">
                                    <?php echo $tag['nama_tag']; ?> (<?php echo $tag['total_publikasi']; ?>)
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include_once BASE_PATH . 'includes/footer.php';
?>