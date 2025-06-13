<?php
include 'koneksi.php';

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id_kategori = $_POST['id_kategori'];
  $nama = $_POST['nama_barang'];
  $beli = $_POST['harga_beli'];
  $jual = $_POST['harga_jual'];
  $satuan = $_POST['satuan_barang'];

  // Validasi sederhana
  if ($id_kategori != '') {
    mysqli_query($conn, "INSERT INTO barang (id_kategori, nama_barang, harga_beli, harga_jual, satuan_barang) 
                         VALUES ('$id_kategori', '$nama', '$beli', '$jual', '$satuan')");
    header('Location: barang.php');
    exit;
  } else {
    echo "<div class='alert alert-danger'>Kategori belum dipilih!</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <h2 class="mb-4">Tambah Barang</h2>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Kategori</label>
      <select name="id_kategori" class="form-select" required>
        <option value="">-- Pilih Kategori --</option>
        <?php
        $kategori = mysqli_query($conn, "SELECT * FROM kategori");
        if (!$kategori) {
          echo "<option value=''>Gagal mengambil data kategori</option>";
        } else {
          while ($row = mysqli_fetch_assoc($kategori)) {
            echo "<option value='" . $row['id_kategori'] . "'>" . $row['nama_kategori'] . "</option>";
          }
        }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Nama Barang</label>
      <input type="text" name="nama_barang" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Harga Jual</label>
      <input type="number" name="harga_jual" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Satuan Barang</label>
      <input type="text" name="satuan_barang" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="barang.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>

</body>
</html>
