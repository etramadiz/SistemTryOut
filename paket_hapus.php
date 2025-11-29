<?php
session_start();
include 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    die();
}

$id = $_GET['id'];

// --- 1. MATIKAN PENGECEKAN KUNCI ASING (Buka Gembok) ---
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");

// --- 2. HAPUS SEMUA DATA YANG TERKAIT ---

// A. Hapus Soal-soal di dalam paket ini
$hapus_soal = mysqli_query($koneksi, "DELETE FROM paket_soal WHERE id_paket = '$id'");

// B. Hapus Riwayat Pengerjaan Siswa (Ini penyebab error tadi)
$hapus_percobaan = mysqli_query($koneksi, "DELETE FROM percobaan_tryout WHERE id_paket = '$id'");

// C. Baru Hapus Paket Utamanya
$query = "DELETE FROM paket_tryout WHERE id_paket = '$id'";
$hasil = mysqli_query($koneksi, $query);

// --- 3. NYALAKAN LAGI PENGECEKAN (Kunci Gembok) ---
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

// --- 4. CEK HASIL ---
if ($hasil) {
    echo "<script>alert('Paket beserta seluruh soal dan riwayat ujiannya berhasil dihapus!'); window.location='admin_paket.php';</script>";
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>