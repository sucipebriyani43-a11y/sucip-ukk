<?php include 'header.php'; ?>

<?php
$transaksi = null;
$biaya = 0;
$durasi = 0;

if (isset($_POST['cek_kode'])) {
    $kode = sanitize($_POST['kode_transaksi']);
    $query = "SELECT t.*, j.nama_jenis, j.tarif_per_jam, a.nama_area 
              FROM transaksi t 
              JOIN jenis_kendaraan j ON t.id_jenis = j.id_jenis 
              JOIN area_parkir a ON t.id_area = a.id_area 
              WHERE t.kode_transaksi = '$kode' AND t.status = 'masuk'";
    
    $result = mysqli_query($koneksi, $query);
    if (mysqli_num_rows($result) > 0) {
        $transaksi = mysqli_fetch_assoc($result);
        
        // Hitung Biaya
        $jam_masuk = new DateTime($transaksi['jam_masuk']);
        $jam_keluar = new DateTime(); // Current time
        $interval = $jam_masuk->diff($jam_keluar);
        
        $jam = $interval->h;
        $jam += ($interval->days * 24);
        if ($interval->i > 0) $jam++; // Round up for any minutes
        if ($jam == 0) $jam = 1; // Minimum 1 hour
        
        $durasi = $jam;
        $biaya = $durasi * $transaksi['tarif_per_jam'];
    } else {
        set_flash('Kode transaksi tidak ditemukan atau sudah keluar!', 'warning');
    }
}

if (isset($_POST['proses_keluar'])) {
    $id_transaksi = $_POST['id_transaksi'];
    $biaya_final = $_POST['biaya'];
    $id_area = $_POST['id_area'];
    $jam_keluar = date('Y-m-d H:i:s');
    $id_petugas = $_SESSION['user']['id_user'];
    
    $query = "UPDATE transaksi SET jam_keluar='$jam_keluar', biaya='$biaya_final', status='keluar' WHERE id_transaksi='$id_transaksi'";
    
    if (mysqli_query($koneksi, $query)) {
        // Update kapasitas
        mysqli_query($koneksi, "UPDATE area_parkir SET terisi = terisi - 1 WHERE id_area = $id_area");
        
        // Log
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas) VALUES ('$id_petugas', 'Proses kendaraan keluar ID $id_transaksi')");
        
        // Redirect to print receipt
        echo "<script>window.location.href='cetak_struk.php?id=$id_transaksi';</script>";
        exit;
    } else {
        set_flash('Gagal memproses: ' . mysqli_error($koneksi), 'danger');
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Parkir Keluar</h1>
</div>

<?= get_flash() ?>

<div class="row">
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header">Cari Transaksi</div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="kode_transaksi" class="form-control" placeholder="Scan Kode / Ketik..." required autofocus value="<?= $transaksi['kode_transaksi'] ?? '' ?>">
                        <button class="btn btn-primary" type="submit" name="cek_kode">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php if ($transaksi) : ?>
    <div class="col-md-7">
        <div class="card border-success">
            <div class="card-header bg-success text-white">Konfirmasi Keluar</div>
            <div class="card-body">
                <form action="" method="POST">
                    <input type="hidden" name="id_transaksi" value="<?= $transaksi['id_transaksi'] ?>">
                    <input type="hidden" name="id_area" value="<?= $transaksi['id_area'] ?>">
                    <input type="hidden" name="biaya" value="<?= $biaya ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Kode Transaksi</label>
                            <h5><?= $transaksi['kode_transaksi'] ?></h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Plat Nomor</label>
                            <h5><?= $transaksi['plat_nomor'] ?></h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Jam Masuk</label>
                            <h5><?= $transaksi['jam_masuk'] ?></h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Durasi Parkir</label>
                            <h5><?= $durasi ?> Jam</h5>
                        </div>
                         <div class="col-md-12 mb-3">
                            <label class="text-muted">Jenis & Tarif</label>
                            <h5><?= $transaksi['nama_jenis'] ?> (Rp <?= number_format($transaksi['tarif_per_jam']) ?>/jam)</h5>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Total Bayar</h3>
                        <h2 class="text-success fw-bold">Rp <?= number_format($biaya, 0, ',', '.') ?></h2>
                    </div>
                    <hr>
                    
                    <button type="submit" name="proses_keluar" class="btn btn-success w-100 btn-lg">Bayar & Cetak Struk</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
