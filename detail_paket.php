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

// Hitung Jumlah Soal
$jml_soal = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jumlah FROM paket_soal WHERE id_paket = '$id_paket'"));

// --- 3. LOGIKA CEK AKSES BELI ---
// Cek apakah user sudah beli?
$cek_beli = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_user='$id_user' AND id_paket='$id_paket' AND status_transaksi='SUCCESS'");
$sudah_beli = mysqli_num_rows($cek_beli) > 0;

// Paket Gratis?
$gratis = ($data['harga'] == 0);

// --- 4. [BARU] LOGIKA CEK STATUS PENGERJAAN ---
// Cek apakah user sudah SELESAI mengerjakan ujian ini?
$cek_selesai = mysqli_query($koneksi, "
    SELECT * FROM percobaan_tryout 
    WHERE id_user='$id_user' 
    AND id_paket='$id_paket' 
    AND status_pengerjaan='SELESAI'
");
$sudah_selesai_ujian = mysqli_num_rows($cek_selesai) > 0;

// Admin boleh akses semua (Beli & Diskusi)
if ($role == 'admin') {
    $sudah_beli = true; 
    $sudah_selesai_ujian = true; 
}
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

                        <div class="d-grid gap-2 col-md-8 mx-auto">
                            <?php if ($sudah_beli || $gratis): ?>
                                <a href="ujian_mulai.php?id=<?= $data['id_paket'] ?>" class="btn btn-primary btn-lg fw-bold shadow-sm py-3">
                                    <i class="bi bi-rocket-takeoff me-2"></i> Mulai Kerjakan Sekarang
                                </a>
                            <?php else: ?>
                                <div class="alert alert-warning text-center border-0 bg-warning bg-opacity-10 text-warning-emphasis">
                                    <i class="bi bi-lock-fill"></i> Paket ini berbayar. Silakan beli untuk akses.
                                </div>
                                <a href="pembayaran.php?id=<?= $data['id_paket'] ?>" class="btn btn-success btn-lg fw-bold shadow-sm py-3">
                                    <i class="bi bi-cart-plus me-2"></i> Beli Paket Ini
                                </a>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <?php if ($sudah_selesai_ujian): ?>
                                    <a href="diskusi.php?id=<?= $data['id_paket'] ?>" class="btn btn-outline-primary border-0 w-100">
                                        <i class="bi bi-chat-dots-fill me-1"></i> Lihat Diskusi & Pembahasan
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary border-0 w-100" disabled title="Selesaikan ujian terlebih dahulu untuk melihat pembahasan">
                                        <i class="bi bi-lock-fill me-1"></i> Diskusi Terkunci (Kerjakan Ujian Dulu)
                                    </button>
                                <?php endif; ?>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>