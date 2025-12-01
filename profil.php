<?php
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    die();
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];

// Ambil data user terbaru dari database
$query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id_user'");
$data_user = mysqli_fetch_assoc($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - TryoutOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .profile-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .profile-header { background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%); height: 150px; border-radius: 15px 15px 0 0; }
        .profile-img-container { margin-top: -75px; text-align: center; }
        .profile-img { width: 150px; height: 150px; object-fit: cover; border: 5px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background-color: #e9ecef; }
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
                <li class="nav-item"><a class="nav-link" href="belajar.php">Materi Belajar</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php">Riwayat</a></li>
            <?php endif; ?>

            <?php if($role == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_paket.php">Kelola Paket</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_materi.php">Materi Belajar</a></li>
                <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
                <li class="nav-item"><a class="nav-link" href="data_user.php">Kelola User</a></li>
            <?php endif; ?>

            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle text-white fw-bold active" href="#" data-bs-toggle="dropdown">
                    Hi, <?= explode(' ', $nama_user)[0] ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item active" href="#">👤 Profil Saya</a></li>
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
                
                <div class="card profile-card">
                    <div class="profile-header"></div>
                    
                    <div class="card-body p-4">
                        <div class="profile-img-container mb-4">
                            <div class="profile-img rounded-circle d-flex align-items-center justify-content-center mx-auto text-primary display-1 fw-bold bg-white">
                                <?= substr($data_user['nama_lengkap'], 0, 1) ?>
                            </div>
                            <h3 class="mt-3 fw-bold"><?= $data_user['nama_lengkap'] ?></h3>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill text-uppercase">
                                <?= $data_user['role'] ?>
                            </span>
                        </div>

                        <hr class="my-4">

                        <form>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label text-muted fw-bold">Nama Lengkap</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control-plaintext fw-bold" value="<?= $data_user['nama_lengkap'] ?>" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label text-muted fw-bold">Email Address</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control-plaintext" value="<?= $data_user['email'] ?>" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label text-muted fw-bold">Status Akun</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control-plaintext text-uppercase" value="<?= $data_user['role'] ?>" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label text-muted fw-bold">Bergabung Sejak</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control-plaintext" value="<?= date('d F Y, H:i', strtotime($data_user['tanggal_daftar'])) ?>" readonly>
                                </div>
                            </div>
                        </form>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="index.php" class="btn btn-outline-secondary px-4">Kembali ke Dashboard</a>
                            </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
