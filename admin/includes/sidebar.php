<?php
/**
 * Admin Sidebar Navigation
 * CDK Wilayah Bojonegoro
 */

// Determine current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar-menu">
    <!-- Dashboard -->
    <div class="sidebar-heading">
        Dashboard
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">
                <i class="ri-dashboard-line nav-icon"></i>
                <span>Dashboard</span>
            </a>
        </li>
    </ul>

    <!-- Konten Website -->
    <div class="sidebar-heading">
        Konten Website
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'layanan.php' || $current_page === 'layanan-form.php') ? 'active' : ''; ?>"
                href="layanan.php">
                <i class="ri-customer-service-line nav-icon"></i>
                <span>Layanan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'program.php' || $current_page === 'program-form.php') ? 'active' : ''; ?>"
                href="program.php">
                <i class="ri-file-list-3-line nav-icon"></i>
                <span>Program & Kegiatan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'publikasi.php' || $current_page === 'publikasi-form.php') ? 'active' : ''; ?>"
                href="publikasi.php">
                <i class="ri-newspaper-line nav-icon"></i>
                <span>Publikasi & Berita</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'galeri.php' || $current_page === 'galeri-form.php') ? 'active' : ''; ?>"
                href="galeri.php">
                <i class="ri-image-2-line nav-icon"></i>
                <span>Galeri</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'dokumen.php' || $current_page === 'dokumen-form.php') ? 'active' : ''; ?>"
                href="dokumen.php">
                <i class="ri-file-pdf-line nav-icon"></i>
                <span>Dokumen</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'statistik.php' || $current_page === 'statistik-form.php') ? 'active' : ''; ?>"
                href="statistik.php">
                <i class="ri-bar-chart-2-line nav-icon"></i>
                <span>Statistik</span>
            </a>
        </li>
    </ul>

    <!-- Pesan Kontak -->
    <div class="sidebar-heading">
        Pesan
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'pesan.php' ? 'active' : ''; ?>" href="pesan.php">
                <i class="ri-message-3-line nav-icon"></i>
                <span>Pesan Kontak</span>
                <?php
                // Get count of unread messages
                $conn = getConnection();
                $query = "SELECT COUNT(*) as count FROM pesan_kontak WHERE status = 'belum_dibaca'";
                $result = $conn->query($query);
                if ($result && $row = $result->fetch_assoc()) {
                    $count = $row['count'];
                    if ($count > 0) {
                        echo '<span class="badge bg-danger ms-2">' . $count . '</span>';
                    }
                }
                ?>
            </a>
        </li>
    </ul>

    <!-- Pengaturan -->
    <div class="sidebar-heading">
        Pengaturan
    </div>
    <ul class="nav flex-column">
        <?php if (isAdmin()): // Only admin can see user management ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'users.php' || $current_page === 'user-form.php') ? 'active' : ''; ?>"
                    href="users.php">
                    <i class="ri-user-settings-line nav-icon"></i>
                    <span>Pengguna</span>
                </a>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'pengaturan.php' ? 'active' : ''; ?>" href="pengaturan.php">
                <i class="ri-settings-3-line nav-icon"></i>
                <span>Pengaturan Website</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                <i class="ri-user-line nav-icon"></i>
                <span>Profil Saya</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">
                <i class="ri-logout-box-line nav-icon"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>

    <!-- Informasi Versi -->
    <div class="sidebar-footer">
        <p>Ver. 1.0.0 &copy; <?php echo date('Y'); ?><br>
            <small>CDK Wilayah Bojonegoro</small>
        </p>
    </div>

    <style>
        .sidebar-footer {
            padding: 1rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 1rem;
        }

        .badge {
            font-size: 0.7rem;
            font-weight: normal;
            padding: 0.25em 0.5em;
        }
    </style>
</div>