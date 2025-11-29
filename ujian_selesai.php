<?php
session_start();
include 'koneksi.php';

if (!isset($_POST['id_percobaan']) && !isset($_GET['auto'])) {
    header("location:index.php"); die();
}

$id_percobaan = $_POST['id_percobaan'];
$id_paket = $_POST['id_paket'];
$jawaban_user = $_POST['jawaban']; 

$query_kunci = mysqli_query($koneksi, "SELECT id_soal, kunci_jawaban, bobot FROM paket_soal WHERE id_paket = '$id_paket'");

$total_skor = 0;

while ($soal = mysqli_fetch_assoc($query_kunci)) {
    $id_soal = $soal['id_soal'];
    $kunci = $soal['kunci_jawaban'];
    $bobot = $soal['bobot'];

    if (isset($jawaban_user[$id_soal]) && $jawaban_user[$id_soal] == $kunci) {
        $total_skor += $bobot;
    }
}

// PERBAIKAN QUERY: Gunakan 'id_percobaan' di WHERE clause
$update = mysqli_query($koneksi, "
    UPDATE percobaan_tryout SET 
    skor_total = '$total_skor', 
    status_pengerjaan = 'SELESAI' 
    WHERE id_percobaan = '$id_percobaan'
");

if ($update) {
    echo "<script>
            alert('Ujian Selesai! Skor Kamu: $total_skor');
            window.location='riwayat.php';
          </script>";
} else {
    echo "Gagal menyimpan nilai: " . mysqli_error($koneksi);
}
?>