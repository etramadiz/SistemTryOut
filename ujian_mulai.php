<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'peserta') {
    header("location:login.php");
    die();
}

$id_user = $_SESSION['id_user'];
$id_paket = $_GET['id'];

// 1. Cek apakah user sedang mengerjakan?
$cek_history = mysqli_query($koneksi, "
    SELECT * FROM percobaan_tryout 
    WHERE id_user='$id_user' AND id_paket='$id_paket' AND status_pengerjaan='SEDANG MENGERJAKAN'
");

if (mysqli_num_rows($cek_history) > 0) {
    $data = mysqli_fetch_assoc($cek_history);
    // PERBAIKAN: Gunakan 'id_percobaan' sesuai database
    $id_percobaan = $data['id_percobaan']; 
    header("location:ujian_kerjakan.php?id=$id_percobaan");
    die();
}

// 2. Buat sesi baru
$tanggal = date('Y-m-d H:i:s');
// Kolom id_percobaan auto increment, jadi tidak perlu ditulis di INSERT
$query_insert = "INSERT INTO percobaan_tryout (id_user, id_paket, waktu_mulai, skor_total, status_pengerjaan) 
                 VALUES ('$id_user', '$id_paket', '$tanggal', 0, 'SEDANG MENGERJAKAN')";

if (mysqli_query($koneksi, $query_insert)) {
    $id_percobaan = mysqli_insert_id($koneksi);
    header("location:ujian_kerjakan.php?id=$id_percobaan");
} else {
    echo "Gagal memulai ujian: " . mysqli_error($koneksi);
}
?>