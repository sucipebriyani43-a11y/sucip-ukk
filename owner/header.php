<?php
require_once '../config/koneksi.php';
session_start();
check_role('owner'); // Protect Owner Area

$user_active = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - E-Parking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">E-Parking Owner</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : '' ?>" href="<?= base_url('owner/laporan.php') ?>">Laporan Transaksi</a>
                    </li>
                </ul>
                <div class="d-flex text-white align-items-center">
                    <span class="me-3">Halo, <?= $user_active['nama_lengkap'] ?></span>
                    <a href="<?= base_url('auth/logout.php') ?>" class="btn btn-outline-light btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
