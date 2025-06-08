<?php
include 'koneksi.php';
$result = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Kategori</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Kategori</h2>
    <a href="kategori_tambah.php" class="btn btn-primary">+ Tambah Kategori</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Nama Kategori</th>
            <th>Tanggal Input</th>
            <th>Terakhir Update</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
            <td><?= $row['tgl_input']; ?></td>
            <td><?= $row['tgl_update']; ?></td>
            <td>
              <a href="kategori_edit.php?id=<?= $row['id_kategori']; ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="kategori_hapus.php?id=<?= $row['id_kategori']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
