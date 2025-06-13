<?php
include 'koneksi.php';

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id_kategori = $_POST['id_kategori'];
  $nama = $_POST['nama_barang'];
  $beli = $_POST['harga_beli'];
  $jual = $_POST['harga_jual'];
  $satuan = $_POST['satuan_barang'];
  $stok = $_POST['stok'];
  $id_toko = 1; // ganti sesuai implementasi multi toko

  if ($id_kategori != '') {
    mysqli_query($conn, "INSERT INTO barang (id_kategori, nama_barang, harga_beli, harga_jual, satuan_barang) 
                         VALUES ('$id_kategori', '$nama', '$beli', '$jual', '$satuan')");
    
    $id_barang_baru = mysqli_insert_id($conn);

    // Tambahkan stok awal ke tabel stoktoko
    mysqli_query($conn, "INSERT INTO stoktoko (id_toko, id_barang, jumlah_stok) 
                         VALUES ('$id_toko', '$id_barang_baru', '$stok')");

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
        while ($row = mysqli_fetch_assoc($kategori)) {
          echo "<option value='" . $row['id_kategori'] . "'>" . $row['nama_kategori'] . "</option>";
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
      <label class="form-label">Jumlah Stok</label>
      <input type="number" name="stok" class="form-control" min="0" required>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="barang.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>

</body>
</html>
