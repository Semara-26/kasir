<?php
$host = "localhost";      // alamat server database, biasanya localhost
$username = "root";       // username database kamu
$password = "";           // password database kamu
$database = "db_pos_kasir";  // ganti dengan nama database POS kamu

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Jika berhasil, $conn bisa dipakai untuk query database
?>
