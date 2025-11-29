<?php 
include 'koneksi.php';
session_start();

// Ambil ID paket dari URL
$id_paket = $_GET['id'];

// Ambil Detail Paket
$query_paket = mysqli_query($koneksi, "
    SELECT p.*, k.nama_kategori 
    FROM paket_tryout p 
    JOIN kategori_ujian k ON p.id_kategori = k.id_kategori 
    WHERE p.id_paket = '$id_paket'
");
$data = mysqli_fetch_assoc($query_paket);

// Hitung Jumlah Soal di paket ini
$query_jumlah_soal = mysqli_query($koneksi, "SELECT COUNT(*) as jumlah FROM paket_soal WHERE id_paket = '$id_paket'");
$jml_soal = mysqli_fetch_assoc($query_jumlah_soal);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Paket - <?= $data['nama_paket'] ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <a href="daftar_paket.php" class="text-decoration-none mb-3 d-block">← Kembali ke Daftar</a>
                
                <div class="card shadow border-0">
                    <div class="card-body p-5 text-center">
                        <span class="badge bg-primary mb-3"><?= $data['nama_kategori'] ?></span>
                        <h1 class="fw-bold mb-3"><?= $data['nama_paket'] ?></h1>
                        <p class="text-muted"><?= $data['deskripsi'] ?></p>
                        
                        <div class="row mt-5 mb-5">
                            <div class="col-4 border-end">
                                <h3 class="fw-bold"><?= $data['durasi_menit'] ?></h3>
                                <small class="text-muted">Menit</small>
                            </div>
                            <div class="col-4 border-end">
                                <h3 class="fw-bold"><?= $jml_soal['jumlah'] ?></h3>
                                <small class="text-muted">Jumlah Soal</small>
                            </div>
                            <div class="col-4">
                                <h3 class="fw-bold"><?= ($data['harga'] == 0) ? 'Gratis' : 'Rp '.number_format($data['harga']) ?></h3>
                                <small class="text-muted">Harga</small>
                            </div>
                        </div>

                        <?php if($data['harga'] > 0): ?>
                             <button class="btn btn-warning btn-lg w-100 text-white fw-bold mb-2">Beli Paket Ini</button>
                        <?php else: ?>
                             <a href="ujian_mulai.php?id=<?= $data['id_paket'] ?>" class="btn btn-primary btn-lg w-100 fw-bold">Mulai Kerjakan Sekarang</a>
                        <?php endif; ?>
                        
                        <div class="alert alert-info mt-3 text-start small">
                            <strong>Perhatian:</strong><br>
                            - Pastikan koneksi internet lancar.<br>
                            - Waktu akan berjalan otomatis saat tombol "Mulai" ditekan.<br>
                            - Dilarang membuka tab lain (Simulasi).
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>