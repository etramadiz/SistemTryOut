<?php
session_start();
include 'koneksi.php';

// Pastikan user login
if (!isset($_SESSION['status'])) { header("location:login.php"); exit; }

// Pastikan tombol bayar ditekan (atau akses via URL dengan parameter id)
if (isset($_POST['bayar']) || isset($_GET['id'])) {
    
    $id_user = $_SESSION['id_user'];
    
    // Ambil ID Paket (Bisa dari POST form atau GET link)
    $id_paket = isset($_POST['id_paket']) ? $_POST['id_paket'] : $_GET['id'];
    
    // Ambil Metode Pembayaran (Kalau dari link langsung, defaultnya 'Transfer Manual')
    $metode = isset($_POST['metode']) ? $_POST['metode'] : 'Transfer Manual';

    // Cek harga paket
    $cek_paket = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga FROM paket_tryout WHERE id_paket='$id_paket'"));
    $harga = $cek_paket['harga'];

    // --- SIMULASI PROSES PEMBAYARAN ---
    // Di dunia nyata, di sini kita connect ke API Midtrans/Xendit.
    // Karena simulasi, kita langsung masukkan ke database.

    // Masukkan ke tabel transaksi
    // Catatan: Karena tabel transaksi kamu belum punya kolom 'metode', kita simpan transaksi biasa saja.
    // Status langsung 'SUCCESS' agar user bisa langsung ujian.
    
    $query = "INSERT INTO transaksi (id_user, id_paket, jumlah_bayar, status_transaksi) VALUES ('$id_user', '$id_paket', '$harga', 'SUCCESS')";

    if(mysqli_query($koneksi, $query)){
        // Tampilkan Alert Sukses dengan Metode yang dipilih
        echo "<script>
                alert('Pembayaran via $metode Berhasil! Selamat Mengerjakan.'); 
                window.location='detail_paket.php?id=$id_paket';
              </script>";
    } else {
        echo "<script>alert('Gagal memproses transaksi.'); window.location='index.php';</script>";
    }

} else {
    // Jika user coba akses file ini tanpa beli
    header("location:index.php");
}
?>