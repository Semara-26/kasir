<?php
include 'koneksi.php';

$id_toko = 1;

if (isset($_POST['submit'])) {
    $nama_barang = $_POST['nama_barang'];
    $harga_jual = $_POST['harga_jual'];
    $stok_awal = $_POST['stok_awal'];

    $insert_barang = mysqli_query($conn, "INSERT INTO barang (nama_barang, harga_jual) VALUES ('$nama_barang', '$harga_jual')");

    if ($insert_barang) {
        $id_barang = mysqli_insert_id($conn);
        $insert_stok = mysqli_query($conn, "INSERT INTO stoktoko (id_toko, id_barang, jumlah_stok) VALUES ($id_toko, $id_barang, $stok_awal)");

        if ($insert_stok) {
            header("Location: barang.php");
            exit;
        } else {
            $error = "Gagal insert stok.";
        }
    } else {
        $error = "Gagal insert barang.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Tambah Barang Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h2>Tambah Barang Baru (Toko ID: <?= $id_toko ?>)</h2>

    <?php if (isset($error)) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
            <div class="invalid-feedback">Nama barang wajib diisi.</div>
        </div>

        <div class="mb-3">
            <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
            <input type="number" class="form-control" id="harga_jual" name="harga_jual" required min="1">
            <div class="invalid-feedback">Harga jual wajib diisi dan harus lebih dari 0.</div>
        </div>

        <div class="mb-3">
            <label for="stok_awal" class="form-label">Stok Awal</label>
            <input type="number" class="form-control" id="stok_awal" name="stok_awal" required min="0">
            <div class="invalid-feedback">Stok awal wajib diisi dan minimal 0.</div>
        </div>

        <button type="submit" name="submit" class="btn btn-success">Tambah Barang</button>
        <a href="barang.php" class="btn btn-secondary ms-2">Kembali</a>
    </form>
</div>

<script>
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
