<?php
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    die();
}

$id_user = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Tryout</title>
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
            <li class="nav-item"><a class="nav-link" href="daftar_paket.php">Daftar Tryout</a></li>
            <li class="nav-item"><a class="nav-link active" href="riwayat.php">Riwayat</a></li>
             <li class="nav-item"><a class="nav-link text-warning fw-bold" href="logout.php">Logout</a></li>
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
</body>
</html>