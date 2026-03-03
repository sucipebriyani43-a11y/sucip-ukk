<?php include 'header.php'; ?>

<?php
// Handle Create/Edit/Delete
if (isset($_POST['save_tarif'])) {
    $nama = sanitize($_POST['nama_jenis']);
    $tarif = sanitize($_POST['tarif_per_jam']);
    
    if (!empty($_POST['id_jenis'])) {
        $id = $_POST['id_jenis'];
        $query = "UPDATE jenis_kendaraan SET nama_jenis='$nama', tarif_per_jam='$tarif' WHERE id_jenis = $id";
        $action = "Mengubah tarif $nama";
    } else {
        $query = "INSERT INTO jenis_kendaraan (nama_jenis, tarif_per_jam) VALUES ('$nama', '$tarif')";
        $action = "Menambah tarif $nama";
    }
    
    if (mysqli_query($koneksi, $query)) {
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas) VALUES ('{$_SESSION['user']['id_user']}', '$action')");
        set_flash('Data tarif berhasil disimpan');
    } else {
        set_flash('Error: ' . mysqli_error($koneksi), 'danger');
    }
    echo "<script>window.location.href='tarif.php';</script>";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM jenis_kendaraan WHERE id_jenis = $id");
    set_flash('Tarif berhasil dihapus');
    echo "<script>window.location.href='tarif.php';</script>";
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tarif Parkir</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tarifModal">
        <i class="bi bi-plus-lg"></i> Tambah Tarif
    </button>
</div>

<?= get_flash() ?>

<div class="row">
    <?php
    $tarifs = query("SELECT * FROM jenis_kendaraan");
    foreach ($tarifs as $t) : ?>
    <div class="col-md-4 mb-3">
        <div class="card h-100 border-primary">
            <div class="card-body text-center">
                <h5 class="card-title"><?= $t['nama_jenis'] ?></h5>
                <h2 class="display-6 fw-bold">Rp <?= number_format($t['tarif_per_jam'],0,',','.') ?></h2>
                <p class="text-muted">Per Jam</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-warning btn-edit" 
                        data-id="<?= $t['id_jenis'] ?>"
                        data-nama="<?= $t['nama_jenis'] ?>"
                        data-tarif="<?= $t['tarif_per_jam'] ?>">
                        Edit
                    </button>
                    <a href="tarif.php?hapus=<?= $t['id_jenis'] ?>" class="btn btn-outline-danger" onclick="return confirm('Hapus tarif ini? Data transaksi terkait laporannya mungkin hilang referensi jika tidak di-handle.')">Hapus</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="tarifModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <input type="hidden" name="id_jenis" id="id_jenis">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Tarif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Jenis Kendaraan</label>
                        <input type="text" name="nama_jenis" id="nama_jenis" class="form-control" required placeholder="Contoh: Motor">
                    </div>
                    <div class="mb-3">
                        <label>Tarif Per Jam (Rp)</label>
                        <input type="number" name="tarif_per_jam" id="tarif_per_jam" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_tarif" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('tarifModal'));
            document.getElementById('modalTitle').innerText = 'Edit Tarif';
            document.getElementById('id_jenis').value = btn.dataset.id;
            document.getElementById('nama_jenis').value = btn.dataset.nama;
            document.getElementById('tarif_per_jam').value = btn.dataset.tarif;
            modal.show();
        });
    });
</script>

<?php include 'footer.php'; ?>
