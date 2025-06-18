<?php
session_start();
include 'koneksi.php';

// Pengecekan Akses & data
if (!isset($_POST['checkout']) || empty($_SESSION['keranjang'])) {
    header("Location: transaksi_kasir.php");
    exit;
}

// Ambil data dari session dan form
$keranjang = $_SESSION['keranjang'];
$id_pengguna = $_SESSION['id_pengguna'];
$id_toko = $_SESSION['id_toko'];
$id_member = !empty($_POST['id_member']) ? $_POST['id_member'] : NULL;
$jumlah_bayar = $_POST['jumlah_bayar'];

// Hitung total belanja dari keranjang
$total_belanja_sebelum_diskon = 0;
foreach ($keranjang as $item) {
    $total_belanja_sebelum_diskon += $item['harga_jual'] * $item['jumlah'];
}

// Terapkan diskon jika ada member yang dipilih
$total_final = $total_belanja_sebelum_diskon;
if (!empty($id_member)) {
    // Ambil diskon dari konstanta (bisa juga dari database nantinya)
    $diskon_persen = 10; // Sesuaikan dengan yang di halaman kasir
    $besar_diskon = $total_belanja_sebelum_diskon * ($diskon_persen / 100);
    $total_final = $total_belanja_sebelum_diskon - $besar_diskon;
}

$kembalian = $jumlah_bayar - $total_final;


// --- MEMULAI DATABASE TRANSACTION ---
mysqli_begin_transaction($conn);

try {
    // 1. Simpan data ke tabel 'transaksi' (header)
    $stmt_transaksi = mysqli_prepare($conn, "INSERT INTO transaksi (id_member, id_pengguna, id_toko, total, jumlah_bayar, kembalian) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_transaksi, "iiiddd", $id_member, $id_pengguna, $id_toko, $total_final, $jumlah_bayar, $kembalian);
    mysqli_stmt_execute($stmt_transaksi);
    
    // Ambil ID transaksi yang baru saja dibuat
    $id_transaksi_baru = mysqli_insert_id($conn);

    // 2. Loop untuk simpan setiap item di keranjang ke tabel 'detailtransaksi' dan update stok
    $stmt_detail = mysqli_prepare($conn, "INSERT INTO detailtransaksi (id_transaksi, id_barang, harga_saat_transaksi, jumlah, sub_total) VALUES (?, ?, ?, ?, ?)");
    $stmt_stok = mysqli_prepare($conn, "UPDATE stoktoko SET jumlah_stok = jumlah_stok - ? WHERE id_barang = ? AND id_toko = ?");

    foreach ($keranjang as $id_barang => $item) {
        $sub_total = $item['harga_jual'] * $item['jumlah'];
        
        // Simpan ke detailtransaksi
        mysqli_stmt_bind_param($stmt_detail, "iidid", $id_transaksi_baru, $id_barang, $item['harga_jual'], $item['jumlah'], $sub_total);
        mysqli_stmt_execute($stmt_detail);

        // Update stoktoko
        mysqli_stmt_bind_param($stmt_stok, "iii", $item['jumlah'], $id_barang, $id_toko);
        mysqli_stmt_execute($stmt_stok);
    }

    // Jika semua query berhasil, commit transaksi
    mysqli_commit($conn);

    // Kosongkan keranjang dan arahkan ke halaman sukses (atau cetak struk)
    unset($_SESSION['keranjang']);
    // Untuk saat ini, kita arahkan kembali ke dashboard dengan pesan sukses
    header('Location: struk.php?id=' . $id_transaksi_baru);
    exit;

} catch (mysqli_sql_exception $exception) {
    // Jika ada satu saja query yang gagal, rollback semua perubahan
    mysqli_rollback($conn);
    
    // Arahkan kembali ke kasir dengan pesan error
    $_SESSION['pesan_error'] = "Transaksi Gagal: " . $exception->getMessage();
    header('Location: transaksi_kasir.php');
    exit;
}
?>