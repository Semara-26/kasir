<?php
session_start();
include 'koneksi.php';

// Pengecekan Akses
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['admin', 'manajer', 'kasir'])) {
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_member = $_POST['nama_member'];
    $alamat_member = $_POST['alamat_member'];
    $telepon = !empty($_POST['telepon']) ? $_POST['telepon'] : NULL;
    $email = !empty($_POST['email']) ? $_POST['email'] : NULL;
    $username = !empty($_POST['username']) ? $_POST['username'] : NULL;
    $password = $_POST['password'];

    if (empty($nama_member) || empty($alamat_member)) {
        $error = "Nama dan Alamat Member wajib diisi.";
    } else {
        // Logika untuk username dan password (opsional)
        $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : NULL;
        
        $stmt = mysqli_prepare($conn, "INSERT INTO member (nama_member, alamat_member, telepon, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssss", $nama_member, $alamat_member, $telepon, $email, $username, $hashed_password);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan_sukses'] = "Member baru berhasil ditambahkan.";
            header('Location: member.php');
            exit;
        } else {
            $error = "Gagal menambahkan member. Error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Member Baru - POS Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow">
        <div class="card-body">
            <h2 class="card-title mb-4">Tambah Member Baru</h2>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Member <span class="text-danger">*</span></label>
                    <input type="text" name="nama_member" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Member <span class="text-danger">*</span></label>
                    <textarea name="alamat_member" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telepon" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <hr>
                <p class="text-muted">Isi username dan password jika member ingin bisa login (opsional).</p>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Simpan Member</button>
                <a href="member.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>