<?php
session_start();
include 'koneksi.php';

// --- 1. SET TIMEZONE WAJIB (PENTING!) ---
// Masalah "Auto Selesai" biasanya karena jam Server beda dengan jam MySQL.
// Kita paksa pakai Waktu Indonesia Barat (WIB).
date_default_timezone_set('Asia/Jakarta');

// Cek Login Peserta
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'peserta') {
    header("location:login.php");
    die();
}

$id_user = $_SESSION['id_user'];
$id_paket = $_GET['id'];

// --- 2. CEK HISTORY (RESUME FITUR) ---
// Cek apakah user sedang mengerjakan paket ini? (Misal internet putus lalu masuk lagi)
$cek_history = mysqli_query($koneksi, "
    SELECT * FROM percobaan_tryout 
    WHERE id_user='$id_user' AND id_paket='$id_paket' AND status_pengerjaan='SEDANG MENGERJAKAN'
");

if (mysqli_num_rows($cek_history) > 0) {
    // Kalau ada ujian yang gantung, lanjutkan saja
    $data = mysqli_fetch_assoc($cek_history);
    $id_percobaan = $data['id_percobaan']; 
    header("location:ujian_kerjakan.php?id=$id_percobaan");
    die();
}

// --- 3. MULAI BARU ---
// Ambil waktu sekarang dari PHP (yang sudah diset Asia/Jakarta)
// JANGAN pakai function NOW() milik MySQL agar tidak selisih waktu
$tanggal_mulai = date('Y-m-d H:i:s');

$query_insert = "INSERT INTO percobaan_tryout (id_user, id_paket, waktu_mulai, skor_total, status_pengerjaan) 
                 VALUES ('$id_user', '$id_paket', '$tanggal_mulai', 0, 'SEDANG MENGERJAKAN')";

if (mysqli_query($koneksi, $query_insert)) {
    // Ambil ID Percobaan yang barusan dibuat
    $id_percobaan = mysqli_insert_id($koneksi);
    
    // Lempar ke halaman soal
    header("location:ujian_kerjakan.php?id=$id_percobaan");
} else {
    echo "Gagal memulai ujian: " . mysqli_error($koneksi);
}
?>