<?php
session_start();
include 'koneksi.php';

// Cek role admin atau manajer
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: login.php");
    exit;
}

// Ambil data toko
$toko = [];
$result = mysqli_query($conn, "SELECT * FROM toko");
while ($row = mysqli_fetch_assoc($result)) {
    $toko[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Toko</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">Data Toko</h2>
      <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <a href="tambah_toko.php" class="btn btn-primary mb-3">+ Tambah Toko</a>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama Toko</th>
          <th>Alamat</th>
          <th>Telepon</th>
          <th>Nama Pemilik</th>
          <th>Gambar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($toko) > 0): ?>
          <?php foreach ($toko as $i => $row): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= htmlspecialchars($row['nama_toko']) ?></td>
              <td><?= htmlspecialchars($row['alamat_toko']) ?></td>
              <td><?= htmlspecialchars($row['tlp']) ?></td>
              <td><?= htmlspecialchars($row['nama_pemilik']) ?></td>
              <td><img src="gambar/<?= $row['image'] ?>" width="80" alt="gambar toko"></td>
              <td>
                <a href="edit_toko.php?id=<?= $row['id_toko'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="hapus_toko.php?id=<?= $row['id_toko'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus toko ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center">Belum ada data toko.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
