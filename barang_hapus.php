<?php
include 'koneksi.php';

$id_toko = 1;
$id_barang = $_GET['id'] ?? 0;

if (!$id_barang) {
    header("Location: barang.php");
    exit;
}

mysqli_query($conn, "DELETE FROM stoktoko WHERE id_toko=$id_toko AND id_barang=$id_barang");
mysqli_query($conn, "DELETE FROM barang WHERE id_barang=$id_barang");

header("Location: barang.php");
exit;
