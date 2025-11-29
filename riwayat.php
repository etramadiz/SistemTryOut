<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat - TryoutOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h3 class="fw-bold mb-4 text-secondary">📜 Riwayat Hasil Ujian</h3>
        
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-3 ps-4">Tanggal</th>
                                <th class="py-3">Nama Paket</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3 text-center">Skor</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($koneksi, "
                                SELECT pt.*, p.nama_paket, p.id_paket 
                                FROM percobaan_tryout pt
                                JOIN paket_tryout p ON pt.id_paket = p.id_paket
                                WHERE pt.id_user = '$id_user' AND pt.status_pengerjaan = 'SELESAI'
                                ORDER BY pt.waktu_mulai DESC
                            ");

                            if(mysqli_num_rows($query) > 0){
                                while($row = mysqli_fetch_assoc($query)){
                                    $badge = ($row['status_pengerjaan'] == 'SELESAI') ? 'bg-success' : 'bg-warning';
                                    echo "<tr>
                                        <td class='ps-4'>".date('d/m/Y H:i', strtotime($row['waktu_mulai']))."</td>
                                        <td class='fw-bold text-primary'>".$row['nama_paket']."</td>
                                        <td class='text-center'><span class='badge $badge rounded-pill'>".$row['status_pengerjaan']."</span></td>
                                        <td class='text-center fw-bold fs-5'>".$row['skor_total']."</td>
                                        <td class='text-center'>
                                            <a href='hasil_ujian.php?id=".$row['id_percobaan']."' class='btn btn-sm btn-outline-primary'>Lihat Detail</a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-5 text-muted'><i>Belum ada riwayat ujian yang diselesaikan.</i></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>