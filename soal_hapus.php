<?php
include 'koneksi.php';

$id_soal = $_GET['id'];
$id_paket = $_GET['id_paket'];

// Hapus gambar dulu jika ada
$cek = mysqli_query($koneksi, "SELECT gambar FROM paket_soal WHERE id_soal='$id_soal'");
$data = mysqli_fetch_assoc($cek);

if(!empty($data['gambar']) && file_exists("foto_soal/".$data['gambar'])){
    unlink("foto_soal/".$data['gambar']);
}

// Hapus data
mysqli_query($koneksi, "DELETE FROM paket_soal WHERE id_soal='$id_soal'");

header("location:kelola_soal.php?id=$id_paket");
?>