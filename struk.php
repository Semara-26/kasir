<?php
session_start();
include 'koneksi.php';

// Pengecekan Akses
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID transaksi dari URL
$id_transaksi = $_GET['id'] ?? 0;
if (!$id_transaksi) {
    die("ID Transaksi tidak valid.");
}

// Query untuk mengambil data header transaksi (JOIN dengan pengguna, member, toko)
$stmt_header = mysqli_prepare($conn, 
    "SELECT t.*, p.nama_lengkap as nama_kasir, m.nama_member, tk.nama_toko, tk.alamat_toko 
     FROM transaksi t
     JOIN pengguna p ON t.id_pengguna = p.id_pengguna
     LEFT JOIN member m ON t.id_member = m.id_member
     JOIN toko tk ON t.id_toko = tk.id_toko
     WHERE t.id_transaksi = ?");
mysqli_stmt_bind_param($stmt_header, "i", $id_transaksi);
mysqli_stmt_execute($stmt_header);
$result_header = mysqli_stmt_get_result($stmt_header);
$header = mysqli_fetch_assoc($result_header);

if (!$header) {
    die("Data transaksi tidak ditemukan.");
}

// Query untuk mengambil data detail transaksi (JOIN dengan barang)
$stmt_detail = mysqli_prepare($conn, 
    "SELECT dt.*, b.nama_barang 
     FROM detailtransaksi dt 
     JOIN barang b ON dt.id_barang = b.id_barang 
     WHERE dt.id_transaksi = ?");
mysqli_stmt_bind_param($stmt_detail, "i", $id_transaksi);
mysqli_stmt_execute($stmt_detail);
$result_detail = mysqli_stmt_get_result($stmt_detail);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi #<?= $header['id_transaksi'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e9ecef; }
        .receipt-container { max-width: 450px; background-color: white; border: 1px dashed #ccc; }
        .receipt-header { text-align: center; border-bottom: 1px dashed #ccc; }
        .receipt-body table { width: 100%; }
        .receipt-body th, .receipt-body td { padding: 2px 5px; }
        .text-end { text-align: right; }
        .totals-table td:first-child { font-weight: bold; }
        @media print {
            body { background-color: white; }
            .no-print { display: none; }
            .receipt-container { margin: 0; max-width: 100%; border: none; }
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="no-print text-center mb-3">
        <a href="transaksi_kasir.php" class="btn btn-success">Buat Transaksi Baru</a>
        <button onclick="window.print()" class="btn btn-primary">Cetak Struk</button>
    </div>

    <div class="receipt-container mx-auto p-3">
        <div class="receipt-header pb-2 mb-2">
            <h4 class="mb-0"><?= htmlspecialchars($header['nama_toko']) ?></h4>
            <p class="mb-0 small"><?= htmlspecialchars($header['alamat_toko']) ?></p>
        </div>

        <div class="receipt-info mb-2">
            <table class="small">
                <tr>
                    <td>No. Transaksi</td>
                    <td>: <?= $header['id_transaksi'] ?></td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: <?= date('d/m/Y H:i:s', strtotime($header['waktu_transaksi'])) ?></td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td>: <?= htmlspecialchars($header['nama_kasir']) ?></td>
                </tr>
                <?php if ($header['nama_member']): ?>
                <tr>
                    <td>Member</td>
                    <td>: <?= htmlspecialchars($header['nama_member']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="receipt-body mb-2">
            <table class="table-sm">
                <thead style="border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc;">
                    <tr>
                        <th>Barang</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Jml</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = mysqli_fetch_assoc($result_detail)): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                        <td class="text-end"><?= number_format($item['harga_saat_transaksi'], 0, ',', '.') ?></td>
                        <td class="text-end"><?= $item['jumlah'] ?></td>
                        <td class="text-end"><?= number_format($item['sub_total'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="receipt-footer">
        <table class="table-sm totals-table" style="border-top: 1px dashed #ccc;">
    <?php
        // Logika untuk menampilkan subtotal dan diskon jika ada member
        $subtotal = 0;
        mysqli_data_seek($result_detail, 0); // Reset pointer result detail
        while($item = mysqli_fetch_assoc($result_detail)){
            $subtotal += $item['sub_total'];
        }

        if ($header['id_member'] && $subtotal > $header['total']) {
            $diskon = $subtotal - $header['total'];
            echo "<tr><td>Subtotal</td><td class='text-end'>Rp ".number_format($subtotal, 0, ',', '.')."</td></tr>";
            echo "<tr><td>Diskon Member</td><td class='text-end'>- Rp ".number_format($diskon, 0, ',', '.')."</td></tr>";
        }
    ?>
    <tr style="border-top: 1px solid #eee;">
        <td><strong>Total Belanja</strong></td>
        <td class="text-end"><strong>Rp <?= number_format($header['total'], 0, ',', '.') ?></strong></td>
    </tr>
    <tr>
        <td>Jumlah Bayar</td>
        <td class="text-end">Rp <?= number_format($header['jumlah_bayar'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td><strong>Kembalian</strong></td>
        <td class="text-end"><strong>Rp <?= number_format($header['kembalian'], 0, ',', '.') ?></strong></td>
    </tr>
</table>
            
            <div class="text-center small mt-3">
                <p>--- Terima Kasih ---</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>