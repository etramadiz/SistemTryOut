<?php
session_start();
include 'koneksi.php';

// --- 1. CEK LOGIN (WAJIB) ---
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    die();
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];

// --- 2. HITUNG STATISTIK (LOGIKA GABUNGAN) ---
if ($role == 'admin') {
    // A. JIKA ADMIN: Hitung data seluruh sistem
    $query_user = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE role='peserta'");
    $stats_user = mysqli_fetch_assoc($query_user);
    $total_peserta = $stats_user['total'];

    $query_paket = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM paket_tryout");
    $stats_paket = mysqli_fetch_assoc($query_paket);
    $total_paket = $stats_paket['total'];

    $query_ujian = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM percobaan_tryout WHERE status_pengerjaan='SELESAI'");
    $stats_ujian = mysqli_fetch_assoc($query_ujian);
    $total_ujian_selesai = $stats_ujian['total'];

} else {
    // B. JIKA PESERTA: Hitung data pribadi saja
    $query_history = mysqli_query($koneksi, "SELECT COUNT(*) as total_tryout, SUM(skor_total) as total_skor FROM percobaan_tryout WHERE id_user = '$id_user' AND status_pengerjaan='SELESAI'");
    $stats = mysqli_fetch_assoc($query_history);
    
    $total_ujian_selesai = $stats['total_tryout']; 
    // Rumus rata-rata (menghindari error pembagian nol)
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        /* CSS Custom untuk mempercantik */
        .stat-card { transition: transform 0.2s; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .stat-card:hover { transform: translateY(-5px); }
        
        .hover-scale { transition: transform 0.2s; }
        .hover-scale:hover { transform: scale(1.05); }
        
        /* Style Link Tabel (Fitur dari Temanmu) */
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
            <li class="nav-item"><a class="nav-link active fw-bold" href="index.php">Dashboard</a></li>
            
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

    <div class="container mt-4">
        
        <div class="p-5 mb-5 rounded-3 shadow-lg text-white" 
             style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); position: relative; overflow: hidden;">
            
            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -50px; left: -20px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>

            <div class="container-fluid py-2 position-relative">
                <?php if($role == 'admin'): ?>
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-4 fw-bold mb-3">Selamat Datang, Admin! 👋</h1>
                            <p class="fs-5 text-white-50 mb-4">
                                Pantau aktivitas peserta, kelola bank soal, dan distribusikan materi belajar.
                            </p>
                            
                            <div class="d-flex flex-wrap">
                                <a href="data_user.php" class="btn btn-light btn-lg px-4 py-3 fw-bold shadow-sm d-flex align-items-center mb-3" style="margin-right: 15px;">
                                    <span class="fs-4 me-2">👥</span> 
                                    <div class="text-start">
                                        <div class="small text-muted" style="font-size: 0.7rem; line-height: 1;">KELOLA</div>
                                        Data User
                                    </div>
                                </a>

                                <a href="admin_materi.php" class="btn btn-light btn-lg px-4 py-3 fw-bold shadow-sm d-flex align-items-center mb-3" style="margin-right: 15px;">
                                    <span class="fs-4 me-2">📚</span>
                                    <div class="text-start">
                                        <div class="small text-muted" style="font-size: 0.7rem; line-height: 1;">UPLOAD</div>
                                        Materi
                                    </div>
                                </a>

                                <a href="admin_paket.php" class="btn btn-warning btn-lg px-4 py-3 fw-bold shadow-sm d-flex align-items-center text-dark mb-3">
                                    <span class="fs-4 me-2">📦</span>
                                    <div class="text-start">
                                        <div class="small" style="font-size: 0.7rem; line-height: 1; opacity: 0.7;">MANAJEMEN</div>
                                        Paket Soal
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 d-none d-md-block text-center opacity-75">
                            <h1 style="font-size: 8rem;">⚙️</h1>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="text-center">
                        <h1 class="display-4 fw-bold">🚀 Siap Mengejar Mimpi?</h1>
                        <p class="col-md-8 fs-4 mx-auto text-white-50 mb-4">
                            Latihan soal UTBK & CPNS terbaik ada di sini. Asah kemampuanmu sekarang.
                        </p>
                        <a class="btn btn-warning btn-lg px-5 py-3 fw-bold shadow hover-scale text-dark" href="daftar_paket.php">
                            🔥 Mulai Tryout Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <h4 class="mb-3 fw-bold text-secondary">
            <?= ($role == 'admin') ? '📊 Ringkasan Sistem' : '📊 Statistik Kamu' ?>
        </h4>
        
        <div class="row mb-5">
            <?php if($role == 'admin'): ?>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Total Peserta</h6>
                            <h2 class="fw-bold text-primary"><?= $total_peserta ?> <span class="fs-6 text-muted">Orang</span></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Total Paket Soal</h6>
                            <h2 class="fw-bold text-success"><?= $total_paket ?> <span class="fs-6 text-muted">Paket</span></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Ujian Selesai</h6>
                            <h2 class="fw-bold text-warning"><?= $total_ujian_selesai ?> <span class="fs-6 text-muted">Kali</span></h2>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Total Tryout Selesai</h6>
                            <h2 class="fw-bold text-primary"><?= $total_ujian_selesai ?> <span class="fs-6 text-muted">Kali</span></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Rata-rata Skor</h6>
                            <h2 class="fw-bold text-success"><?= $rata_rata ?> <span class="fs-6 text-muted">Poin</span></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Status Akun</h6>
                            <h2 class="fw-bold text-info">Peserta</h2>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <h4 class="mb-3 fw-bold text-secondary">
            <?= ($role == 'admin') ? '⏳ Aktivitas User Terbaru' : '📜 Riwayat Terakhir Kamu' ?>
        </h4>
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
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
                        // LOGIKA QUERY DATA
                        if ($role == 'admin') {
                            // Query Admin: Ambil dari semua user
                            $query_aktivitas = mysqli_query($koneksi, "
                                SELECT pt.waktu_mulai, pt.skor_total, pt.status_pengerjaan, 
                                       p.nama_paket, u.nama_lengkap 
                                FROM percobaan_tryout pt 
                                JOIN paket_tryout p ON pt.id_paket = p.id_paket
                                JOIN user u ON pt.id_user = u.id_user
                                ORDER BY pt.waktu_mulai DESC LIMIT 5
                            ");
                        } else {
                            // Query Peserta: Ambil ID Percobaan juga (Penting buat link!)
                            $query_aktivitas = mysqli_query($koneksi, "
                                SELECT pt.id_percobaan, pt.waktu_mulai, pt.skor_total, pt.status_pengerjaan, 
                                       p.nama_paket, p.id_paket
                                FROM percobaan_tryout pt 
                                JOIN paket_tryout p ON pt.id_paket = p.id_paket
                                WHERE pt.id_user = '$id_user'
                                ORDER BY pt.waktu_mulai DESC LIMIT 5
                            ");
                        }

                        if(mysqli_num_rows($query_aktivitas) > 0){
                            while($row = mysqli_fetch_assoc($query_aktivitas)){
                                $badge = ($row['status_pengerjaan'] == 'SELESAI') ? 'bg-success' : 'bg-warning text-dark';
                                $tanggal = date('d M Y, H:i', strtotime($row['waktu_mulai']));
                                
                                // --- FITUR LINK CERDAS (DARI TEMANMU) ---
                                // Jika sedang mengerjakan, buat link agar bisa diklik
                                $nama_paket_display = $row['nama_paket'];
                                
                                if($role == 'peserta' && $row['status_pengerjaan'] == 'SEDANG MENGERJAKAN') {
                                    $nama_paket_display = "<a href='ujian_kerjakan.php?id={$row['id_percobaan']}' class='link-paket' title='Lanjut Mengerjakan'>
                                        {$row['nama_paket']} <i class='bi bi-arrow-right-circle-fill text-warning ms-1'></i>
                                    </a>";
                                }

                                echo "<tr>
                                    <td class='text-muted'>{$tanggal}</td>";
                                
                                if($role == 'admin') {
                                    echo "<td class='fw-bold'>".htmlspecialchars($row['nama_lengkap'])."</td>";
                                }

                                echo "<td>{$nama_paket_display}</td>
                                    <td><span class='badge $badge rounded-pill'>{$row['status_pengerjaan']}</span></td>
                                    <td class='fw-bold text-primary'>{$row['skor_total']}</td>
                                </tr>";
                            }
                        } else {
                            $cols = ($role == 'admin') ? 5 : 4;
                            echo "<tr><td colspan='$cols' class='text-center py-4 text-muted'>Belum ada aktivitas ujian yang terekam.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>