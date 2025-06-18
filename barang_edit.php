<?php
include 'koneksi.php';

$id_toko = $_SESSION['id_toko'];
$id_barang = $_GET['id'] ?? 0;

if (!$id_barang) {
    header("Location: barang.php");
    exit;
}

$query = mysqli_query($conn, "SELECT b.*, s.jumlah_stok FROM barang b LEFT JOIN stoktoko s ON b.id_barang = s.id_barang AND s.id_toko = $id_toko WHERE b.id_barang = $id_barang LIMIT 1");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data barang tidak ditemukan.";
    exit;
}

if (isset($_POST['submit'])) {
    $nama_barang = $_POST['nama_barang'];
    $harga_jual = $_POST['harga_jual'];
    $stok = $_POST['stok'];

    $update_barang = mysqli_query($conn, "UPDATE barang SET nama_barang='$nama_barang', harga_jual='$harga_jual' WHERE id_barang=$id_barang");
    $update_stok = mysqli_query($conn, "INSERT INTO stoktoko (id_toko, id_barang, jumlah_stok)
                                    VALUES ($id_toko, $id_barang, $stok)
                                    ON DUPLICATE KEY UPDATE jumlah_stok = $stok");

    if ($update_barang && $update_stok) {
        header("Location: barang.php");
        exit;
    } else {
        $error = "Gagal update data.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h2>Edit Barang (ID: <?= $id_barang ?>)</h2>

    <?php if (isset($error)) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required value="<?= htmlspecialchars($data['nama_barang']) ?>">
            <div class="invalid-feedback">Nama barang wajib diisi.</div>
        </div>

        <div class="mb-3">
            <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
            <input type="number" class="form-control" id="harga_jual" name="harga_jual" required min="1" value="<?= $data['harga_jual'] ?>">
            <div class="invalid-feedback">Harga jual wajib diisi dan harus lebih dari 0.</div>
        </div>

        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok" required min="0" value="<?= $data['jumlah_stok'] !== null ? $data['jumlah_stok'] : 0 ?>">
            <div class="invalid-feedback">Stok wajib diisi dan minimal 0.</div>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Update Barang</button>
        <a href="barang.php" class="btn btn-secondary ms-2">Batal</a>
    </form>
</div>

<script>
// Bootstrap validation
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})();
</script>

</body>
</html>