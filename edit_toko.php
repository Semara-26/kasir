<?php
session_start();
include 'koneksi.php';

// Cek login & role
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: toko.php");
    exit;
}

$error = '';
$success = '';

// Ambil data toko saat ini
$result = mysqli_query($conn, "SELECT * FROM toko WHERE id_toko = $id");
$toko = mysqli_fetch_assoc($result);
if (!$toko) {
    die("Data toko tidak ditemukan.");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = mysqli_real_escape_string($conn, $_POST['nama_toko']);
    $alamat_toko = mysqli_real_escape_string($conn, $_POST['alamat_toko']);
    $tlp = mysqli_real_escape_string($conn, $_POST['tlp']);
    $nama_pemilik = mysqli_real_escape_string($conn, $_POST['nama_pemilik']);

    $image_update = ""; // default, tidak update gambar

    if ($_FILES['image']['name']) {
        $target_dir = "gambar/";
        $upload_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $upload_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed)) {
            $error = "Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan.";
        } elseif ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
            $error = "Ukuran gambar maksimal 2MB.";
        } else {
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            $image_update = ", image = '$upload_name'";
        }
    }

    if (!$error) {
        $query = "UPDATE toko SET 
                    nama_toko = '$nama_toko', 
                    alamat_toko = '$alamat_toko', 
                    tlp = '$tlp', 
                    nama_pemilik = '$nama_pemilik'
                    $image_update
                  WHERE id_toko = $id";

        if (mysqli_query($conn, $query)) {
            header("Location: toko.php");
            exit;
        } else {
            $error = "Gagal mengupdate data toko.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Toko</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-5">
    <h2 class="mb-4">Edit Data Toko</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Nama Toko</label>
        <input type="text" name="nama_toko" class="form-control" required value="<?= htmlspecialchars($toko['nama_toko']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat_toko" class="form-control" rows="3" required><?= htmlspecialchars($toko['alamat_toko']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Telepon</label>
        <input type="text" name="tlp" class="form-control" required value="<?= htmlspecialchars($toko['tlp']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Nama Pemilik</label>
        <input type="text" name="nama_pemilik" class="form-control" required value="<?= htmlspecialchars($toko['nama_pemilik']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Gambar Toko Saat Ini</label><br>
        <img src="gambar/<?= htmlspecialchars($toko['image']) ?>" width="120" alt="gambar toko">
      </div>
      <div class="mb-3">
        <label class="form-label">Upload Gambar Baru (opsional)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
      </div>
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="toko.php" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</body>
</html>
