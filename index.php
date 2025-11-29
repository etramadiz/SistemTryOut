<?php
include 'koneksi.php';

// --- SIMULASI LOGIN ---
// Ceritanya user sudah login. Kita pakai ID 2 (Budi Santoso) dari database kamu.
$id_user_login = 2; 

// Ambil data user
$query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id_user_login'");
$user = mysqli_fetch_assoc($query_user);

// Hitung statistik user dari tabel percobaan_tryout
$query_history = mysqli_query($koneksi, "SELECT COUNT(*) as total_tryout, SUM(skor_total) as total_skor FROM percobaan_tryout WHERE id_user = '$id_user_login' AND status_pengerjaan='SELESAI'");
$stats = mysqli_fetch_assoc($query_history);
$rata_rata = ($stats['total_tryout'] > 0) ? round($stats['total_skor'] / $stats['total_tryout'], 1) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Tryout</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        .hero-section { background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%); color: white; border-radius: 15px; }
        .stat-card { transition: transform 0.2s; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .stat-card:hover { transform: translateY(-5px); }
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
            <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="daftar_paket.php">Daftar Tryout</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Riwayat</a></li>
            <li class="nav-item ms-2"><span class="nav-link text-white fw-bold">Hi, <?= $user['nama_lengkap'] ?></span></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
        <div class="p-5 mb-4 hero-section">
            <div class="container-fluid py-2">
                <h1 class="display-5 fw-bold">Siap Mengejar Mimpi?</h1>
                <p class="col-md-8 fs-4">Latihan soal UTBK & CPNS terbaik ada di sini. Tingkatkan skormu sekarang juga.</p>
                <a class="btn btn-light btn-lg fw-bold text-primary" href="daftar_paket.php">Mulai Tryout Baru</a>
            </div>
        </div>

        <h4 class="mb-3 fw-bold text-secondary">Statistik Kamu</h4>
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card stat-card bg-white p-3">
                    <div class="card-body">
                        <h6 class="text-muted">Total Tryout Selesai</h6>
                        <h2 class="fw-bold text-primary"><?= $stats['total_tryout'] ?> <span class="fs-6 text-muted">Kali</span></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-white p-3">
                    <div class="card-body">
                        <h6 class="text-muted">Rata-rata Skor</h6>
                        <h2 class="fw-bold text-success"><?= $rata_rata ?> <span class="fs-6 text-muted">Poin</span></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-white p-3">
                    <div class="card-body">
                        <h6 class="text-muted">Status Akun</h6>
                        <h2 class="fw-bold text-warning"><?= ucfirst($user['role']) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mb-3 fw-bold text-secondary">Aktivitas Terakhir</h4>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Paket</th>
                            <th>Status</th>
                            <th>Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_aktivitas = mysqli_query($koneksi, "
                            SELECT pt.waktu_mulai, pt.skor_total, pt.status_pengerjaan, p.nama_paket 
                            FROM percobaan_tryout pt 
                            JOIN paket_tryout p ON pt.id_paket = p.id_paket
                            WHERE pt.id_user = '$id_user_login'
                            ORDER BY pt.waktu_mulai DESC LIMIT 5
                        ");

                        if(mysqli_num_rows($query_aktivitas) > 0){
                            while($row = mysqli_fetch_assoc($query_aktivitas)){
                                $badge = ($row['status_pengerjaan'] == 'SELESAI') ? 'bg-success' : 'bg-warning';
                                echo "<tr>
                                    <td>".date('d M Y, H:i', strtotime($row['waktu_mulai']))."</td>
                                    <td>".$row['nama_paket']."</td>
                                    <td><span class='badge $badge'>".$row['status_pengerjaan']."</span></td>
                                    <td class='fw-bold'>".$row['skor_total']."</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-3'>Belum ada aktivitas.</td></tr>";
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