<?php
/**
 * Authentication Functions
 * CDK Wilayah Bojonegoro
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration
require_once 'config.php';

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect if not logged in
 * 
 * @param string $redirect Path to redirect to
 * @return void
 */
function requireLogin($redirect = 'login.php')
{
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Check if user has admin role
 * 
 * @return bool True if admin, false otherwise
 */
function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Restrict access to admins only
 * 
 * @param string $redirect Path to redirect to
 * @return void
 */
function requireAdmin($redirect = 'index.php')
{
    if (!isAdmin()) {
        // Set error message
        $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman tersebut.';

        // Redirect to dashboard
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Authenticate user
 * 
 * @param string $username User's username
 * @param string $password User's password (plain text)
 * @return array|bool User data array or false on failure
 */
function loginUser($username, $password)
{
    $conn = getConnection();

    // Sanitize input
    $username = $conn->real_escape_string($username);

    // Prepare query
    $query = "SELECT id, username, password, nama_lengkap, role FROM users WHERE username = '$username' LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            return $user;
        }
    }

    return false;
}

/**
 * Log out current user
 * 
 * @return void
 */
function logoutUser()
{
    // Unset all session variables
    $_SESSION = array();

    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destroy session
    session_destroy();
}

/**
 * Generates a secure hash for a password
 * 
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Check if a token is valid for password reset
 * 
 * @param string $token Reset token
 * @param string $email User email
 * @return bool True if valid, false otherwise
 */
function isValidResetToken($token, $email)
{
    $conn = getConnection();

    // Sanitize inputs
    $token = $conn->real_escape_string($token);
    $email = $conn->real_escape_string($email);

    // Check token
    $query = "SELECT * FROM password_resets WHERE token = '$token' AND email = '$email' AND expires_at > NOW() LIMIT 1";
    $result = $conn->query($query);

    return ($result && $result->num_rows === 1);
}

/**
 * Create password reset token
 * 
 * @param string $email User email
 * @return string|bool Reset token or false on failure
 */
function createResetToken($email)
{
    $conn = getConnection();

    // Sanitize input
    $email = $conn->real_escape_string($email);

    // Check if user exists
    $query = "SELECT id FROM users WHERE email = '$email' LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows === 1) {
        // Create token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Create password_resets table if it doesn't exist
        $createTable = "CREATE TABLE IF NOT EXISTS `password_resets` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `email` varchar(100) NOT NULL,
            `token` varchar(100) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `expires_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `email` (`email`),
            KEY `token` (`token`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $conn->query($createTable);

        // Delete any existing tokens for this user
        $deleteQuery = "DELETE FROM password_resets WHERE email = '$email'";
        $conn->query($deleteQuery);

        // Insert new token
        $insertQuery = "INSERT INTO password_resets (email, token, expires_at) VALUES ('$email', '$token', '$expires')";
        if ($conn->query($insertQuery)) {
            return $token;
        }
    }

    return false;
}

/**
 * Reset user password
 * 
 * @param string $token Reset token
 * @param string $email User email
 * @param string $password New password
 * @return bool True on success, false on failure
 */
function resetPassword($token, $email, $password)
{
    if (!isValidResetToken($token, $email)) {
        return false;
    }

    $conn = getConnection();

    // Sanitize input
    $email = $conn->real_escape_string($email);

    // Hash new password
    $hashedPassword = hashPassword($password);

    // Update password
    $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE email = '$email'";
    $result = $conn->query($updateQuery);

    if ($result) {
        // Delete used token
        $deleteQuery = "DELETE FROM password_resets WHERE email = '$email'";
        $conn->query($deleteQuery);

        return true;
    }

    return false;
}

/**
 * Check if current user has permission for a specific action
 * 
 * @param string $permission Permission to check
 * @return bool True if has permission, false otherwise
 */
function hasPermission($permission)
{
    // Admin has all permissions
    if (isAdmin()) {
        return true;
    }

    // Define permission map for staff role
    $staffPermissions = [
        'view_dashboard' => true,
        'view_publikasi' => true,
        'add_publikasi' => true,
        'edit_publikasi' => true,
        'view_galeri' => true,
        'add_galeri' => true,
        'edit_galeri' => true,
        'view_dokumen' => true,
        'add_dokumen' => true,
        'edit_dokumen' => true,
        'view_statistik' => true,
        'add_statistik' => true,
        'edit_statistik' => true
    ];

    // Check if staff has this permission
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
        return isset($staffPermissions[$permission]) && $staffPermissions[$permission];
    }

    return false;
}

/**
 * Record user activity log
 * 
 * @param string $action Description of the action
 * @param string $module Name of the module
 * @param int $item_id ID of the affected item (optional)
 * @return bool True on success, false on failure
 */
function logActivity($action, $module, $item_id = null)
{
    if (!isLoggedIn()) {
        return false;
    }

    $conn = getConnection();

    // Create activity_logs table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS `activity_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `action` varchar(255) NOT NULL,
        `module` varchar(50) NOT NULL,
        `item_id` int(11) DEFAULT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $conn->query($createTable);

    // Sanitize inputs
    $user_id = (int) $_SESSION['user_id'];
    $action = $conn->real_escape_string($action);
    $module = $conn->real_escape_string($module);
    $item_id = $item_id !== null ? (int) $item_id : null;
    $ip_address = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');
    $user_agent = $conn->real_escape_string($_SERVER['HTTP_USER_AGENT'] ?? '');

    // Insert log
    $query = "INSERT INTO activity_logs (user_id, action, module, item_id, ip_address, user_agent) 
              VALUES ($user_id, '$action', '$module', " . ($item_id === null ? "NULL" : $item_id) . ", '$ip_address', '$user_agent')";

    return $conn->query($query);
}