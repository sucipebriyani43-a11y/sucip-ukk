<?php include 'header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard Petugas</h1>
</div>

<?php
$userid = $_SESSION['user']['id_user'];
$hari_ini = date('Y-m-d');

// Stats specific to this petugas or general
$kendaraan_masuk = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE DATE(jam_masuk) = '$hari_ini'"));
$kendaraan_keluar = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE DATE(jam_keluar) = '$hari_ini'"));
$parkir_saat_ini = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE status='masuk'"));
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Kendaraan Masuk Hari Ini</h6>
                        <h3 class="mb-0"><?= $kendaraan_masuk ?></h3>
                    </div>
                    <i class="bi bi-box-arrow-in-right fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Kendaraan Keluar Hari Ini</h6>
                        <h3 class="mb-0"><?= $kendaraan_keluar ?></h3>
                    </div>
                    <i class="bi bi-box-arrow-left fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Sedang Parkir</h6>
                        <h3 class="mb-0"><?= $parkir_saat_ini ?></h3>
                    </div>
                    <i class="bi bi-car-front fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark text-white">Status Area Parkir</div>
            <div class="card-body">
                <?php
                $areas = query("SELECT * FROM area_parkir");
                foreach ($areas as $a) : 
                    $persen = ($a['kapasitas'] > 0) ? round(($a['terisi'] / $a['kapasitas']) * 100) : 0;
                    $color = ($persen < 50) ? 'success' : (($persen < 80) ? 'warning' : 'danger');
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><?= $a['nama_area'] ?> (<?= $a['terisi'] ?>/<?= $a['kapasitas'] ?>)</span>
                        <span class="fw-bold"><?= $persen ?>%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-<?= $color ?>" style="width: <?= $persen ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark text-white">Aksi Cepat</div>
            <div class="card-body d-grid gap-2">
                <a href="parkir_masuk.php" class="btn btn-primary btn-lg"><i class="bi bi-box-arrow-in-right"></i> Input Kendaraan Masuk</a>
                <a href="parkir_keluar.php" class="btn btn-danger btn-lg"><i class="bi bi-box-arrow-left"></i> Proses Kendaraan Keluar</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
