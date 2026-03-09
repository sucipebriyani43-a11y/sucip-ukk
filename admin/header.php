<?php
require_once __DIR__ . '/../config/koneksi.php';
check_role('admin'); // Protect Admin Area

$user_active = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Parking UKK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column flex-shrink-0 p-3">
            <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <span class="fs-4">Admin Panel</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="<?= base_url('admin/dashboard.php') ?>" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : 'text-white' ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/users.php') ?>" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : 'text-white' ?>">
                        <i class="bi bi-people me-2"></i> Kelola User
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/tarif.php') ?>" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'tarif.php') ? 'active' : 'text-white' ?>">
                        <i class="bi bi-cash-coin me-2"></i> Tarif Parkir
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/area.php') ?>" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'area.php') ? 'active' : 'text-white' ?>">
                        <i class="bi bi-geo-alt me-2"></i> Area Parkir
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/logs.php') ?>" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'logs.php') ? 'active' : 'text-white' ?>">
                        <i class="bi bi-list-ul me-2"></i> Log Aktivitas
                    </a>
                </li>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user_active['nama_lengkap']) ?>&background=random" alt="" width="32" height="32" class="rounded-circle me-2">
                    <strong><?= $user_active['username'] ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                    <li><a class="dropdown-item" href="<?= base_url('auth/logout.php') ?>">Sign out</a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1" style="max-height: 100vh; overflow-y: auto;">
            <div class="content-wrapper">
