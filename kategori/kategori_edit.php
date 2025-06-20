<?php
include '../config/koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori = $id"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = $_POST['nama_kategori'];
  mysqli_query($conn, "
    UPDATE kategori 
    SET nama_kategori = '$nama', tgl_update = CURRENT_TIMESTAMP 
    WHERE id_kategori = $id
  ");
  header('Location: kategori.php');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Kategori</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <h2 class="mb-4">Edit Kategori</h2>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Nama Kategori</label>
      <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($data['nama_kategori']); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="kategori.php" class="btn btn-secondary">Batal</a>
  </form>
</div>

</body>
</html>
