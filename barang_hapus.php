<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_toko']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id_toko = (int) $_SESSION['id_toko'];
$id_barang = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$from = $_GET['from'] ?? '';

if ($id_barang <= 0) {
    header("Location: barang.php");
    exit;
}

// Proses hapus
$hapus_stok = mysqli_query($conn, "DELETE FROM stoktoko WHERE id_toko = $id_toko AND id_barang = $id_barang");
$hapus_barang = mysqli_query($conn, "DELETE FROM barang WHERE id_barang = $id_barang");

// Simpan notifikasi
if ($hapus_stok && $hapus_barang) {
    $_SESSION['pesan'] = ['tipe' => 'success', 'teks' => 'Barang berhasil dihapus.'];
} else {
    $_SESSION['pesan'] = ['tipe' => 'danger', 'teks' => 'Gagal menghapus barang.'];
}


$redirect = ($from === 'stok_toko') ? 'stok_toko.php' : 'barang.php';
header("Location: $redirect");
exit;
