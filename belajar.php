<?php
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materi Belajar - TryoutOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        /* Styling agar mirip referensi */
        .card-materi {
            border: 1px solid #eee;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: white;
        }
        .card-materi:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: #0d6efd;
        }
        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        /* Warna-warni icon random */
        .bg-icon-1 { background-color: #e0f2fe; color: #0284c7; } /* Biru */
        .bg-icon-2 { background-color: #dcfce7; color: #16a34a; } /* Hijau */
        .bg-icon-3 { background-color: #fee2e2; color: #dc2626; } /* Merah */
        .bg-icon-4 { background-color: #fef9c3; color: #ca8a04; } /* Kuning */
        .bg-icon-5 { background-color: #f3e8ff; color: #9333ea; } /* Ungu */

        .link-belajar {
            text-decoration: none;
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }
        .link-belajar:hover {
            color: #0d6efd;
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
                <li class="nav-item"><a class="nav-link active fw-bold" href="belajar.php">Materi Belajar</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php">Riwayat</a></li>
            <?php endif; ?>

            <?php if($role == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_paket.php">Kelola Paket</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_materi.php">Materi Belajar</a></li>
                <li class="nav-item"><a class="nav-link" href="data_user.php">Kelola User</a></li>
            <?php endif; ?>

            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
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
        
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h3 class="fw-bold mb-1">📚 Pusat Belajar</h3>
                <p class="text-muted">Pelajari materi yang telah disiapkan untuk persiapan ujianmu.</p>
            </div>
        </div>

        <div class="row g-4">
            <?php
            // Ambil data materi dari database (yang diupload admin)
            $query = mysqli_query($koneksi, "SELECT * FROM materi ORDER BY id_materi DESC");

            if(mysqli_num_rows($query) > 0){
                $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    // Logika warna-warni icon biar tidak bosan
                    $color_class = 'bg-icon-' . (($i % 5) + 1); 
                    
                    // Icon random (book, file, calculator, dll)
                    $icons = ['bi-journal-text', 'bi-calculator', 'bi-translate', 'bi-graph-up', 'bi-file-earmark-pdf'];
                    $icon_class = $icons[($i % 5)];
            ?>
            
                <div class="col-md-6 col-lg-4">
                    <div class="card card-materi h-100 p-4 border-0 shadow-sm">
                        <div class="d-flex align-items-start">
                            <div class="icon-box <?= $color_class ?> me-3">
                                <i class="bi <?= $icon_class ?>"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2 text-dark"><?= $row['judul'] ?></h5>
                                <p class="text-muted small mb-3" style="min-height: 40px;">
                                    <?= substr($row['deskripsi'], 0, 80) . (strlen($row['deskripsi']) > 80 ? '...' : '') ?>
                                </p>
                                
                                <a href="file_materi/<?= $row['nama_file'] ?>" target="_blank" class="link-belajar">
                                    Mulai Belajar <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                <div class="mt-2">
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        <i class="bi bi-clock"></i> Upload: <?= date('d M Y', strtotime($row['tgl_upload'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php 
                    $i++;
                }
            } else {
                echo '<div class="col-12 text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="mb-3 opacity-50">
                        <h5 class="text-muted">Belum ada materi yang diupload oleh Admin.</h5>
                      </div>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>