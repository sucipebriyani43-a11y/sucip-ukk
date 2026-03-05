<?php
$hostname = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'db_parkir_ukk';
$port = getenv('DB_PORT') ?: '3306';

// Initialize connection
$koneksi = mysqli_init();
if (!$koneksi) {
    die("mysqli_init failed");
}

// Check if we are on serverless (Vercel)
if (getenv('VERCEL') == '1' || getenv('DB_SSL') == 'true') {
    // If you need SSL, some providers like Aiven require it. 
    // Usually, you can connect with SSL without a CA file or just use the flag.
    mysqli_real_connect($koneksi, $hostname, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL);
} else {
    mysqli_real_connect($koneksi, $hostname, $username, $password, $database, $port);
}

if (!$koneksi || mysqli_connect_errno()) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Base URL for assets and links
function base_url($path = '') {
    $host = $_SERVER['HTTP_HOST'];
    $isLocal = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);
    
    // On local, we use the folder name /parkirsucip/, on production, we use the root /
    $root = $isLocal ? '/parkirsucip/' : '/';
    
    return $root . ltrim($path, '/');
}

// Helper to query and return array
function query($query) {
    global $koneksi;
    $result = mysqli_query($koneksi, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Input Sanitation
function sanitize($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, htmlspecialchars(trim($data ?? '')));
}

// Redirect helper
function redirect($url) {
    echo "<script>window.location.href='" . base_url($url) . "';</script>";
    exit;
}

// Flash message helper
function set_flash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return '<div class="alert alert-' . $flash['type'] . ' alert-dismissible fade show" role="alert">
                    ' . $flash['message'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }
    return '';
}

// Check role middleware
function check_role($role) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != $role) {
        redirect('auth/login.php');
    }
}
?>
