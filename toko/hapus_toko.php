<?php
session_start();
include '../config/koneksi.php';

// Cek login & role
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ada parameter ID
if (!isset($_GET['id'])) {
    header("Location: toko.php");
    exit;
}

$id = intval($_GET['id']);

// Ambil data toko dulu untuk menghapus gambar jika ada
$result = mysqli_query($conn, "SELECT * FROM toko WHERE id_toko = $id");
$toko = mysqli_fetch_assoc($result);

if ($toko) {
    // Hapus gambar jika ada
    if (!empty($toko['image']) && file_exists("gambar/" . $toko['image'])) {
        unlink("gambar/" . $toko['image']);
    }

    // Hapus dari database
    $delete = mysqli_query($conn, "DELETE FROM toko WHERE id_toko = $id");

    if ($delete) {
        header("Location: toko.php?msg=sukses");
        exit;
    } else {
        echo "Gagal menghapus data toko.";
    }
} else {
    echo "Data tidak ditemukan.";
}
?>
