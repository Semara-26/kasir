<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama_lengkap'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - POS Kasir</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .dashboard-card {
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform .2s;
    }
    .dashboard-card:hover {
      transform: translateY(-3px);
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="text-center mb-4">
      <h1 class="fw-bold">Dashboard</h1>
      <p class="text-muted">Selamat datang, <strong><?= $nama; ?></strong> (<?= $role; ?>)</p>
    </div>

    <div class="row g-4">
      <?php if ($role === 'admin'): ?>
        <div class="col-md-4">
          <div class="card dashboard-card border-primary">
            <div class="card-body text-center">
              <h5 class="card-title text-primary">Kelola Pengguna</h5>
              <p class="card-text">Tambah, ubah, atau hapus pengguna sistem.</p>
              <a href="pengguna.php" class="btn btn-primary">Buka</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card dashboard-card border-secondary">
            <div class="card-body text-center">
              <h5 class="card-title text-secondary">Kategori Barang</h5>
              <p class="card-text">Atur kategori untuk barang dagang.</p>
              <a href="kategori.php" class="btn btn-secondary">Buka</a>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($role === 'kasir'): ?>
        <div class="col-md-4">
          <div class="card dashboard-card border-success">
            <div class="card-body text-center">
              <h5 class="card-title text-success">Transaksi</h5>
              <p class="card-text">Lakukan penjualan dan cetak struk.</p>
              <a href="transaksi.php" class="btn btn-success">Mulai</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card dashboard-card border-warning">
            <div class="card-body text-center">
              <h5 class="card-title text-warning">Data Member</h5>
              <p class="card-text">Kelola member dan data pelanggan.</p>
              <a href="member.php" class="btn btn-warning">Kelola</a>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($role === 'manajer'): ?>
        <div class="col-md-4">
          <div class="card dashboard-card border-info">
            <div class="card-body text-center">
              <h5 class="card-title text-info">Laporan Penjualan</h5>
              <p class="card-text">Lihat rekap penjualan harian dan bulanan.</p>
              <a href="laporan.php" class="btn btn-info">Lihat</a>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($role === 'admin' || $role === 'manajer'): ?>
        <div class="col-md-4">
            <div class="card dashboard-card border-primary">
             <div class="card-body text-center">
                <h5 class="card-title text-primary">Data Barang</h5>
                <p class="card-text">Kelola data barang dagangan.</p>
                <a href="barang.php" class="btn btn-primary">Kelola</a>
             </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="text-center mt-5">
      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
  </div>
</body>
</html>
