<?php 
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    die();
}

$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Paket - TryoutOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hover-effect:hover { transform: translateY(-5px); transition: 0.3s; }
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

    <div class="container mt-5">
        <h3 class="fw-bold mb-4 text-primary">📚 Daftar Paket Tryout</h3>
        
        <div class="row">
            <?php
            $query = "SELECT p.*, k.nama_kategori FROM paket_tryout p JOIN kategori_ujian k ON p.id_kategori = k.id_kategori WHERE p.status_publish = 1";
            $result = mysqli_query($koneksi, $query);

            if(mysqli_num_rows($result) > 0) {
                while($paket = mysqli_fetch_assoc($result)) {
                    $harga_display = ($paket['harga'] == 0) ? '<span class="badge bg-success">GRATIS</span>' : '<span class="badge bg-warning text-dark">Rp '.number_format($paket['harga']).'</span>';
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 hover-effect">
                        <div class="card-header bg-white border-0 pt-4">
                            <span class="badge bg-info text-dark mb-2"><?= $paket['nama_kategori'] ?></span>
                            <h5 class="card-title fw-bold"><?= $paket['nama_paket'] ?></h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small"><?= $paket['deskripsi'] ?></p>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>⏱ Durasi:</span>
                                <span class="fw-bold"><?= $paket['durasi_menit'] ?> Menit</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>🏷 Harga:</span>
                                <?= $harga_display ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pb-4">
                            <a href="detail_paket.php?id=<?= $paket['id_paket'] ?>" class="btn btn-primary w-100 fw-bold">Lihat Detail & Kerjakan</a>
                        </div>
                    </div>
                </div>
            <?php 
                } 
            } else {
                echo '<div class="col-12"><div class="alert alert-info text-center">Belum ada paket tersedia.</div></div>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>