<?php
// Konfigurasi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sistem_tryout"; 

// Buat Koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek Koneksi
if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    die();
}
?>