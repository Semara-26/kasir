<?php
session_start();
include '../config/koneksi.php';

// Cek hak akses yang sesuai (Admin & Manajer)
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data dari session dan URL
$id_toko = $_SESSION['id_toko'];
$id_barang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$from = $_GET['from'] ?? 'barang.php'; // Tangkap asal halaman, defaultnya 'barang.php'

if ($id_barang <= 0) {
    header("Location: barang.php");
    exit;
}

// --- Ambil data barang yang akan diedit (Versi Aman) ---
$stmt_get = mysqli_prepare($conn, 
    "SELECT b.*, s.jumlah_stok 
     FROM barang b 
     LEFT JOIN stoktoko s ON b.id_barang = s.id_barang AND s.id_toko = ?
     WHERE b.id_barang = ? 
     LIMIT 1");
mysqli_stmt_bind_param($stmt_get, "ii", $id_toko, $id_barang);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$data = mysqli_fetch_assoc($result_get);
mysqli_stmt_close($stmt_get);

if (!$data) {
    die("Data barang tidak ditemukan.");
}

$error = '';
// --- Proses Update Data ---
if (isset($_POST['submit'])) {
    // Ambil semua data dari form
    $nama_barang = $_POST['nama_barang'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $satuan_barang = $_POST['satuan_barang'];
    $stok = $_POST['stok'];
    $from_post = $_POST['from'] ?? 'barang.php';

    // Gunakan Database Transaction untuk memastikan semua update berhasil
    mysqli_begin_transaction($conn);
    try {
        // --- Update tabel barang (Versi Aman) ---
        $stmt_update_barang = mysqli_prepare($conn, "UPDATE barang SET nama_barang=?, harga_beli=?, harga_jual=?, satuan_barang=?, tgl_update=CURRENT_TIMESTAMP WHERE id_barang=?");
        mysqli_stmt_bind_param($stmt_update_barang, "sddsi", $nama_barang, $harga_beli, $harga_jual, $satuan_barang, $id_barang);
        mysqli_stmt_execute($stmt_update_barang);

        // --- Update/Insert tabel stok (Versi Aman) ---
        $stmt_update_stok = mysqli_prepare($conn, "INSERT INTO stoktoko (id_toko, id_barang, jumlah_stok) VALUES (?, ?, ?)
                                                ON DUPLICATE KEY UPDATE jumlah_stok = ?");
        mysqli_stmt_bind_param($stmt_update_stok, "iiii", $id_toko, $id_barang, $stok, $stok);
        mysqli_stmt_execute($stmt_update_stok);

        // Jika semua berhasil, commit perubahan
        mysqli_commit($conn);
        
        // Set pesan sukses
        $_SESSION['pesan'] = ['tipe' => 'success', 'teks' => "Barang ID #{$id_barang} berhasil diperbarui."];

        // --- Perbaikan Path Redirect ---
        $redirect_page = ($from_post === 'stok_toko') ? '../stok_toko.php' : 'barang.php';
        header("Location: $redirect_page");
        exit;

    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);
        $error = "Gagal update data. Error: " . $e->getMessage();
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
<body class="bg-light">
<div class="container mt-4 py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title mb-4">Edit Barang (ID: <?= $id_barang ?>)</h2>

                    <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="barang_edit.php?id=<?= $id_barang ?>&from=<?= urlencode($from) ?>">
                        <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">
                        
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required value="<?= htmlspecialchars($data['nama_barang']) ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga_beli" class="form-label">Harga Beli (Rp)</label>
                                <input type="number" class="form-control" id="harga_beli" name="harga_beli" required min="0" step="0.01" value="<?= $data['harga_beli'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
                                <input type="number" class="form-control" id="harga_jual" name="harga_jual" required min="0" step="0.01" value="<?= $data['harga_jual'] ?>">
                            </div>
                        </div>

                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label for="satuan_barang" class="form-label">Satuan</label>
                                <input type="text" class="form-control" id="satuan_barang" name="satuan_barang" required value="<?= htmlspecialchars($data['satuan_barang']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="stok" class="form-label">Stok di Toko Ini</label>
                                <input type="number" class="form-control" id="stok" name="stok" required min="0" value="<?= $data['jumlah_stok'] ?? 0 ?>">
                            </div>
                        </div>
                        
                        <button type="submit" name="submit" class="btn btn-primary">Update Barang</button>
                        <a href="<?= ($from === 'stok_toko') ? '../stok_toko.php' : 'barang.php' ?>" class="btn btn-secondary ms-2">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>