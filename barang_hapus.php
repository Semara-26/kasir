<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna sudah login dan memiliki akses yang sesuai
if (!isset($_SESSION['id_toko']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id_toko = (int) $_SESSION['id_toko'];
$id_barang = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Jika ID barang tidak valid, kembali ke halaman barang
if ($id_barang <= 0) {
    header("Location: barang.php");
    exit;
}

// Hapus stok barang dari tabel stoktoko untuk toko terkait
$hapus_stok = mysqli_query($conn, "DELETE FROM stoktoko WHERE id_toko = $id_toko AND id_barang = $id_barang");

// Hapus data barang dari tabel barang
$hapus_barang = mysqli_query($conn, "DELETE FROM barang WHERE id_barang = $id_barang");

// Jika keduanya berhasil, arahkan kembali ke halaman barang
if ($hapus_stok && $hapus_barang) {
    header("Location: barang.php");
    exit;
} else {
    echo "Gagal menghapus data barang.";
    // Tambahkan log atau pesan error detail jika dibutuhkan
}
?>
