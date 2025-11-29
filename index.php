<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    die();
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];

// Hitung Statistik
$query_history = mysqli_query($koneksi, "SELECT COUNT(*) as total_tryout, SUM(skor_total) as total_skor FROM percobaan_tryout WHERE id_user = '$id_user' AND status_pengerjaan='SELESAI'");
$stats = mysqli_fetch_assoc($query_history);
$rata_rata = ($stats['total_tryout'] > 0) ? round($stats['total_skor'] / $stats['total_tryout'], 1) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TryoutOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section { background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%); color: white; border-radius: 15px; }
        .stat-card { transition: transform 0.2s; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .stat-card:hover { transform: translateY(-5px); }
        /* Style tambahan untuk link tabel */
        .link-paket { text-decoration: none; font-weight: bold; color: #0d6efd; }
        .link-paket:hover { text-decoration: underline; color: #0a58ca; }
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
            
            <?php if($role == 'peserta'): ?>
                <li class="nav-item"><a class="nav-link" href="daftar_paket.php">Daftar Tryout</a></li>
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

    <div class="container mt-4">
        <div class="p-5 mb-4 hero-section shadow-sm">
            <div class="container-fluid py-2">
                <h1 class="display-5 fw-bold">Siap Mengejar Mimpi?</h1>
                <p class="col-md-8 fs-4">Latihan soal UTBK & CPNS terbaik ada di sini.</p>
                <a class="btn btn-light btn-lg fw-bold text-primary shadow-sm" href="daftar_paket.php">Mulai Tryout Baru</a>
            </div>
        </div>

        <h4 class="mb-3 fw-bold text-secondary">Statistik Kamu</h4>
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card stat-card bg-white p-3 mb-3">
                    <div class="card-body">
                        <h6 class="text-muted">Total Tryout Selesai</h6>
                        <h2 class="fw-bold text-primary"><?= $stats['total_tryout'] ?> <span class="fs-6 text-muted">Kali</span></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-white p-3 mb-3">
                    <div class="card-body">
                        <h6 class="text-muted">Rata-rata Skor</h6>
                        <h2 class="fw-bold text-success"><?= $rata_rata ?> <span class="fs-6 text-muted">Poin</span></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-white p-3 mb-3">
                    <div class="card-body">
                        <h6 class="text-muted">Status Akun</h6>
                        <h2 class="fw-bold text-warning"><?= ucfirst($role) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mb-3 fw-bold text-secondary">Aktivitas Terakhir</h4>
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Nama Paket (Klik untuk Aksi)</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // REVISI QUERY: Kita butuh id_paket dan id_percobaan untuk membuat link
                            $query_aktivitas = mysqli_query($koneksi, "
                                SELECT pt.id_percobaan, pt.waktu_mulai, pt.skor_total, pt.status_pengerjaan, p.nama_paket, p.id_paket 
                                FROM percobaan_tryout pt 
                                JOIN paket_tryout p ON pt.id_paket = p.id_paket
                                WHERE pt.id_user = '$id_user'
                                ORDER BY pt.waktu_mulai DESC LIMIT 5
                            ");

                            if(mysqli_num_rows($query_aktivitas) > 0){
                                while($row = mysqli_fetch_assoc($query_aktivitas)){
                                    
                                    // LOGIKA LINK CERDAS
                                    if ($row['status_pengerjaan'] == 'BERLANGSUNG') {
                                        $badge = 'bg-warning text-dark';
                                        // Jika Berlangsung -> Link ke ujian.php
                                        $link = "ujian.php?id=" . $row['id_paket'];
                                        $tooltip = "Lanjut Mengerjakan";
                                    } else {
                                        $badge = 'bg-success';
                                        // Jika Selesai -> Link ke hasil_ujian.php
                                        $link = "hasil_ujian.php?id=" . $row['id_percobaan'];
                                        $tooltip = "Lihat Hasil";
                                    }

                                    echo "<tr>
                                        <td class='ps-4'>".date('d M Y, H:i', strtotime($row['waktu_mulai']))."</td>
                                        <td>
                                            <a href='$link' class='link-paket' title='$tooltip'>
                                                ".$row['nama_paket']." <i class='small bi bi-box-arrow-up-right ms-1'></i>
                                            </a>
                                        </td>
                                        <td><span class='badge $badge rounded-pill'>".$row['status_pengerjaan']."</span></td>
                                        <td class='fw-bold pe-4 text-end'>".$row['skor_total']."</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Belum ada aktivitas.</td></tr>";
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