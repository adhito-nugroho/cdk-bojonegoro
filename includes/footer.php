<?php
/**
 * Footer untuk website CDK Wilayah Bojonegoro
 */

// Pastikan file config sudah diinclude
if (!defined('BASE_PATH')) {
    require_once 'config.php';
}

// Include functions jika belum
if (!function_exists('getMenuByPosition')) {
    require_once 'functions.php';
}

// Ambil pengaturan website
$contact_email = getSetting('contact_email', 'info@cdk-bojonegoro.jatimprov.go.id');
$contact_phone = getSetting('contact_phone', '(0353) 123456');
$contact_address = getSetting('contact_address', 'Jl. Hayam Wuruk No. 9, Bojonegoro, Jawa Timur');
$office_hours = getSetting('office_hours', 'Senin - Jumat: 08:00 - 16:00 WIB');

// Ambil social media
$facebook_url = getSetting('facebook_url', '#');
$twitter_url = getSetting('twitter_url', '#');
$instagram_url = getSetting('instagram_url', '#');
$youtube_url = getSetting('youtube_url', '#');

// Ambil menu footer
$footer_menu = getMenuByPosition('footer_menu');
$quick_links = getMenuByPosition('quick_links');
?>

</div>
<!-- Main Content End -->

<!-- Footer -->
<footer class="footer bg-dark text-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-info">
                    <img src="<?php echo ASSETS_URL; ?>/images/logo-white.png" alt="Logo" height="60" class="mb-3" />
                    <p>Cabang Dinas Kehutanan Wilayah Bojonegoro</p>
                    <div class="social-links mt-3">
                        <a href="<?php echo $facebook_url; ?>" class="social-icon" target="_blank"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo $twitter_url; ?>" class="social-icon" target="_blank"><i
                                class="fab fa-twitter"></i></a>
                        <a href="<?php echo $instagram_url; ?>" class="social-icon" target="_blank"><i
                                class="fab fa-instagram"></i></a>
                        <a href="<?php echo $youtube_url; ?>" class="social-icon" target="_blank"><i
                                class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <h5>Link Cepat</h5>
                <ul class="footer-links">
                    <?php foreach ($footer_menu as $menu): ?>
                        <li><a href="<?php echo $menu['url']; ?>"><?php echo $menu['title']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-3">
                <h5>Layanan Utama</h5>
                <ul class="footer-links">
                    <?php foreach ($quick_links as $link): ?>
                        <li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-3">
                <h5>Informasi Kontak</h5>
                <ul class="footer-contact-list">
                    <li>
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <?php echo $contact_address; ?>
                    </li>
                    <li>
                        <i class="fas fa-phone me-2"></i>
                        <?php echo $contact_phone; ?>
                    </li>
                    <li>
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:<?php echo $contact_email; ?>"
                            class="text-white"><?php echo $contact_email; ?></a>
                    </li>
                    <li>
                        <i class="fas fa-clock me-2"></i>
                        <?php echo $office_hours; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom mt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> CDK Wilayah Bojonegoro. Hak Cipta Dilindungi.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="<?php echo SITE_URL; ?>/pages/privacy-policy.php" class="text-white me-3">Kebijakan
                        Privasi</a>
                    <a href="<?php echo SITE_URL; ?>/pages/terms.php" class="text-white">Syarat & Ketentuan</a>
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
<script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script src="https://unpkg.com/scrollreveal"></script>

<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Initialize Particles
    if (document.getElementById('particles-js')) {
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
    }
</script>
</body>

</html>