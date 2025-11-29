<?php
session_start();
include 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak");
}

$id = $_GET['id'];

// CEK: Jangan hapus diri sendiri
if ($id == $_SESSION['id_user']) {
    echo "<script>alert('Tidak bisa menghapus akun sendiri!'); window.location='data_user.php';</script>";
    die();
}

// --- SOLUSI ERROR FOREIGN KEY ---
// 1. Matikan dulu pengecekan Foreign Key (Ibarat mematikan alarm keamanan sebentar)
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");

// 2. Hapus semua data milik user di berbagai tabel
mysqli_query($koneksi, "DELETE FROM diskusi_soal WHERE id_user = '$id'");
mysqli_query($koneksi, "DELETE FROM percobaan_tryout WHERE id_user = '$id'");

// 3. Hapus User Utamanya
$query_hapus = mysqli_query($koneksi, "DELETE FROM user WHERE id_user = '$id'");

// 4. Nyalakan lagi pengecekan Foreign Key (Nyalakan alarm lagi)
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

// --- CEK HASIL ---
if ($query_hapus) {
    echo "<script>alert('User berhasil dihapus secara permanen!'); window.location='data_user.php';</script>";
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>