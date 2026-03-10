<?php
if (ob_get_level() === 0) ob_start();
/**
 * Vercel PHP Router - Fixed Version
 * Memfungsikan file PHP di luar folder api/ agar tetap bisa dijalankan di Vercel.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Redirect root ke index.php
if ($path === '/' || $path === '') {
    $path = '/index.php';
}

// Pastikan tidak mengakses folder api/ secara langsung untuk menghindari loop
if (strpos($path, '/api/') === 0) {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

// Lokasi file asli di root proyek
$rootDoc = dirname(__DIR__) . $path;

// Cek jika file PHP tersebut ada
if (file_exists($rootDoc) && is_file($rootDoc) && pathinfo($rootDoc, PATHINFO_EXTENSION) === 'php') {
    // Jalankan file tersebut
    require_once $rootDoc;
} else if (file_exists($rootDoc) && is_file($rootDoc)) {
    // Jika file statis (seperti .sql atau lainnya yang tidak ditangkap vercel.json)
    return false;
} else {
    // Jika tidak ditemukan, coba cari file .php secara otomatis (misal /login -> /login.php)
    if (file_exists($rootDoc . '.php')) {
        require_once $rootDoc . '.php';
    } else {
        http_response_code(404);
        echo "404 Not Found: " . htmlspecialchars($path);
    }
}

