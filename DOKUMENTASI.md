# Dokumentasi Aplikasi Parkir (UKK RPL)

## Deskripsi Program
Aplikasi manajemen parkir berbasis web yang dirancang untuk memenuhi kebutuhan UKK RPL. Aplikasi ini mencatat kendaraan masuk dan keluar, menghitung biaya parkir secara otomatis, dan menyediakan laporan pendapatan.

**Teknologi:** PHP Native, MySQL, Bootstrap 5.

## Fitur & Akses User

### 1. Admin
- **Login:** `admin` / `admin123`
- **Fitur:** 
  - Manajemen User (CRUD)
  - Manajemen Tarif Parkir (CRUD)
  - Manajemen Area/Kapasitas Parkir (CRUD)
  - Melihat Log Aktivitas Sistem
  - Dashboard Statistik

### 2. Petugas
- **Login:** `petugas` / `petugas123`
- **Fitur:**
  - Input Kendaraan Masuk (Otomatis Cek Kapasitas)
  - Proses Kendaraan Keluar (Hitung Biaya Otomatis)
  - Cetak Struk Parkir

### 3. Owner
- **Login:** `owner` / `owner123`
- **Fitur:**
  - Melihat Laporan Transaksi
  - Filter Laporan per Periode Tanggal
  - Ringkasan Pendapatan

## Instalasi
1. Copy folder `parkirsucip` ke `C:\xampp\htdocs\`.
2. Buka phpMyAdmin, buat database `db_parkit_ukk`.
3. Import file `database.sql` yang ada di root folder aplikasi.
4. Buka browser: `http://localhost/parkirsucip`.

## Struktur Database (ERD)
- **users**: `id_user` (PK), `username`, `password` (MD5), `role`.
- **jenis_kendaraan**: `id_jenis` (PK), `nama_jenis`, `tarif_per_jam`.
- **area_parkir**: `id_area` (PK), `nama_area`, `kapasitas`, `terisi`.
- **transaksi**: `id_transaksi` (PK), `kode`, `plat`, `jam_masuk`, `jam_keluar`, `biaya`, `status`, `id_petugas` (FK), `id_jenis` (FK), `id_area` (FK).
- **log_aktivitas**: `id_log` (PK), `id_user` (FK), `aktivitas`, `waktu`.

## Troubleshooting
- **Gagal Login**: Pastikan password di database ter-hash MD5.
- **Koneksi Gagal**: Cek `config/koneksi.php`, pastikan username root dan password kosong (default XAMPP).
- **Struk Tidak Muncul**: Pastikan browser mengizinkan pop-up atau print dialog.

## Alur Program (Pseudocode)

### 1. Proses Login
```text
BEGIN
    INPUT username, password
    HASH password WITH MD5
    QUERY database "SELECT * FROM users WHERE username = input_user AND password = input_pass"
    IF data found THEN
        SET Session Login
        LOG aktivitas "Login ke sistem"
        CASE role OF
            "admin": REDIRECT to admin/dashboard
            "petugas": REDIRECT to petugas/dashboard
            "owner": REDIRECT to owner/laporan
        END CASE
    ELSE
        SHOW Message "Username/Password Salah"
        REDIRECT to login
    END IF
END
```

### 2. Proses Transaksi Parkir
**Masuk:**
```text
BEGIN
    INPUT plat_nomor, jenis_kendaraan, area_parkir
    CHECK Kapasitas Area
    IF Kapasitas Penuh THEN
        SHOW Message "Area Penuh"
    ELSE
        GENERATE kode_transaksi (TRX-TIMESTAMP-RANDOM)
        INSERT INTO transaksi (kode, plat, jenis, area, waktu_masuk, status='masuk')
        UPDATE area_parkir SET terisi = terisi + 1
        PRINT Karcis
    END IF
END
```

**Keluar:**
```text
BEGIN
    INPUT kode_transaksi
    SEARCH data transaksi BY kode_transaksi
    IF Found AND Status='masuk' THEN
        CALCULATE Durasi (Waktu Sekarang - Waktu Masuk)
        IF Durasi == 0 THEN Durasi = 1 (Minimum 1 jam)
        CALCULATE Biaya = Durasi * Tarif_Per_Jam
        DISPLAY Detail & Biaya
        
        IF Konfirmasi Bayar THEN
            UPDATE transaksi SET waktu_keluar=NOW, biaya=Biaya, status='keluar'
            UPDATE area_parkir SET terisi = terisi - 1
            LOG aktivitas "Transaksi Keluar"
            REDIRECT to Cetak Struk
        END IF
    ELSE
        SHOW Message "Data tidak ditemukan / Sudah keluar"
    END IF
END
```

### 3. Proses Cetak Struk
```text
BEGIN
    GET id_transaksi
    QUERY data transaksi JOIN jenis_kendaraan JOIN user_petugas
    DISPLAY format HTML struk
    TRIGGER window.print()
END
```
