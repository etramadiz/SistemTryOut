<?php
session_start();
include 'koneksi.php';
// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    exit;
}

$id = $_SESSION['id_user'];
// Ambil data terbaru dari database
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🎓 TryoutOnline</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link text-white" href="index.php">Kembali ke Dashboard</a></li>
            </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white pt-4 pb-0 border-0">
                        <h4 class="fw-bold text-primary">👤 Profil Pengguna</h4>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Nama Lengkap</label>
                                <input type="text" class="form-control fw-bold" value="<?= $user['nama_lengkap'] ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Email Address</label>
                                <input type="text" class="form-control" value="<?= $user['email'] ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Status Akun</label>
                                <input type="text" class="form-control" value="<?= strtoupper($user['role']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Bergabung Sejak</label>
                                <input type="text" class="form-control" value="<?= date('d F Y', strtotime($user['tanggal_daftar'])) ?>" readonly>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-white border-0 pb-4">
                        <a href="index.php" class="btn btn-secondary w-100">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>