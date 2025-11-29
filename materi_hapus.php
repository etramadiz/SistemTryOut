<?php
include 'koneksi.php';

$id = $_GET['id'];

// Ambil nama file dulu untuk dihapus dari folder
$q = mysqli_query($koneksi, "SELECT nama_file FROM materi WHERE id_materi='$id'");
$data = mysqli_fetch_assoc($q);
$file = $data['nama_file'];

// Hapus file fisik
if (file_exists("file_materi/$file")) {
    unlink("file_materi/$file");
}

// Hapus dari database
mysqli_query($koneksi, "DELETE FROM materi WHERE id_materi='$id'");

header("location:admin_materi.php");
?>