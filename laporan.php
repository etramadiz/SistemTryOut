<?php
session_start();
include 'koneksi.php';

// --- 1. CEK LOGIN ---
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    die();
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];

// Cek Admin (Hanya admin boleh akses ini)
if ($role != 'admin') {
    header("location:index.php");
    die();
}

// Logika Filter Paket
$id_paket_pilih = isset($_GET['paket']) ? $_GET['paket'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Ujian</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
                <li class="nav-item"><a class="nav-link" href="riwayat.php">Riwayat</a></li>
            <?php endif; ?>

            <?php if($role == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_paket.php">Kelola Paket</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_materi.php">Materi Belajar</a></li>
                <li class="nav-item"><a class="nav-link active fw-bold" href="laporan.php">Laporan</a></li>
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

    <div class="container mt-5">
        <h2 class="fw-bold mb-4 text-primary"><i class="bi bi-bar-chart-line-fill"></i> Laporan & Hasil Ujian</h2>

        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body bg-white p-4 rounded">
                <form action="" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Pilih Paket Ujian</label>
                        <select name="paket" class="form-select" required>
                            <option value="">-- Pilih Paket --</option>
                            <?php
                            $q_paket = mysqli_query($koneksi, "SELECT * FROM paket_tryout");
                            while($p = mysqli_fetch_assoc($q_paket)){
                                $selected = ($p['id_paket'] == $id_paket_pilih) ? 'selected' : '';
                                echo "<option value='{$p['id_paket']}' $selected>{$p['nama_paket']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="bi bi-search"></i> Tampilkan
                        </button>
                    </div>
                    
                    <?php if($id_paket_pilih != ''): ?>
                    <div class="col-md-3 ms-auto text-end">
                        <a href="export_excel.php?paket=<?= $id_paket_pilih ?>" class="btn btn-success fw-bold shadow-sm">
                            <i class="bi bi-file-earmark-excel-fill"></i> Download Excel
                        </a>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if($id_paket_pilih != ''): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Peringkat</th>
                                <th>Nama Peserta</th>
                                <th>Email</th>
                                <th>Tanggal Ujian</th>
                                <th>Skor Akhir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query Sakti: Join User + Percobaan, Urutkan berdasarkan Skor Tertinggi (DESC)
                            $query = mysqli_query($koneksi, "
                                SELECT u.nama_lengkap, u.email, pt.waktu_mulai, pt.skor_total, pt.status_pengerjaan
                                FROM percobaan_tryout pt
                                JOIN user u ON pt.id_user = u.id_user
                                WHERE pt.id_paket = '$id_paket_pilih' AND pt.status_pengerjaan = 'SELESAI'
                                ORDER BY pt.skor_total DESC
                            ");

                            $rank = 1;
                            if(mysqli_num_rows($query) > 0){
                                while($row = mysqli_fetch_assoc($query)){
                                    // Juara 1, 2, 3 dikasih icon piala
                                    $icon = "";
                                    if($rank == 1) $icon = "🥇";
                                    elseif($rank == 2) $icon = "🥈";
                                    elseif($rank == 3) $icon = "🥉";

                                    echo "<tr>
                                        <td class='fw-bold ps-3'>#{$rank} <span class='fs-5'>{$icon}</span></td>
                                        <td>{$row['nama_lengkap']}</td>
                                        <td>{$row['email']}</td>
                                        <td>".date('d/m/Y H:i', strtotime($row['waktu_mulai']))."</td>
                                        <td class='fw-bold text-primary fs-5'>{$row['skor_total']}</td>
                                        <td><span class='badge bg-success'>SELESAI</span></td>
                                    </tr>";
                                    $rank++;
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-5 text-muted'>Belum ada peserta yang menyelesaikan ujian ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>