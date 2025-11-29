<<<<<<< HEAD
=======
<?php
session_start();
include 'koneksi.php';

// Cek sesi login
if (!isset($_SESSION['status'])) { header("location:login.php"); exit; }

// Tangkap data dari form ujian.php
$id_percobaan = $_POST['id_percobaan']; // INI PENTING: Ambil ID yang sudah dibuat di awal
$id_paket = $_POST['id_paket'];
$jawaban_user = isset($_POST['jawaban']) ? $_POST['jawaban'] : []; // Array [id_soal => 'A']

$total_soal = 0;
$benar = 0;
$skor_per_soal = 4; // Konfigurasi bobot nilai (bisa disesuaikan)

// Ambil Kunci Jawaban dari Database
$q_soal = mysqli_query($koneksi, "SELECT s.id_soal, s.kunci_jawaban FROM paket_soal ps JOIN soal s ON ps.id_soal = s.id_soal WHERE ps.id_paket='$id_paket'");

while($db_soal = mysqli_fetch_assoc($q_soal)){
    $total_soal++;
    $id_s = $db_soal['id_soal'];
    $kunci = $db_soal['kunci_jawaban'];
    
    // Cek jawaban user
    $jawab = isset($jawaban_user[$id_s]) ? $jawaban_user[$id_s] : null;
    
    // Tentukan status jawaban
    if ($jawab == $kunci) {
        $status = 'BENAR';
        $benar++;
    } elseif ($jawab == null) {
        $status = 'KOSONG';
    } else {
        $status = 'SALAH';
    }

    // Simpan detail jawaban ke tabel jawaban_peserta
    // Kita pakai escape string agar aman dari karakter aneh
    $jawab_aman = mysqli_real_escape_string($koneksi, $jawab);
    mysqli_query($koneksi, "INSERT INTO jawaban_peserta (id_percobaan, id_soal, jawaban_pilihan, status_jawaban) VALUES ('$id_percobaan', '$id_s', '$jawab_aman', '$status')");
}

// Hitung Skor Total
$skor_akhir = $benar * $skor_per_soal; 

// [REVISI UTAMA] UPDATE data percobaan yang sudah ada, BUKAN INSERT baru.
// Kita set waktu_selesai = SEKARANG, status = SELESAI, dan masukkan skornya.
$update = mysqli_query($koneksi, "UPDATE percobaan_tryout SET 
    waktu_selesai = NOW(), 
    skor_total = '$skor_akhir', 
    status_pengerjaan = 'SELESAI' 
    WHERE id_percobaan = '$id_percobaan'");

if($update) {
    // Redirect ke Hasil
    header("location:hasil_ujian.php?id=$id_percobaan");
} else {
    echo "Gagal menyimpan ujian: " . mysqli_error($koneksi);
}
?>
>>>>>>> 0b68867373fe0d10e213f7334de3ca0ee8096945
