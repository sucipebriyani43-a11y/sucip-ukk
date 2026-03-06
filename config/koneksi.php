<?php
// Report mode off for custom error handling
mysqli_report(MYSQLI_REPORT_OFF);

// Ambil variabel environment (Prioritas: Vercel/Environment Variables)
$hostname = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'db_parkir_ukk';
$port = getenv('DB_PORT') ?: '3306';

$isVercel = getenv('VERCEL') == '1';

// Mencegah localhost di Vercel (Penyebab utama error 'No such file or directory')
if ($isVercel && ($hostname === 'localhost' || $hostname === '127.0.0.1')) {
    die("<div style='font-family:sans-serif; padding:30px; line-height:1.6;'>
            <h2 style='color:#e74c3c;'>⚠️ Database Belum Dikonfigurasi di Hosting</h2>
            <p>Anda melihat pesan ini karena aplikasi berjalan di <b>Vercel</b>, tetapi masih mencoba menyambung ke <i>localhost</i>.</p>
            <p><b>Solusi:</b></p>
            <ol>
                <li>Buka dashboard Vercel Anda.</li>
                <li>Masuk ke <b>Settings > Environment Variables</b>.</li>
                <li>Tambahkan: <b>DB_HOST, DB_USER, DB_PASSWORD, DB_NAME</b>.</li>
                <li>Gunakan database online (seperti Aiven atau Clever-Cloud), bukan localhost.</li>
            </ol>
         </div>");
}

// Inisialisasi koneksi
$koneksi = mysqli_init();
if (!$koneksi) {
    die("mysqli_init failed");
}

// Tambahkan timeout agar tidak hang
mysqli_options($koneksi, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

// Koneksi dengan SSL jika di Vercel (wajib untuk Aiven/PlanetScale)
if ($isVercel || getenv('DB_SSL') == 'true') {
    $connected = @mysqli_real_connect($koneksi, $hostname, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL);
} else {
    $connected = @mysqli_real_connect($koneksi, $hostname, $username, $password, $database, $port);
}

// Cek kesalahan koneksi
if (!$connected || mysqli_connect_errno()) {
    $err_msg = mysqli_connect_error();
    $err_no = mysqli_connect_errno();
    
    die("<div style='font-family:sans-serif; padding:20px; border:2px solid #cc0000; background:#fff5f5;'>
            <h3 style='color:#cc0000;'>Gagal Menyambung ke Database!</h3>
            <p>Pesan Error: <b>$err_msg</b> (Kode: $err_no)</p>
            <hr>
            <p><b>Tips Perbaikan:</b></p>
            <ul>
                <li><b>Local (XAMPP):</b> Pastikan MySQL di XAMPP sudah di-<b>START</b>.</li>
                <li><b>Local (XAMPP):</b> Pastikan database <u>$database</u> sudah dibuat di phpMyAdmin.</li>
                <li><b>Hosting (Vercel):</b> Pastikan IP database Anda sudah di-<b>Whitelist</b> (izinkan akses dari mana saja).</li>
            </ul>
         </div>");
}

// Set charset agar data tidak berantakan
mysqli_set_charset($koneksi, "utf8mb4");

// ---------------------------------------------------------
// Helper Functions
// ---------------------------------------------------------

// Base URL helper
function base_url($path = '') {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $isLocal = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);
    
    // Sesuaikan dengan folder proyek Anda
    $root = $isLocal ? '/parkirsucip/' : '/';
    
    return $root . ltrim($path, '/');
}

// Query helper
function query($query) {
    global $koneksi;
    $result = mysqli_query($koneksi, $query);
    $rows = [];
    if ($result && !is_bool($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

// Sanitize input
function sanitize($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, htmlspecialchars(trim($data ?? '')));
}

// Redirect helper
function redirect($url) {
    echo "<script>window.location.href='" . base_url($url) . "';</script>";
    exit;
}

// Session & Flash Message
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function set_flash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
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

// Middleware sederhana
function check_role($role) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != $role) {
        redirect('auth/login.php');
    }
}
?>
