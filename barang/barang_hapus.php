<?php
session_start();
include '../config/koneksi.php';

// Cek hak akses (hanya admin & manajer)
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: ../login.php");
    exit;
}

$id_barang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Menentukan halaman kembali jika ada parameter 'from'
$from = $_GET['from'] ?? 'barang.php';

if ($id_barang <= 0) {
    header("Location: barang.php");
    exit;
}

// Cek apakah barang pernah ada di transaksi
$stmt_cek = mysqli_prepare($conn, "SELECT COUNT(*) as jumlah FROM detailtransaksi WHERE id_barang = ?");
mysqli_stmt_bind_param($stmt_cek, "i", $id_barang);
mysqli_stmt_execute($stmt_cek);
$result_cek = mysqli_stmt_get_result($stmt_cek);
$data_cek = mysqli_fetch_assoc($result_cek);
mysqli_stmt_close($stmt_cek);

if ($data_cek['jumlah'] > 0) {
    // Jika barang sudah pernah terjual, HANYA set pesan error dan kembali.
    $_SESSION['pesan'] = ['tipe' => 'danger', 'teks' => "Gagal! Barang ID #{$id_barang} tidak dapat dihapus karena sudah memiliki riwayat transaksi."];
    header('Location: barang.php');
    exit;
}

// Jika aman, baru hapus dari stok dan barang
mysqli_begin_transaction($conn);
try {
    // Hapus dari stok semua toko
    $stmt_stok = mysqli_prepare($conn, "DELETE FROM stoktoko WHERE id_barang = ?");
    mysqli_stmt_bind_param($stmt_stok, "i", $id_barang);
    mysqli_stmt_execute($stmt_stok);
    mysqli_stmt_close($stmt_stok);

    // Hapus dari tabel barang
    $stmt_barang = mysqli_prepare($conn, "DELETE FROM barang WHERE id_barang = ?");
    mysqli_stmt_bind_param($stmt_barang, "i", $id_barang);
    mysqli_stmt_execute($stmt_barang);
    mysqli_stmt_close($stmt_barang);

    mysqli_commit($conn);
    // Jika berhasil, HANYA set pesan sukses.
    $_SESSION['pesan'] = ['tipe' => 'success', 'teks' => "Barang ID #{$id_barang} berhasil dihapus."];

} catch (mysqli_sql_exception $exception) {
    mysqli_rollback($conn);
    // Jika ada error lain, HANYA set pesan error.
    $_SESSION['pesan'] = ['tipe' => 'danger', 'teks' => 'Terjadi kesalahan saat menghapus data.'];
}

// Redirect ke halaman yang sesuai di akhir
$redirect_page = ($from === 'stok_toko') ? '../stok_toko.php' : 'barang.php';
header("Location: $redirect_page");
exit;
?>