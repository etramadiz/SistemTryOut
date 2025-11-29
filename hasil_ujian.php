<?php
session_start();
include 'koneksi.php';
$id_percobaan = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT pt.*, p.nama_paket FROM percobaan_tryout pt JOIN paket_tryout p ON pt.id_paket=p.id_paket WHERE id_percobaan='$id_percobaan'"));
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"></head>
<body class="bg-light text-center pt-5">
    <div class="container">
        <div class="card shadow p-5 mx-auto" style="max-width: 500px;">
            <h2 class="text-success fw-bold">Hasil Ujian</h2>
            <h4><?= $data['nama_paket'] ?></h4>
            <hr>
            <h1 class="display-1 fw-bold"><?= $data['skor_total'] ?></h1>
            <p class="text-muted">Skor Akhir Anda</p>
            <a href="diskusi.php?id=<?= $data['id_paket'] ?>" class="btn btn-outline-primary mt-3">💬 Diskusi Pembahasan</a>
            <a href="index.php" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>