<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'peserta') {
    header("location:login.php"); die();
}

$id_percobaan = $_GET['id'];

// PERBAIKAN QUERY: Ganti 'pt.id_tryout' jadi 'pt.id_percobaan'
$query_info = mysqli_query($koneksi, "
    SELECT pt.*, p.nama_paket, p.durasi_menit 
    FROM percobaan_tryout pt
    JOIN paket_tryout p ON pt.id_paket = p.id_paket
    WHERE pt.id_percobaan = '$id_percobaan' AND pt.id_user = '$_SESSION[id_user]'
");
$info = mysqli_fetch_assoc($query_info);

// Hitung Sisa Waktu
$waktu_mulai = strtotime($info['waktu_mulai']);
$durasi_detik = $info['durasi_menit'] * 60;
$waktu_habis = $waktu_mulai + $durasi_detik;
$sekarang = time();
$sisa_waktu = $waktu_habis - $sekarang;

if ($sisa_waktu < 0) {
    header("location:ujian_selesai.php?id=$id_percobaan&auto=1");
    die();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ujian: <?= $info['nama_paket'] ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        .timer-box { position: fixed; top: 80px; right: 20px; z-index: 1000; width: 150px; }
        .soal-card { border-left: 5px solid #0d6efd; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container">
            <span class="navbar-brand fw-bold">📝 Sedang Mengerjakan: <?= $info['nama_paket'] ?></span>
        </div>
    </nav>

    <div class="card timer-box shadow border-0 bg-white text-center">
        <div class="card-body py-2">
            <small class="text-muted fw-bold">SISA WAKTU</small>
            <h3 class="fw-bold text-danger mb-0" id="timer">00:00:00</h3>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <form action="ujian_selesai.php" method="POST" id="formUjian">
            <input type="hidden" name="id_percobaan" value="<?= $id_percobaan ?>">
            <input type="hidden" name="id_paket" value="<?= $info['id_paket'] ?>">

            <div class="row">
                <div class="col-md-9">
                    <?php
                    $id_paket = $info['id_paket'];
                    $q_soal = mysqli_query($koneksi, "SELECT * FROM paket_soal WHERE id_paket='$id_paket' ORDER BY id_soal ASC");
                    
                    $no = 1;
                    while($soal = mysqli_fetch_assoc($q_soal)){
                    ?>
                        <div class="card shadow-sm mb-4 border-0 soal-card">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">No. <?= $no ?></h5>
                                <p class="fs-5"><?= nl2br($soal['pertanyaan']) ?></p>
                                
                                <?php if(!empty($soal['gambar'])): ?>
                                    <img src="foto_soal/<?= $soal['gambar'] ?>" class="img-fluid rounded mb-3" style="max-height: 200px;">
                                <?php endif; ?>

                                <div class="list-group">
                                    <?php 
                                    $opsi = ['A', 'B', 'C', 'D', 'E'];
                                    foreach($opsi as $opt) { 
                                        $kolom = 'opsi_'.strtolower($opt);
                                    ?>
                                    <label class="list-group-item list-group-item-action">
                                        <input class="form-check-input me-2" type="radio" name="jawaban[<?= $soal['id_soal'] ?>]" value="<?= $opt ?>">
                                        <span class="fw-bold text-primary"><?= $opt ?>.</span> <?= $soal[$kolom] ?>
                                    </label>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                        $no++;
                    } 
                    ?>

                    <div class="card mb-5">
                        <div class="card-body">
                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold" onclick="return confirm('Yakin ingin menyelesaikan ujian ini?')">✅ KIRIM JAWABAN & SELESAI</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        var timeLeft = <?= $sisa_waktu ?>;
        var timerInterval = setInterval(function() {
            var hours = Math.floor(timeLeft / 3600);
            var minutes = Math.floor((timeLeft % 3600) / 60);
            var seconds = timeLeft % 60;

            document.getElementById("timer").innerHTML = 
                (hours < 10 ? "0" : "") + hours + ":" + 
                (minutes < 10 ? "0" : "") + minutes + ":" + 
                (seconds < 10 ? "0" : "") + seconds;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                alert("WAKTU HABIS! Jawaban dikirim otomatis.");
                document.getElementById("formUjian").submit();
            }
            timeLeft -= 1;
        }, 1000);
    </script>
</body>
</html>