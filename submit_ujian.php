<?php
session_start();
include 'koneksi.php';

$id_user = $_SESSION['id_user'];
$id_paket = $_POST['id_paket'];
$jawaban_user = $_POST['jawaban']; // Array [id_soal => 'A']

$total_soal = 0;
$benar = 0;
$skor_per_soal = 4; // Misalnya 1 soal benar = 4 poin

// Buat ID Percobaan Baru
mysqli_query($koneksi, "INSERT INTO percobaan_tryout (id_user, id_paket, waktu_selesai, status_pengerjaan) VALUES ('$id_user', '$id_paket', NOW(), 'SELESAI')");
$id_percobaan = mysqli_insert_id($koneksi);

// Ambil Kunci Jawaban
$q_soal = mysqli_query($koneksi, "SELECT s.id_soal, s.kunci_jawaban FROM paket_soal ps JOIN soal s ON ps.id_soal = s.id_soal WHERE ps.id_paket='$id_paket'");

while($db_soal = mysqli_fetch_assoc($q_soal)){
    $total_soal++;
    $id_s = $db_soal['id_soal'];
    $kunci = $db_soal['kunci_jawaban'];
    
    // Cek jawaban user
    $jawab = isset($jawaban_user[$id_s]) ? $jawaban_user[$id_s] : null;
    $status = ($jawab == $kunci) ? 'BENAR' : (($jawab == null) ? 'KOSONG' : 'SALAH');
    
    if($status == 'BENAR') $benar++;

    // Simpan detail jawaban
    mysqli_query($koneksi, "INSERT INTO jawaban_peserta (id_percobaan, id_soal, jawaban_pilihan, status_jawaban) VALUES ('$id_percobaan', '$id_s', '$jawab', '$status')");
}

// Hitung Skor Total
$skor_akhir = $benar * $skor_per_soal; // Atau logika skor UTBK lain
mysqli_query($koneksi, "UPDATE percobaan_tryout SET skor_total='$skor_akhir' WHERE id_percobaan='$id_percobaan'");

// Redirect ke Hasil
header("location:hasil_ujian.php?id=$id_percobaan");
?>