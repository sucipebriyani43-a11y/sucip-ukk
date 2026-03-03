<?php
require_once '../config/koneksi.php';
session_start();
check_role('petugas');

if (!isset($_GET['id'])) {
    die("ID Transaksi tidak ditemukan");
}

$id = sanitize($_GET['id']);
$query = "SELECT t.*, j.nama_jenis, u.nama_lengkap as nama_petugas 
          FROM transaksi t 
          JOIN jenis_kendaraan j ON t.id_jenis = j.id_jenis 
          LEFT JOIN users u ON t.id_petugas = u.id_user 
          WHERE t.id_transaksi = '$id'";

$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data tidak ditemukan");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Parkir - <?= $data['kode_transaksi'] ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 14px; max-width: 300px; margin: 0 auto; padding: 20px; }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .flex { display: flex; justify-content: space-between; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <h3>E-PARKING UKK</h3>
        <p>Jl. Pendidikan No. 1, Jakarta</p>
    </div>
    <div class="divider"></div>
    <div class="flex">
        <span>Kode:</span>
        <span><?= $data['kode_transaksi'] ?></span>
    </div>
    <div class="flex">
        <span>Tgl:</span>
        <span><?= date('d/m/Y H:i', strtotime($data['jam_keluar'])) ?></span>
    </div>
    <div class="flex">
        <span>Petugas:</span>
        <span><?= $data['nama_petugas'] ?></span>
    </div>
    <div class="divider"></div>
    <div class="flex">
        <span>Plat:</span>
        <span><?= $data['plat_nomor'] ?></span>
    </div>
    <div class="flex">
        <span>Jenis:</span>
        <span><?= $data['nama_jenis'] ?></span>
    </div>
    <div class="flex">
        <span>Masuk:</span>
        <span><?= date('H:i', strtotime($data['jam_masuk'])) ?></span>
    </div>
    <div class="flex">
        <span>Keluar:</span>
        <span><?= date('H:i', strtotime($data['jam_keluar'])) ?></span>
    </div>
    <div class="divider"></div>
    <div class="flex" style="font-weight: bold; font-size: 16px;">
        <span>TOTAL:</span>
        <span>Rp <?= number_format($data['biaya'], 0, ',', '.') ?></span>
    </div>
    <div class="divider"></div>
    <div class="text-center">
        <p>Terima Kasih Atas Kunjungan Anda</p>
        <p>Hati-hati di jalan</p>
    </div>
    
    <button class="no-print" onclick="window.history.back()">Kembali</button>
</body>
</html>
