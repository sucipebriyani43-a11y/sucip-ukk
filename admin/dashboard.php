<?php include 'header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<?php
// Statistics
$total_pegawai = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM users WHERE role='petugas'"));
$total_transaksi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE DATE(jam_masuk) = CURDATE()"));
$parkir_aktif = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE status='masuk'"));
$pendapatan_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(biaya) as total FROM transaksi WHERE DATE(jam_keluar) = CURDATE()"))['total'] ?? 0;
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card card-stat bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Petugas Aktif</h6>
                        <h3 class="mb-0"><?= $total_pegawai ?></h3>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stat bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Transaksi Hari Ini</h6>
                        <h3 class="mb-0"><?= $total_transaksi ?></h3>
                    </div>
                    <i class="bi bi-receipt fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stat bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Kendaraan Parkir</h6>
                        <h3 class="mb-0"><?= $parkir_aktif ?></h3>
                    </div>
                    <i class="bi bi-car-front fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stat bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Pendapatan Hari Ini</h6>
                        <h3 class="mb-0">Rp <?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></h3>
                    </div>
                    <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        Log Aktivitas Terbaru
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $logs = query("SELECT l.*, u.username FROM log_aktivitas l JOIN users u ON l.id_user = u.id_user ORDER BY l.waktu DESC LIMIT 5");
                    foreach ($logs as $log) : ?>
                    <tr>
                        <td><?= $log['waktu'] ?></td>
                        <td><?= $log['username'] ?></td>
                        <td><?= $log['aktivitas'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
