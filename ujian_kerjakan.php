<?php
session_start();
include 'koneksi.php';

// Atur Timezone agar sinkron (WIB)
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'peserta') {
    header("location:login.php"); die();
}

$id_percobaan = $_GET['id'];

// 1. AMBIL DATA UJIAN
$query_info = mysqli_query($koneksi, "
    SELECT pt.*, p.nama_paket, p.durasi_menit 
    FROM percobaan_tryout pt
    JOIN paket_tryout p ON pt.id_paket = p.id_paket
    WHERE pt.id_percobaan = '$id_percobaan' AND pt.id_user = '$_SESSION[id_user]'
");
$info = mysqli_fetch_assoc($query_info);

// Jika data tidak ditemukan (user iseng ganti URL)
if (!$info) {
    header("location:index.php"); die();
}

// 2. HITUNG SISA WAKTU (LOGIKA SERVER-SIDE)
// Waktu Mulai dari Database
$waktu_mulai = strtotime($info['waktu_mulai']); 

// Hitung Waktu Selesai Seharusnya (Jam Mulai + Durasi)
$durasi_detik = $info['durasi_menit'] * 60;
$waktu_habis = $waktu_mulai + $durasi_detik;

// Waktu Sekarang (Real-time Server)
$sekarang = time();

// Sisa Waktu = Deadline - Sekarang
$sisa_detik = $waktu_habis - $sekarang;

// 3. CEK APAKAH WAKTU SUDAH HABIS?
if ($sisa_detik <= 0) {
    // Jika waktu minus, artinya user telat atau waktu habis saat offline
    // Paksa redirect ke halaman selesai (Auto Submit)
    header("location:ujian_selesai.php?id_percobaan=$id_percobaan&auto=1");
    die();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian: <?= $info['nama_paket'] ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        /* Timer Melayang yang Keren */
        .timer-box { 
            position: fixed; top: 80px; right: 20px; z-index: 1000; width: 160px; 
            border-left: 5px solid #dc3545;
        }
        .soal-card { border-left: 5px solid #0d6efd; transition: 0.3s; }
        .soal-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        /* Agar tidak bisa select text (Anti Copas Sederhana) */
        .no-select { user-select: none; }
    </style>
</head>
<body class="bg-light no-select">

    <nav class="navbar navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container">
            <span class="navbar-brand fw-bold">📝 <?= $info['nama_paket'] ?></span>
        </div>
    </nav>

    <div class="card timer-box shadow border-0 bg-white text-center">
        <div class="card-body py-2">
            <small class="text-muted fw-bold d-block">SISA WAKTU</small>
            <h3 class="fw-bold text-danger mb-0" id="timer">--:--:--</h3>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <form action="ujian_selesai.php" method="POST" id="formUjian">
            <input type="hidden" name="id_percobaan" value="<?= $id_percobaan ?>">
            <input type="hidden" name="id_paket" value="<?= $info['id_paket'] ?>">

            <div class="row">
                <div class="col-md-9">
                    <?php
                    // Ambil Soal
                    $id_paket = $info['id_paket'];
                    $q_soal = mysqli_query($koneksi, "SELECT * FROM paket_soal WHERE id_paket='$id_paket' ORDER BY id_soal ASC");
                    
                    $no = 1;
                    while($soal = mysqli_fetch_assoc($q_soal)){
                    ?>
                        <div class="card shadow-sm mb-4 border-0 soal-card" id="soal-<?= $no ?>">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3 text-primary">Soal No. <?= $no ?></h5>
                                <p class="fs-5 text-dark"><?= nl2br($soal['pertanyaan']) ?></p>
                                
                                <?php if(!empty($soal['gambar'])): ?>
                                    <img src="foto_soal/<?= $soal['gambar'] ?>" class="img-fluid rounded mb-3 border" style="max-height: 250px;">
                                <?php endif; ?>

                                <div class="list-group mt-3">
                                    <?php 
                                    $opsi = ['A', 'B', 'C', 'D', 'E'];
                                    foreach($opsi as $opt) { 
                                        $kolom = 'opsi_'.strtolower($opt);
                                    ?>
                                    <label class="list-group-item list-group-item-action d-flex align-items-center" style="cursor: pointer;">
                                        <input class="form-check-input me-3" type="radio" 
                                               name="jawaban[<?= $soal['id_soal'] ?>]" 
                                               value="<?= $opt ?>" 
                                               style="transform: scale(1.3);">
                                        <div>
                                            <span class="fw-bold badge bg-secondary me-2"><?= $opt ?></span> 
                                            <?= $soal[$kolom] ?>
                                        </div>
                                    </label>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                        $no++;
                    } 
                    ?>

                    <div class="card mb-5 border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <p class="text-muted mb-3">Pastikan semua jawaban sudah terisi sebelum menyelesaikan ujian.</p>
                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold py-3" onclick="return confirm('Yakin ingin menyelesaikan ujian ini?')">
                                ✅ KIRIM JAWABAN & SELESAI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Ambil sisa waktu dari perhitungan PHP (Server)
        // Jadi kalau di-refresh, waktunya tidak reset ke awal, tapi lanjut dari sisa server
        var totalSeconds = <?= $sisa_detik ?>;
        
        var timerElement = document.getElementById("timer");

        function updateTimer() {
            var hours = Math.floor(totalSeconds / 3600);
            var minutes = Math.floor((totalSeconds % 3600) / 60);
            var seconds = Math.floor(totalSeconds % 60);

            // Format waktu biar cantik (01:05:09)
            var hDisplay = hours < 10 ? "0" + hours : hours;
            var mDisplay = minutes < 10 ? "0" + minutes : minutes;
            var sDisplay = seconds < 10 ? "0" + seconds : seconds;

            timerElement.innerHTML = hDisplay + ":" + mDisplay + ":" + sDisplay;

            // Jika waktu habis
            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                timerElement.innerHTML = "WAKTU HABIS";
                timerElement.classList.remove("text-danger");
                timerElement.classList.add("text-dark");
                
                alert("WAKTU TELAH HABIS! Jawaban Anda akan dikirim otomatis.");
                document.getElementById("formUjian").submit(); // Auto Submit
            } else {
                totalSeconds--;
            }
        }

        // Jalankan timer setiap 1 detik
        var timerInterval = setInterval(updateTimer, 1000);
        
        // Panggil sekali agar tidak nunggu 1 detik pertama
        updateTimer();

        // Fitur Tambahan: Peringatan jika mau refresh/tutup tab
        window.onbeforeunload = function() {
            return "Waktu ujian terus berjalan. Yakin ingin keluar?";
        };
        
        // Hapus peringatan saat submit agar tidak mengganggu
        document.getElementById("formUjian").onsubmit = function() {
            window.onbeforeunload = null;
        };
    </script>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>