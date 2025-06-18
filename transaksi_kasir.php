<?php
session_start();
include 'koneksi.php';

// Tentukan besaran diskon dalam persen (misal: 10% untuk semua member)
define('DISKON_MEMBER_PERSEN', 10);

// Pengecekan Akses
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['admin', 'manajer', 'kasir'])) {
    header("Location: login.php");
    exit;
}

$id_toko_session = $_SESSION['id_toko'];

// --- LOGIKA KERANJANG BELANJA (SESSION) ---

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// 1. TAMBAH BARANG KE KERANJANG
if (isset($_GET['tambah_id'])) {
    $id_barang = $_GET['tambah_id'];
    $barang_query = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = $id_barang");
    $barang = mysqli_fetch_assoc($barang_query);

    if ($barang) {
        if (isset($_SESSION['keranjang'][$id_barang])) {
            $_SESSION['keranjang'][$id_barang]['jumlah']++; // Jika sudah ada, tambah jumlahnya
        } else {
            // Jika belum ada, tambahkan sebagai item baru
            $_SESSION['keranjang'][$id_barang] = [
                'nama_barang' => $barang['nama_barang'],
                'harga_jual' => $barang['harga_jual'],
                'jumlah' => 1
            ];
        }
    }
    // Redirect untuk membersihkan parameter GET dari URL
    header('Location: transaksi_kasir.php');
    exit;
}

// 2. UPDATE JUMLAH BARANG DI KERANJANG
if (isset($_POST['update_keranjang'])) {
    foreach ($_POST['jumlah'] as $id_barang => $jumlah) {
        if ($jumlah > 0) {
            $_SESSION['keranjang'][$id_barang]['jumlah'] = $jumlah;
        } else {
            unset($_SESSION['keranjang'][$id_barang]); // Hapus jika jumlah 0 atau kurang
        }
    }
    header('Location: transaksi_kasir.php');
    exit;
}

// 3. HAPUS ITEM DARI KERANJANG
if (isset($_GET['hapus_id'])) {
    $id_barang = $_GET['hapus_id'];
    unset($_SESSION['keranjang'][$id_barang]);
    header('Location: transaksi_kasir.php');
    exit;
}

// 4. KOSONGKAN KERANJANG
if (isset($_GET['kosongkan'])) {
    $_SESSION['keranjang'] = [];
    header('Location: transaksi_kasir.php');
    exit;
}

// --- Mengambil Data Produk untuk Ditampilkan ---
$produk_query = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid p-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>
    <div class="row">
        <div class="col-md-7">
            <h3>Daftar Produk</h3>
            <div class="card shadow-sm">
                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($produk = mysqli_fetch_assoc($produk_query)): ?>
                            <tr>
                                <td><?= htmlspecialchars($produk['nama_barang']) ?></td>
                                <td>Rp <?= number_format($produk['harga_jual'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="transaksi_kasir.php?tambah_id=<?= $produk['id_barang'] ?>" class="btn btn-sm btn-success">+ Keranjang</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <h3>Keranjang Belanja</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="transaksi_kasir.php">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th style="width: 100px;">Jumlah</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_semua = 0; ?>
                                <?php if (!empty($_SESSION['keranjang'])): ?>
                                    <?php foreach ($_SESSION['keranjang'] as $id_barang => $item): ?>
                                    <?php
                                        $sub_total = $item['harga_jual'] * $item['jumlah'];
                                        $total_semua += $sub_total;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                        <td><input type="number" name="jumlah[<?= $id_barang ?>]" value="<?= $item['jumlah'] ?>" class="form-control form-control-sm" min="1"></td>
                                        <td>Rp <?= number_format($sub_total, 0, ',', '.') ?></td>
                                        <td><a href="transaksi_kasir.php?hapus_id=<?= $id_barang ?>" class="btn btn-sm btn-outline-danger">×</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">Keranjang kosong</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <button type="submit" name="update_keranjang" class="btn btn-sm btn-secondary">Update Keranjang</button>
                        <a href="transaksi_kasir.php?kosongkan=1" class="btn btn-sm btn-danger">Kosongkan</a>
                    </form>
                    <hr>
                    <hr>
        <h4 id="label-total-semua">Total: Rp <span id="nilai-total-semua"><?= number_format($total_semua, 0, ',', '.') ?></span></h4
<h4 id="total-setelah-diskon" class="text-success" style="display:none;"></h4>

<form action="proses_checkout.php" method="POST">
    <input type="hidden" id="total_asli" value="<?= $total_semua ?>">
    <div class="mb-3">
        <label class="form-label">Bayar (Rp)</label>
        <input type="number" name="jumlah_bayar" class="form-control" required min="<?= $total_semua ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Member (Opsional)</label>
         <select name="id_member" id="pilih-member" class="form-select">
            <option value="">-- Non-Member --</option>
            <?php
            $member_query = mysqli_query($conn, "SELECT id_member, nama_member FROM member ORDER BY nama_member ASC");
            while($member = mysqli_fetch_assoc($member_query)){
                echo "<option value='{$member['id_member']}'>".htmlspecialchars($member['nama_member'])."</option>";
            }
            ?>
        </select>
    </div>
    <div class="d-grid">
       <button type="submit" name="checkout" class="btn btn-lg btn-primary" <?= empty($_SESSION['keranjang']) ? 'disabled' : '' ?>>PROSES PEMBAYARAN</button>
    </div>
</form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('pilih-member').addEventListener('change', function() {
        const totalAsli = parseFloat(document.getElementById('total_asli').value);
        const diskonPersen = <?= DISKON_MEMBER_PERSEN ?>;
        const elTotalDiskon = document.getElementById('total-setelah-diskon');
        const elLabelTotal = document.getElementById('label-total-semua');

        if (this.value !== '') { // Jika seorang member dipilih
            const besarDiskon = totalAsli * (diskonPersen / 100);
            const totalBaru = totalAsli - besarDiskon;

            // Tampilkan harga baru
            elTotalDiskon.innerHTML = `Diskon Member: <strong>Rp ${totalBaru.toLocaleString('id-ID')}</strong>`;
            elTotalDiskon.style.display = 'block';
            elLabelTotal.style.textDecoration = 'line-through'; // Coret harga lama

            // Update minimum pembayaran
            document.querySelector('input[name="jumlah_bayar"]').min = totalBaru;

        } else { // Jika kembali ke Non-Member
            elTotalDiskon.style.display = 'none';
            elLabelTotal.style.textDecoration = 'none';
            document.querySelector('input[name="jumlah_bayar"]').min = totalAsli;
        }
    });
</script>
</body>
</html>
</body>
</html>