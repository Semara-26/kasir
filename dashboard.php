<?php
session_start();
include 'koneksi.php';
$timeout = 300; // 5 menit
$warning_time = 180; // 3 menit

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
$_SESSION['last_activity'] = time();

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
    body { background: #f8f9fa; }
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
        <div class="col-md-4">
          <div class="card dashboard-card border-info">
            <div class="card-body text-center">
              <h5 class="card-title text-info">Stok Toko</h5>
              <p class="card-text">Lihat stok barang di toko kamu.</p>
              <a href="stok_toko.php" class="btn btn-info">Lihat</a>
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
        <div class="col-md-4">
          <div class="card dashboard-card border-dark">
            <div class="card-body text-center">
              <h5 class="card-title text-dark">Data Toko</h5>
              <p class="card-text">Kelola informasi cabang toko.</p>
              <a href="toko.php" class="btn btn-dark">Kelola</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card dashboard-card border-success">
            <div class="card-body text-center">
              <h5 class="card-title text-success">Stok Toko</h5>
              <p class="card-text">Lihat stok per barang di setiap toko.</p>
              <a href="stok_toko.php" class="btn btn-success">Lihat</a>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <div class="text-center mt-5">
      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
  </div>

  <script>
    let warningShown = false;
    let warningTimeout;
    let logoutTimeout;

    function resetTimers() {
      clearTimeout(warningTimeout);
      clearTimeout(logoutTimeout);
      warningShown = false;

      warningTimeout = setTimeout(() => {
        alert("Anda tidak aktif selama 3 menit. Jika tidak ada aktivitas selama 2 menit lagi, Anda akan logout otomatis.");
        warningShown = true;
      }, 600000); // 10 menit

      logoutTimeout = setTimeout(() => {
        window.location.href = "logout.php";
      }, 900000); // 15 menit
    }

    const activityEvents = ['mousemove', 'keydown', 'scroll', 'click'];
    activityEvents.forEach(event => {
      document.addEventListener(event, resetTimers, true);
    });

    resetTimers();
  </script>
</body>
</html>
