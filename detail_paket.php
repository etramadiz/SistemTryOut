<<<<<<< HEAD
=======
<?php 
session_start();
include 'koneksi.php';
session_start();

// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    die();
}

$id_paket = $_GET['id'];
$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Paket - <?= $data['nama_paket'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .detail-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .detail-header { background: linear-gradient(45deg, #0d6efd, #0dcaf0); height: 10px; }
        .stat-box { background-color: #f8f9fa; border-radius: 10px; padding: 15px; text-align: center; height: 100%; transition: 0.3s; }
        .stat-box:hover { background-color: #e9ecef; transform: translateY(-3px); }
        .stat-icon { font-size: 1.5rem; color: #0d6efd; margin-bottom: 5px; }
    </style>
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
            
            <?php if($role == 'peserta'): ?>
                <li class="nav-item"><a class="nav-link active" href="daftar_paket.php">Daftar Tryout</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php">Riwayat</a></li>
            <?php endif; ?>

            <?php if($role == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="data_user.php">Kelola User</a></li>
            <?php endif; ?>

            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle text-white fw-bold" href="#" data-bs-toggle="dropdown">
                    Hi, <?= explode(' ', $nama_user)[0] ?>
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
            <div class="col-lg-8">
                
                <a href="daftar_paket.php" class="text-decoration-none text-muted mb-3 d-inline-block hover-underline">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Paket
                </a>
                
                <div class="card detail-card">
                    <div class="detail-header"></div>
                    
                    <div class="card-body p-5 text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3 fw-bold">
                            <?= $data['nama_kategori'] ?>
                        </span>
                        
                        <h2 class="fw-bold mb-3"><?= $data['nama_paket'] ?></h2>
                        <p class="text-muted mb-5"><?= $data['deskripsi'] ?></p>
                        
                        <div class="row g-3 mb-5">
                            <div class="col-4">
                                <div class="stat-box">
                                    <div class="stat-icon"><i class="bi bi-stopwatch"></i></div>
                                    <h5 class="fw-bold mb-0"><?= $data['durasi_menit'] ?></h5>
                                    <small class="text-muted">Menit</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                                    <h5 class="fw-bold mb-0"><?= $jml_soal['jumlah'] ?></h5>
                                    <small class="text-muted">Soal</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <div class="stat-icon"><i class="bi bi-tag"></i></div>
                                    <h5 class="fw-bold mb-0 text-success">
                                        <?= ($data['harga'] == 0) ? 'Gratis' : 'Rp '.number_format($data['harga']/1000).'k' ?>
                                    </h5>
                                    <small class="text-muted">Harga</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 col-md-8 mx-auto">
                            <?php if ($sudah_beli || $gratis): ?>
                                <a href="ujian.php?id=<?= $data['id_paket'] ?>" class="btn btn-primary btn-lg fw-bold shadow-sm py-3">
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
                            
                            <a href="diskusi.php?id=<?= $data['id_paket'] ?>" class="btn btn-outline-secondary mt-2 border-0">
                                <i class="bi bi-chat-dots me-1"></i> Lihat Diskusi Soal
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
>>>>>>> 0b68867373fe0d10e213f7334de3ca0ee8096945
