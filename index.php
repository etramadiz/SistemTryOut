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

// --- LOGIKA HITUNG STATISTIK (SESUAI ROLE) ---
if ($role == 'admin') {
    // Statistik untuk Admin (Total User, Paket, & Ujian Selesai)
    $q_user  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE role='peserta'"));
    $q_paket = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM paket_tryout"));
    $q_ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM percobaan_tryout WHERE status_pengerjaan='SELESAI'"));
    
    $stat1_label = "Total Peserta";
    $stat1_value = $q_user['total'] . " Orang";
    
    $stat2_label = "Total Paket Soal";
    $stat2_value = $q_paket['total'] . " Paket";
    
    $stat3_label = "Total Ujian Selesai";
    $stat3_value = $q_ujian['total'] . " Kali";

} else {
    // Statistik untuk Peserta (Riwayat Pribadi)
    $query_history = mysqli_query($koneksi, "SELECT COUNT(*) as total_tryout, SUM(skor_total) as total_skor FROM percobaan_tryout WHERE id_user = '$id_user' AND status_pengerjaan='SELESAI'");
    $stats = mysqli_fetch_assoc($query_history);
    $rata_rata = ($stats['total_tryout'] > 0) ? round($stats['total_skor'] / $stats['total_tryout'], 1) : 0;

    $stat1_label = "Total Tryout Selesai";
    $stat1_value = $stats['total_tryout'] . " Kali";
    
    $stat2_label = "Rata-rata Skor";
    $stat2_value = $rata_rata . " Poin";
    
    $stat3_label = "Status Akun";
    $stat3_value = "Peserta";
}
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
                <h1 class="display-5 fw-bold">Selamat Datang, <?= explode(' ', $nama_user)[0] ?>!</h1>
                <p class="col-md-8 fs-4">
                    <?php if($role == 'admin'): ?>
                        Panel Admin untuk mengelola sistem tryout.
                    <?php else: ?>
                        Siap untuk menaklukkan ujian hari ini? Yuk latihan!
                    <?php endif; ?>
                </p>
                <?php if($role == 'peserta'): ?>
                    <a class="btn btn-light btn-lg fw-bold text-primary shadow-sm" href="daftar_paket.php">Mulai Tryout Baru</a>
                <?php endif; ?>
            </div>
        </div>

        <h4 class="mb-3 fw-bold text-secondary">Statistik & Ringkasan</h4>
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card stat-card bg-white p-3 mb-3">
                    <div class="card-body">
                        <h6 class="text-muted"><?= $stat1_label ?></h6>
                        <h2 class="fw-bold text-primary"><?= $stat1_value ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-white p-3 mb-3">
                    <div class="card-body">
                        <h6 class="text-muted"><?= $stat2_label ?></h6>
                        <h2 class="fw-bold text-success"><?= $stat2_value ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-white p-3 mb-3">
                    <div class="card-body">
                        <h6 class="text-muted"><?= $stat3_label ?></h6>
                        <h2 class="fw-bold text-warning"><?= $stat3_value ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <?php if($role == 'peserta'): ?>
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
                                        $link = "ujian.php?id=" . $row['id_paket'];
                                        $tooltip = "Lanjut Mengerjakan";
                                    } else {
                                        $badge = 'bg-success';
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
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>