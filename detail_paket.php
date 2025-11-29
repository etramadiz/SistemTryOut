<?php 
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['status'])) { header("location:login.php"); exit; }

$id_paket = $_GET['id'];
$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama']; // Ambil nama untuk Navbar

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

// --- LOGIKA CEK BELI ---
// Cek apakah user sudah pernah beli/transaksi paket ini yang statusnya SUCCESS
$cek_beli = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_user='$id_user' AND id_paket='$id_paket' AND status_transaksi='SUCCESS'");
$sudah_beli = mysqli_num_rows($cek_beli) > 0;

// Cek apakah paketnya gratis
$gratis = ($data['harga'] == 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Paket - <?= $data['nama_paket'] ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🎓 TryoutOnline</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="daftar_paket.php">Daftar Tryout</a></li>
            
            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                    Hi, <?= $nama_user ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profil.php">👤 Profil Saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">🚪 Logout</a></li>
                </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5 mb-5">
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

                        <div class="d-grid gap-2">
                            <?php if ($sudah_beli || $gratis): ?>
                                <a href="ujian.php?id=<?= $data['id_paket'] ?>" class="btn btn-primary btn-lg fw-bold">🚀 Mulai Kerjakan Sekarang</a>
                            <?php else: ?>
                                <div class="alert alert-warning text-center">
                                    Paket ini berbayar. Silakan beli terlebih dahulu untuk mengakses soal.
                                </div>
                                <a href="pembayaran.php?id=<?= $data['id_paket'] ?>" class="btn btn-success btn-lg fw-bold">🛒 Beli Paket Ini</a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4">
                            <a href="diskusi.php?id=<?= $data['id_paket'] ?>" class="btn btn-outline-secondary btn-sm">💬 Lihat Diskusi Soal Paket Ini</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>