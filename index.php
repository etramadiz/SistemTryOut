<?php
session_start();
include 'koneksi.php';

// --- CEK LOGIN ---
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    die();
}

// Ambil Data Session
$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];

// --- LOGIKA STATISTIK BERDASARKAN ROLE ---
if ($role == 'admin') {
    // 1. Hitung Total Peserta (Selain Admin)
    $query_user = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE role='peserta'");
    $data_user = mysqli_fetch_assoc($query_user);
    $total_peserta = $data_user['total'];

    // 2. Hitung Total Paket Soal
    $query_paket = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM paket_tryout");
    $data_paket = mysqli_fetch_assoc($query_paket);
    $total_paket = $data_paket['total'];

    // 3. Hitung Total Tryout yang Telah Dikerjakan (Oleh semua user)
    $query_tryout = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM percobaan_tryout WHERE status_pengerjaan='SELESAI'");
    $data_tryout = mysqli_fetch_assoc($query_tryout);
    $total_ujian_selesai = $data_tryout['total'];

} else {
    // LOGIKA PESERTA (Statistik Pribadi)
    $query_history = mysqli_query($koneksi, "SELECT COUNT(*) as total_tryout, SUM(skor_total) as total_skor FROM percobaan_tryout WHERE id_user = '$id_user' AND status_pengerjaan='SELESAI'");
    $stats = mysqli_fetch_assoc($query_history);
    
    $total_ujian_selesai = $stats['total_tryout']; // Variabel disamakan namanya biar gampang di HTML
    $rata_rata = ($stats['total_tryout'] > 0) ? round($stats['total_skor'] / $stats['total_tryout'], 1) : 0;
}
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
            
            <?php if($role == 'peserta'): ?>
                <li class="nav-item"><a class="nav-link" href="daftar_paket.php">Daftar Tryout</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php">Riwayat</a></li>
            <?php endif; ?>

           <?php if($role == 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="admin_paket.php">Kelola Paket</a></li>
            <li class="nav-item"><a class="nav-link bg-warning text-dark rounded px-3 mx-2 fw-bold" href="data_user.php">Kelola User</a></li>
             <?php endif; ?>

            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                    Hi, <?= $nama_user ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout / Keluar</a></li>
                </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
        <div class="p-5 mb-4 hero-section">
            <div class="container-fluid py-2">
                <?php if($role == 'admin'): ?>
                    <h1 class="display-5 fw-bold">Selamat Datang, Admin!</h1>
                    <p class="col-md-8 fs-4">Pantau aktivitas peserta dan kelola materi ujian dari dashboard ini.</p>
                    <a class="btn btn-light btn-lg fw-bold text-primary" href="data_user.php">Kelola Data User</a>
                <?php else: ?>
                    <h1 class="display-5 fw-bold">Siap Mengejar Mimpi?</h1>
                    <p class="col-md-8 fs-4">Latihan soal UTBK & CPNS terbaik ada di sini. Tingkatkan skormu sekarang juga.</p>
                    <a class="btn btn-light btn-lg fw-bold text-primary" href="daftar_paket.php">Mulai Tryout Baru</a>
                <?php endif; ?>
            </div>
        </div>

        <h4 class="mb-3 fw-bold text-secondary">
            <?= ($role == 'admin') ? 'Ringkasan Sistem' : 'Statistik Kamu' ?>
        </h4>
        
        <div class="row mb-5">
            <?php if($role == 'admin'): ?>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted">Total Peserta</h6>
                            <h2 class="fw-bold text-primary"><?= $total_peserta ?> <span class="fs-6 text-muted">Orang</span></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted">Total Paket Soal</h6>
                            <h2 class="fw-bold text-success"><?= $total_paket ?> <span class="fs-6 text-muted">Paket</span></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted">Total Ujian Selesai</h6>
                            <h2 class="fw-bold text-warning"><?= $total_ujian_selesai ?> <span class="fs-6 text-muted">Kali</span></h2>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted">Total Tryout Selesai</h6>
                            <h2 class="fw-bold text-primary"><?= $total_ujian_selesai ?> <span class="fs-6 text-muted">Kali</span></h2>
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
                            <h2 class="fw-bold text-warning">Peserta</h2>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <h4 class="mb-3 fw-bold text-secondary">
            <?= ($role == 'admin') ? 'Aktivitas User Terbaru' : 'Riwayat Terakhir Kamu' ?>
        </h4>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <?php if($role == 'admin') echo "<th>Nama Peserta</th>"; ?>
                            <th>Nama Paket</th>
                            <th>Status</th>
                            <th>Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($role == 'admin') {
                            // Query Admin: Tampilkan aktivitas dari SEMUA user, join tabel user untuk ambil nama
                            $query_aktivitas = mysqli_query($koneksi, "
                                SELECT pt.waktu_mulai, pt.skor_total, pt.status_pengerjaan, 
                                       p.nama_paket, u.nama_lengkap 
                                FROM percobaan_tryout pt 
                                JOIN paket_tryout p ON pt.id_paket = p.id_paket
                                JOIN user u ON pt.id_user = u.id_user
                                ORDER BY pt.waktu_mulai DESC LIMIT 5
                            ");
                        } else {
                            // Query Peserta: Hanya aktivitas sendiri
                            $query_aktivitas = mysqli_query($koneksi, "
                                SELECT pt.waktu_mulai, pt.skor_total, pt.status_pengerjaan, p.nama_paket 
                                FROM percobaan_tryout pt 
                                JOIN paket_tryout p ON pt.id_paket = p.id_paket
                                WHERE pt.id_user = '$id_user'
                                ORDER BY pt.waktu_mulai DESC LIMIT 5
                            ");
                        }

                        if(mysqli_num_rows($query_aktivitas) > 0){
                            while($row = mysqli_fetch_assoc($query_aktivitas)){
                                $badge = ($row['status_pengerjaan'] == 'SELESAI') ? 'bg-success' : 'bg-warning';
                                $tanggal = date('d M Y, H:i', strtotime($row['waktu_mulai']));
                                
                                echo "<tr>
                                    <td>{$tanggal}</td>";
                                
                                // Jika admin, tampilkan kolom nama peserta
                                if($role == 'admin') {
                                    echo "<td>".htmlspecialchars($row['nama_lengkap'])."</td>";
                                }

                                echo "<td>{$row['nama_paket']}</td>
                                    <td><span class='badge $badge'>{$row['status_pengerjaan']}</span></td>
                                    <td class='fw-bold'>{$row['skor_total']}</td>
                                </tr>";
                            }
                        } else {
                            $cols = ($role == 'admin') ? 5 : 4;
                            echo "<tr><td colspan='$cols' class='text-center py-3'>Belum ada aktivitas.</td></tr>";
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