<?php
session_start();
include 'koneksi.php';

// --- Pengecekan Akses ---
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- Mengambil Data Pengguna ---
$query = mysqli_query($conn, "
    SELECT p.*, t.nama_toko 
    FROM pengguna p 
    LEFT JOIN toko t ON p.id_toko = t.id_toko 
    ORDER BY p.id_pengguna DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna - POS Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Kelola Pengguna</h2>
        <div>
            <a href="pengguna_tambah.php" class="btn btn-primary me-2">+ Tambah Pengguna</a>
            <a href="dashboard.php" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
        </div>
    </div>

    <?php if (!empty($_SESSION['pesan_sukses'])): ?>
        <div class="alert alert-success"><?= $_SESSION['pesan_sukses']; ?></div>
        <?php unset($_SESSION['pesan_sukses']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['pesan_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['pesan_error']; ?></div>
        <?php unset($_SESSION['pesan_error']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Toko</th>
                        <th>Status</th>
                        <th style="width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><span class="badge bg-secondary"><?= ucfirst($row['role']); ?></span></td>
                            <td><?= htmlspecialchars($row['nama_toko']); ?></td>
                            <td>
                                <span class="badge <?= $row['status_aktif'] == 1 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $row['status_aktif'] == 1 ? 'Aktif' : 'Tidak Aktif'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="pengguna_edit.php?id=<?= $row['id_pengguna']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <?php if ($row['id_pengguna'] != $_SESSION['id_pengguna']): ?>
                                    <a href="pengguna_hapus.php?id=<?= $row['id_pengguna']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data pengguna.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
