<?php
session_start();
include 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    die();
}

$id = $_GET['id'];

// Hapus Paket (Perlu diperhatikan: idealnya hapus soal-soal terkait dulu jika relasinya tidak CASCADE di database)
// Anggap kita hapus paketnya saja
$query = "DELETE FROM paket_tryout WHERE id_paket = '$id'";

if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Data berhasil dihapus!'); window.location='admin_paket.php';</script>";
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>