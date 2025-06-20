<?php
session_start();
include '../config/koneksi.php';

// --- Pengecekan Akses ---
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = '';
// --- Proses Form ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $id_toko = $_POST['id_toko'];
    $status_aktif = $_POST['status_aktif'];

    // Validasi dasar
    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role) || empty($id_toko)) {
        $error = "Semua field wajib diisi.";
    } else {
        // Enkripsi password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // --- Menggunakan PREPARED STATEMENT untuk keamanan ---
        $stmt = mysqli_prepare($conn, "INSERT INTO pengguna (nama_lengkap, username, password, role, id_toko, status_aktif) VALUES (?, ?, ?, ?, ?, ?)");
        
        // 'ssssii' adalah tipe data untuk setiap parameter: s=string, i=integer
        mysqli_stmt_bind_param($stmt, "ssssii", $nama_lengkap, $username, $hashed_password, $role, $id_toko, $status_aktif);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan_sukses'] = "Pengguna baru berhasil ditambahkan.";
            header('Location: pengguna.php');
            exit;
        } else {
            // Cek jika username sudah ada
            if(mysqli_errno($conn) == 1062){
                $error = "Gagal menambahkan: Username '$username' sudah digunakan.";
            } else {
                $error = "Gagal menambahkan pengguna. Error: " . mysqli_stmt_error($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Ambil data toko untuk dropdown
$toko_query = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengguna - POS Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow">
        <div class="card-body">
            <h2 class="card-title mb-4">Tambah Pengguna Baru</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="kasir">Kasir</option>
                        <option value="manajer">Manajer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Toko</label>
                    <select name="id_toko" class="form-select" required>
                        <option value="">-- Pilih Toko --</option>
                        <?php while($toko = mysqli_fetch_assoc($toko_query)): ?>
                            <option value="<?= $toko['id_toko'] ?>"><?= htmlspecialchars($toko['nama_toko']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status_aktif" class="form-select" required>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan Pengguna</button>
                <a href="pengguna.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>