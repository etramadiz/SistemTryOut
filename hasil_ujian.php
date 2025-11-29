<?php
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    die();
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];

$id_percobaan = $_GET['id'];

// Ambil Data Hasil Ujian
$query = mysqli_query($koneksi, "
    SELECT pt.*, p.nama_paket, p.id_paket 
    FROM percobaan_tryout pt 
    JOIN paket_tryout p ON pt.id_paket=p.id_paket 
    WHERE id_percobaan='$id_percobaan'
");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian - <?= $data['nama_paket'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .score-circle {
            width: 150px; height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 4rem; font-weight: bold;
            margin: 0 auto;
            box-shadow: 0 10px 20px rgba(25, 135, 84, 0.3);
        }
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
                <li class="nav-item"><a class="nav-link" href="daftar_paket.php">Daftar Tryout</a></li>
                <li class="nav-item"><a class="nav-link active" href="riwayat.php">Riwayat</a></li>
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
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 text-center">
                        <h5 class="text-muted text-uppercase letter-spacing-1">Hasil Pengerjaan</h5>
                        <h3 class="fw-bold text-primary mt-2"><?= $data['nama_paket'] ?></h3>
                    </div>
                    <div class="card-body p-5 text-center">
                        
                        <div class="score-circle mb-4">
                            <?= $data['skor_total'] ?>
                        </div>
                        
                        <h5 class="text-muted">Skor Akhir Anda</h5>
                        <p class="small text-secondary">Diselesaikan pada: <?= date('d M Y, H:i', strtotime($data['waktu_selesai'])) ?></p>

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <a href="diskusi.php?id=<?= $data['id_paket'] ?>" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-chat-dots-fill me-2"></i> Lihat Pembahasan & Diskusi
                            </a>
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                <i class="bi bi-house-door-fill me-2"></i> Kembali ke Dashboard
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