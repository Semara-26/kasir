<?php
session_start();

// Cek role
if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: dashboard.php");
    exit;
}

include 'koneksi.php';

$id_toko = 1;

$query = mysqli_query($conn, "SELECT b.id_barang, b.nama_barang, b.harga_jual, s.jumlah_stok 
                             FROM barang b
                             LEFT JOIN stoktoko s ON b.id_barang = s.id_barang AND s.id_toko = $id_toko");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Daftar Barang & Stok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Daftar Barang & Stok (Toko ID: <?= $id_toko ?>)</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">← Kembali ke Dashboard</a>
    </div>

    <!-- Notifikasi sukses -->
    <?php if (isset($_SESSION['pesan_sukses_barang'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['pesan_sukses_barang']; ?>
        </div>
        <?php unset($_SESSION['pesan_sukses_barang']); ?>
    <?php endif; ?>

    <a href="barang_tambah.php" class="btn btn-primary mb-3">Tambah Barang Baru</a>

    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Harga Jual (Rp)</th>
                <th>Jumlah Stok</th>
                <th style="width:150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($query) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($query)) : ?>
                <tr>
                    <td><?= $row['id_barang'] ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                    <td><?= $row['jumlah_stok'] ?? 0 ?></td>
                    <td>
                        <a href="barang_edit.php?id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="barang_hapus.php?id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Belum ada data barang.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
