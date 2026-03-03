<?php include 'header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Transaksi</h1>
</div>

<?php
$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-d');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

// Query Filter Layout
$query = "SELECT t.*, j.nama_jenis, u.nama_lengkap 
          FROM transaksi t 
          JOIN jenis_kendaraan j ON t.id_jenis = j.id_jenis 
          LEFT JOIN users u ON t.id_petugas = u.id_user 
          WHERE DATE(t.jam_masuk) BETWEEN '$tgl_awal' AND '$tgl_akhir'";

if (isset($_GET['filter']) && $_GET['filter'] == 'Semua') {
    // Optional: View all logic if needed, but usually limited by date is better for performance
}

$transaksi = query($query);

// Hitung Ringkasan
$total_pendapatan = 0;
$total_kendaraan = count($transaksi);
foreach ($transaksi as $t) {
    $total_pendapatan += $t['biaya'];
}
?>

<div class="card mb-4">
    <div class="card-body">
        <form action="" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Tanggal Awal</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filter Laporan</button>
            </div>
        </form>
    </div>
</div>

<!-- Ringkasan Statistik -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>Total Pendapatan</h5>
                <h2 class="fw-bold">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h2>
                <small>Periode: <?= $tgl_awal ?> s/d <?= $tgl_akhir ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5>Total Kendaraan</h5>
                <h2 class="fw-bold"><?= $total_kendaraan ?> Unit</h2>
                <small>Periode: <?= $tgl_awal ?> s/d <?= $tgl_akhir ?></small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Detail Transaksi</span>
        <button onclick="window.print()" class="btn btn-sm btn-secondary"><i class="bi bi-printer"></i> Cetak Laporan</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode TRX</th>
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Petugas</th>
                        <th>Biaya</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($transaksi as $tr) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $tr['kode_transaksi'] ?></td>
                        <td><?= $tr['plat_nomor'] ?></td>
                        <td><?= $tr['nama_jenis'] ?></td>
                        <td><?= $tr['jam_masuk'] ?></td>
                        <td><?= $tr['jam_keluar'] ?? '-' ?></td>
                        <td><?= $tr['nama_lengkap'] ?></td>
                        <td class="text-end">Rp <?= number_format($tr['biaya'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-<?= ($tr['status']=='masuk') ? 'warning' : 'success' ?>"><?= strtoupper($tr['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transaksi)) : ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">Tidak ada data transaksi pada periode ini.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="7" class="text-end">TOTAL</td>
                        <td class="text-end">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
