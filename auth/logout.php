<?php
require_once __DIR__ . '/../config/koneksi.php';

if (isset($_SESSION['user'])) {
    $id_user = $_SESSION['user']['id_user'];
    mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas) VALUES ('$id_user', 'Logout dari sistem')");
    
    session_destroy();
}

redirect('auth/login.php');
?>
