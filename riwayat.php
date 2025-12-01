<?php
session_start();
include 'koneksi.php';

// --- 1. CEK LOGIN & AMBIL DATA SESSION (WAJIB ADA) ---
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    die();
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Tryout</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
            <li class="nav-item"><a class="nav-link" href="daftar_paket.php">Daftar Tryout</a></li>
            <li class="nav-item"><a class="nav-link" href="belajar.php">Materi Belajar</a></li>
            <li class="nav-item"><a class="nav-link active fw-bold" href="riwayat.php">Riwayat</a></li>

            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
        <h2 class="fw-bold mb-4">📜 Riwayat Hasil Ujian</h2>
        
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Paket</th>
                            <th>Status</th>
                            <th>Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "
                            SELECT pt.*, p.nama_paket 
                            FROM percobaan_tryout pt
                            JOIN paket_tryout p ON pt.id_paket = p.id_paket
                            WHERE pt.id_user = '$id_user'
                            ORDER BY pt.waktu_mulai DESC
                        ");

                        if(mysqli_num_rows($query) > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $badge = ($row['status_pengerjaan'] == 'SELESAI') ? 'bg-success' : 'bg-warning';
                                echo "<tr>
                                    <td>".date('d/m/Y H:i', strtotime($row['waktu_mulai']))."</td>
                                    <td>".$row['nama_paket']."</td>
                                    <td><span class='badge $badge'>".$row['status_pengerjaan']."</span></td>
                                    <td class='fw-bold'>".$row['skor_total']."</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>Belum ada riwayat.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>