<?php
session_start();
include 'config/koneksi.php';

// Ambil role & id toko dari session lebih awal
$user_role = $_SESSION['role'];
$user_id_toko = isset($_SESSION['id_toko']) ? intval($_SESSION['id_toko']) : 0;

// Pengecekan Akses
if (!isset($user_role) || !in_array($user_role, ['admin', 'manajer', 'kasir'])) {
    header("Location: login.php");
    exit;
}

// Ambil filter dari GET
$filter_toko_id = isset($_GET['filter_toko_id']) ? intval($_GET['filter_toko_id']) : 0;
$filter_nama_barang = isset($_GET['filter_nama_barang']) ? $_GET['filter_nama_barang'] : '';

// Paksa filter toko untuk kasir
if ($user_role === 'kasir') {
    $filter_toko_id = $user_id_toko;
}

// --- Data Toko (Versi Aman) ---
$sql_query_toko = "SELECT id_toko, nama_toko FROM toko";
$params_toko = [];
$types_toko = '';
if ($user_role === 'kasir') {
    $sql_query_toko .= " WHERE id_toko = ?";
    $params_toko[] = $user_id_toko;
    $types_toko .= 'i';
}
$sql_query_toko .= " ORDER BY nama_toko ASC";
$stmt_toko = mysqli_prepare($conn, $sql_query_toko);
if ($user_role === 'kasir') {
    mysqli_stmt_bind_param($stmt_toko, $types_toko, ...$params_toko);
}
mysqli_stmt_execute($stmt_toko);
$query_toko = mysqli_stmt_get_result($stmt_toko);


// --- Query Data Stok Barang (Versi Aman dengan Prepared Statement) ---
$sql_stok = "
    SELECT b.id_barang, b.nama_barang, t.nama_toko, st.jumlah_stok, st.tgl_update_stok as tanggal_update_stok_terakhir
    FROM barang b
    JOIN stoktoko st ON b.id_barang = st.id_barang
    JOIN toko t ON st.id_toko = t.id_toko
";
$conditions = [];
$params = [];
$types = '';

if ($filter_toko_id > 0) {
    $conditions[] = "st.id_toko = ?";
    $params[] = $filter_toko_id;
    $types .= 'i';
}
if (!empty($filter_nama_barang)) {
    $conditions[] = "b.nama_barang LIKE ?";
    $params[] = "%" . $filter_nama_barang . "%";
    $types .= 's';
}

if (count($conditions) > 0) {
    $sql_stok .= " WHERE " . implode(" AND ", $conditions);
}
$sql_stok .= " ORDER BY b.nama_barang ASC, t.nama_toko ASC";

$stmt_stok = mysqli_prepare($conn, $sql_stok);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt_stok, $types, ...$params);
}
mysqli_stmt_execute($stmt_stok);
$result_stok = mysqli_stmt_get_result($stmt_stok);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Stok Barang Per Toko</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Data Stok Barang Per Toko</h2>
            <a href="dashboard.php" class="btn btn-outline-secondary">← Kembali ke Dashboard</a>
        </div>
        <div class="mb-2">
            <span class="badge bg-danger">Stok &lt; 10</span>
            <span class="badge bg-warning text-dark">Stok &lt; 20</span>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_SESSION['pesan']['tipe']); ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['pesan']['teks']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="stok_toko.php" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="filter_toko_id" class="form-label">Filter Toko:</label>
                        <select name="filter_toko_id" id="filter_toko_id" class="form-select" <?= ($user_role === 'kasir') ? 'disabled' : '' ?> >
                            <?php if ($user_role !== 'kasir'): ?>
                                <option value="0">-- Semua Toko --</option>
                            <?php endif; ?>
                            <?php while ($toko = mysqli_fetch_assoc($query_toko)): ?>
                                <option value="<?= $toko['id_toko'] ?>" <?= ($filter_toko_id == $toko['id_toko']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($toko['nama_toko']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if ($user_role === 'kasir'): ?>
                            <input type="hidden" name="filter_toko_id" value="<?= $user_id_toko ?>">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label for="filter_nama_barang" class="form-label">Filter Nama Barang:</label>
                        <input type="text" name="filter_nama_barang" id="filter_nama_barang" class="form-control" placeholder="Cari Nama Barang..." value="<?= htmlspecialchars($filter_nama_barang) ?>">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Barang</th>
                            <th>Nama Barang</th>
                            <th>Nama Toko</th>
                            <th>Jumlah Stok</th>
                            <th>Update Terakhir</th>
                            <?php if (in_array($user_role, ['admin', 'manajer'])): ?>
                                <th style="width:150px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_stok) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result_stok)): ?>
                                <?php
                                    $jumlah_stok = intval($row['jumlah_stok']);
                                    $row_class = '';
                                    if ($jumlah_stok < 10) { $row_class = 'table-danger'; } 
                                    elseif ($jumlah_stok < 20) { $row_class = 'table-warning'; }
                                ?>
                                <tr class="<?= $row_class ?>">
                                    <td><?= $row['id_barang'] ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_toko']) ?></td>
                                    <td><?= $jumlah_stok ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_update_stok_terakhir'] ?? 'N/A') ?></td>
                                    <?php if (in_array($user_role, ['admin', 'manajer'])): ?>
                                    <td>
                                       <a href="barang/barang_edit.php?id=<?= $row['id_barang'] ?>&from=stok_toko" class="btn btn-sm btn-warning">Edit</a>
                                       <a href="barang/barang_hapus.php?id=<?= $row['id_barang'] ?>&from=stok_toko" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="<?= (in_array($user_role, ['admin', 'manajer'])) ? '6' : '5' ?>" class="text-center">Tidak ada data stok ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>