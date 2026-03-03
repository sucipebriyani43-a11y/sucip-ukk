<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'db_parkir_ukk';

$koneksi = mysqli_connect($hostname, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Base URL for assets and links
function base_url($path = '') {
    return "/parkirsucip/" . $path;
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
