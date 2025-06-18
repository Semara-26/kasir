<?php
session_start();
include 'koneksi.php';


$id_toko = $_SESSION['id_toko'];

// Ambil data kategori untuk ditampilkan di dropdown
$kategori_query = mysqli_query($conn, "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC");

$error = ''; // Inisialisasi variabel error di luar blok if

// Proses form HANYA jika tombol 'submit' ditekan
if (isset($_POST['submit'])) {
    
    // PENTING: Semua baris yang menggunakan $_POST ada DI DALAM blok if ini.
    // Inilah yang mencegah munculnya warning saat halaman pertama kali dibuka.
    $id_kategori = $_POST['id_kategori'];
    $nama_barang = $_POST['nama_barang'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $satuan_barang = $_POST['satuan_barang'];
    $stok_awal = $_POST['stok_awal'];

    // Validasi dasar
    if (empty($id_kategori) || empty($nama_barang) || empty($harga_jual) || empty($harga_beli) || empty($satuan_barang)) {
        $error = "Semua field wajib diisi.";
    } else {
        // Gunakan Prepared Statement untuk INSERT ke tabel 'barang'
        $stmt_barang = mysqli_prepare($conn, "INSERT INTO barang (id_kategori, nama_barang, harga_beli, harga_jual, satuan_barang) VALUES (?, ?, ?, ?, ?)");
        // Tipe data: i = integer, s = string, d = double/decimal
        mysqli_stmt_bind_param($stmt_barang, "isdds", $id_kategori, $nama_barang, $harga_beli, $harga_jual, $satuan_barang);
        
        if (mysqli_stmt_execute($stmt_barang)) {
            $id_barang_baru = mysqli_insert_id($conn);

            // Gunakan Prepared Statement untuk INSERT ke tabel 'stoktoko'
            $stmt_stok = mysqli_prepare($conn, "INSERT INTO stoktoko (id_toko, id_barang, jumlah_stok) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_stok, "iii", $id_toko, $id_barang_baru, $stok_awal);

            if (mysqli_stmt_execute($stmt_stok)) {
                // Set pesan sukses di session untuk ditampilkan di halaman barang.php
                $_SESSION['pesan_sukses'] = "Barang baru berhasil ditambahkan.";
                header("Location: barang.php");
                exit;
            } else {
                $error = "Gagal menambahkan data stok. Error: " . mysqli_stmt_error($stmt_stok);
            }
            mysqli_stmt_close($stmt_stok);

        } else {
            $error = "Gagal menambahkan data barang. Error: " . mysqli_stmt_error($stmt_barang);
        }
        mysqli_stmt_close($stmt_barang);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Tambah Barang Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h2>Tambah Barang Baru (Toko ID: <?= $id_toko ?>)</h2>

    <?php if (!empty($error)) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="id_kategori" class="form-label">Kategori Barang</label>
            <select class="form-select" id="id_kategori" name="id_kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while($kategori = mysqli_fetch_assoc($kategori_query)): ?>
                    <option value="<?= $kategori['id_kategori'] ?>"><?= htmlspecialchars($kategori['nama_kategori']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
        </div>

         <div class="row">
            <div class="col-md-6 mb-3">
                <label for="harga_beli" class="form-label">Harga Beli (Rp)</label>
                <input type="number" class="form-control" id="harga_beli" name="harga_beli" required min="0" step="0.01">
            </div>
            <div class="col-md-6 mb-3">
                <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
                <input type="number" class="form-control" id="harga_jual" name="harga_jual" required min="0" step="0.01">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="satuan_barang" class="form-label">Satuan</label>
                <input type="text" class="form-control" id="satuan_barang" name="satuan_barang" placeholder="Contoh: pcs, kg, botol" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="stok_awal" class="form-label">Stok Awal</label>
                <input type="number" class="form-control" id="stok_awal" name="stok_awal" required min="0">
            </div>
        </div>

        <button type="submit" name="submit" class="btn btn-success">Tambah Barang</button>
        <a href="barang.php" class="btn btn-secondary ms-2">Kembali</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>