<?php
session_start();

// Cek role, halaman ini bisa diakses oleh semua peran yang login
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['admin', 'manajer', 'kasir'])) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

$role = $_SESSION['role'];
$id_toko_session = $_SESSION['id_toko'] ?? 0;

// Ambil filter toko dari GET (hanya untuk admin/manajer)
$filter_toko_id = isset($_GET['toko']) ? intval($_GET['toko']) : 0;

// Logika penentuan id_toko yang akan ditampilkan
if ($role === 'kasir') {
    $id_toko = $id_toko_session;
} else {
    // Jika admin/manajer memfilter, gunakan filter. Jika tidak, default ke toko pertama.
    if ($filter_toko_id > 0) {
        $id_toko = $filter_toko_id;
    } else {
        // Ambil id toko pertama sebagai default jika tidak ada filter
        $first_toko_query = mysqli_query($conn, "SELECT id_toko FROM toko ORDER BY id_toko ASC LIMIT 1");
        $first_toko = mysqli_fetch_assoc($first_toko_query);
        $id_toko = $first_toko['id_toko'] ?? 0;
    }
}

// Ambil daftar toko untuk dropdown (hanya untuk admin/manajer)
$toko_list = [];
if ($role !== 'kasir') {
    $toko_result = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko ORDER BY nama_toko ASC");
    while ($row = mysqli_fetch_assoc($toko_result)) {
        $toko_list[] = $row;
    }
}

// Menggunakan Prepared Statement untuk mengambil data barang & stok
$stmt = mysqli_prepare($conn, 
    "SELECT b.id_barang, b.nama_barang, b.harga_jual, s.jumlah_stok 
    FROM barang b
    LEFT JOIN stoktoko s ON b.id_barang = s.id_barang AND s.id_toko = ?
    ORDER BY b.nama_barang ASC");

mysqli_stmt_bind_param($stmt, "i", $id_toko);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Daftar Barang & Stok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Daftar Barang & Stok</h2>
        <a href="../dashboard.php" class="btn btn-outline-secondary">← Kembali ke Dashboard</a>
    </div>

    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['pesan']['tipe']); ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['pesan']['teks']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['pesan']); ?>
    <?php endif; ?>

    <?php if (in_array($role, ['admin', 'manajer'])): ?>
        <form method="GET" class="mb-3">
            <div class="input-group">
                <label class="input-group-text" for="toko">Tampilkan Stok untuk Toko:</label>
                <select name="toko" id="toko" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($toko_list as $toko_item): ?>
                        <option value="<?= $toko_item['id_toko'] ?>" <?= ($toko_item['id_toko'] == $id_toko) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($toko_item['nama_toko']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    <?php endif; ?>

    <?php if (in_array($role, ['admin', 'manajer'])): ?>
        <a href="barang_tambah.php" class="btn btn-primary mb-3">Tambah Barang Baru</a>
    <?php endif; ?>

    <div class="mb-2">
        <span class="badge bg-danger">Stok &lt; 10</span>
        <span class="badge bg-warning text-dark">Stok &lt; 20</span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Harga Jual (Rp)</th>
                        <th>Jumlah Stok</th>
                        <?php if (in_array($role, ['admin', 'manajer'])): ?>
                        <th style="width:150px;">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                            <?php
                                $jumlah_stok = intval($row['jumlah_stok'] ?? 0);
                                $row_class = '';
                                if ($jumlah_stok < 10) {
                                    $row_class = 'table-danger';
                                } elseif ($jumlah_stok < 20) {
                                    $row_class = 'table-warning';
                                }
                            ?>
                            <tr class="<?= $row_class ?>">
                                <td><?= $row['id_barang'] ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                                <td><?= $jumlah_stok ?></td>
                                
                                <?php if (in_array($role, ['admin', 'manajer'])): ?>
                                <td>
                                    <a href="barang_edit.php?id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="barang_hapus.php?id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</a>                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="<?= in_array($role, ['admin', 'manajer']) ? '5' : '4' ?>" class="text-center">Belum ada data barang untuk toko ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>