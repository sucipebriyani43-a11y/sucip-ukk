<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Report mode off for custom error handling
mysqli_report(MYSQLI_REPORT_OFF);

// Helper untuk meload file .env secara manual (untuk environment lokal XAMPP)
function load_env($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \"'");
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load .env jika ada di root project
load_env(__DIR__ . '/../.env');

// Ambil variabel environment (Prioritas: Vercel/Environment Variables)
$hostname = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: getenv('DB_PASSWORD') ?: '';
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
$db_port = (int)$port;
if ($isVercel || getenv('DB_SSL') == 'true') {
    $connected = @mysqli_real_connect($koneksi, $hostname, $username, $password, $database, $db_port, NULL, MYSQLI_CLIENT_SSL);
} else {
    $connected = @mysqli_real_connect($koneksi, $hostname, $username, $password, $database, $db_port);
}

// Cek kesalahan koneksi
if (!$connected || mysqli_connect_errno()) {
    $err_msg = mysqli_connect_error() ?: mysqli_error($koneksi);
    $err_no = mysqli_connect_errno() ?: mysqli_errno($koneksi);
    
    // Cek jika password kosong padahal hostnya bukan localhost
    $pass_warning = "";
    if ($hostname !== 'localhost' && $hostname !== '127.0.0.1' && empty($password)) {
        $pass_warning = "<p style='color:orange;'>⚠️ <b>Peringatan:</b> Anda mencoba menyambung ke host remote ($hostname) tanpa password. Biasanya database online (seperti Aiven) mewajibkan password.</p>";
    }
    
    die("<div style='font-family:sans-serif; padding:20px; border:2px solid #cc0000; background:#fff5f5;'>
            <h3 style='color:#cc0000;'>Gagal Menyambung ke Database!</h3>
            <p>Pesan Error: <b>$err_msg</b> (Kode: $err_no)</p>
            $pass_warning
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
if (!function_exists('base_url')) {
    function base_url($path = '') {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Deteksi jika akses lokal (localhost, 127.0.0.1, atau IP lokal 192.168.x.x)
        $isLocal = (strpos($host, 'localhost') !== false || 
                    strpos($host, '127.0.0.1') !== false || 
                    strpos($host, '192.168.') !== false || 
                    strpos($host, '10.') === 0);
        
        // Sesuaikan dengan folder proyek Anda (Ubah jika folder dipindah)
        $root = $isLocal ? '/parkirsucip/' : '/';
        
        return $root . ltrim($path, '/');
    }
}

// Query helper
if (!function_exists('query')) {
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
}

// Sanitize input
if (!function_exists('sanitize')) {
    function sanitize($data) {
        global $koneksi;
        return mysqli_real_escape_string($koneksi, htmlspecialchars(trim($data ?? '')));
    }
}

// Redirect helper
if (!function_exists('redirect')) {
    function redirect($url) {
        echo "<script>window.location.href='" . base_url($url) . "';</script>";
        exit;
    }
}


if (!function_exists('set_flash')) {
    function set_flash($message, $type = 'success') {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }
}

if (!function_exists('get_flash')) {
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
}

// Middleware sederhana
if (!function_exists('check_role')) {
    function check_role($role) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != $role) {
            redirect('auth/login.php');
        }
    }
}
