<?php
session_start();
include 'koneksi.php';

$id_user = $_SESSION['id_user'];
$id_paket = $_GET['id'];

// Cek harga paket
$cek_paket = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga FROM paket_tryout WHERE id_paket='$id_paket'"));

// Masukkan ke tabel transaksi (Otomatis SUCCESS biar gampang)
$harga = $cek_paket['harga'];
$query = "INSERT INTO transaksi (id_user, id_paket, jumlah_bayar, status_transaksi) VALUES ('$id_user', '$id_paket', '$harga', 'SUCCESS')";

if(mysqli_query($koneksi, $query)){
    echo "<script>alert('Pembelian Berhasil!'); window.location='detail_paket.php?id=$id_paket';</script>";
} else {
    echo "Gagal beli.";
}
?>