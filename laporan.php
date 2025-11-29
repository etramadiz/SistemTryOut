<?php
session_start();
include 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    die();
}

// Logika Filter Paket
$id_paket_pilih = isset($_GET['paket']) ? $_GET['paket'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Ujian</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">📊 Laporan & Hasil</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_paket.php">Kelola Paket</a></li>
            </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5">
        <h2 class="fw-bold mb-4">🏆 Rekap Nilai Peserta</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
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
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Tampilkan</button>
                    </div>
                    
                    <?php if($id_paket_pilih != ''): ?>
                    <div class="col-md-3 ms-auto text-end">
                        <a href="export_excel.php?paket=<?= $id_paket_pilih ?>" class="btn btn-success fw-bold">
                            📥 Download Excel
                        </a>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if($id_paket_pilih != ''): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-striped table-hover align-middle">
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
                                    <td class='fw-bold'>#{$rank} {$icon}</td>
                                    <td>{$row['nama_lengkap']}</td>
                                    <td>{$row['email']}</td>
                                    <td>".date('d/m/Y H:i', strtotime($row['waktu_mulai']))."</td>
                                    <td class='fw-bold text-primary fs-5'>{$row['skor_total']}</td>
                                    <td><span class='badge bg-success'>SELESAI</span></td>
                                </tr>";
                                $rank++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-4'>Belum ada peserta yang menyelesaikan ujian ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>