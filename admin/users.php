<?php include 'header.php'; ?>

<?php
// Handle Create/Edit/Delete
if (isset($_POST['save_user'])) {
    $nama = sanitize($_POST['nama_lengkap']);
    $username = sanitize($_POST['username']);
    $role = sanitize($_POST['role']);
    
    // Check if new or edit
    if (!empty($_POST['id_user'])) {
        $id = $_POST['id_user'];
        $query = "UPDATE users SET nama_lengkap='$nama', username='$username', role='$role'";
        if (!empty($_POST['password'])) {
            $pass = md5(sanitize($_POST['password']));
            $query .= ", password='$pass'";
        }
        $query .= " WHERE id_user = $id";
        $action = "Mengubah data user $username";
    } else {
        $pass = md5(sanitize($_POST['password']));
        $query = "INSERT INTO users (nama_lengkap, username, password, role) VALUES ('$nama', '$username', '$pass', '$role')";
        $action = "Menambah user baru $username";
    }
    
    if (mysqli_query($koneksi, $query)) {
        // Log
        $id_admin = $_SESSION['user']['id_user'];
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas) VALUES ('$id_admin', '$action')");
        set_flash('Data user berhasil disimpan');
    } else {
        set_flash('Gagal menyimpan: ' . mysqli_error($koneksi), 'danger');
    }
    echo "<script>window.location.href='users.php';</script>";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user = $id");
    set_flash('User berhasil dihapus');
    echo "<script>window.location.href='users.php';</script>";
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kelola User</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
        <i class="bi bi-plus-lg"></i> Tambah User
    </button>
</div>

<?= get_flash() ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $users = query("SELECT * FROM users ORDER BY role ASC");
            $no = 1;
            foreach ($users as $u) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $u['nama_lengkap'] ?></td>
                <td><?= $u['username'] ?></td>
                <td><span class="badge bg-<?= ($u['role']=='admin'?'danger':($u['role']=='petugas'?'primary':'success')) ?>"><?= ucfirst($u['role']) ?></span></td>
                <td>
                    <button class="btn btn-sm btn-warning btn-edit" 
                        data-id="<?= $u['id_user'] ?>"
                        data-nama="<?= $u['nama_lengkap'] ?>"
                        data-username="<?= $u['username'] ?>"
                        data-role="<?= $u['role'] ?>">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="users.php?hapus=<?= $u['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus user ini?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <input type="hidden" name="id_user" id="id_user">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak ubah password">
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_user" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Edit Script
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            document.getElementById('modalTitle').innerText = 'Edit User';
            document.getElementById('id_user').value = btn.dataset.id;
            document.getElementById('nama_lengkap').value = btn.dataset.nama;
            document.getElementById('username').value = btn.dataset.username;
            document.getElementById('role').value = btn.dataset.role;
            document.getElementById('password').required = false;
            modal.show();
        });
    });
</script>

<?php include 'footer.php'; ?>
