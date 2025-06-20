<?php
session_start();
include '../config/koneksi.php';

// Set header output sebagai JSON
header('Content-Type: application/json');

// Respon default jika ada masalah
$response = ['bisa_dihapus' => false, 'pesan' => 'ID Barang tidak valid atau tidak diberikan.'];

if (isset($_GET['id'])) {
    $id_barang = (int)$_GET['id'];

    if ($id_barang > 0) {
        // Query untuk menghitung riwayat transaksi barang ini
        $stmt_cek = mysqli_prepare($conn, "SELECT COUNT(*) as jumlah FROM detailtransaksi WHERE id_barang = ?");
        mysqli_stmt_bind_param($stmt_cek, "i", $id_barang);
        mysqli_stmt_execute($stmt_cek);
        $result_cek = mysqli_stmt_get_result($stmt_cek);
        $data_cek = mysqli_fetch_assoc($result_cek);
        mysqli_stmt_close($stmt_cek);

        if ($data_cek['jumlah'] > 0) {
            // Jika JUMLAH > 0, barang tidak bisa dihapus
            $response = [
                'bisa_dihapus' => false, 
                'pesan' => "Gagal! Barang ID #{$id_barang} tidak dapat dihapus karena sudah memiliki riwayat transaksi."
            ];
        } else {
            // Jika JUMLAH = 0, barang aman untuk dihapus
            $response = ['bisa_dihapus' => true, 'pesan' => 'Barang bisa dihapus.'];
        }
    }
}

// Cetak response dalam format JSON yang akan dibaca oleh JavaScript
echo json_encode($response);
exit;