<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = $_POST['nama_kategori'];
  mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
  header('Location: kategori.php');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Kategori</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <h2 class="mb-4">Tambah Kategori</h2>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Nama Kategori</label>
      <input type="text" name="nama_kategori" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="kategori.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>

</body>
</html>
