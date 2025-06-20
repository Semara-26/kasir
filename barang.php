<?php
session_start();

// Cek role
if (!in_array($_SESSION['role'], ['admin', 'manajer', 'kasir'])) {
    header("Location: dashboard.php");
    exit;
}

include 'koneksi.php';


$id_toko = $_SESSION['id_toko'];

$role = $_SESSION['role'];
$id_toko_session = $_SESSION['id_toko'] ?? null;


// Ambil filter toko dari GET (hanya untuk admin/manajer)
$filter_toko_id = isset($_GET['toko']) ? intval($_GET['toko']) : 0;

if ($role === 'kasir') {
    $id_toko = $id_toko_session;
} else {
    $id_toko = ($filter_toko_id > 0) ? $filter_toko_id : 1;
}

// Ambil daftar toko untuk dropdown (admin/manajer)
$toko_list = [];
if ($role !== 'kasir') {
    $toko_result = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko ORDER BY nama_toko ASC");
    while ($row = mysqli_fetch_assoc($toko_result)) {
        $toko_list[] = $row;
    }
}

// Ambil data barang & stok untuk toko terpilih
$query = mysqli_query($conn, "SELECT b.id_barang, b.nama_barang, b.harga_jual, s.jumlah_stok 
    FROM barang b
    LEFT JOIN stoktoko s ON b.id_barang = s.id_barang AND s.id_toko = $id_toko
    ORDER BY b.nama_barang ASC");
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Daftar Barang & Stok (Toko ID: <?= $id_toko ?>)</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">← Kembali ke Dashboard</a>
    </div>

    <!-- Filter Toko (admin/manajer) -->
    <?php if ($role !== 'kasir'): ?>
        <form method="GET" class="mb-3">
            <label for="toko">Pilih Toko:</label>
            <select name="toko" id="toko" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
                <?php foreach ($toko_list as $toko): ?>
                    <option value="<?= $toko['id_toko'] ?>" <?= ($toko['id_toko'] == $id_toko) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($toko['nama_toko']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    <?php endif; ?>

    <!-- Notifikasi -->
    <?php if (isset($_SESSION['pesan_sukses_barang'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['pesan_sukses_barang']; ?>
        </div>
        <?php unset($_SESSION['pesan_sukses_barang']); ?>
    <?php endif; ?>

    <a href="barang_tambah.php" class="btn btn-primary mb-3">Tambah Barang Baru</a>
    <div class="mb-2">
        <span class="badge bg-danger">Stok < 10</span>
        <span class="badge bg-warning text-dark">Stok < 20</span>
    </div>

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
