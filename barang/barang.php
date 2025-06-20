<?php
session_start();

// Cek role
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['admin', 'manajer', 'kasir'])) {
    header("Location: ../dashboard.php");
    exit;
}

include '../config/koneksi.php';

$role = $_SESSION['role'];
$id_toko_session = $_SESSION['id_toko'] ?? 0;

// Ambil filter toko dari GET (hanya untuk admin/manajer)
$filter_toko_id = isset($_GET['toko']) ? intval($_GET['toko']) : 0;

// Logika penentuan id_toko
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

// PERBAIKAN: Menggunakan Prepared Statement untuk keamanan
$stmt = mysqli_prepare($conn, "SELECT b.id_barang, b.nama_barang, b.harga_jual, s.jumlah_stok 
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
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Daftar Barang & Stok</h2>
        <a href="../dashboard.php" class="btn btn-outline-secondary">← Kembali ke Dashboard</a>
    </div>

    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="alert alert-<?= $_SESSION['pesan']['tipe']; ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['pesan']['teks']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['pesan']); ?>
    <?php endif; ?>

    <?php if ($role !== 'kasir'): ?>
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

    <table class="table table-striped table-bordered align-middle">
        </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>