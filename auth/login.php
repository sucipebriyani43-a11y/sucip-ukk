<?php
require_once __DIR__ . '/../config/koneksi.php';

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] == 'admin') redirect('admin/dashboard.php');
    if ($_SESSION['user']['role'] == 'petugas') redirect('petugas/dashboard.php');
    if ($_SESSION['user']['role'] == 'owner') redirect('owner/laporan.php');
}

if (isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = md5(sanitize($_POST['password'])); // MD5 as requested

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user'] = $user;
        
        // Log Activity
        $id_user = $user['id_user'];
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas) VALUES ('$id_user', 'Login ke sistem')");

        if ($user['role'] == 'admin') redirect('admin/dashboard.php');
        else if ($user['role'] == 'petugas') redirect('petugas/dashboard.php');
        else if ($user['role'] == 'owner') redirect('owner/laporan.php');
    } else {
        set_flash('Username atau Password salah!', 'danger');
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Parking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h3>E-Parking</h3>
                <p class="text-muted">Silahkan login untuk masuk</p>
            </div>
            
            <?= get_flash() ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="mt-3 text-center text-muted">
                <small>UKK RPL &copy; 2025</small>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
