<?php
session_start();
include '../config/koneksi.php';

// Pengecekan Akses
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['admin', 'manajer', 'kasir'])) {
    header("Location: login.php");
    exit;
}

// Mengambil semua data member, diurutkan berdasarkan nama
$query = mysqli_query($conn, "SELECT * FROM member ORDER BY nama_member ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Member - POS Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Kelola Member</h2>
        <div>
            <a href="../dashboard.php" class="btn btn-outline-secondary">← Kembali</a>
            <a href="member_tambah.php" class="btn btn-primary ms-2">+ Tambah Member</a>
        </div>
    </div>

    <?php if (isset($_SESSION['pesan_sukses'])): ?>
        <div class="alert alert-success"><?= $_SESSION['pesan_sukses']; ?></div>
        <?php unset($_SESSION['pesan_sukses']); ?>
    <?php endif; ?>
     <?php if (isset($_SESSION['pesan_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['pesan_error']; ?></div>
        <?php unset($_SESSION['pesan_error']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Member</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Tgl Registrasi</th>
                        <th style="width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama_member']); ?></td>
                            <td><?= htmlspecialchars($row['alamat_member']); ?></td>
                            <td><?= htmlspecialchars($row['telepon'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
                            <td><?= date('d M Y, H:i', strtotime($row['tgl_registrasi'])); ?></td>
                            <td>
                                <a href="member_edit.php?id=<?= $row['id_member']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="member_hapus.php?id=<?= $row['id_member']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus member ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">Belum ada data member.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>