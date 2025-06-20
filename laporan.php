<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manajer') {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';

$filter_tampilan = isset($_GET['mode']) ? $_GET['mode'] : 'semua'; // 'harian', 'bulanan', 'semua'
$filter_harian = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$filter_bulanan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Query laporan harian
$q_harian = mysqli_query($conn, "
SELECT 
  t.id_transaksi,
  t.waktu_transaksi,
  u.nama_lengkap AS kasir,
  b.nama_barang,
  dt.harga_saat_transaksi,
  dt.jumlah,
  dt.sub_total
FROM transaksi t
JOIN detailtransaksi dt ON dt.id_transaksi = t.id_transaksi
JOIN pengguna u ON t.id_pengguna = u.id_pengguna
JOIN barang b ON dt.id_barang = b.id_barang
WHERE DATE(t.waktu_transaksi) = '$filter_harian'
ORDER BY t.waktu_transaksi ASC
");

// Query laporan bulanan
$q_bulanan = mysqli_query($conn, "
SELECT 
  DATE(t.waktu_transaksi) AS tanggal,
  COUNT(DISTINCT t.id_transaksi) AS jumlah_transaksi,
  SUM(t.total) AS total_penjualan
FROM transaksi t
WHERE DATE_FORMAT(t.waktu_transaksi, '%Y-%m') = '$filter_bulanan'
GROUP BY DATE(t.waktu_transaksi)
ORDER BY tanggal ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    h2, h4 { color: #343a40; }
    .table thead { background: #e9ecef; }
    .card { margin-bottom: 2rem; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Laporan Penjualan</h2>
    <a href="dashboard.php" class="btn btn-outline-secondary">← Kembali ke Dashboard</a>
  </div>

  <form class="row g-3 mb-4" method="GET">
    <div class="col-md-2">
      <label class="form-label">Mode Tampilan</label>
      <select name="mode" class="form-select">
        <option value="semua" <?= $filter_tampilan == 'semua' ? 'selected' : '' ?>>Harian & Bulanan</option>
        <option value="harian" <?= $filter_tampilan == 'harian' ? 'selected' : '' ?>>Harian Saja</option>
        <option value="bulanan" <?= $filter_tampilan == 'bulanan' ? 'selected' : '' ?>>Bulanan Saja</option>
      </select>
    </div>
    <div class="col-md-3">
      <label for="tanggal" class="form-label">Tanggal Harian</label>
      <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= $filter_harian ?>">
    </div>
    <div class="col-md-3">
      <label for="bulan" class="form-label">Bulan</label>
      <input type="month" name="bulan" id="bulan" class="form-control" value="<?= $filter_bulanan ?>">
    </div>
    <div class="col-md-2 align-self-end">
      <button class="btn btn-primary w-100">Terapkan Filter</button>
    </div>
  </form>

  <?php if ($filter_tampilan !== 'bulanan'): ?>
  <div class="card">
    <div class="card-header bg-primary text-white">
      Laporan Harian – <?= $filter_harian ?>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Waktu</th><th>Transaksi</th><th>Kasir</th><th>Barang</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($q_harian)): ?>
          <tr>
            <td><?= $row['waktu_transaksi'] ?></td>
            <td><span class="badge bg-secondary"><?= $row['id_transaksi'] ?></span></td>
            <td><?= $row['kasir'] ?></td>
            <td><?= $row['nama_barang'] ?></td>
            <td>Rp <?= number_format($row['harga_saat_transaksi'], 0, ',', '.') ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td>Rp <?= number_format($row['sub_total'], 0, ',', '.') ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($filter_tampilan !== 'harian'): ?>
  <div class="card">
    <div class="card-header bg-success text-white">
      Laporan Bulanan – <?= $filter_bulanan ?>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Tanggal</th><th>Jumlah Transaksi</th><th>Total Penjualan</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($q_bulanan)): ?>
          <tr>
            <td><?= $row['tanggal'] ?></td>
            <td><?= $row['jumlah_transaksi'] ?></td>
            <td>Rp <?= number_format($row['total_penjualan'], 0, ',', '.') ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
