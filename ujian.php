<?php
session_start();
include 'koneksi.php';

// Cek Login: Jika belum login, tendang keluar
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    exit;
}

$id_paket = $_GET['id'];
$id_user = $_SESSION['id_user'];

// --- [REVISI PENTING] LOGIKA MULAI UJIAN ---
// 1. Cek apakah user ini SEDANG mengerjakan ujian ini (status BERLANGSUNG)
// Ini berguna jika user tidak sengaja refresh halaman atau internet mati sebentar
$cek_mulai = mysqli_query($koneksi, "SELECT * FROM percobaan_tryout WHERE id_user='$id_user' AND id_paket='$id_paket' AND status_pengerjaan='BERLANGSUNG'");

if(mysqli_num_rows($cek_mulai) > 0){
    // Jika sudah ada sesi yang berlangsung, gunakan ID Percobaan yang lama
    $data_percobaan = mysqli_fetch_assoc($cek_mulai);
    $id_percobaan = $data_percobaan['id_percobaan'];
} else {
    // Jika belum ada, BUAT DATA BARU (Mencatat Waktu Mulai Sekarang)
    $insert_awal = mysqli_query($koneksi, "INSERT INTO percobaan_tryout (id_user, id_paket, waktu_mulai, status_pengerjaan) VALUES ('$id_user', '$id_paket', NOW(), 'BERLANGSUNG')");
    
    if(!$insert_awal) {
        die("Gagal memulai ujian: " . mysqli_error($koneksi));
    }
    
    // Ambil ID Percobaan yang baru saja dibuat oleh database
    $id_percobaan = mysqli_insert_id($koneksi);
}
// ------------------------------------------

// Ambil Info Paket (untuk Judul & Durasi)
$paket = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM paket_tryout WHERE id_paket='$id_paket'"));

// Ambil Soal-soal yang ada di paket ini menggunakan JOIN tabel paket_soal
$q_soal = mysqli_query($koneksi, "SELECT s.* FROM paket_soal ps JOIN soal s ON ps.id_soal = s.id_soal WHERE ps.id_paket='$id_paket'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Ujian - <?= $paket['nama_paket'] ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4 mb-5">
        <h3 class="fw-bold text-primary"><?= $paket['nama_paket'] ?></h3>
        
        <div class="alert alert-info">
            <strong>Waktu pengerjaan: <?= $paket['durasi_menit'] ?> Menit.</strong><br>
            Jangan refresh halaman sembarangan! Jawaban akan dikirim saat tombol selesai ditekan.
        </div>
        
        <form action="submit_ujian.php" method="POST">
            <input type="hidden" name="id_percobaan" value="<?= $id_percobaan ?>">
            <input type="hidden" name="id_paket" value="<?= $id_paket ?>">
            
            <?php $no = 1; while($soal = mysqli_fetch_assoc($q_soal)): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Soal No. <?= $no++ ?></h5>
                        <p class="card-text fs-5"><?= $soal['pertanyaan'] ?></p>
                        
                        <div class="list-group">
                            <?php 
                            // Loop untuk menampilkan opsi A sampai E
                            $opsi = ['A', 'B', 'C', 'D', 'E'];
                            foreach($opsi as $pil): 
                                // Ambil teks opsi dari database (opsi_a, opsi_b, dst)
                                $teks_opsi = $soal['opsi_'.strtolower($pil)];
                                
                                // Hanya tampilkan jika opsinya tidak kosong (karena opsi E kadang null/kosong)
                                if(!empty($teks_opsi)):
                            ?>
                            <label class="list-group-item list-group-item-action">
                                <input class="form-check-input me-1" type="radio" name="jawaban[<?= $soal['id_soal'] ?>]" value="<?= $pil ?>">
                                <strong><?= $pil ?>.</strong> <?= $teks_opsi ?>
                            </label>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg fw-bold" onclick="return confirm('Apakah Anda yakin sudah selesai mengerjakan dan ingin mengumpulkan jawaban?')">
                    ✅ Kirim Jawaban & Selesai
                </button>
            </div>
        </form>
    </div>
    
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>