<?php
/**
 * Login Admin untuk website CDK Wilayah Bojonegoro
 * 
 * File ini menangani proses login ke halaman admin
 */

// Mulai session
session_start();

// Define BASE_PATH
define('BASE_PATH', dirname(__DIR__) . '/');

// Include config dan fungsi-fungsi
require_once BASE_PATH . 'includes/config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/functions.php';

// Jika user sudah login, redirect ke dashboard
if (isLoggedIn()) {
    redirect(ADMIN_URL . '/index.php');
}

// Inisialisasi variabel
$error = '';
$username = '';

// Cek apakah ada form yang disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi token CSRF
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $error = 'Token CSRF tidak valid. Silakan coba lagi.';
    } else {
        // Ambil data dari form
        $username = cleanInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validasi input
        if (empty($username) || empty($password)) {
            $error = 'Username dan password wajib diisi.';
        } else {
            // Cek user di database
            $user = db_fetch("SELECT * FROM users WHERE username = ?", [$username]);

            if ($user && password_verify($password, $user['password'])) {
                // Update last login
                db_query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);

                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Log aktivitas
                logActivity('Login', 'users', $user['id']);

                // Redirect ke dashboard
                redirect(ADMIN_URL . '/index.php');
            } else {
                $error = 'Username atau password salah.';

                // Log aktivitas login gagal untuk keamanan
                logActivity('Login gagal', 'users', null, null, json_encode(['username' => $username]));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - CDK Wilayah Bojonegoro</title>

    <!-- Favicon -->
    <link rel="shortcut icon"
        href="<?php echo SITE_URL; ?>/<?php echo getSetting('site_favicon', 'assets/images/favicon.ico'); ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom styles -->
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #1b4332;
            --accent-color: #52b788;
            --text-color: #333;
            --light-color: #fff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: var(--text-color);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
        }

        .login-card {
            background-color: var(--light-color);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--light-color);
            padding: 1.5rem;
            text-align: center;
        }

        .login-header img {
            max-width: 80px;
            margin-bottom: 1rem;
        }

        .login-form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1rem;
            font-weight: 600;
            width: 100%;
        }

        .btn-success:hover,
        .btn-success:focus {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: var(--secondary-color);
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="<?php echo SITE_URL; ?>/<?php echo getSetting('site_logo', 'assets/images/logo.png'); ?>"
                    alt="Logo CDK Bojonegoro" class="img-fluid">
                <h4>Admin Dashboard</h4>
                <p class="mb-0">CDK Wilayah Bojonegoro</p>
            </div>

            <div class="login-form">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <?php echo csrfField(); ?>

                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?php echo htmlspecialchars($username); ?>" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <a href="<?php echo SITE_URL; ?>" class="back-link">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Website
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom script -->
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>