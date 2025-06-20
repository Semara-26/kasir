<?php
session_start();
include 'koneksi.php';

// Pengecekan Akses
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['admin', 'manajer', 'kasir'])) {
    header("Location: login.php");
    exit;
}

$id_edit = $_GET['id'] ?? 0;
if (!$id_edit) { header('Location: member.php'); exit; }

// Ambil data member yang akan diedit
$stmt_data = mysqli_prepare($conn, "SELECT * FROM member WHERE id_member = ?");
mysqli_stmt_bind_param($stmt_data, "i", $id_edit);
mysqli_stmt_execute($stmt_data);
$result = mysqli_stmt_get_result($stmt_data);
$data = mysqli_fetch_assoc($result);
if (!$data) { die("Member tidak ditemukan."); }
mysqli_stmt_close($stmt_data);

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_member = $_POST['nama_member'];
    $alamat_member = $_POST['alamat_member'];
    $telepon = !empty($_POST['telepon']) ? $_POST['telepon'] : NULL;
    $email = !empty($_POST['email']) ? $_POST['email'] : NULL;
    $username = !empty($_POST['username']) ? $_POST['username'] : NULL;
    $password = $_POST['password'];

    if (empty($nama_member) || empty($alamat_member)) {
        $error = "Nama dan Alamat Member wajib diisi.";
    } else {
        if (!empty($password)) {
            // Jika password diisi, update password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE member SET nama_member=?, alamat_member=?, telepon=?, email=?, username=?, password=? WHERE id_member=?");
            mysqli_stmt_bind_param($stmt, "ssssssi", $nama_member, $alamat_member, $telepon, $email, $username, $hashed_password, $id_edit);
        } else {
            // Jika password kosong, jangan update password
            $stmt = mysqli_prepare($conn, "UPDATE member SET nama_member=?, alamat_member=?, telepon=?, email=?, username=? WHERE id_member=?");
            mysqli_stmt_bind_param($stmt, "sssssi", $nama_member, $alamat_member, $telepon, $email, $username, $id_edit);
        }

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan_sukses'] = "Data member berhasil diupdate.";
            header('Location: member.php');
            exit;
        } else {
            $error = "Gagal update member. Error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Member - POS Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow">
        <div class="card-body">
            <h2 class="card-title mb-4">Edit Member</h2>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Member <span class="text-danger">*</span></label>
                    <input type="text" name="nama_member" class="form-control" required value="<?= htmlspecialchars($data['nama_member']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Member <span class="text-danger">*</span></label>
                    <textarea name="alamat_member" class="form-control" rows="3" required><?= htmlspecialchars($data['alamat_member']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($data['telepon']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>">
                </div>
                <hr>
                <p class="text-muted">Isi username dan password jika member ingin bisa login (opsional).</p>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                </div>
                <button type="submit" class="btn btn-primary">Update Member</button>
                <a href="member.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>