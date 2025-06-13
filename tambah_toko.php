<?php
session_start();
include 'koneksi.php';

// Cek role admin atau manajer
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = mysqli_real_escape_string($conn, $_POST['nama_toko']);
    $alamat_toko = mysqli_real_escape_string($conn, $_POST['alamat_toko']);
    $tlp = mysqli_real_escape_string($conn, $_POST['tlp']);
    $nama_pemilik = mysqli_real_escape_string($conn, $_POST['nama_pemilik']);

    // Proses upload gambar
    $gambar = '';
    if ($_FILES['image']['name']) {
        $target_dir = "upload/";
        $gambar = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $gambar;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi jenis file
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed)) {
            $error = "Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan.";
        } elseif ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
            $error = "Ukuran gambar maksimal 2MB.";
        } else {
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }
    }

    if (!$error) {
        $query = "INSERT INTO toko (nama_toko, alamat_toko, tlp, image, nama_pemilik) 
                  VALUES ('$nama_toko', '$alamat_toko', '$tlp', '$gambar', '$nama_pemilik')";
        if (mysqli_query($conn, $query)) {
            $success = "Toko berhasil ditambahkan!";
            header("Location: toko.php");
            exit;
        } else {
            $error = "Gagal menambahkan toko.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Toko</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-5">
    <h2 class="mb-4">Tambah Toko</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Nama Toko</label>
        <input type="text" name="nama_toko" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat_toko" class="form-control" rows="3" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Telepon</label>
        <input type="text" name="tlp" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Nama Pemilik</label>
        <input type="text" name="nama_pemilik" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Upload Gambar Toko</label>
        <input type="file" name="image" class="form-control" accept="image/*">
      </div>
      <button type="submit" class="btn btn-success">Simpan</button>
      <a href="toko.php" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</body>
</html>
