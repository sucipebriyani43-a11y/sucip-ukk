<?php include 'header.php'; ?>

<?php
if (isset($_POST['masuk'])) {
    $plat = strtoupper(sanitize($_POST['plat_nomor']));
    $id_jenis = $_POST['id_jenis'];
    $id_area = $_POST['id_area'];
    $id_petugas = $_SESSION['user']['id_user'];
    
    // Generate Kode Transaksi (TRX-TIMESTAMP-RANDOM)
    $kode = "TRX-" . time() . "-" . rand(100, 999);
    $jam_masuk = date('Y-m-d H:i:s');
    
    // Check kapasitas
    $area = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM area_parkir WHERE id_area = $id_area"));
    
    if ($area['terisi'] >= $area['kapasitas']) {
        set_flash('Area Parkir Penuh!', 'danger');
    } else {
        $query = "INSERT INTO transaksi (kode_transaksi, plat_nomor, id_jenis, id_area, jam_masuk, status, id_petugas) 
                  VALUES ('$kode', '$plat', '$id_jenis', '$id_area', '$jam_masuk', 'masuk', '$id_petugas')";
        
        if (mysqli_query($koneksi, $query)) {
            // Update kapasitas area
            mysqli_query($koneksi, "UPDATE area_parkir SET terisi = terisi + 1 WHERE id_area = $id_area");
            
            // Log
            mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas) VALUES ('$id_petugas', 'Input kendaraan masuk $plat')");
            
            set_flash("Berhasil! Kode Karcis: <b>$kode</b>. Silahkan cetak jika perlu.");
            // Redirect to print/view separate page if needed, but for now stay here
        } else {
            set_flash('Gagal input: ' . mysqli_error($koneksi), 'danger');
        }
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Parkir Masuk</h1>
</div>

<?= get_flash() ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Form Kendaraan Masuk</div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label>Plat Nomor</label>
                        <input type="text" name="plat_nomor" class="form-control" placeholder="B 1234 CD" required style="text-transform: uppercase;">
                    </div>
                    <div class="mb-3">
                        <label>Jenis Kendaraan</label>
                        <select name="id_jenis" class="form-select" required>
                            <?php
                            $jenis = query("SELECT * FROM jenis_kendaraan");
                            foreach ($jenis as $j) : ?>
                                <option value="<?= $j['id_jenis'] ?>"><?= $j['nama_jenis'] ?> (Rp <?= number_format($j['tarif_per_jam']) ?>/jam)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Area Parkir</label>
                        <select name="id_area" class="form-select" required>
                            <?php
                            $area = query("SELECT * FROM area_parkir");
                            foreach ($area as $a) : ?>
                                <option value="<?= $a['id_area'] ?>" <?= ($a['terisi'] >= $a['kapasitas']) ? 'disabled' : '' ?>>
                                    <?= $a['nama_area'] ?> (Sisa: <?= $a['kapasitas'] - $a['terisi'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="masuk" class="btn btn-primary w-100">Simpan & Cetak Karcis</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Kendaraan Baru Masuk (Hari Ini)</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm text-center">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Plat</th>
                                <th>Kode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $today = date('Y-m-d');
                            $recent = query("SELECT * FROM transaksi WHERE DATE(jam_masuk) = '$today' AND status='masuk' ORDER BY jam_masuk DESC LIMIT 10");
                            foreach ($recent as $r) : ?>
                            <tr>
                                <td><?= date('H:i', strtotime($r['jam_masuk'])) ?></td>
                                <td><?= $r['plat_nomor'] ?></td>
                                <td><span class="badge bg-secondary"><?= $r['kode_transaksi'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
