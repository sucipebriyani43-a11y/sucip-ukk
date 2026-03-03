<?php include 'header.php'; ?>

<?php
if (isset($_POST['save_area'])) {
    $nama = sanitize($_POST['nama_area']);
    $kapasitas = sanitize($_POST['kapasitas']);
    
    if (!empty($_POST['id_area'])) {
        $id = $_POST['id_area'];
        $query = "UPDATE area_parkir SET nama_area='$nama', kapasitas='$kapasitas' WHERE id_area = $id";
        $action = "Mengubah area $nama";
    } else {
        $query = "INSERT INTO area_parkir (nama_area, kapasitas) VALUES ('$nama', '$kapasitas')";
        $action = "Menambah area $nama";
    }
    
    if (mysqli_query($koneksi, $query)) {
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas) VALUES ('{$_SESSION['user']['id_user']}', '$action')");
        set_flash('Data area berhasil disimpan');
    } else {
        set_flash('Error', 'danger');
    }
    echo "<script>window.location.href='area.php';</script>";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM area_parkir WHERE id_area = $id");
    set_flash('Area berhasil dihapus');
    echo "<script>window.location.href='area.php';</script>";
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Area Parkir</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#areaModal">
        <i class="bi bi-plus-lg"></i> Tambah Area
    </button>
</div>

<?= get_flash() ?>

<div class="row">
    <?php
    $areas = query("SELECT * FROM area_parkir");
    foreach ($areas as $a) : 
        $persen = ($a['kapasitas'] > 0) ? round(($a['terisi'] / $a['kapasitas']) * 100) : 0;
        $color = ($persen < 50) ? 'success' : (($persen < 80) ? 'warning' : 'danger');
    ?>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h5 class="card-title"><?= $a['nama_area'] ?></h5>
                    <div class="dropdown">
                        <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item btn-edit" href="#" 
                                data-id="<?= $a['id_area'] ?>"
                                data-nama="<?= $a['nama_area'] ?>"
                                data-kapasitas="<?= $a['kapasitas'] ?>">Edit</a></li>
                            <li><a class="dropdown-item text-danger" href="area.php?hapus=<?= $a['id_area'] ?>" onclick="return confirm('Hapus area ini?')">Hapus</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Terisi: <?= $a['terisi'] ?> / <?= $a['kapasitas'] ?></span>
                        <span class="fw-bold <?= ($persen >= 90) ? 'text-danger' : 'text-success' ?>"><?= $persen ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-<?= $color ?>" role="progressbar" style="width: <?= $persen ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="areaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <input type="hidden" name="id_area" id="id_area">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Area</label>
                        <input type="text" name="nama_area" id="nama_area" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Kapasitas Total</label>
                        <input type="number" name="kapasitas" id="kapasitas" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_area" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('areaModal'));
            document.getElementById('modalTitle').innerText = 'Edit Area';
            document.getElementById('id_area').value = btn.dataset.id;
            document.getElementById('nama_area').value = btn.dataset.nama;
            document.getElementById('kapasitas').value = btn.dataset.kapasitas;
            modal.show();
        });
    });
</script>

<?php include 'footer.php'; ?>
