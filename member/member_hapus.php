<?php
session_start();
include '../config/koneksi.php';

// Pengecekan Akses
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['admin', 'manajer', 'kasir'])) {
    header("Location: login.php");
    exit;
}

$id_hapus = $_GET['id'] ?? 0;

if ($id_hapus > 0) {
    // Karena relasi di database untuk id_member di tabel transaksi adalah ON DELETE SET NULL,
    // kita bisa langsung menghapus member tanpa khawatir error foreign key.
    // Riwayat transaksinya akan tetap ada, hanya saja tidak lagi terhubung ke member ini.
    $stmt = mysqli_prepare($conn, "DELETE FROM member WHERE id_member = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_hapus);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['pesan_sukses'] = "Member berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus member.";
    }
    mysqli_stmt_close($stmt);
}

header('Location: member.php');
exit;
?>