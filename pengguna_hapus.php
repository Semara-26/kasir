<?php
session_start();
include 'koneksi.php';

// --- Pengecekan Akses ---
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id_hapus = $_GET['id'] ?? 0;

// --- Mencegah admin menghapus diri sendiri ---
if ($id_hapus == $_SESSION['id_pengguna']) {
    $_SESSION['pesan_error'] = "Anda tidak dapat menghapus akun Anda sendiri.";
    header('Location: pengguna.php');
    exit;
}

// --- Menggunakan PREPARED STATEMENT untuk keamanan ---
$stmt = mysqli_prepare($conn, "DELETE FROM pengguna WHERE id_pengguna = ?");
mysqli_stmt_bind_param($stmt, "i", $id_hapus);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Pengguna berhasil dihapus.";
} else {
    $_SESSION['pesan_error'] = "Gagal menghapus pengguna.";
}

mysqli_stmt_close($stmt);
header('Location: pengguna.php');
exit;